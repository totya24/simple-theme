<?php

the_post();

$data = array(
    'title' => get_the_title(),
    'content' => apply_filters('the_content', get_the_content())
);

twig_render('pages/page.twig', $data);