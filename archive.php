<div class="uk-container">
<?php
if ( have_posts() ) {
    while ( have_posts() ) {
        the_post(); 
        ?>
        <div class="uk-card uk-card-default uk-card-body uk-margin-top">
            <h3 class="uk-card-title"><?php the_title(); ?></h3>
            <div><?php the_content(); ?></div>
        </div>
        <?php
    }
}

get_template_part('template-parts/paginator');

?>
</div>