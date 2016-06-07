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
 * Error Fix patcher
 * 
 * @package ErrorFix
 * @author Vasyl Martyniuk <vasyl@vasyltech.com>
 */
class ErrorFix_Patcher {

    /**
     *
     * @var type 
     */
    protected $patch;
    
    /**
     * 
     * @return type
     */
    public function patch(array $patch) {
        $this->setPatch($patch);

        //first get the patch from the storage
        if (!is_writeable($patch['filepath'])) {
            Throw new Exception(
                sprintf('File %s is not writable', $patch['relpath'])
            );
        }
        
        //make sure that file was not modified from the original state
        if ($patch['checksum'] != ErrorFix_File::getChecksum($patch['filepath'])) {
            Throw new Exception('File checksum mismatch');
        }

        //backup file
        if (!$this->backupFile()) {
            Throw new Exception(
                sprintf('Failed to backup %s file', $patch['relpath'])
            );
        }
        
        //retrieve patch from the external server & overwrite the file
        if (!file_put_contents($patch['filepath'], $this->retrievePatch())) {
            Throw new Exception(
                sprintf('Failed to overwrite %s file', $patch['relpath'])
            );
        }

        return true;
    }

    /**
     * 
     * @param type $basedir
     * @return type
     */
    protected function backupFile() {
        $res = true;

        if (class_exists('ZipArchive')) {
            $zipname  = ErrorFix_Core::get('basedir') . '/';
            $zipname .= date('M_d_Y') . '-backup.zip';
            $zip      = new ZipArchive;

            if (file_exists($zipname)) {
                $res = $zip->open($zipname);
            } else {
                $res = $zip->open($zipname, ZipArchive::CREATE);
            }

            $patch = $this->getPatch();
            if ($res && !$zip->locateName($patch['relpath'])) {
                $res = $zip->addFile($patch['filepath'], $patch['relpath']);
                $zip->close();
            }
        } else {
            Throw new Exception('PHP Zip extension is required');
        }

        return $res;
    }
    
    /**
     * 
     * @return type
     * @throws Exception
     */
    protected function retrievePatch() {
        $server = new ErrorFix_Server;
        $patch  = $this->getPatch();
        
        $response = $server->apply(
                ErrorFix_Core::get('instance'), $patch['id']
        );
        if ($response->status == 'success') {
            $source = base64_decode($response->content);
            if (md5($source) != $response->checksum) {
                Throw new Exception('Failed to get fix. Checksum mismatch');
            }
        } else {
            Throw new Exception($response->reason);
        }
        
        return $source;
    }

    /**
     * 
     * @param array $patch
     */
    protected function setPatch(array $patch) {
        $this->patch = $patch;
    }

    /**
     * 
     * @return type
     */
    protected function getPatch() {
        return $this->patch;
    }

}