<?php
/**
 * Created by PhpStorm.
 * User: kirill
 * Date: 10.05.16
 * Time: 20:18
 */

namespace TST;


class Widgets
{
    public function __construct(){
        add_action('widgets_init', [$this, 'add_widgets']);
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
}