<?php

Class Optimize
{
    public function __construct()
    {
        # Remove wp version from the head
        remove_action( 'wp_head', 'wp_generator' ); 
        add_filter( 'the_generator', '__return_empty_string' );

        # Theme supports - https://www.daddydesign.com/wordpress/how-to-add-features-in-wordpress-using-add_theme_support-function/
        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );

        if ( isset($_GET['debug']) && $_GET['debug'] == 1 ) {
            show_admin_bar( true );
        } else {
            show_admin_bar( false );
        }
    }
}

$optimize = new Optimize();