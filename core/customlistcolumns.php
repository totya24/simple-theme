<?php

if( ! defined( 'ABSPATH' ) ) exit;

class CustomAdminListColumn
{
    public $columns;
    public $type;
    public $name;

    
    public function __construct( $name = 'post', $args = array(), $type = 'post' )
    {
        $this->name = $name;
        $this->columns = $args;

        if(empty($args)) return;

        if($type == 'post'){
            $this->type = 'post';
            add_filter('manage_'. $this->name .'_posts_columns', array($this, 'handleColumnHeaders'));
            add_action('manage_'. $this->name .'_posts_custom_column', array($this, 'handlePostColumnContents'), 10, 2);
        }

        if($type == 'tax'){
            $this->type = 'tax';
            add_filter('manage_edit-'. $this->name .'_columns', array($this, 'handleColumnHeaders') );
            add_filter('manage_'. $this->name .'_custom_column', array($this, 'handleTaxColumnContents'), 10, 3);
        }
    }
    
    public function handleColumnHeaders( $defaults )
    {
        $headers = array_keys($this->columns);
        $new = array();
        
        foreach ($defaults as $key => $value) {
            $before = $this->type == 'post' ? 'date' : 'posts';
            if ($key == $before) {
                if(is_array($headers)){
                    foreach($headers as $header) {
                        $sanitized = 'custom_'. sanitize_title($header);
                        $new[$sanitized] = $header;
                    }
                }
            }
            $new[$key] = $value;
        }
        return $new;
    }
    
    public function handlePostColumnContents( $column, $postId = null )
    {
        if(is_array($this->columns)){
            foreach($this->columns as $title => $callback){
                $sanitized = 'custom_'. sanitize_title($title);
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

    public function handleTaxColumnContents( $content, $column, $taxId = null )
    {
        if(is_array($this->columns)){
            foreach($this->columns as $title => $callback){
                $sanitized = 'custom_'. sanitize_title($title);
                if($column == $sanitized) {
                    if(is_object($callback) && ($callback instanceof Closure)) {
                        $callback($taxId);
                    } else {
                        echo $callback;
                    }
                }
            }
        }
    }
    
}