<?php

if(isset($_GET['debug'])){
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
}

$currentTheme = wp_get_theme();

$currentLocale = get_locale();
if(!is_admin()){
    setlocale(LC_ALL, $currentLocale . '.utf8');
}

define('THEME_TEXTDOMAIN', $currentTheme->template);

$themeOptions = array(
    'textdomain' => THEME_TEXTDOMAIN,
    'usePiklist' => true,
    'twig' => array(
        'debug' => false,
        'paths' => array(
            'svg' => get_template_directory() . '/assets/svg'
        )
    ),
    'adminAssets' => array(
        'css' => get_template_directory_uri() . '/assets/css/admin.css',
        'js' => false,
        'editorStyle' => false
    ),
    'disableJquery' => true,
    'addScriptJs' => true,
);

require_once('core/core.php');

$tweaks = glob(get_template_directory()."/tweaks/*.php");
if(is_array($tweaks)){
    foreach($tweaks as $tweak){
        require_once($tweak);
    }
}

$includes = glob(get_template_directory()."/includes/*.php");
if(is_array($includes)){
    foreach($includes as $include){
        require_once($include);
    }
}