<?php

/**
 * ======================================================================
 * LICENSE: This file is subject to the terms and conditions defined in *
 * file 'license.txt', which is part of this source code package.       *
 * ======================================================================
 */

/**
 * Backend ajax manager
 * 
 * @package ErrorFix
 * @author Vasyl Martyniuk <vasyl@vasyltech.com>
 */
class ErrorFix_Backend_Ajax {
    
    /**
     *
     * @var type 
     */
    protected $errorTypes = array(
        E_ERROR             => 'Fatal Error',
        E_WARNING           => 'Warning',
        E_PARSE             => 'Parse Error',
        E_NOTICE            => 'Notice',
        E_CORE_ERROR        => 'Core Fatal Error',
        E_CORE_WARNING      => 'Core Warning',
        E_COMPILE_ERROR     => 'Compiler Fatal Error',
        E_COMPILE_WARNING   => 'Compiler Warning',
        E_USER_ERROR        => 'User Fatal Error',
        E_USER_WARNING      => 'User Warning',
        E_USER_NOTICE       => 'User Notice',
        E_STRICT            => 'Strict Standards',
        E_RECOVERABLE_ERROR => 'Recoverable Fatal Error',
        E_DEPRECATED        => 'Deprecated',
        E_USER_DEPRECATED   => 'User Deprecated'
    );

    /**
     * 
     * @return type
     */
    public function process() {
        $action = filter_input(INPUT_POST, 'sub_action');

        if (method_exists($this, $action)) {
            $response = call_user_func(array($this, $action));
        } else {
            $response = null;
        }

        return json_encode($response);
    }

    /**
     * 
     * @return type
     */
    protected function getErrorList() {
        $response = array(
            'draw' => filter_input(INPUT_POST, 'draw'),
            'data' => array()
        );

        foreach (ErrorFix_Storage::getInstance()->getErrors() as $error) {
            $response['data'][] = array(
                $this->formatMessage($error),
                date('Y-m-d H:i:s', $error->time) . ' (' . $error->hits . ')',
                $error->module['name'],
                $this->getReportStatus($error),
                $error->status,
                'DT_RowClass' => str_replace(
                        ' ', '-', strtolower($this->getTypeLabel($error->type))
                ),
                'DT_RowId' => $error->hash
            );
        }

        return $response;
    }
    
    /**
     * 
     * @param type $error
     * @return string
     */
    protected function getReportStatus($error) {
        if ($error->status == 'resolved') {
            $status = $error->patch['id'];
        } elseif ($error->status == 'rejected') {
            $status = $error->reason;
        } else {
            $status = '';
        }
        
        return $status;
    }

    /**
     * 
     * @param type $error
     * @return string
     */
    protected function formatMessage($error) {
        $type     = $this->getTypeLabel($error->type);
        $message  = '<b>' . $type . ':</b> ' . $error->message . '<br/>';
        $message .= '<small>' . $error->relpath . ' ' . $error->line . '</small>';

        return $message;
    }
    
    /**
     * 
     * @param type $type
     * @return string
     */
    protected function getTypeLabel($type) {
        if (isset($this->errorTypes[$type])) {
            $label = $this->errorTypes[$type];
        } else {
            $label = 'Unknown';
        }
        
        return __($label, ERRORFIX_KEY);
    }

    /**
     * 
     * @return type
     */
    protected function getPatchList() {
        $response = array(
            'draw' => filter_input(INPUT_POST, 'draw'), 'data' => array()
        );

        foreach (ErrorFix_Storage::getInstance()->getPatchList() as $patch) {
            $response['data'][] = array(
                $patch['id'],
                false,
                $patch['errors'],
                sprintf('%01.2f', $patch['price']),
                ''
            );
        }

        return $response;
    }
    
    /**
     * 
     */
    protected function ignoreErrors() {
        ErrorFix_Storage::getInstance()->ignoreRejectedByCode(
                filter_input(INPUT_POST, 'code')
        );
    }
    
    /**
     * 
     * @return type
     */
    protected function getRejectedList() {
        $response = array(
            'draw' => filter_input(INPUT_POST, 'draw'), 'data' => array()
        );

        foreach (ErrorFix_Storage::getInstance()->getRejectedList() as $error) {
            if (isset($response['data'][$error->reason])) {
                $response['data'][$error->reason][0] ++;
            } else {
                $response['data'][$error->reason] = array(
                    1,
                    $error->reason,
                    $error->code
                );
            }
        }

        //reset array
        $response['data'] = array_values($response['data']);

        return $response;
    }

