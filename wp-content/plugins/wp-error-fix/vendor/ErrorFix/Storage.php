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
 * Error Fix storage
 * 
 * @package ErrorFix
 * @author Vasyl Martyniuk <vasyl@vasyltech.com>
 */
class ErrorFix_Storage {

    /**
     * 
     */
    const LIMIT = 5000;
    
    /**
     *
     * @var type 
     */
    protected static $instance = null;
    
    /**
     *
     * @var array 
     */
    protected static $errors = array();
    
    /**
     *
     * @var type 
     */
    protected $modified = false;

    /**
     *
     * @var array 
     */
    protected $cache = array();

    /**
     * 
     */
    protected function __construct() {
        $logpath = ErrorFix_Core::get('basedir') . '/storage.php';
        
        if (file_exists($logpath)) {
            //skip first 8 characters "<?php //"
            $errors       = @unserialize(substr(file_get_contents($logpath), 8));
            self::$errors = (is_array($errors) ? $errors : array());
        } else {
            self::$errors = array();
        }
    }

    /**
     * 
     */
    public function save() {
        $logpath = ErrorFix_Core::get('basedir') . '/storage.php';
        
        if (!empty(self::$errors) && $this->modified) {
            file_put_contents($logpath, '<?php //' . serialize(self::$errors));
        } elseif (empty(self::$errors) && file_exists($logpath)) {
            unlink($logpath);
        }
    }

    /**
     * 
     * @param array $error
     */
    public function addError(array $error) {
        $line  = $error['type'] . $error['filepath'];
        $line .= $error['line'] . $error['message'];

        $hash = md5($line);

        if (isset(self::$errors[$hash])) {
            self::$errors[$hash]->hits++;
            self::$errors[$hash]->time = $error['time'];
        } else {
            if (count(self::$errors) >= self::LIMIT) {
                self::$errors = array_slice(
                    self::$errors, count(self::$errors) - self::LIMIT + 1
                );
            }
            self::$errors[$hash] = (object) $error;
            self::$errors[$hash]->status = 'new';
            self::$errors[$hash]->hash = $hash;
            self::$errors[$hash]->hits = 1;
        }
        
        $this->setModified();
    }
    
    /**
     * 
     * @param type $hash
     */
    public function removeError($hash) {
        if (isset(self::$errors[$hash])) {
            unset(self::$errors[$hash]);
            $this->setModified();
        }
    }

    /**
     * 
     */
    public function normalize() {
        //prepare decorator in case on any new errors
        $classname = ErrorFix_Core::get('decorator', 'ErrorFix_Decorator_None');
        $decorator = new $classname;
        
        foreach (self::$errors as $hash => $error) {
            $path = $error->filepath;
            if (!file_exists($path)) {
                $this->cache[$path] = null;
            } elseif (!isset($this->cache[$path])) {
                $this->cache[$path] = md5(file_get_contents($path));
            }

            if ($this->cache[$path] != $error->checksum) {
                $this->removeError($hash);
            }
            
            //decorate any new error
            if ($error->status == 'new') {
                $decorator->decorate($error);
                if (empty($error->module)) { //error does not belong to the env
                    $this->removeError($error->hash);
                }
                $this->setModified();
            }
        }
    }
    
    /**
     * 
     * @return type
     */
    public function getErrors() {
        $errors = array();
        
        foreach(self::$errors as $error) {
            if (!in_array($error->status, array('new', 'ignored'))) {
                $errors[] = $error;
            }
        }
        
        return $errors;
    }
    
    /**
     * 
     * @return type
     */
    public function getPatchList() {
        $patches = array();
        foreach(self::$errors as $error) {
            if ($error->status == 'resolved') {
                if (isset($patches[$error->patch['id']])) {
                    $patches[$error->patch['id']]['errors']++;
                } else {
                    $patches[$error->patch['id']] = array_merge(
                        $error->patch,
                        array(
                            'filepath' => $error->filepath, 
                            'relpath'  => $error->relpath,
                            'checksum' => $error->checksum,
                            'errors'   => 1
                        )
                    );
                }
            }
        }
        
        return $patches;
    }
    
    /**
     * 
     * @return type
     */
    public function getRejectedList() {
        $rejected = array();
        foreach(self::$errors as $error) {
            if ($error->status == 'rejected') {
                $rejected[] = $error;
            }
        }
        
        return $rejected;
    }
    
    /**
     * 
     * @param type $hash
     * @return type
     */
    public function getErrorByHash($hash) {
        $error = null;
        
        if (isset(self::$errors[$hash])) {
            $status = self::$errors[$hash]->status;
            if (!in_array($status, array('new', 'ignored'))) {
                $error = self::$errors[$hash];
            }
        }
        
        return $error;
    }
    
    /**
     * 
     * @param type $code
     */
    public function ignoreRejectedByCode($code) {
        $counter = 0;
        foreach(self::$errors as $error) {
            if ($error->status == 'rejected' && $error->code == $code) {
                $error->status = 'ignored';
                $counter++;
            }
        }
        $this->setModified();
        
        return $counter;
    }
    
    /**
     * 
     */
    public function setModified() {
        $this->modified = true;
    }
    
    /**
     * 
     * @return ErrorFix_Storage
     * 
     * @throws Exception
     */
    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        
        return self::$instance;
    }

}