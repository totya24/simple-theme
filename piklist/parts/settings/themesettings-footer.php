<?php
/*
Title: Hivatkozások
Setting: theme_settings
Flow: theme-settings
Tab: Lábléc
*/

piklist('field', array(
    'type' => 'group',
    'field' => 'footer_content',
    'label' => 'Dobozok',
    'add_more' => true,
    'template' => 'field',
    'fields' => array(
        array(
            'type' => 'text',
            'field' => 'title',
            'label' => 'Cím'
        ),
        array(
            'type' => 'textarea',
            'field' => 'content',
            'label' => 'Tartalom',
            'attributes' => array(
                'rows' => 5,
                'cols' => 50,
                'class' => 'large-text'
            )
        )
    )
));