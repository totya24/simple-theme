<?php

if( ! defined( 'ABSPATH' ) ) exit;

class CustomAdminListColumn
{
    var $columns;
    var $post_type;
    
    function __construct( $post_type = 'post', $args )
    {
        $this->post_type = $post_type;
        $this->columns = $args;
        
        if(!empty($args)){
            add_filter('manage_edit-'. $this->post_type .'_columns', array($this, 'handle_column_headers'));
            add_action('manage_'. $this->post_type .'_posts_custom_column', array($this, 'handle_column_contents'), 10, 2);
        }
    }
    
    function handle_column_headers( $defaults )
    {
        $headers = array_keys($this->columns);
        $new = array();
        foreach ($defaults as $key => $value) {
            if ($key == 'date') {
                if(is_array($headers)){
                    foreach($headers as $header) {
                        $sanitized = 'cuztom_'. sanitize_title($header);
                        $new[$sanitized] = $header;
                    }
                }
            }
            $new[$key] = $value;
        }
        return $new;
    }
    
    function handle_column_contents( $column, $postId = null )
    {
        if(is_array($this->columns)){
            foreach($this->columns as $title => $callback){
                $sanitized = 'cuztom_'. sanitize_title($title);
                if($column == $sanitized) {
                    if(is_object($callback) && ($callback instanceof Closure)) {
                        $callback($postId);
                    } else {
                        echo $callback;
                    }
                }
            }
        }
    }
    
}