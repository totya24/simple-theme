<?php

$data = ThemeFunctions::paginator();

echo '<ul class="uk-pagination uk-flex-center">';
if($data['prev']){
    echo '<li><a href="'. $data['prev'] .'"><span uk-pagination-previous></span></a></li>';
}

if(is_array($data['links'])){
    foreach($data['links'] as $link){
        echo '<li class="'. (empty($link['url']) ? 'uk-disabled' : '') . ' ' . ($link['active'] ? 'uk-active' : '') . '">';
        if(empty($link['url']) || $link['active']){
            echo '<span>'. $link['title'] . '</span>';
        } else {
            echo '<a href="'. $link['url'] .'">'. $link['title'] .'</a>';
        }
        echo '</li>';
    }
}

if($data['next']){
    echo '<li><a href="'. $data['next'] .'"><span uk-pagination-next></span></a></li>';
}
echo '</ul>';