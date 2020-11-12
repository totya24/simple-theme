<?php
/*
Name: Google térkép
Description: Oldalba beágyazott google térkép a megadott cím alapján
Shortcode: googlemap
Icon: dashicons-location-alt
Inline: true
*/

piklist('field', array(
    'type' => 'text',
    'field' => 'location',
    'label' => 'Cím'
));

piklist('field', array(
    'type' => 'range',
    'field' => 'zoom',
    'label' => 'Nagyítás',
    'value' => 15,
    'attributes' => array(
        'min' => 5,
        'max' => 19,
        'step' => 1
    )
));

piklist('field', array(
    'type' => 'number',
    'field' => 'height',
    'label' => 'Magasság',
    'value' => 300
));