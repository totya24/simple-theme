<?php
/*
    Title: Kapcsolódó etrmékek
    Post Type: product
    Order: 3
*/

piklist('field', array(
    'type' => 'group',
    'field' => 'related',
    'label' => 'Kapcsolódó termékek',
    'add_more' => true,
    'description' => '',
    'template' => 'field',
    'fields' => array(
        array(
            'type' => 'select',
            'field' => 'productid',
            'label' => '',
            'choices' => ['0' => 'Válasszon'] + piklist(get_posts(array('post_type' => 'product', 'orderby' => 'post_title', 'posts_per_page' => -1), 'objects'), array('ID', 'post_title')),
			'columns' => 12
        )
    )
));