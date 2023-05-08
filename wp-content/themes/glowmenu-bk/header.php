<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head <?php do_action( 'add_head_attributes' ); ?>>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta name="viewport" content="width=device-width">
<meta name="HandheldFriendly" content="true" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<META name="Language" content="English">
<META HTTP-EQUIV="CACHE-CONTROL" content="PUBLIC">
<META name="Publisher" content="glowmenu">
<META name="distribution" content="Global">
<META name="Robots" content="INDEX,FOLLOW">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="google-site-verification" content="BXeuZsidjrB68V5I0kBKuv1wGr5P9cyCusbfD5AlyZg" />
<meta name="robots" content="noimageindex">

<!-- FAVICON -->
<link rel="shortcut icon" type="image/x-ico" href="<?php bloginfo( 'template_directory' ); ?>/dist/images/favicon/favicon.ico" />
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
<link rel="icon" type="image/png" href="<?php bloginfo( 'template_directory' ); ?>/dist/images/favicon/android-chrome-192x192.png" sizes="192x192">
<link rel="icon" type="image/png" href="<?php bloginfo( 'template_directory' ); ?>/dist/images/favicon/favicon-96x96.png" sizes="96x96">
<link rel="icon" type="image/png" href="<?php bloginfo( 'template_directory' ); ?>/dist/images/favicon/favicon-16x16.png" sizes="16x16">
<link rel="manifest" href="<?php bloginfo( 'template_directory' ); ?>/dist/images/favicon/manifest.json">
<link rel="mask-icon" href="<?php bloginfo( 'template_directory' ); ?>/dist/images/favicon/safari-pinned-tab.svg" color="#ffffff">
<meta name="msapplication-TileColor" content="#603cba">
<meta name="msapplication-TileImage" content="<?php bloginfo( 'template_directory' ); ?>/dist/images/favicon/mstile-144x144.png">
<meta name="theme-color" content="#ffffff">

<!--[if IE 7]>
  <link rel="stylesheet" href="<?php bloginfo( 'template_directory' ); ?>/assets/fonts/css/font-awesome-ie7.css" />
<![endif]-->
<link href="https://fonts.googleapis.com/css?family=Arimo:400,700|Questrial|Lato|Montserrat:200,400" rel="stylesheet"> 
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="<?php bloginfo('name'); ?> Atom Feed" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php wp_head(); ?>
<title><?php wp_title(''); ?></title>

<?php wp_enqueue_script("jquery"); ?>

<!-- SLAASK -->
<script src='https://cdn.slaask.com/chat.js'></script>
<script>
    _slaask.init('2d0171ebdf5e20a04101d2fe28962c30');
</script>

<!-- AGILE CRM -->
<script id="_agile_min_js" async type="text/javascript" src="https://glowmenus.agilecrm.com/stats/min/agile-min.js"> </script>
<script type="text/javascript" >var Agile_API = Agile_API || {}; Agile_API.on_after_load = function(){_agile.set_account('nu60e96ktguh17oer9pop34d5s', 'glowmenus');_agile.track_page_view();};</script>

</head>

<body <?php body_class('responsive'); ?> >
<div id="theme-wrapper">
<!--Header-->
<header class="header">
  <div class="container-fluid clearfix">
        <div class="col-md-4">
        	<?php  wp_nav_menu( array( 'items_wrap' => '<ul class="header__menu__items">%3$s</ul>', 'container_class' => 'header__menu', 'theme_location' => 'header') ); ?>
        </div>
        <div class="header__logo col-md-4"> 
        	<div class="header__logo__position">
        		<a href="<?php bloginfo('url')?>/"><?php include('inc/logo.php'); ?></a>
        	</div>
        </div>
        <div class="col-md-4">
        	<?php  wp_nav_menu( array( 'items_wrap' => '<ul class="header__shopping__items">%3$s</ul>', 'container_class' => 'header__shopping', 'theme_location' => 'header-links') ); ?>
        </div>
  </div>
</header>