<?php

namespace Theme;

use Amostajo\WPPluginCore\Plugin as Theme;

require_once 'Models/StarterSite.php';
/**
 * Main class.
 * Registers HOOKS used within the plugin.
 * Acts like a bridge or router of actions between Wordpress and the plugin.
 *
 * @link http://wordpress-dev.evopiru.com/documentation/main-class/
 * @version 1.0
 */
class Main extends Theme
{
	/**
	 * Declares public HOOKS.
	 * - Can be removed if not used.
	 * @since 1.0
	 */
	public function init()
	{
		\Timber::$dirname = array('templates', 'views');
		new \StarterSite();
		$this->add_action( 'wp_enqueue_scripts', 'AssertsController@addCss' );
		$this->add_action( 'wp_enqueue_scripts', 'AssertsController@addJs' );
		$this->add_action( 'widgets_init', 'AssertsController@addWidgets' );

	}

	/**
	 * Declares admin dashboard HOOKS.
	 * - Can be removed if not used.
	 * @since 1.0
	 */
	public function on_admin()
	{

	}


}

