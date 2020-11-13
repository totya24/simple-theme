<?php
/*
    Title: Galéria
    Post Type: product
    Order: 1
*/

piklist('field', array(
    'type' => 'file',
    'field' => 'gallery',
    'scope' => 'post_meta',
    'template' => 'field',
    'options' => array(
        'title' => 'Képek hozzáadása',
        'button' => 'Képek hozzáadása'
    )
));