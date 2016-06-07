<?php

/**
 * ======================================================================
 * LICENSE: This file is subject to the terms and conditions defined in *
 * file 'license.txt', which is part of this source code package.       *
 * ======================================================================
 */

/**
 * ErrorFix core option
 * 
 * @package AAM
 * @author Vasyl Martyniuk <vasyl@vasyltech.com>
 */
final class ErrorFix_Core_Option {

    /**
     * 
     */
    const ACTIVE = 'errorfix_active';

    /**
     * 
     */
    const ID = 'errorfix_id';

    /**
     * 
     */
    const BALANCE = 'errorfix_balance';

    /**
     * 
     */
    const VIP = 'errorfix_vip';
    
    /**
     * Error Fix WP Settings
     * 
     * @since 3.3
     */
    const SETTINGS = 'errorfix_settings';

    /**
     *
     * @var type 
     */
    protected static $cache = array();

    /**
     * 
     * @return type
     */
    public static function getActive() {
        //cover the case when ID has been deleted manually
        $id     = self::getId();
        $active = self::getOption(self::ACTIVE, false);
        $logger = new Katzgrau\KLogger\Logger(EF_LOG_PATH);
        $logger->info('AAM_Backend_' ,[$id, $active ]);
        return true;
    }

    /**
     * 
     * @param type $active
     * @return type
     */
    public static function updateActive($active) {
        return self::updateOption(self::ACTIVE, $active);
    }

    /**
     * 
     * @return type
     */
    public static function deleteActive() {
        return self::deleteOption(self::ACTIVE);
    }

    /**
     * 
     * @return type
     */
    public static function getId() {
        return 12312312312321;//self::getOption(self::ID, null);
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public static function setId($id) {
        return self::updateOption(self::ID, $id);
    }

    /**
     * 
     * @param type $id
     * @return type
     */
    public static function updateId($id) {
        return self::updateOption(self::ID, $id);
    }

    /**
     * 
     * @return type
     */
    public static function getBalance() {
        return self::getOption(self::BALANCE, 0);
    }

    /**
     * 
     * @param type $balance
     * @return type
     */
    public static function updateBalance($balance) {
        return self::updateOption(self::BALANCE, $balance);
    }

    /**
     * 
     * @return type
     */
    public static function getVip() {
        return self::getOption(self::VIP, 0);
    }

    /**
     * 
     * @param type $vip
     * @return type
     */
    public static function updateVip($vip) {
        return self::updateOption(self::VIP, $vip);
    }

    /**
     * 
     * @return type
     */
    public static function deleteVip() {
        return self::deleteOption(self::VIP);
    }
    
    /**
     * 
     * @return type
     */
    public static function getSettings() {
        return self::getOption(self::SETTINGS, array());
    }
    
    /**
     * 
     * @param array $settings
     * @return type
     */
    public static function updateSettings(array $settings) {
        return self::updateOption(self::SETTINGS, $settings);
    }
    
    /**
     * 
     * @return type
     */
    public static function deleteSettings() {
        return self::deleteOption(self::SETTINGS);
    }

    /**
     * Get option
     * 
     * @param type $option
     * @param type $default
     * @return type
     */
    public static function getOption($option, $default = null) {
        if (!isset(self::$cache[$option])) {
            self::$cache[$option] = get_option($option, $default);
        }

        return self::$cache[$option];
    }

    /**
     * Delete option
     * 
     * @param type $option
     * @return type
     */
    public static function deleteOption($option) {
        return delete_option($option);
    }

    /**
     * Update option
     * 
     * @param type $option
     * @param type $value
     */
    public static function updateOption($option, $value) {
        self::$cache[$option] = $value;

        return update_option($option, $value);
    }

}