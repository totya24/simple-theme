<?php
/*
    Title: Jellemzők
    Post Type: product
    Order: 2
*/

piklist('field', array(
    'type' => 'group',
    'field' => 'properties',
    'label' => 'Jellemzők',
    'add_more' => true,
    'description' => '',
    'template' => 'field',
    'fields' => array(
        array(
            'type' => 'text',
            'field' => 'label',
            'label' => 'Megnezevés',
            'columns' => 5

        ),
        array(
            'type' => 'text',
            'field' => 'property',
            'label' => 'Érték',
            'columns' => 7
        ),
    )
));