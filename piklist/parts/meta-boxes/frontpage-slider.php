<?php
/*
    Title: Slider
    Post Type: page
    Frontpage: true
    Order: 1
*/

piklist('field', array(
    'type' => 'group',
    'field' => 'slider',
    'label' => 'Slider',
    'add_more' => true,
    'description' => '',
    'template' => 'field',
    'fields' => array(
        array(
            'type' => 'text',
            'field' => 'heading',
            'label' => 'Cím',
            'columns' => 3
        ),
        array(
            'type' => 'text',
            'field' => 'content',
            'label' => 'Szöveg',
            'columns' => 3
        ),
        array(
            'type' => 'text',
            'field' => 'url',
            'label' => 'Url',
            'columns' => 3
        ),
        array(
            'type' => 'file',
            'field' => 'image',
            'label' => 'Kép',
            'columns' => 3
        ),
    )
));