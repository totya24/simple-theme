<?php

Class TwigGlobals extends Singleton 
{
    public function __construct()
    {
        if(!is_admin()){
            add_filter('twig_site_variables', array($this, 'addTwigGlobals'));
            add_filter('twig_post_template_vars', array($this, 'addTwigGlobals'));
        }
    }
    
    public function addTwigGlobals( $globals )
    {
        $isAjax = wp_doing_ajax();

        $themeDirectory = get_template_directory();
        
        $themeData = wp_get_theme();
        
        $privacyPolicyPageId = get_option( 'wp_page_for_privacy_policy' );
        
        $theHeader = '';
        $theFooter = '';
        $languageAttributes = '';
        
        if(!$isAjax){
            ob_start();
            wp_head();
            $theHeader = ob_get_clean();
            
            ob_start();
            wp_footer();
            $theFooter = ob_get_clean();
            
            ob_start();
            language_attributes();
            $languageAttributes = ob_get_clean();
        }
        
        $themeSettings = get_option('theme_settings');
        
        $globals['site'] = array(
            'title' => wp_title('&ndash;', false),
            'charset' => get_bloginfo( 'charset' ),
            'bodyClass' => implode( ' ', get_body_class() ),
            'languageAttributes' => $languageAttributes,
            'lang' => get_locale(),
            'baseUrl' => get_bloginfo( 'url' ),
            'themeUrl' => get_stylesheet_directory_uri(),
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'cssFileTime' => 0,
            'headerContent' => $theHeader,
            'footerContent' => $theFooter,
            'privacyPolicyPage' => get_post_status( $privacyPolicyPageId ) == 'publish',
            'privacyPolicyUrl' => get_permalink( $privacyPolicyPageId )
        );
        
        if( file_exists($themeDirectory . '/assets/css/style.min.css') ){
            $globals['site']['cssFileTime'] = filemtime( $themeDirectory . '/assets/css/style.min.css' );
        }
        return $globals;
    }
}

TwigGlobals::getInstance();