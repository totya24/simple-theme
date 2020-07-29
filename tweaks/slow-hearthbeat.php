<?php

add_filter( 'heartbeat_settings', function($settings) {
    $settings['interval'] = 60; 
    return $settings;
} );