<?php

class WpTwig
{
    protected $version = '2.0.0';
    private static $instance;
    private static $twigLoader;
    protected static $twigEnvironment;
    private static $twigEnvironmentSettings;
    private static $globalVariables;
    public static $template;
    
    private function __construct()
    {
        add_action('init', array($this, 'setupTwigEnvironment'), 0, 0);
        if(!is_admin() || defined( 'DOING_AJAX' ) && DOING_AJAX){
            add_action('template_redirect', array($this, 'setGlobalVariables'), 9999);
        }
    }
    
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    public function setupTwigEnvironment()
    {
        global $themeOptions;
        self::setupTwigEnvironmentOptions();
        self::$twigLoader = new Twig_Loader_Filesystem(get_stylesheet_directory() . '/templates');
        if(!empty($themeOptions['twig']['paths']) && is_array($themeOptions['twig']['paths'])){
            foreach($themeOptions['twig']['paths'] as $alias => $dir){
                self::$twigLoader->addPath($dir, $alias);
            }
        }
        
        self::$twigEnvironment = new Twig_Environment(self::$twigLoader, self::$twigEnvironmentSettings);

        self::$twigEnvironment->addExtension(new \nochso\HtmlCompressTwig\Extension());
        self::$twigEnvironment->addExtension(new Teraone\Twig\Extension\StrftimeExtension());

        $translationFilter = new Twig_SimpleFilter('translate', function ($string) {
            return __($string, THEME_TEXTDOMAIN);
        });
        self::$twigEnvironment->addFilter($translationFilter); 

        $callStatic = new Twig_SimpleFunction("callStatic", function($class, $function, $args = array()){
            if (class_exists($class) && method_exists($class, $function))
                return call_user_func_array(array($class, $function), $args);
            return null;
        });

        self::$twigEnvironment->addFunction($callStatic);
    }
    
    private function setupTwigEnvironmentOptions()
    {
        global $themeOptions;
        $upload_dir = wp_upload_dir();
        $cache_dir = $upload_dir['basedir'] . "/twig_cache";
        
        self::$twigEnvironmentSettings = array(
            'charset' => get_bloginfo('charset'),
            'autoescape' => false,
            'auto_reload' => true,
            'cache' => $cache_dir,
            'debug' => $themeOptions['twig']['debug']
        );
    }
    
    public function setGlobalVariables()
    {
        self::$globalVariables = apply_filters('twig_site_variables', self::$globalVariables);
        
        foreach(self::$globalVariables as $name => $value) {
            self::$twigEnvironment->addGlobal($name, $value);
        }
    }
    
    public function renderTemplate($template, $values)
    {
        $values = apply_filters('twig_post_template_vars', $values);
        return self::$twigEnvironment->render($template, $values);
    }
}

$WpTwig = WpTwig::getInstance();

function twig_render($template = false, $values = array(), $echo = true)
{
    $WpTwig = WpTwig::getInstance();
    
    if (false === $echo) {
        return $WpTwig->renderTemplate($template, $values);
    } 
    
    echo $WpTwig->renderTemplate($template, $values);
}