<?php

Class ThemeFunctions extends Singleton 
{
    public function __construct()
    {
        /**
         * Ha hozzá szeretnnk adni saját képméretet, akkor az alábbi módon lehetséges.
         * Bővebben: https://developer.wordpress.org/reference/functions/add_image_size/
         */
        //add_image_size( 'slider', 300, 300, true);

        /**
         * Ez a kódrészlet azt valósítja meg, hogy ne rontsa le a wordpress a képek minőségét
         */
        add_filter( 'jpeg_quality', function() { return 100;} );
    }

    public static function getImageById($id, $size='original')
    {
        $img = wp_get_attachment_image_src( $id, $size );
        if(!empty($img)){
            return $img[0];
        }
        return 'data:image/gif;base64,R0lGODlhAQABAAAAACw='; //1x1 transparent blank image
    }

    public static function paginator( $range = 4 )
    {
        global $paged, $wp_query;
    
        if ( !$max_page ) {
            $max_page = $wp_query->max_num_pages;
        }
    
        $data = array();

        if ( $max_page > 1 ) {
            if ( !$paged ) $paged = 1;
    
            if($paged > 1){
                $data['prev'] = get_pagenum_link($paged - 1);
            }

            if ( $max_page > $range + 1 ) {
                if ( $paged >= $range ){
                    $data['links'][] = array(
                        'active' => false,
                        'url' => get_pagenum_link(1),
                        'title' => '1'
                    );
                }

                if ( $paged >= ($range + 1) ) {
                    $data['links'][] = array(
                        'active' => false,
                        'title' => '&hellip;'
                    );
                }
            }
    
            $i_start = 1;
            $i_end = $max_page;

            if ( $max_page > $range ) {
                if ( $paged < $range ) {
                    $i_start = 1;
                    $i_end = $range + 1;
                } elseif ( $paged >= ($max_page - ceil(($range/2))) ) {
                    $i_start = $max_page - $range;
                    $i_end = $max_page;
                } elseif ( $paged >= $range && $paged < ($max_page - ceil(($range/2))) ) {
                    $i_start = $paged - ceil($range/2);
                    $i_end = $paged + ceil(($range/2));
                }
            }
            
            for ( $i = $i_start; $i <= $i_end; $i++ ) {
                $data['links'][] = array(
                    'active' => ( $i == $paged ),
                    'url' => get_pagenum_link($i),
                    'title' => $i
                );
            }

            if ( $max_page > $range + 1 ) {
                if ( $paged <= $max_page - ($range - 1) ) {
                    $data['links'][] = array(
                        'active' => false,
                        'title' => '&hellip;'
                    );
                    $data['links'][] = array(
                        'active' => false,
                        'url' => get_pagenum_link($max_page),
                        'title' => $max_page
                    );  
                }
            }
    
            if($paged < $max_page){
                $data['next'] = get_pagenum_link($paged+1);
            }
        }

        $view = tr_view('components.pagination', $data);
        return $view->load(false);
    }

    public static function breadcrumbs() {
        $data = array();
        if (!is_front_page()) {
            global $post;
        
        $homeTitle = 'Kezdőlap';

        // Start the breadcrumb with a link to your homepage
            $data[] = array(
                'title' => $homeTitle,
                'url' => get_option('home')
            );
        
        // Check if the current page is a category, an archive or a single page. If so show the category or archive name.
            if (is_archive() || is_single()){
                $obj = get_post_type_object($post->post_type);
                $url = 'javascript:void(0)';
                if(is_tax() || is_single()){
                    $url = get_post_type_archive_link( $post->post_type );
                }

                $title = $obj->labels->name;

                if($obj->name == 'post'){
                    $page_for_posts = get_option( 'page_for_posts' );
                    if($page_for_posts){
                        $title = get_the_title($page_for_posts);
                    }
                }

                $data[] = array(
                    'title' => $title,
                    'url' => $url
                );

                
            }

        // If the current page is a single post, show its title with the separator
            if (is_single()) {
                $data[] = array(
                    'title' => get_the_title()
                );
            }
        
        // If the current page is a static page, show its title.
            if (is_page()) {

                $parents = get_post_ancestors($post);
                if(!empty($parents)){
                    foreach($parents as $parent){
                        $data[] = array(
                            'title' => get_the_title($parent),
                            'url' => get_permalink($parent)
                        );
                    }
                }

                $data[] = array(
                    'title' => get_the_title()
                );
            }

            if (is_tax()) {
                $taxonomy = get_queried_object();
                $data[] = array(
                    'title' => ucfirst($taxonomy->name)
                );
            }
        
        // if you have a static page assigned to be you posts list page. It will find the title of the static page and display it. i.e Home >> Blog
            if (is_home()){
                global $post;
                $page_for_posts_id = get_option('page_for_posts');
                if ( $page_for_posts_id ) { 
                    $post = get_page($page_for_posts_id);
                    setup_postdata($post);
                    $data[] = array(
                        'title' => get_the_title()
                    );
                    rewind_posts();
                }
            }
        }
        $data = apply_filters( 'breadcrumbs_items', $data );
        $data['items'] = $data;
        $view = tr_view('components.breadcrumbs', $data);
        return $view->load( false );
    }
}

ThemeFunctions::getInstance();