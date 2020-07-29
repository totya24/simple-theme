<?php
/**
 * Az osztály kezeli le a létrehozható menüket, 
 * továbbá a TWIG sablon számára értelmezhető formában
 * adja vissza az adott menü elemeit (2 szintig)
*/

Class MenuHandler extends Singleton 
{

    public function __construct()
    {
		/**
         * Itt regisztráljuk a menük helyeit
         * Bővebben: https://developer.wordpress.org/reference/functions/register_nav_menus/
		*/
        register_nav_menus( 
            array(
                'main' => 'Főmenü'
            )
        );
    }

    public static function getMenuItems( $location = 'main' )
    {
        global $wp_query;
        $locations = get_nav_menu_locations();
        $object = wp_get_nav_menu_object( $locations[$location] );
        $items   = wp_get_nav_menu_items($object->name);
        $result  = array();
        $parents = array();
        $active = null;
        $currentPostType = get_post_type();
        $taxonomyPostType = array();
        if(is_tax()){
            $taxonomy = get_queried_object();
            $tax = get_taxonomy( $taxonomy->taxonomy );
            $taxonomyPostType->object_type;
        }

        if(!empty($items)){
            _wp_menu_item_classes_by_context($items);
        }

        if (is_array($items)) {
            foreach ($items as $itm) {
                $parents[$itm->ID] = $itm->menu_item_parent;
            }
            foreach ($items as $itm) {
                $tmp = array(
                    'id' => $itm->ID,
                    'parent' => $itm->menu_item_parent,
                    'title' => $itm->title ? $itm->title : $itm->post_title,
                    'url' => $itm->url ? $itm->url : 'javascript:void(0)',
                    'target' => $itm->target ? $itm->target : '',
                    'classes' => is_array($itm->classes) ? implode(' ', $itm->classes) : '',
                    'active' => false,
                    'object_id' => $itm->object_id,
                    'object' => $itm->object,
                    'type' => $itm->type
                );

                if ($itm->menu_item_parent == 0) {
                    $children = isset($result[$itm->ID]) ? $result[$itm->ID] : array();
                    $result[$itm->ID] = array_merge($tmp, $children);
                    if(stristr($tmp['classes'],'current-menu-item') !== false){
                        $result[$itm->ID]['active'] = true;
                        $active = $itm->ID;
                    } else {
                        if($itm->type == 'post_type_archive' && is_tax() || is_single()){
                            if($currentPostType == $itm->object || in_array($itm->object, $taxonomyPostType)){
                                $result[$itm->ID]['active'] = true;
                                $active = $itm->ID;
                            }
                        }
                    }
                } else {
                    if ($parents[$itm->menu_item_parent] == 0) {
                        $result[$itm->menu_item_parent]['children'][$itm->ID] = $tmp;
                        if(stristr($tmp['classes'],'current-menu-item') !== false){
                            $result[$itm->menu_item_parent]['children'][$itm->ID]['active'] = true;
                            $result[$itm->menu_item_parent]['active'] = true;
                            $active = $itm->menu_item_parent;
                        }
                    } else {
                        $result[$parents[$itm->menu_item_parent]]['children'][$itm->menu_item_parent]['children'][$itm->ID] = $tmp;
                        if(stristr($tmp['classes'],'current-menu-item') !== false){
                            $result[$parents[$itm->menu_item_parent]]['children'][$itm->menu_item_parent]['children'][$itm->ID]['active'] = true;
                            $result[$parents[$itm->menu_item_parent]]['children'][$itm->menu_item_parent]['active'] = true;
                            $result[$parents[$itm->menu_item_parent]]['active'] = true;
                            $active = $parents[$itm->menu_item_parent];
                        }
                    }
                }
            }
        }
        return apply_filters( 'menu_items', $result, $location, $active );
    }

}

MenuHandler::getInstance();