    /**
     * 
     */
    protected function getPieData() {
        $grouped = array();
        foreach (ErrorFix_Storage::getInstance()->getErrors() as $error) {
            if (!isset($grouped[$error->module['name']])) {
                $grouped[$error->module['name']] = 0;
            }
            $grouped[$error->module['name']] ++;
        }

        //decorate the response
        $response = array();
        foreach ($grouped as $module => $counter) {
            $response[] = array('label' => $module, 'value' => $counter);
        }

        return $response;
    }

    /**
     * 
     */
    protected function activate() {
        $response = array('status' => 'failure');

        //check if environment is registered already
        $registered = ErrorFix_Core_Option::getId();

        if ($registered === null) {
            $server = new ErrorFix_Server;
            $res = $server->register(site_url(), 'WordPress');
            if ($res->status === 'success') {
                ErrorFix_Core_Option::setId($res->instance);
                ErrorFix_Core_Option::updateBalance($res->balance);
                $registered = true;
            } else {
                $response['message'] = $res->reason;
            }
        }

        if ($registered && ErrorFix_Core_Option::updateActive(true)) {
            $response['status'] = 'success';
        }

        return $response;
    }

    /**
     * 
     * @return type
     */
    protected function balance() {
        $response = array(
            'status' => 'success',
            'balance' => sprintf('%01.2f', ErrorFix_Core_Option::getBalance())
        );

        $id = ErrorFix_Core_Option::getId();

        if ($id) {
            $server = new ErrorFix_Server;
            $res = $server->balance($id);
            if ($res->status === 'success') {
                ErrorFix_Core_Option::updateBalance($res->balance);
                ErrorFix_Core_Option::updateVip($res->vip);
                $response['balance'] = sprintf('%01.2f', $res->balance);
            }
        }

        return $response;
    }

    /**
     * 
     * @return type
     */
    protected function apply() {
        $result = false;

        $patch   = filter_input(INPUT_POST, 'patch');
        $patches = ErrorFix_Storage::getInstance()->getPatchList();

        if (isset($patches[$patch])) {
            try {
                $patcher = new ErrorFix_Patcher;
                $result  = $patcher->patch($patches[$patch]);
            } catch (Exception $e) {
                $result = $e->getMessage();
            }
        } else {
            $result = __('Failed to find the report', ERRORFIX_KEY);
        }

        return array(
            'status' => ($result !== true ? 'failure' : 'success'),
            'message' => ($result !== true ? $result : '')
        );
    }
    
    /**
     * 
     * @return type
     */
    protected function check() {
        $routine = new ErrorFix_Routine;
        
        return $routine->execute();
    }
    
    /**
     * 
     * @return type
     * 
     * @since 3.3
     */
    protected function updateSetting() {
        $setting = filter_input(INPUT_POST, 'setting');
        $value   = filter_input(INPUT_POST, 'value');
        
        $settings = ErrorFix_Core_Option::getSettings();
        $settings[$setting] = $value;
        
        $result = ErrorFix_Core_Option::updateSettings($settings);
        
        return array('status' => ($result ? 'success' : 'failure'));
    }
    
    /**
     * 
     * @return type
     */
    protected function sendMessage() {
        $response = array('status' => 'failure');
        
        $fullname = filter_input(INPUT_POST, 'fullname');
        $email    = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $message  = filter_input(INPUT_POST, 'message');
        $id       = ErrorFix_Core_Option::getId();

        if ($fullname && $email && $message && $id) {
            $server = new ErrorFix_Server;
            $res = $server->message($id, $fullname, $email, $message);
            if ($res->status === 'success') {
                $response['status'] = 'success';
                //save Fullname and Contact Email address for future
                $settings = ErrorFix_Core_Option::getSettings();
                $settings['contactFullname'] = $fullname;
                $settings['contactEmail']    = $email;
                ErrorFix_Core_Option::updateSettings($settings);
            } else {
                $response['reason'] = $res->reason;
            }
        } else {
            $response['reason'] = 'All fields are required to contact us';
        }

        return $response;
    }

}