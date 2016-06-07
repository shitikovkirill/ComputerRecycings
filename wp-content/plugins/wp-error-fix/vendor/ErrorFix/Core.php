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
 * Error Fix framework core
 * 
 * @package ErrorFix
 * @author Vasyl Martyniuk <vasyl@vasyltech.com>
 */
class ErrorFix_Core {

    /**
     * Single instance of itself
     * 
     * @var ErrorFix_Core
     * 
     * @access protected 
     */
    protected static $instance = null;
    
    /**
     * Error Fix global configurations
     * 
     * @var array
     * 
     * @access protected
     * @see ErrorFix_Core::bootstrap
     */
    protected static $config = array();
    
    /**
     * Core construct
     * 
     * Initialize the core framework object and register PHP error handlers for
     * errors, uncatched exceptions and script shutdown execution
     * 
     * @return void
     * 
     * @access protected
     */
    protected function __construct() {
        //set custom error handlers
        set_error_handler(array($this, 'errorHandler'));
        set_exception_handler(array($this, 'exceptionHandler'));
        register_shutdown_function(array($this, 'shutdownHandler'));
    }
    
    /**
     * Handle the PHP error
     * 
     * When triggered PHP error matches the error reporting level, this function
     * prepare the md5 checksum of the reported file and store the error info to
     * the Error Fix storage.
     * 
     * @param int    $type
     * @param string $message
     * @param string $filepath
     * @param int    $line
     * 
     * @return    bool  Always return false to trigger PHP core error handling
     * @staticvar array $cache
     * 
     * @access public
     */
    public function errorHandler($type, $message, $filepath, $line) {
        if (error_reporting() & $type) {
            $original = ErrorFix_File::getChecksum($filepath);
            $actual   = ErrorFix_File::getChecksum($filepath, true);

            //add only whe file exists and was not overwritten during the patching
            if ($original == $actual) {  
                ErrorFix_Storage::getInstance()->addError(array(
                    'type'     => $type,
                    'message'  => $message,
                    'filepath' => $filepath,
                    'line'     => $line,
                    'checksum' => $original,
                    'time'     => time()
                ));
            }
        }
        
        //let PHP core error handler finish the rest
        return false;
    }
    
    /**
     * Handle uncatched exception
     * 
     * @param Exception $e
     * 
     * @return void
     * 
     * @access public
     */
    public function exceptionHandler(Exception $e) {
        $this->errorHandler(
                E_ERROR, 
                get_class($e) . ': ' . $e->getMessage(), 
                $e->getFile(), 
                $e->getLine()
        );
    }
    
    /**
     * Handle PHP shut down process
     * 
     * If the shut down has been initiated by the Fatal Error, then this
     * function will store the error to the Error Fix storage.
     * 
     * In addition this function trigger storage normalization process as well
     * as ask Error Fix storage to save data if modified
     *
     * @return void
     * 
     * @access public
     * @see ErrorFix_Storage::normalize
     * @see ErrorFix_Storage::save
     */
    public function shutdownHandler() {
        $err = error_get_last();
        
        if ($err) {
            if (in_array($err['type'], array(E_ERROR, E_USER_ERROR))) {
                $this->errorHandler(
                    $err['type'], $err['message'], $err['file'], $err['line']
                );
            }
        }
        
        ErrorFix_Storage::getInstance()->normalize();
        ErrorFix_Storage::getInstance()->save();
    }

    /**
     * Autoloader for Error Fix framework
     *
     * Try to load a class if prefix is ErrorFix_
     *
     * @param string $classname
     * 
     * @return void
     * 
     * @access public
     * @static
     */
    public static function autoload($classname) {
        $chunks = explode('_', $classname);
        $prefix = array_shift($chunks);

        if ($prefix === 'ErrorFix') {
            $basedir  = dirname(__FILE__);
            $filename = $basedir . '/' . implode('/', $chunks) . '.php';
        }

        if (!empty($filename) && file_exists($filename)) {
            require($filename);
        }
    }

    /**
     * 
     * @param type $param
     * @param type $default
     * @return type
     */
    public static function get($param, $default = null) {
        return isset(self::$config[$param]) ? self::$config[$param] : $default;
    }

    /**
     * Register autoloader
     * 
     * @return void
     * 
     * @access public
     */
    public static function bootstrap(array $config = array()) {
        //set autoloader for the Error Fix framework
        spl_autoload_register('ErrorFix_Core::autoload');
        
        //set Error Fix configurations
        self::$config = $config;
        
        //create an instance of Error Fix Core
        self::$instance = new self;
    }

}