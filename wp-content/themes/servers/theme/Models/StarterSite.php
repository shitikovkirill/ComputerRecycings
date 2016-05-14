<?php

class StarterSite extends TimberSite {

    function __construct() {
        add_theme_support( 'post-formats' );
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'menus' );
        add_filter( 'timber_context', array( $this, 'add_to_context' ) );
        add_filter( 'get_twig', array( $this, 'add_to_twig' ) );

        parent::__construct();
    }

    function add_to_context( $context ) {
        $context['menu_footer_top'] = new TimberMenu("Menu footer top");
        $context['menu_footer_mid'] = new TimberMenu("Menu footer mid");
        $context['site'] = $this;
        return $context;
    }

    function myfoo( $text ) {
        $text .= ' bar!';
        return $text;
    }

    function add_to_twig( $twig ) {
        /* this is where you can add your own fuctions to twig */
        $twig->addExtension( new Twig_Extension_StringLoader() );
        $twig->addFilter('myfoo', new Twig_SimpleFilter('myfoo', array($this, 'myfoo')));
        return $twig;
    }

}