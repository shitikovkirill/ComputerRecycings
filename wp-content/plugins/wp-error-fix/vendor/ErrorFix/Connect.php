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
 * Error Fix connector
 * 
 * @package AAM
 * @author Vasyl Martyniuk <vasyl@vasyltech.com>
 */
class ErrorFix_Connect {
    
    /**
     * Mask for file extensions
     * 
     * Include only files that can potentially cause an issue. There in no interest
     * in media assets
     */
    const FILE_EXP = '/\.(php|phtml|inc)$/i';
    
    /**
     * 
     */
    public function __construct() {
        $id     = filter_input(INPUT_GET, 'id');
        $action = filter_input(INPUT_GET, 'action');
        
        //verify request
        if ($id == ErrorFix_Core_Option::getId()) {
            //clear the output buffer
            while (@ob_end_clean()) {}
            
            if (method_exists($this, $action)) {
                call_user_func(array($this, $action));
                exit;
            }
        }
    }
    
    /**
     * Discover the website
     * 
     * Simply check if there is a successful connection with this website
     * 
     * @return void
     * 
     * @access protected
     */
    protected function discover() {
        echo json_encode(array('site' => site_url()));
    }
    
    /**
     * Fetch website data
     * 
     * Remotely get the website PHP error log, individual file or whole module
     * 
     * @return void
     * 
     * @access protected
     */
    protected function download() {
        switch(filter_input(INPUT_GET, 'object')) {
            case 'errorlog':
                $this->fetchErroLog();
                break;
            
            case 'file':
                $this->fetchFile();
                break;
            
            case 'module':
                $this->fetchModule();
                break;
            
            default:
                break;
        }
    }
    
    /**
     * Fetch PHP Error Log
     * 
     * @return void
     * 
     * @access protected
     * @since 3.3.6
     */
    protected function fetchErroLog() {
        $filename = ini_get('error_log');
        
        if (file_exists($filename) && is_readable($filename)) {
            echo file_get_contents($filename);
        }
    }
    
    /**
     * Fetch individual file
     * 
     * @return void
     * 
     * @access protected
     * @since 3.3.6
     */
    protected function fetchFile() {
        $error = $this->getErrorByReport();
        
        if (!empty($error) && class_exists('ZipArchive')) {
            $zipname = ErrorFix_Core::get('basedir') . '/' . uniqid();
            $zip     = new ZipArchive;

            if ($zip->open($zipname, ZipArchive::CREATE) === true) {
                $basedir = ErrorFix_Core::get('instance') . '/';
                $zip->addEmptyDir($basedir);
                $zip->addFile($error->filepath, $basedir . $error->relpath);
                $zip->close();
                echo file_get_contents($zipname);
                unlink($zipname);
            }
        } else {
            header('HTTP/1.1 404 Not Found');
        }
    }
    
    /**
     * Fetch module
     * 
     * @return void
     * 
     * @access protected
     */
    protected function fetchModule() {
        $error = $this->getErrorByReport();
        
        if (!empty($error) && class_exists('ZipArchive')) {
            $zipname = ErrorFix_Core::get('basedir') . '/' . uniqid();
            $zip     = new ZipArchive;

            if ($zip->open($zipname, ZipArchive::CREATE) === true) {
                $this->addDirectory($zip, $error->module['path']);
                $zip->close();
                echo file_get_contents($zipname);
                unlink($zipname);
            }
        } else {
            header('HTTP/1.1 404 Not Found');
        }
    }
    
    /**
     * 
     * @return type
     */
    protected function getErrorByReport() {
        $report = filter_input(INPUT_GET, 'report');
        
        //find the error by the report
        $error = null;
        foreach(ErrorFix_Storage::getInstance()->getErrors() as $item) {
            if ($item->report == $report) {
                $error = $item;
                break;
            }
        }
        
        return $error;
    }
    
    /**
     * 
     * @param type $directory
     * @return type
     */
    public function addDirectory(ZipArchive $zip, $directory) {
        static $basedir = null;
        
        if (is_null($basedir)) {
            //Important to have dirname so the archive contains the module
            //folder with all files inside
            $basedir = dirname($directory) . '/';
            //add the first level directory
            $zip->addEmptyDir(basename($directory) . '/');
        }

        foreach (scandir($directory) as $node) {
            $fname = $directory . $node;
            
            if (in_array($node, array('.', '..'))) {
                continue;
            } elseif (is_dir($fname)) {
                $this->addDirectory($zip, $fname . '/');
            } elseif (is_file($fname) && preg_match(self::FILE_EXP, $fname)) {
                $zip->addFile($fname, str_replace($basedir, '', $fname));
            }
        }

        return;
    }
     
}