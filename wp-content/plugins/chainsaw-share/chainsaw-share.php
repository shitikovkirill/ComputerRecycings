<?php
	/*
	Plugin Name: Chainsaw - Share
	Plugin URI: http://inkwell.upstatement.com/plugins/inkwell-share
	Description: Integrates Simple Share so that you can .... simply share?
	Version: 0.1
	Author: Jared Novack + Upstatement
	Depends: Inkwell Core
	*/

	class ChainsawShare {

		var $default_services = array('twitter', 'facebook');
		var $ID;
		var $link;

		function __construct($services = array(), $pid = null){
			if ($pid === null && have_posts()){
				$pid = get_the_ID();
			}
			if (!count($services)){
				$services = $this->default_services;
			}
			foreach($services as &$service){
				$service = new ChainsawShareService($service);
			}
			$this->services = $services;
			$this->ID = $pid;
		}

		function get_services(){
			return $this->services;
		}

		function get_social_stats_data($url){
			$trans_name = 'social-stats-'.$url;
			if (false === ($data = get_transient($trans_name))){
				$json = self::get_json('http://social-count.eu01.aws.af.cm/'.$url);
				if ($json){
					set_transient($trans_name, $json);
				}
			}
			return $data;
		}

		function social_stats($service){
			if (isset($this->link)){
				$link = $this->link;
			} else {
				$link = TimberHelper::get_current_url();
			}
			$data = $this->get_social_stats_data($link);
			if (isset($data->$service)){
				return $data->$service;
			}
		}

		function get_shares($service='facebook'){
			if ($service == 'facebook'){
				return $this->social_stats('shares');
			}
		}

		function get_likes($service = 'facebook'){
			if ($service == 'facebook'){
				return $this->social_stats('likes');
			}
		}

		function get_markup($services = array('twitter', 'facebook'), $classes="simple-share"){
			$html = '<div class="'.$classes.'">';
			foreach($services as $service){
				$html .= '<a data-service="'.$service.'">'.$service.'</a>';
			}
			$html .= '</div>';
			return $html;
		}

		function get_likes_facebook($pid, $url){
			return $this->social_stats('likes');
		}

		function get_shares_facebook($pid, $url){
			return $this->social_stats('shares');
		}

		function get_shares_twitter($url){
			return $this->social_stats('tweets');
		}

		function get_shares_email($pid, $url){
			return get_post_meta($pid, 'shares_email', true);
		}

		public static function get_json($url) {
			$data = self::get_curl($url);
			return json_decode($data);
		}

		public static function get_curl($url) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
			$content = curl_exec($ch);
			curl_close($ch);
			return $content;
		}
	}

	class ChainsawShareService {

		function __construct($slug, $name = ''){
			$this->slug = $slug;
			if ($name){
				$this->name = $name;
			} else {
				$this->name = ucfirst($slug);
			}
		}

		function __toString(){
			return $this->slug;
		}

		function markup(){
			return '<a data-service="'.$this->slug.'" target="_blank">'.$this->name.'</a>';
		}

	}

	class ChainsawShareManager {

		function __construct(){
			add_action('wp_enqueue_scripts', array($this, 'load_scripts'));
			add_action('wp_enqueue_scripts', array($this, 'load_styles'));
			add_action('wp_ajax_count_share', array($this, 'ajax_count_share'));
		}

		function load_scripts(){
			wp_enqueue_script('simple-share', plugin_dir_url(__FILE__).'simple-share/jquery.simple-share.js', array('jquery'), false, true);
			wp_enqueue_script('inkwell-share', plugin_dir_url(__FILE__).'js/chainsaw-share.js', array('jquery'), false, true);
		}

		function load_styles(){
			$style = plugin_dir_url(__FILE__).'/simple-share/sst-style.css';
			wp_register_style('simple-share-css', $style);
			wp_enqueue_style('simple-share-css');
		}

		function ajax_count_share(){
			$pid = $_POST['pid'];
			$service = $_POST['service'];
			$this->count_share($pid, $service);
		}

		function count_share($pid, $service){
			$key = 'shares_'.$service;
			$shares = get_post_meta( $pid, $key, true);
			if ($shares){
				$shares++;
			} else {
				$shares = 1;
			}
			update_post_meta($pid, $key, $shares);
		}

		
	}

	$inkwellShareManager = new ChainsawShareManager();
