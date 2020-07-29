<?php
Class postOrder
{
    private static $types = array();

    public function __construct()
    {
        add_action( 'init', array( $this, 'init' ), 1000, 0 );
        add_action( 'load-edit.php', array($this, 'loadEditScreen') );
        add_action( 'wp_ajax_simple_page_ordering', array($this, 'ajaxSimplePageOrdering') );
        add_filter( 'pre_get_posts', array($this, 'setDefaultOrder'), 5 );
    }

    public function setDefaultOrder($wp_query)
    {
        global $pagenow;

        if (!in_array($wp_query->get( 'post_type' ), static::$types)) return;
        
        if ( is_admin() && 'edit.php' == $pagenow && !isset($_GET['orderby'])) {
            $wp_query->set( 'orderby', 'menu_order' );
            $wp_query->set( 'order', 'ASC' );
        }
    }

    public function init()
    {
        self::$types = apply_filters( 'post_order_types', array() );
        foreach( self::$types as $postType ) {
            add_post_type_support( $postType, 'page-attributes' );
        }
    }

    public function loadEditScreen()
    {
        $screen = get_current_screen();
        $postType = $screen->post_type;

        if (!in_array($postType, static::$types)) return;

        # is post type sortable?
        $sortable = (post_type_supports($postType, 'page-attributes') || is_post_type_hierarchical($postType));

        if (!$sortable = apply_filters('simple_page_ordering_is_sortable', $sortable, $postType)) {
            return;
        }

        # does user have the right to manage these post objects?
        if (!self::checkEditOthersCaps($postType)) {
            return;
        }

        add_filter('views_' . $screen->id, array($this, 'sortByOrderLink'));        // add view by menu order to view
        add_action('admin_enqueue_scripts', array($this, 'insertOrderingAssets'));

    }

    public function insertOrderingAssets()
    {
        $orderBy = get_query_var('orderby');
        if (!isset($orderBy) || (is_string($orderBy) && 0 === strpos($orderBy, 'menu_order')) || (isset($orderBy['menu_order']) && $orderBy['menu_order'] == 'ASC')) {
            wp_enqueue_script('postorder', get_template_directory_uri() . '/core/postorder/postorder.js', array('jquery-ui-sortable'), '2.1', true);
            wp_enqueue_style('postorder', get_template_directory_uri() . '/core/postorder/postorder.css');
        }

    }

    public function ajaxSimplePageOrdering()
    {
        // check and make sure we have what we need
        if (empty($_POST['id']) || (!isset($_POST['previd']) && !isset($_POST['nextid']))) {
            die(-1);
        }

        // real post?
        if (!$post = get_post($_POST['id'])) {
            die(-1);
        }

        // does user have the right to manage these post objects?
        if (!self::checkEditOthersCaps($post->post_type)) {
            die(-1);
        }

        // badly written plug-in hooks for save post can break things
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            error_reporting(0);
        }

        global $wp_version;

        $prevId = empty($_POST['previd']) ? false : (int)$_POST['previd'];
        $nextId = empty($_POST['nextid']) ? false : (int)$_POST['nextid'];
        $start = empty($_POST['start']) ? 1 : (int)$_POST['start'];
        $excluded = empty($_POST['excluded']) ? array($post->ID) : array_filter((array)$_POST['excluded'], 'intval');

        $newPos = array(); // store new positions for ajax
        $returnData = new stdClass;

        do_action('simple_page_ordering_pre_order_posts', $post, $start);

        // attempt to get the intended parent... if either sibling has a matching parent ID, use that
        $parentId = $post->post_parent;
        $nextPostParent = $nextId ? wp_get_post_parent_id($nextId) : false;
        if ($prevId == $nextPostParent) {    // if the preceding post is the parent of the next post, move it inside
            $parentId = $nextPostParent;
        } elseif ($nextPostParent !== $parentId) {  // otherwise, if the next post's parent isn't the same as our parent, we need to study
            $prevPostParent = $prevId ? wp_get_post_parent_id($prevId) : false;
            if ($prevPostParent !== $parentId) {    // if the previous post is not our parent now, make it so!
                $parentId = ($prevPostParent !== false) ? $prevPostParent : $nextPostParent;
            }
        }
        // if the next post's parent isn't our parent, it might as well be false (irrelevant to our query)
        if ($nextPostParent !== $parentId) {
            $nextId = false;
        }

        $maxSortablePosts = (int)apply_filters('simple_page_ordering_limit', 50);    // should reliably be able to do about 50 at a time
        if ($maxSortablePosts < 5) {    // don't be ridiculous!
            $maxSortablePosts = 50;
        }

        // we need to handle all post stati, except trash (in case of custom stati)
        $postStati = get_post_stati(array(
            'show_in_admin_all_list' => true,
        ));

        $siblingsQuery = array(
            'depth' => 1,
            'posts_per_page' => $maxSortablePosts,
            'post_type' => $post->post_type,
            'post_status' => $postStati,
            'post_parent' => $parentId,
            'orderby' => array('menu_order' => 'ASC', 'title' => 'ASC'),
            'post__not_in' => $excluded,
            'update_post_term_cache' => false,
            'update_post_meta_cache' => false,
            'suppress_filters' => true,
            'ignore_sticky_posts' => true,
        );
        if (version_compare($wp_version, '4.0', '<')) {
            $siblingsQuery['orderby'] = 'menu_order title';
            $siblingsQuery['order'] = 'ASC';
        }
        $siblings = new WP_Query($siblingsQuery); // fetch all the siblings (relative ordering)

        // don't waste overhead of revisions on a menu order change (especially since they can't *all* be rolled back at once)
        remove_action('pre_post_update', 'wp_save_post_revision');

        foreach ($siblings->posts as $sibling) :

            // don't handle the actual post
            if ($sibling->ID === $post->ID) {
                continue;
            }

            // if this is the post that comes after our repositioned post, set our repositioned post position and increment menu order
            if ($nextId === $sibling->ID) {
                wp_update_post(array(
                    'ID' => $post->ID,
                    'menu_order' => $start,
                    'post_parent' => $parentId,
                ));
                $ancestors = get_post_ancestors($post->ID);
                $newPos[$post->ID] = array(
                    'menu_order' => $start,
                    'post_parent' => $parentId,
                    'depth' => count($ancestors),
                );
                $start++;
            }

            // if repositioned post has been set, and new items are already in the right order, we can stop
            if (isset($newPos[$post->ID]) && $sibling->menu_order >= $start) {
                $returnData->next = false;
                break;
            }

            // set the menu order of the current sibling and increment the menu order
            if ($sibling->menu_order != $start) {
                wp_update_post(array(
                    'ID' => $sibling->ID,
                    'menu_order' => $start,
                ));
            }
            $newPos[$sibling->ID] = $start;
            $start++;

            if (!$nextId && $prevId == $sibling->ID) {
                wp_update_post(array(
                    'ID' => $post->ID,
                    'menu_order' => $start,
                    'post_parent' => $parentId
                ));
                $ancestors = get_post_ancestors($post->ID);
                $newPos[$post->ID] = array(
                    'menu_order' => $start,
                    'post_parent' => $parentId,
                    'depth' => count($ancestors));
                $start++;
            }

        endforeach;

        // max per request
        if (!isset($returnData->next) && $siblings->max_num_pages > 1) {
            $returnData->next = array(
                'id' => $post->ID,
                'previd' => $prevId,
                'nextid' => $nextId,
                'start' => $start,
                'excluded' => array_merge(array_keys($newPos), $excluded),
            );
        } else {
            $returnData->next = false;
        }

        do_action('simple_page_ordering_ordered_posts', $post, $newPos);

        if (!$returnData->next) {
            // if the moved post has children, we need to refresh the page (unless we're continuing)
            $children = get_posts(array(
                'numberposts' => 1,
                'post_type' => $post->post_type,
                'post_status' => $postStati,
                'post_parent' => $post->ID,
                'fields' => 'ids',
                'update_post_term_cache' => false,
                'update_post_meta_cache' => false,
            ));

            if (!empty($children)) {
                die('children');
            }
        }

        $returnData->new_pos = $newPos;

        wp_send_json($returnData);
    }

    public function sortByOrderLink($views)
    {
        $class = ((get_query_var('orderby') == 'menu_order title') || (get_query_var('orderby') == 'menu_order')) ? 'current' : '';
        if($class == 'current'){
            //fixme: van erre filter?
            $views['all'] = str_replace('class="current"', '', $views['all']);
        }
        $queryString = esc_url(remove_query_arg(array('orderby', 'order')));
        if (!is_post_type_hierarchical(get_post_type())) {
            $queryString = add_query_arg('order', 'asc', $queryString);
            $queryString = add_query_arg('orderby', 'menu_order title', $queryString);

        }
        $queryString = str_replace('#038;','&',$queryString);
        $views['byorder'] = sprintf('<a href="%s" class="%s">%s</a>', $queryString, $class, __("Order"));

        return $views;
    }

    private function checkEditOthersCaps($postType)
    {
        $postTypeObject = get_post_type_object($postType);
        $editOthersCap = empty($postTypeObject) ? 'edit_others_' . $postType . 's' : $postTypeObject->cap->edit_others_posts;
        return apply_filters('simple_page_ordering_edit_rights', current_user_can($editOthersCap), $postType);
    }

}

$postOrder = new postOrder();