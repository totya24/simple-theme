<?php

Class Assets extends Singleton
{
    protected $themeUri = null;

    public function __construct()
    {
        $this->themeUri = get_template_directory_uri();

        add_action('wp_enqueue_scripts', [$this, 'addScriptsStyles']);
        //add_action('admin_enqueue_scripts', array($this, 'addAdminScriptsStyles'));
    }

    public function addScriptsStyles() {
        if(!is_admin()) {
            wp_enqueue_script( 'uikit', $this->themeUri . '/assets/js/uikit.min.js', [], '1.0', true );
            wp_enqueue_script( 'uikit-icons', $this->themeUri . '/assets/js/uikit-icons.min.js', [], '1.0', true );
            
            wp_register_script( 'theme', $this->themeUri . '/assets/js/scripts.js', [], '1.1', true  );
            wp_enqueue_script( 'theme' );

            wp_enqueue_style( 'uikit', $this->themeUri . '/assets/css/style.min.css' );
		}
    }

    public function addAdminScriptsStyles() {
        wp_enqueue_style('admin-style', $this->themeUri . '/assets/css/admin.css');
        wp_enqueue_script('admin-script', $this->themeUri . '/assets/js/admin.js', [], '1.0', true);
    }
}

Assets::getInstance();