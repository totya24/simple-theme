<?php

class PostTypeArchiveLinks
{

    private $ininited;
    const NONCE = 'ptal_nonce';
    const METABOXID = 'ptal-metabox';
    const METABOXLISTID = 'post-type-archive-checklist';
    protected $cpts;

    /**
     * Handle backward compatibility for removed object variables
     */
    public function __get($name)
    {
        switch ($name) {
            case 'metabox_id' :
                return self::METABOXID;
            case 'metabox_list_id' :
                return self::METABOXLISTID;
            case 'nonce' :
                return self::NONCE;
            case 'instance' :
                return $this;
        }
    }

    /**
     * Instantiates the class, add hooks
     * @return \Post_Type_Archive_Links
     */
    public function __construct()
    {
        if (!$this->ininited) {
            $this->ininited = true;

            add_action('admin_init', array($this, 'getCpts'));
            add_action('admin_init', array($this, 'addMetaBox'), 20);
            add_filter('wp_setup_nav_menu_item', array($this, 'setupArchiveItem'));
            add_filter('wp_nav_menu_objects', array($this, 'maybeMakeCurrent'));
            add_action('admin_enqueue_scripts', array($this, 'metaboxScript'));
            add_action("wp_ajax_" . self::NONCE, array($this, 'ajaxAddPostType'));
            add_filter('post_type_archive_links', array($this, __FUNCTION__));
        }
        return $this;
    }

    /**
     * Get CPTs that plugin should handle: having true
     * 'has_archive', 'publicly_queryable' and 'show_in_nav_menu'
     * @return void
     */
    public function getCpts()
    {
        $cpts = array();
        $hasArchiveCps = get_post_types(
            array(
                'has_archive' => true,
                '_builtin' => false
            ),
            'object'
        );
        foreach ($hasArchiveCps as $ptid => $pt) {
            $toShow = $pt->show_in_nav_menus && $pt->publicly_queryable;
            if (apply_filters("show_{$ptid}_archive_in_nav_menus", $toShow, $pt)) {
                $cpts[] = $pt;
            }
        }
        if (!empty($cpts)) {
            $this->cpts = $cpts;
        }
    }

    /**
     * Adds the meta box to the menu page
     * @return void
     */
    public function addMetaBox()
    {
        add_meta_box(self::METABOXID, __('Archives', 'default'), array($this, 'metabox'), 'nav-menus', 'side', 'low');
    }


