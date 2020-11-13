<?php

/**
 * Ez az oldal több esetben is bejöhet.
 * Ha nincsen beállítva, hogy külön főoldal legyen, akkro a legutóbbi posztok is itt listázódnak
 * Bővebben: https://developer.wordpress.org/themes/basics/template-hierarchy/#front-page-display
 */

get_header();

the_post();

$slider = get_post_meta(get_the_ID(), 'slider', true);
if(!empty($slider)){ ?>

<div class="uk-position-relative uk-visible-toggle" tabindex="-1" uk-slideshow="animation: push; ratio: 8:2">
    <ul class="uk-slideshow-items">
        <?php foreach($slider as $slide){ ?>
        <li>
            <a href="<?php echo !empty($slide['url']) ? $slide['url'] : 'javascript:void(0)' ?>">
                <img src="<?php echo ThemeFunctions::getImageById($slide['image'][0]); ?>" alt="" uk-cover>
                <div class="uk-overlay uk-overlay-default uk-position-bottom-left uk-background-muted uk-position-large">
                    <h1 class="uk-margin-remove uk-heading-large uk-dark"><?php echo $slide['heading']; ?></h1>
                    <p class="uk-margin-remove"><?php echo $slide['content']; ?></p>
                </div>
            </a>
        </li>
        <?php } ?>
    </ul>

    <div class="uk-light">
        <a class="uk-position-center-left uk-position-small uk-hidden-hover" href="#" uk-slidenav-previous uk-slideshow-item="previous"></a>
        <a class="uk-position-center-right uk-position-small uk-hidden-hover" href="#" uk-slidenav-next uk-slideshow-item="next"></a>
    </div>
</div>
<?php } ?>
<div class="uk-container">
    <div class="uk-padding uk-padding-remove-horizontal">
        <?php the_content(); ?>
    </div>
</div>
<?php

get_footer();