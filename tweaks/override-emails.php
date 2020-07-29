<?php

add_action('admin_init', 'overrideSenderEmail');  

function overrideSenderEmail() {
    add_settings_section(
        'sender_email_settings',
        'Kimenő levelek',
        '',
        'general'
    );

    add_settings_field(
        'sender_email',
        'Email cím',
        function($args) {
            $option = get_option($args[0]);
            echo '<input type="text" class="regular-text" id="'. $args[0] .'" name="'. $args[0] .'" value="' . $option . '" />';
        },
        'general',
        'sender_email_settings',
        array( 'sender_email' )
    ); 

    add_settings_field(
        'sender_name',
        'Küldő neve',
        function($args) {
            $option = get_option($args[0]);
            echo '<input type="text" class="regular-text" id="'. $args[0] .'" name="'. $args[0] .'" value="' . $option . '" />';
        },
        'general',
        'sender_email_settings',
        array( 'sender_name' )
    );

    register_setting('general','sender_email', 'esc_attr');
    register_setting('general','sender_name', 'esc_attr');
}

add_filter( 'wp_mail_from', function(){
    $from_email = get_option('sender_email');
    if(empty($from_email)){
        $from_email = get_option('admin_email');
    }
    return $from_email;
} );

add_filter( 'wp_mail_from_name', function(){
    $from_name = get_option('sender_name');
    if(empty($from_name)){
        $from_name = get_bloginfo('name');
    }
    return $from_name;
} );