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
            'name' => 'Text content on home page (right)',
            'id' => 'home-page-right',
            'class'=>'',
            'description' => 'Appears in home page (right)',
            'before_widget' => '',
            'after_widget' => '',
            'before_title' => '<h1>',
            'after_title' => '</h1>',
        ) );

        register_sidebar( array(
            'name' => 'Text content on home page (left)',
            'id' => 'home-page',
            'class'=>'',
            'description' => 'Appears in home page',
            'before_widget' => '',
            'after_widget' => '',
            'before_title' => '<h1>',
            'after_title' => '</h1>',
        ) );
    }
}