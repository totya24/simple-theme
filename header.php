<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="msapplication-tap-highlight" content="no">
	<link rel='stylesheet' id='wp-block-library-css'  href='<?php echo get_template_directory_uri(); ?>/assets/css/style.min.css' type='text/css' media='all' />
    
	<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/assets/favicon.ico">
	
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php get_template_part('template-parts/nav'); ?>