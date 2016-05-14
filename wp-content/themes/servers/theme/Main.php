<?php

namespace Theme;

use Amostajo\WPPluginCore\Plugin as Theme;

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
		// i.e.
		// add_action( 'save_post', [ &$this, 'save_post' ] );
		// 
		// $this->add_action( 'save_post', 'PostController@save' );
		// 
		// $this->add_shortcode( 'hello_world', 'view@shout', [ 'message' => 'Hello World!' ] );
		add_action( 'wp_enqueue_scripts', [&$this, 'add_css'] );
		add_action( 'wp_enqueue_scripts', [$this, 'add_js'] );
		add_action('widgets_init', [&$this, 'add_widgets']);
	}

	/**
	 * Declares admin dashboard HOOKS.
	 * - Can be removed if not used.
	 * @since 1.0
	 */
	public function on_admin()
	{
		// i.e.
		// add_action( 'admin_init', [ &$this, 'admin_init' ] );
		// 
		// $this->add_action( 'admin_init', 'AdminController@init' );
	}



	public function add_css() {
		wp_enqueue_style( 'style', get_template_directory_uri().'/assets/css/style.css' );
		wp_enqueue_style( 'media', get_template_directory_uri().'/assets/css/media.css' );
		wp_enqueue_style( 'flexslider', get_template_directory_uri().'/assets/flexslider/flexslider.css' );
		wp_enqueue_style( 'mob_menu', get_template_directory_uri().'/assets/css/mob_menu.css' );
		wp_enqueue_style( 'slicknav', get_template_directory_uri().'/assets/css/slicknav.css' );
		wp_enqueue_style( 'jquery', get_template_directory_uri().'/assets/css/jquery.css' );
		wp_enqueue_style( 'jquery-ui', 'http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css' );
	}

	public function add_widgets(){

		register_sidebar( array(
			'name' => 'Home left sidebar',
			'id' => 'home_left',
			'before_widget' => '<div>',
			'after_widget' => '</div>',
			'before_title' => '<h1>',
			'after_title' => '</h1>',
		) );

		register_sidebar( array(
			'name' => 'Home right sidebar',
			'id' => 'home_right',
			'before_widget' => '<div>',
			'after_widget' => '</div>',
			'before_title' => '<h2>',
			'after_title' => '</h2>',
		) );
	}

	public function add_js() {
		// header
		wp_deregister_script( 'jquery' );
		wp_enqueue_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js', array(), false, false );
		wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/assets/js/modernizr.js', array('jquery'), false, false );
		wp_enqueue_script( 'slicknav', get_template_directory_uri() . '/assets/js/jquery.slicknav.js', array('jquery'), false, false );
		wp_add_inline_script('slicknav', "jQuery(document).ready(function(){
        jQuery('#mob_menu').slicknav();
        });");

		wp_enqueue_script( 'flexslider', get_template_directory_uri() . '/assets/flexslider/jquery.flexslider.js', array('jquery'), false, false );
		wp_add_inline_script('flexslider', 'jQuery(window).load(function(){
        jQuery(".flexslider").flexslider({
            animation: "slide",
            randomize: true,
            pauseOnAction: false,
            pauseOnHover: false,
            touch: true,
            controlNav: false,
            start: function(slider){
                jQuery("body").removeClass("loading");
            }
        });
    });');

		wp_enqueue_script( 'jquery-ui', 'http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css', array('jquery'), false, false );
		wp_add_inline_script('jquery-ui',"jQuery(document).ready(function(){
                jQuery('.abc').hide();
                jQuery('#maincontainer').hide();
            });

            var precon = '';
            function opensesame(con, obj){

                jQuery('.menu a').removeClass('active');

                jQuery('#'+obj).addClass('active');

                if(precon == con){
                    closesesame();
                    precon ='';
                    return;
                }else{
                    precon = con;
                }

                if(!jQuery('#maincontainer').is(':hidden')){
                    jQuery('.abc').fadeOut('fast');
                    jQuery('#'+con).delay(100).show('slide', {direction: 'right'}, 1000);
                }else{
                    jQuery('#'+con).show();
                    jQuery('#maincontainer').slideDown('slow');
                }
            }

            function closesesame(){
                jQuery('#maincontainer').slideUp('slow',function(){
                    jQuery('.abc').hide();
                });
            }

            jQuery(function() {
                jQuery('.showhide').click(function() {
                    jQuery('.ph_slidediv').slideToggle();
                });
            });");

		wp_enqueue_script( 'jquery_2', get_template_directory_uri() . '/assets/js/jquery.js', array('jquery'), false, false );
		wp_enqueue_script( 'jquery_002', get_template_directory_uri() . '/assets/js/jquery_002.js', array('jquery_2'), false, false );
		wp_add_inline_script('jquery_002','/* <![CDATA[ */
            jQuery(function(){
                jQuery("#name").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter the Required field"
                });
                jQuery("#inquiry_2").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "Please enter the Required field"
                });

                jQuery("#email").validate({
                    expression: "if (VAL.match(/^[^\\W][a-zA-Z0-9\\_\\-\\.]+([a-zA-Z0-9\\_\\-\\.]+)*\\@[a-zA-Z0-9_]+(\\.[a-zA-Z0-9_]+)*\\.[a-zA-Z]{2,4}$/)) return true; else return false;",
                    message: "Please enter a valid Email ID"
                });
            });
            /* ]]> */');

		// footer
		wp_enqueue_script( 'getclicky', '//static.getclicky.com/js', array(), false, true );
		wp_add_inline_script('getclicky', "var _gaq = _gaq || [];
            _gaq.push(['_setAccount', 'UA-4859999-4']);
            _gaq.push(['_trackPageview']);
            _gaq.push(['global._setAccount', 'UA-76380621-1']);
            _gaq.push(['global._trackPageview']);

            (function() {
                var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
            })();", 'before');

		wp_add_inline_script('getclicky', "try{ clicky.init(100698899); }catch(e){}");
	}
}