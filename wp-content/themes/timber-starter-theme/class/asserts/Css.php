<?php
/**
 * Created by PhpStorm.
 * User: kirill
 * Date: 10.05.16
 * Time: 20:16
 */

namespace TST_Asserts;


class Css
{
    public function __construct(){
        add_action( 'wp_enqueue_scripts', [$this, 'add_css'] );
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
}