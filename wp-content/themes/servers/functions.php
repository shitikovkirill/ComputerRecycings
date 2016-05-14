<?php
//------------------------------------------------------------
//
// NOTE:
//
// Try NOT to add any code line in this file.
//
// Use "theme\Main.php" to add your hooks.
//
//------------------------------------------------------------

if ( ! class_exists( 'Timber' ) ) {
    add_action( 'admin_notices', function() {
        echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
    } );
    return;
}

require_once( __DIR__ . '/boot/bootstrap.php' );