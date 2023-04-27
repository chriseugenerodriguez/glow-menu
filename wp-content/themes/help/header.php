<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<!-- HEAD CODE -->
<head <?php do_action( 'add_head_attributes' ); ?>>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta name="viewport" content="width=device-width">
<meta name="HandheldFriendly" content="true" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<META name="Language" content="English">
<META HTTP-EQUIV="CACHE-CONTROL" content="PUBLIC">
<META name="Copyright" content="2016">
<META name="Designer" content="Chris Rodriguez">
<META name="Publisher" content="glowmenu help center">
<META name="Revisit-After" content="51 days">
<META name="distribution" content="Local">
<META name="Robots" content="INDEX,FOLLOW">

<link rel="shortcut icon" type="image/x-ico" href="<?php bloginfo( 'template_directory' ); ?>/dist/images/favicon.ico" />
<link rel="apple-touch-icon" sizes="57x57" href="<?php bloginfo( 'template_directory' ); ?>/dist/images/favicon/apple-touch-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="<?php bloginfo( 'template_directory' ); ?>/dist/images/favicon/apple-touch-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="<?php bloginfo( 'template_directory' ); ?>/dist/images/favicon/apple-touch-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="<?php bloginfo( 'template_directory' ); ?>/dist/images/favicon/apple-touch-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="<?php bloginfo( 'template_directory' ); ?>/dist/images/favicon/apple-touch-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="<?php bloginfo( 'template_directory' ); ?>/dist/images/favicon/apple-touch-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="<?php bloginfo( 'template_directory' ); ?>/dist/images/favicon/apple-touch-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="<?php bloginfo( 'template_directory' ); ?>/dist/images/favicon/apple-touch-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="<?php bloginfo( 'template_directory' ); ?>/dist/images/favicon/apple-touch-icon-180x180.png">
<link rel="icon" type="image/png" href="<?php bloginfo( 'template_directory' ); ?>/dist/images/favicon/favicon-32x32.png" sizes="32x32">
<link rel="icon" type="image/png" href="<?php bloginfo( 'template_directory' ); ?>/dist/images/favicon/favicon-194x194.png" sizes="194x194">
<link rel="icon" type="image/png" href="<?php bloginfo( 'template_directory' ); ?>/dist/images/favicon/favicon-96x96.png" sizes="96x96">
<link rel="icon" type="image/png" href="<?php bloginfo( 'template_directory' ); ?>/dist/images/favicon/android-chrome-192x192.png" sizes="192x192">
<link rel="icon" type="image/png" href="<?php bloginfo( 'template_directory' ); ?>/dist/images/favicon/favicon-16x16.png" sizes="16x16">
<link rel="manifest" href="<?php bloginfo( 'template_directory' ); ?>/dist/images/favicon/manifest.json">
<link rel="mask-icon" href="<?php bloginfo( 'template_directory' ); ?>/dist/images/favicon/safari-pinned-tab.svg" color="#a6e12e">
<meta name="apple-mobile-web-app-title" content="glowmenu">
<meta name="application-name" content="glowmenu">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="<?php bloginfo( 'template_directory' ); ?>/dist/images/favicon/mstile-144x144.png">
<meta name="theme-color" content="#ffffff">
<!--[if IE 7]>
  <link rel="stylesheet" href="<?php bloginfo( 'template_directory' ); ?>/dist/fonts/css/font-awesome-ie7.css" />
  <script type="text/javascript" src="<?php bloginfo( 'template_directory' ); ?>/dist/scripts/html5.js" />
<![endif]-->



<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php wp_head(); ?>
<title>
<?php /** Print the <title> tag based on what is being viewed. **/
	global $page, $paged;
	wp_title( '|', true, 'right' );

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 ) echo ('|') . sprintf( __( 'Page %s', 'help' ), max( $paged, $page ) ); ?>
</title>
<?php wp_enqueue_script("jquery"); ?>
</head>
<body <?php body_class('responsive'); ?> data-responsive="1" >
<!--Header-->
<header>
  <div class="container">
      <div class="logo"> 
        <a id="logo" href="<?php bloginfo('url')?>/">
          <?php get_template_part('/inc/logo');?>
        </a> 
      </div>
	     <?php wp_nav_menu( array( 'container_id' => 'nav', 'container_class' => 'menu', 'theme_location' => 'primary' ) ); ?>
    </div>
</header>
