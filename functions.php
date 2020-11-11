<?php

if(isset($_GET['debug'])){
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
}

$currentLocale = get_locale();
if(!is_admin()){
    setlocale(LC_ALL, $currentLocale . '.utf8');
}

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