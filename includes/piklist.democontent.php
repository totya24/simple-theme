<?php

Class PiklistDemoContent extends Singleton 
{
    public function __construct()
    {
        add_filter('piklist_admin_pages', [$this, 'my_admin_pages']);
    }
    
    function my_admin_pages($pages) {

        $pages[] = [
            'sub_menu' => 'piklist',
            'page_title' => 'Demo tartalom',
            'menu_title' => 'Demo tartalom',
            'menu_slug' => 'simple-demo-content',
            'capability' => 'manage_options'
        ];
    
        return $pages;
    }
}

PiklistDemoContent::getInstance();