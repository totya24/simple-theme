<?php
$data = ThemeFunctions::breadcrumbs();

if(!empty($data)){
    echo '<ul class="uk-breadcrumb uk-text-uppercase">';
    foreach($data as $item){
        echo '<li>';
        if($item['url']){
            echo '<a href="'. $item['url'] .'">'. $item['title'] .'</a>';
        } else {
            echo '<span>'. $item['title'] .'</span>';
        }
        echo '</li>';
    }
    echo '</ul>';
}