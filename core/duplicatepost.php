<?php

Class DuplicatePost
{
    private $postTypes = array( 'post', 'page' );
    
    public function __construct()
    {
        if( is_array( $this->postTypes ) ){
            foreach( $this->postTypes as $postType ){
                add_filter( "{$postType}_row_actions", array($this, 'duplicatePostLink'), 10, 2);
            }
        }
        
        add_action( 'admin_action_duplicate_post_as_draft', array($this, 'duplicatePostAsDraft') );
    }
    
    public function duplicatePostAsDraft()
    {
        global $wpdb;
        if (! ( isset( $_GET['post']) || isset( $_POST['post']) || ( isset($_REQUEST['action']) && 'duplicate_post_as_draft' == $_REQUEST['action'] ) ) ) {
            wp_die('No post to duplicate has been supplied!');
        }
        
        $postId = isset($_GET['post']) ? $_GET['post'] : $_POST['post'];
        $post = get_post( $postId );
        
        $currentUser = wp_get_current_user();
        $newPostAuthor = $currentUser->ID;
        
        if ( isset( $post ) && $post != null ) {
            
            $args = array(
                'comment_status' => $post->comment_status,
                'ping_status' => $post->ping_status,
                'post_author' => $new_post_author,
                'post_content' => $post->post_content,
                'post_excerpt' => $post->post_excerpt,
                'post_name' => $post->post_name,
                'post_parent' => $post->post_parent,
                'post_password' => $post->post_password,
                'post_status' => 'draft',
                'post_title' => $post->post_title,
                'post_type' => $post->post_type,
                'to_ping' => $post->to_ping,
                'menu_order' => $post->menu_order
            );
            
            $newPostId = wp_insert_post( $args );
            
            $taxonomies = get_object_taxonomies( $post->post_type );
            foreach ( $taxonomies as $taxonomy ) {
                $postTerms = wp_get_object_terms($postId, $taxonomy, array('fields' => 'slugs'));
                wp_set_object_terms($newPostId, $postTerms, $taxonomy, false);
            }
            
            $postMetaInfos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$postId");
            if ( count($postMetaInfos) != 0 ) {
                $sqlQuery = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
                foreach ( $postMetaInfos as $metaInfo ) {
                    $metaKey = $metaInfo->meta_key;
                    $metaValue = addslashes( $metaInfo->meta_value );
                    $sqlQuerySel[]= "SELECT $newPostId, '$metaKey', '$metaValue'";
                }
                $sqlQuery.= implode( " UNION ALL ", $sqlQuerySel );
                $wpdb->query( $sqlQuery );
            }
            
            wp_redirect( admin_url( 'post.php?action=edit&post=' . $newPostId ) );
            exit;
            
        } else {
            wp_die( 'Post creation failed, could not find original post: ' . $postId );
        }
    }
    
    public function duplicatePostLink( $actions, $post )
    {
        if ( current_user_can('edit_posts') ) {
            $actions['duplicate'] = '<a href="admin.php?action=duplicate_post_as_draft&amp;post=' . $post->ID . '" title="Az elem szerkesztése újként" rel="permalink">Másolás</a>';
        }
        return $actions;
    }
    
}

$duplicatePost = new DuplicatePost();