<?php get_header(); 

the_post();
?>
<div class="uk-container">
    <div class="uk-padding uk-padding-remove-horizontal">
        <?php the_content(); ?>
    </div>
</div>
<?php get_footer(); ?>