<?php

/**
  Plugin Name: WP Error Fix
  Description: Monitor your website for any errors and search for solutions.
  Version: 3.4.1
  Author: Vasyl Martyniuk <vasyl@vasyltech.com>
  Author URI: http://www.vasyltech.com

  -------
  LICENSE: This file is subject to the terms and conditions defined in
  file 'license.txt', which is part of this source package.
 *
 */
require_once 'vendor/autoload.php';
define('EF_LOG_PATH', __DIR__.'/logs');
/**
 * Main plugin's class
 * 
 * @package ErrorFix
 * @author Vasyl Martyniuk <vasyl@vasyltech.com>
 */
class ErrorFix {

    /**
     * Single instance of itself
     *
     * @var ErrorFix
     *
     * @access private
     */
    private static $_instance = null;

    /**
     * Initialize the ErrorFix Object
     *
     * @return void
     *
     * @access protected
     */
    protected function __construct() {
        if (is_admin()) { //bootstrap the backend interface if necessary
            ErrorFix_Backend_Manager::bootstrap();
        } elseif (filter_input(INPUT_GET, 'errorfix-connect')) {
            new ErrorFix_Connect;
        }
    }

    /**
     * Make sure that ErrorFix UI Page is used
     *
     * @return boolean
     *
     * @access public
     */
    public static function isErrorFix() {
        $page   = filter_input(INPUT_GET, 'page');
        $action = filter_input(INPUT_POST, 'action');
        
        return (is_admin() && in_array('errorfix', array($page, $action)));
    }

    /**
     * Initialize the ErrorFix plugin
     *
     * @return ErrorFix
     *
     * @access public
     * @static
     */
    public static function getInstance() {
        if (is_null(self::$_instance)) {
            load_plugin_textdomain(
                ERRORFIX_KEY, 
                false, 
                dirname(plugin_basename(__FILE__)) . '/lang/'
            );
            self::$_instance = new self;
        }

        return self::$_instance;
    }
    
    /**
     * Execute hourly routine
     * 
     * @return void
     * 
     * @access public
     */
    public static function cron() {
        if (ErrorFix_Core_Option::getActive()) {
            $routine = new ErrorFix_Routine;
            $routine->execute();
        }
    }
    
    /**
     * Send user email notifications
     * 
     * If configured, send twice a day an email notification to the user when
     * new errors occured or new fixes are available for download
     * 
     * @return void
     * 
     * @access public
     */
    public static function notify() {
        //get number of new errors
        $errors = 0;
        foreach(ErrorFix_Storage::getInstance()->getErrors() as $error) {
            $errors += (empty($error->notified) ? 1 : 0);
            $error->notified = true;
        }
        ErrorFix_Storage::getInstance()->save();
        
        //get number of available fixes
        $fixes = count(ErrorFix_Storage::getInstance()->getPatchList());
        
        $sender = new ErrorFix_Core_Sender;
        if ($errors) {
            $sender->sendErrorReport($errors);
        }
        
        if ($fixes) {
            $sender->sendFixReport($fixes);
        }
    }

    /**
     * Activate plugin
     * 
     * @return void
     * 
     * @access public
     */
    public static function activate() {
        global $wp_version;
        
        //check PHP Version
        if (version_compare(PHP_VERSION, '5.2') == -1) {
            exit(__('PHP 5.2 or higher is required.', ERRORFIX_KEY));
        } elseif (version_compare($wp_version, '3.8') == -1) {
            exit(__('WP 3.8 or higher is required.', ERRORFIX_KEY));
        }

        //create an wp-content/errorfix folder if does not exist
        if (file_exists(ERRORFIX_BASEDIR) === false) {
            @mkdir(ERRORFIX_BASEDIR, fileperms(ABSPATH) & 0777 | 0755);
            @file_put_contents(ERRORFIX_BASEDIR . '/index.php', '<?php');
        }
    }

    /**
     * Uninstall hook
     *
     * Remove all leftovers from ErrorFix execution
     *
     * @return void
     *
     * @access public
     */
    public static function uninstall() {
        //remove errorfix working files. Leave the directory for backups
        if (file_exists(ERRORFIX_BASEDIR)) {
            @unlink(ERRORFIX_BASEDIR . '/storage.php');
            @unlink(ERRORFIX_BASEDIR . '/queue.php');
        }
        
        //delete options but not all in case customer will want to re-install
        ErrorFix_Core_Option::deleteActive();
        ErrorFix_Core_Option::deleteVip();
        ErrorFix_Core_Option::deleteSettings();
        
        //clear schedules
        wp_clear_scheduled_hook('errorfix-cron');
        wp_clear_scheduled_hook('errorfix-notification');
    }

}

if (defined('ABSPATH')) {
    //define few common constants
    define('ERRORFIX_MEDIA', plugins_url('/media', __FILE__));
    define('ERRORFIX_KEY', 'wp-error-fix');
    define('ERRORFIX_BASEDIR', WP_CONTENT_DIR . '/errorfix');
    
    //register autoloader
    require (dirname(__FILE__) . '/autoloader.php');
    ErrorFix_Autoloader::register();
    
    //the lowest priority
    add_action('init', 'ErrorFix::getInstance');
    
    //bootstrap the ErrorFix framework
    require (dirname(__FILE__) . '/vendor/ErrorFix/bootstrap.php');
    
    //schedule cron
    if (!wp_next_scheduled('errorfix-cron')) {
        wp_schedule_event(time(), 'hourly', 'errorfix-cron');
    }
    add_action('errorfix-cron', 'ErrorFix::cron');
    
    //schedule notification cron
    if (!wp_next_scheduled('errorfix-notification')) {
        wp_schedule_event(time(), 'twicedaily', 'errorfix-notification');
    }
    add_action('errorfix-notification', 'ErrorFix::notify');

    //activation & deactivation hooks
    register_activation_hook(__FILE__, array('ErrorFix', 'activate'));
    register_uninstall_hook(__FILE__, array('ErrorFix', 'uninstall'));
}