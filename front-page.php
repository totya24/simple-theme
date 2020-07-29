<?php

/**
 * Ez az oldal több esetben is bejöhet.
 * Ha nincsen beállítva, hogy külön főoldal legyen, akkro a legutóbbi posztok is itt listázódnak
 * Bővebben: https://developer.wordpress.org/themes/basics/template-hierarchy/#front-page-display
 */

the_post();

$data = array(
    'title' => get_the_title(),
    'content' => apply_filters('the_content', get_the_content())
);

twig_render('pages/front-page.twig', $data);