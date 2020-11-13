<?php

Class Products extends Singleton 
{

    public function __construct()
    {
        add_filter('piklist_post_types', [$this, '_register']);
        add_filter('piklist_taxonomies', [$this, '_registerTaxonomy']);
        add_action('pre_get_posts', [$this, '_customOrder'] );

        add_filter('post_order_types', function($types){ $types[] = 'product'; return $types; });

        $this->_customListColumns();
    }

    public function _register( $postTypes )
    {
        $postTypes['product'] = [
            'labels' => Greedo::postLabels('Termék', 'Termékek'),
            'title' => __('Termék megnevezése'),
            'public' => true,
            'menu_icon' => 'dashicons-cart',
            'page_icon' => 'dashicons-cart',
            'rewrite' => [
                'slug' => 'termekek'
            ],
            'supports' => [
                'title', 'page-attributes', 'editor', 'thumbnail', 'excerpt'
            ],
            'has_archive' => true,
            'hide_meta_box' => [
                'author',
                'revisions',
                'comments',
                'commentstatus'
            ]
        ];
        return $postTypes;
    }

    
    public function _registerTaxonomy( $taxonomies )
    {
        $taxonomies['product_category'] = [
            'post_type' => 'product',
            'show_admin_column' => true,
            'configuration' => [
                'hierarchical' => true,
                'labels' => Greedo::taxLabels('Termékkategória', 'Termékkategóriák'),
                'hide_meta_box' => false,
                'show_ui' => true,
                'query_var' => true,
                'rewrite' => [
                    'slug' => 'kategoria'
                ]
            ]
        ];
        return $taxonomies;
    }

    public function _customListColumns()
    {
        $column = new CustomAdminListColumn('product', [
            'Kiemelt kép' => function($postId){
                echo get_the_post_thumbnail($postId, [50,50]);
            },
            'Kategória' => function($postId){
                $categories = get_the_terms( $postId, 'product_category' );
                echo is_array($categories) ? join(', ', wp_list_pluck($categories, 'name')) : '-';
            }
        ]);
    }

    public function _customOrder($query)
    {
        if (!is_admin() && is_post_type_archive('product')){
            $query->set('orderby', 'menu_order');
            $query->set('order', 'ASC' );
        }
        return $query;
    }

}
Products::getInstance();