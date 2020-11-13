<?php get_header(); ?>
<div class="uk-container">
<div class="uk-child-width-1-3@m" uk-grid uk-height-match=".uk-card">
<?php
if ( have_posts() ) {
    while ( have_posts() ) {
        the_post(); 
        ?>
        <div>

            <div class="uk-card uk-card-default uk-card-hover uk-transition-toggle">
                <div class="uk-card-media-top uk-text-center">
                    <a href="<?php the_permalink(); ?>">
                        <div class="uk-inline-clip" tabindex="0">
                        <?php echo get_the_post_thumbnail(get_the_ID(), 'medium', ['class' => 'uk-transition-scale-up uk-transition-opaque']); ?>
                        </div>
                    </a>
                </div>
                <div class="uk-card-body">
                <h3 class="uk-card-title"><?php the_title(); ?></h3>
                    <div><?php the_excerpt(); ?></div>
                    <div class="uk-text-center">
                        <a href="<?php the_permalink(); ?>" class="uk-button uk-button-primary">Bővebben</a>
                    </div>
                </div>
            </div>

        </div>
        <?php
    }
}

get_template_part('template-parts/paginator');

?>
</div>
</div>
<?php get_footer(); ?>