    /**
     * Scripts for AJAX call
     * Only loads on nav-menus.php
     * @param  string $hook Page Name
     * @return void
     */
    public function metaboxScript($hook)
    {
        if ('nav-menus.php' !== $hook)
            return;

        // Do nothing if no CPTs to handle
        if (empty($this->cpts)) return;


        add_action('admin_footer', function () {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    $('#submit-post-type-archives').click(function (event) {
                        event.preventDefault();
                        var $hptal_list_items = $('#<?php echo self::METABOXLISTID; ?> li :checked');
                        var $hptal_submit = $('input#submit-post-type-archives');
                        var postTypes = [];
                        $hptal_list_items.each(function () {
                            postTypes.push($(this).val());
                        });
                        $('#<?php echo self::METABOXID; ?>').find('.spinner').show();
                        $hptal_submit.prop('disabled', true);
                        $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                                action: '<?php echo self::NONCE; ?>',
                                posttypearchive_nonce: '<?php echo wp_create_nonce( self::NONCE ); ?>',
                                post_types: postTypes,
                                nonce: '<?php echo wp_create_nonce( self::NONCE ); ?>'
                            },
                            function (response) {
                                $('#menu-to-edit').append(response);
                                $('#<?php echo self::METABOXID; ?>').find('.spinner').hide();
                                $hptal_list_items.prop("checked", false);
                                $hptal_submit.prop('disabled', false);
                            }
                        );
                    });
                });
            </script>
            <?php
        });
    }

    /**
     * MetaBox Content Callback
     * @return string $html
     */
    public function metabox()
    {
        if (empty($this->cpts)) {
            echo '<p>' . __('No items.') . '</p>';
            return;
        }

        global $nav_menu_selected_id;

        $html = '<ul id="' . self::METABOXLISTID . '">';
        foreach ($this->cpts as $pt) {
            $html .= sprintf(
                '<li><label><input type="checkbox" value ="%s" />&nbsp;%s</label></li>',
                esc_attr($pt->name),
                esc_attr($pt->labels->name)
            );
        }
        $html .= '</ul>';

        // 'Add to Menu' button
        $html .= '<p class="button-controls"><span class="add-to-menu">';
        $html .= '<input type="submit"' . disabled($nav_menu_selected_id, 0, false) . ' class="button-secondary
              submit-add-to-menu right" value="' . esc_attr__('Add to Menu', 'default') . '"
              name="add-post-type-menu-item" id="submit-post-type-archives" />';
        $html .= '<span class="spinner"></span>';
        $html .= '</span></p>';

        print $html;
    }

    /**
     * AJAX Callback to create the menu item and add it to menu
     * @return string $HTML built with walk_nav_menu_tree()
     * use \Post_Type_Archive_Links::is_allowed() Check request and return choosen post types
     */
    public function ajaxAddPostType()
    {
        $postTypes = $this->isAllowed();

        // Create menu items and store IDs in array
        $itemIds = array();
        foreach ($postTypes as $postType) {
            $postTypeObj = get_post_type_object($postType);

            if (!$postTypeObj)
                continue;

            $menuItemData = array(
                'menu-item-title' => esc_attr($postTypeObj->labels->name),
                'menu-item-type' => 'post_type_archive',
                'menu-item-object' => esc_attr($postType),
                'menu-item-url' => get_post_type_archive_link($postType)
            );

            // Collect the items' IDs.
            $itemIds[] = wp_update_nav_menu_item(0, 0, $menuItemData);
        }

        // If there was an error die here
        is_wp_error($itemIds) AND die('-1');

        // Set up menu items
        foreach ((array)$itemIds as $menuItemId) {
            $menuObj = get_post($menuItemId);
            if (!empty($menuObj->ID)) {
                $menuObj = wp_setup_nav_menu_item($menuObj);
                // don't show "(pending)" in ajax-added items
                $menuObj->label = $menuObj->title;

                $menuItems[] = $menuObj;
            }
        }

        // Needed to get the Walker up and running
        require_once ABSPATH . 'wp-admin/includes/nav-menu.php';

        // This gets the HTML to returns it to the menu
        if (!empty($menuItems)) {
            $args = array(
                'after' => '',
                'before' => '',
                'link_after' => '',
                'link_before' => '',
                'walker' => new Walker_Nav_Menu_Edit
            );

            echo walk_nav_menu_tree($menuItems, 0, (object)$args);
        }
        exit;

    }

    /**
     * Is the AJAX request allowed and should be processed?
     * @return void
     */
    public function isAllowed()
    {
        // Capability Check
        !current_user_can('edit_theme_options') AND die('-1');
        // Nonce check
        check_ajax_referer(self::NONCE, 'nonce');
        // Is a post type chosen?
        $postTypes = filter_input_array(INPUT_POST, array('post_types' => array('filter' => FILTER_SANITIZE_STRING, 'flags' => FILTER_REQUIRE_ARRAY)));
        empty($postTypes['post_types']) AND exit;
        // return post types if chosen
        return array_values($postTypes['post_types']);
    }

    /**
     * Assign menu item the appropriate url
     * @param  object $menu_item
     * @return object $menu_item
     */
    public function setupArchiveItem($menuItem)
    {
        if ($menuItem->type !== 'post_type_archive')
            return $menuItem;
        $postType = $menuItem->object;
        $menuItem->type_label = __('Archive', 'default');
        $menuItem->url = get_post_type_archive_link($postType);
        return $menuItem;
    }

    /**
     * Make post type archive link 'current'
     * @uses   Post_Type_Archive_Links :: get_item_ancestors()
     * @param  array $items
     * @return array $items
     */
    public function maybeMakeCurrent($items)
    {
        foreach ($items as $item) {
            if ('post_type_archive' !== $item->type)
                continue;

            $postType = $item->object;
            if (!is_post_type_archive($postType) AND !is_singular($postType)) continue;

            // Make item current
            $item->current = true;
            $item->classes[] = 'current-menu-item';

            // Loop through ancestors and give them 'parent' or 'ancestor' class
            $activeAncItemIds = $this->getItemAncestors($item);
            foreach ($items as $key => $parentItem) {
                $classes = (array)$parentItem->classes;

                // If menu item is the parent
                if ($parentItem->db_id == $item->menu_item_parent) {
                    $classes[] = 'current-menu-parent';
                    $items[$key]->current_item_parent = true;
                }

                // If menu item is an ancestor
                if (in_array(intval($parentItem->db_id), $activeAncItemIds)) {
                    $classes[] = 'current-menu-ancestor';
                    $items[$key]->current_item_ancestor = true;
                }

                $items[$key]->classes = array_unique($classes);
            }
        }

        return $items;
    }

    /**
     * Get menu item's ancestors
     * @param  object $item
     * @return array  $activeAncItemIds
     */
    public function getItemAncestors($item)
    {
        $ancId = absint($item->db_id);

        $activeAncItemIds = array();
        while (
            $ancId = get_post_meta($ancId, '_menu_item_menu_item_parent', true)
            AND !in_array($ancId, $activeAncItemIds)
        )
            $activeAncItemIds[] = $ancId;

        return $activeAncItemIds;
    }
}

$PostTypeArchiveLinks = new PostTypeArchiveLinks();