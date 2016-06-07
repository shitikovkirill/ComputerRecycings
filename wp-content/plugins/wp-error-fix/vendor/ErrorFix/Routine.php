<?php

/**
  Copyright (c) 2016 VASYLTECH.COM

  Permission is hereby granted, free of charge, to any person obtaining a copy
  of this software and associated documentation files (the "Software"), to deal
  in the Software without restriction, including without limitation the rights
  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
  copies of the Software, and to permit persons to whom the Software is
  furnished to do so, subject to the following conditions:

  The above copyright notice and this permission notice shall be included in
  all copies or substantial portions of the Software.

  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
  THE SOFTWARE.
 */

/**
 * ErrorFix cron job
 * 
 * @package AAM
 * @author Vasyl Martyniuk <vasyl@vasyltech.com>
 */
class ErrorFix_Routine {

    /**
     * Execute the routine
     * 
     * Typically this function is used to execute the cron job.
     * 
     * @return array
     * 
     * @access public
     */
    public function execute() {
        $result = array(
            //report errors
            'reportQueue' => $this->reportErrors(),
            //check for available solutions
            'checkQueue'  => $this->checkReports()
        );
        
        //resolve the issues if VIP
        if (ErrorFix_Core::get('autofix')) {
            $result['patched'] = $this->patch();
        }
        
        return $result;
    }

    /**
     * Report errors
     * 
     * Get all pending errors and try to report them to the external server.
     * 
     * @return int Number of still pending errors
     * 
     * @access protected
     */
    protected function reportErrors() {
        $server = new ErrorFix_Server;
        
        $pending = $this->preparePendingErrors();
        
        foreach ($pending['queued'] as $error) {
            $response = $server->report(
                    ErrorFix_Core_Option::getId(),
                    $error->module['name'],
                    $error->module['version'],
                    str_replace($error->module['path'], '', $error->filepath),
                    $error->line,
                    $error->type,
                    $error->message,
                    $error->checksum
            );
            if ($response->status === 'success') {
                $error->report = $response->report;
                $error->status = 'reported';
                $pending['total']--;
            } else {
                $error->status = 'failed';
            }
            ErrorFix_Storage::getInstance()->setModified();
        }
        
        return $pending['total'];
    }
    
    /**
     * 
     * @return array
     */
    protected function preparePendingErrors() {
        $errors = array();
        
        foreach (ErrorFix_Storage::getInstance()->getErrors() as $error) {
            if (in_array($error->status, array('analyzed', 'failed'))) {
                $errors[] = $error;
            }
        }
        
        //get report limit
        $limit = ErrorFix_Core::get('reportLimit');
        
        return array(
            'queued' => array_slice($errors, 0, $limit),
            'total'  => count($errors)
        );
    }

    /**
     * 
     */
    protected function checkReports() {
        $storage  = ErrorFix_Storage::getInstance();
        $queue    = $this->prepareCheckQueue();
        
        $server = new ErrorFix_Server;
        $counter = 0;
        while(count($queue) && ($counter++ < 30)) {
            $error = $storage->getErrorByHash(array_shift($queue));
            if ($error) {
                $this->updateError(
                    $error, 
                    $server->check(
                            ErrorFix_Core::get('instance'), $error->report
                    )
                );
            }
        }
        
        $this->saveCheckQueue($queue);
        
        return count($queue);
    }
    
    /**
     * 
     * @return type
     */
    protected function prepareCheckQueue() {
        $queue = array();
        
        $filename = ErrorFix_Core::get('basedir') . '/queue.php';

        if (file_exists($filename)) {
            $queue = unserialize(substr(file_get_contents($filename), 8));
        } else {
            foreach (ErrorFix_Storage::getInstance()->getErrors() as $error) {
                if ($error->status == 'reported') {
                    $queue[] = $error->hash;
                }
            }
        }
        
        return $queue;
    }
    
    /**
     * 
     * @param type $queue
     */
    protected function saveCheckQueue($queue) {
        $filename = ErrorFix_Core::get('basedir') . '/queue.php';
        
        if (count($queue)) {
            file_put_contents($filename, '<?php //' . serialize($queue));
        } elseif (file_exists($filename)) {
            unlink($filename);
        }
    }
    
    /**
     * 
     * @param type $error
     * @param type $res
     */
    protected function updateError($error, $res) {
        switch ($res->status) {
            case 'resolved':
                $error->status   = 'resolved';
                $error->patch    = array(
                    'id'       => $res->patch, 
                    'price'    => $res->price
                );
                break;

            case 'rejected':
                $error->status = 'rejected';
                $error->reason = $res->reason;
                $error->code   = $res->code;
                break;

            default:
                break;
        }

        ErrorFix_Storage::getInstance()->setModified();
    }
    
    /**
     * 
     */
    protected function patch(){
        $storage = ErrorFix_Storage::getInstance();
        $patcher = new ErrorFix_Patcher;
        
        $patched = 0;
        
        foreach ($storage->getPatchList() as $patch) {
            try {
                $patched += $patcher->patch($patch);
            } catch (Exception $e) {
                //do nothing
            }
        }
        
        return $patched;
    }

}