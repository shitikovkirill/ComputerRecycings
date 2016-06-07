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
 * Error Fix REST API
 * 
 * @package ErrorFix
 * @author Vasyl Martyniuk <vasyl@vasyltech.com>
 */
class ErrorFix_Server {
    
    /**
     * 
     * @param type $site
     * @param type $environment
     * 
     * @return type
     */
    public function register($site, $environment) {
        return $this->send(
            '/register', 
            array('site' => $site, 'environment' => $environment)
        );
    }
    
    /**
     * 
     * @param type $instance
     * @param type $patch
     * 
     * @return type
     */
    public function apply($instance, $patch) {
        return $this->send(
            '/apply', array('instance' => $instance, 'patch' => $patch)
        );
    }

    /**
     * 
     * @param type $instance
     * @param type $module
     * @param type $version
     * @param type $file
     * @param type $line
     * @param type $type
     * @param type $message
     * @param type $checksum
     * @return type
     */
    public function report($instance,$module, $version, $file, $line, $type,
                                                         $message, $checksum) {
        return $this->send(
            '/report', 
            array(
                'instance' => $instance,
                'module'   => $module,
                'version'  => $version,
                'file'     => $file,
                'line'     => $line,
                'type'     => $type,
                'message'  => $message,
                'checksum' => $checksum
            )
        );
    }
    
    /**
     * 
     * @param type $instance
     * @return type
     */
    public function balance($instance) {
        return $this->send('/balance', array('instance' => $instance));
    }
    
    /**
     * 
     * @param stdClass $error
     * @return type
     */
    public function check($instance, $report) {
        return $this->send(
                '/check', array('instance' => $instance, 'report' => $report)
        );
    }
    
    /**
     * 
     * @param type $instance
     * @param type $fullname
     * @param type $email
     * @param type $message
     * @return type
     */
    public function message($instance, $fullname, $email, $message) {
        return $this->send(
            '/message', 
            array(
                'instance' => $instance, 
                'fullname' => $fullname,
                'email'    => $email,
                'message'  => $message,
            )
        );
    }
    
    /**
     * 
     * @param type $uri
     * @param array $params
     */
    protected function send($uri, array $params) {
        //initialiaze the curl and send the request
        $ch = curl_init();

        // set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, ErrorFix_Core::get('endpoint') . $uri);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        
        $res = json_decode(curl_exec($ch));
        curl_close($ch);
        
        if (is_null($res)) {
            $res = (object) array('status' => 'failure');
            $res->reason = 'Failed to execute the request';
        }
        
        return $res;
    }

}