<?php

function tweakDisableFeed() {
    wp_die( __( 'No feed available, please visit the <a href="'. esc_url( home_url( '/' ) ) .'">homepage</a>!' ) );
}
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'feed_links', 2 );
add_action('do_feed', 'tweakDisableFeed', 1);
add_action('do_feed_rdf', 'tweakDisableFeed', 1);
add_action('do_feed_rss', 'tweakDisableFeed', 1);
add_action('do_feed_rss2', 'tweakDisableFeed', 1);
add_action('do_feed_atom', 'tweakDisableFeed', 1);
add_action('do_feed_rss2_comments', 'tweakDisableFeed', 1);
add_action('do_feed_atom_comments', 'tweakDisableFeed', 1);