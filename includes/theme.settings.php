<?php

Class ThemeSettings extends Singleton 
{
    public function __construct()
    {
        add_filter( 'piklist_admin_pages', [$this, 'themeSettingsPage' ]);
    }

    public function themeSettingsPage( $pages )
    {
        $pages[] = [
            'page_title' => 'Sablon beállításai',
            'menu_title' => 'Sablon beállításai',
            'sub_menu' => 'themes.php',
            'capability' => 'manage_options',
            'menu_slug' => 'theme_settings',
            'setting' => 'theme_settings',
            'menu_icon' => 'dashicons-art',
            'page_icon' => 'dashicons-art',
            'single_line' => true,
            'default_tab' => 'Általános',
            'save_text' => 'Beállítások mentése'
        ];

        return $pages;
    }
}

ThemeSettings::getInstance();