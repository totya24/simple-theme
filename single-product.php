<?php get_header(); 

the_post();
?>
<div class="uk-container">

    <h1 class="uk-heading-line"><span><?php the_title(); ?></span></h1>
    <div uk-grid>
        <div class="uk-width-1-3@m">
        <?php
            $featuredImageId = get_post_thumbnail_id();
            $gallery = [
                [
                'thumb' => ThemeFunctions::getImageById($featuredImageId, 'thumbnail'),
                'large' => ThemeFunctions::getImageById($featuredImageId, 'large'),
                ]
            ];

            $images = get_post_meta(get_the_ID(), 'gallery', false);
            if(!empty($images)){
                foreach($images as $image){
                    $gallery[] = [
                        'thumb' => ThemeFunctions::getImageById($image, 'thumbnail'),
                        'large' => ThemeFunctions::getImageById($image, 'large'),
                    ];
                }
            }
        ?>
            <div uk-slideshow="animation: fade; min-height: 340; max-height: 600"">

                <ul class="uk-slideshow-items">
                <?php foreach($gallery as $index => $g){ ?>
                    <li><img src="<?php echo $g['large']; ?>" alt=""></li>
                <?php } ?>
                </ul>

                <ul class="uk-thumbnav">
                <?php foreach($gallery as $index => $g){ ?>
                    <li uk-slideshow-item="<?php echo $index; ?>"><a href="#"><img src="<?php echo $g['thumb']; ?>" width="100" alt=""></a></li>
                <?php } ?>
                </ul>

            </div>

        </div>
        <div class="uk-width-2-3@m">
            <h4>Részletek</h4>
            <div class="uk-padding-bottom">
            <?php the_content(); ?>
            </div>
            <hr>

            <h4>Jellemzők</h4>

            <?php $details = get_post_meta(get_the_ID(), 'properties', true); 
            if(!empty($details)){
                foreach($details as $detail){
                    echo '<div class="uk-grid-small" uk-grid><div class="uk-width-expand" uk-leader="media: @m">'. $detail['label'] .'</div><div>'. $detail['property'] .'</div></div>';
                }
            }
            ?>

            <div class="uk-padding uk-padding-remove-horizontal">
                <a href="#ajanlatkeres" class="uk-button uk-button-primary uk-text-uppercase" uk-toggle>Ajánlatkérés</a>
            </div>
        
        </div>
    </div>

    <div id="ajanlatkeres" uk-modal>
        <div class="uk-modal-dialog uk-modal-body">
            <h2 class="uk-modal-title">Headline</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
            <p class="uk-text-right">
                <button class="uk-button uk-button-default uk-modal-close" type="button">Cancel</button>
                <button class="uk-button uk-button-primary" type="button">Save</button>
            </p>
        </div>
    </div>

</div>
<?php get_footer(); ?>