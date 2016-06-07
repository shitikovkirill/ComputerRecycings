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

if (defined('ABSPATH') && !defined('ERRORFIX_LOADED')) {
    require dirname(__FILE__) . '/Core.php';

    ErrorFix_Core::bootstrap(array(
        'decorator'   => 'ErrorFix_Decorator_WordPress',
        'instance'    => ErrorFix_Core_Option::getId(),
        'basedir'     => ERRORFIX_BASEDIR,
        'reportLimit' => (ErrorFix_Core_Option::getVip() ? 20 : 5),
        'checkLimit'  => 20,
        'autofix'     => (ErrorFix_Core_Option::getVip() ? true : false),
        'endpoint'    => 'http://' . (getenv('ENV') == 'dev' ? 'dev.errorfix-server/v2' : 'errorfix.vasyltech.com/v2')
    ));
    
    //error fix can be loaded only once
    define('ERRORFIX_LOADED', true);
}