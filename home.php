<?php

$data = array();

if ( have_posts() ) {
    while ( have_posts() ) {
        the_post(); 
        
        /**
        * Ebben az iterációban összegyűjtjük azokat az adatokat posztonként, amikre szükségünk lesz a sablonban
        * Itt megtalálod az összes lehetséges függvényt hozzá (Post tags rész)
        * https://codex.wordpress.org/Template_Tags
        */
        
        $data['posts'][] = array(
            'title' => get_the_title(),
            'content' => apply_filters('the_content', get_the_content())
        );
    }
}

twig_render('pages/archive.twig', $data);