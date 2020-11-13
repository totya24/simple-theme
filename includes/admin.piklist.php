<?php

Class PiklistMods extends Singleton 
{

    private $isPiklistActive = false;

    public function __construct()
    {
        remove_action('wp_footer', ['piklist_theme', 'piklist_love'], 1000 );
        remove_action('wp_head', ['piklist_theme', 'version_in_header']);

        add_action('admin_init', [$this, 'piklistCheck']);

        add_filter('piklist_part_data', [$this, 'customCommentBlock'], 10, 2);
        add_filter('piklist_part_process_callback', [$this, 'showOnlyFrontpage'], 10, 2);
    }

    public function piklistCheck()
    {
        if(is_admin()){
            $this->isPiklistActive = is_plugin_active('piklist/piklist.php');

            if(!$this->isPiklistActive){
                add_action('admin_notices', [$this, 'showAdminWarning']);
            }
        }
    }

    function showAdminWarning()
    {
        $message = __('A sablon megfelelő működéséhez szükség van a <a href="https://piklist.com/" target="_blank">Piklist</a> bővítményre!', 'simple');
        printf( '<div class="notice notice-error"><p>%1$s</p></div>', $message );
    }
    
    public function customCommentBlock( $data, $folder )
    {
        if($folder != 'meta-boxes') {
            return $data;
        }
        
        $data['frontpage'] = 'Frontpage';
        return $data;
    }
    
    public function showOnlyFrontpage( $part, $type )
    {
        global $post;
        
        if($type != 'meta-boxes') {
            return $part;
        }
        
        if ($part['data']['frontpage']) {
            $homepageId = get_option( 'page_on_front' );
            if ($post->ID != $homepageId) {
                $part['data']['role'] = 'no-role';
            }
        }
        return $part;
    }
}

PiklistMods::getInstance();