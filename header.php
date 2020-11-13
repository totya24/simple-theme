<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="msapplication-tap-highlight" content="no">
    
	<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/assets/favicon.ico">
    <script>const ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';</script>
	
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php get_template_part('template-parts/nav'); ?>

<main uk-height-viewport="expand: true">

<?php if(!is_front_page()){ ?>
<div class="uk-container">
    <div class="uk-margin-top">
    <?php get_template_part( 'template-parts/breadcrumbs' ); ?>
    </div>
</div>
<?php } ?>