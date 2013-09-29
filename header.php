<?php
#
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

	<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?></title>
	
	<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->

	<!-- gallery 2 integration -->
	<?php 
	if (function_exists('g2_imageframes')) { ?>
	<?php g2_imageframes(); ?>
	<?php } ?>

	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
	<link rel="alternate" type="application/atom+xml" title="Atom 0.3" href="<?php bloginfo('atom_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<style type="text/css" media="screen">
	
		/* BEGIN IMAGE CSS */
			/*	To accomodate differing install paths of WordPress, images are referred only here,
				and not in the wp-layout.css file. If you prefer to use only CSS for colors and what
				not, then go right ahead and delete the following lines, and the image files. */
			
		body	 	{ background: url("<?php bloginfo('stylesheet_directory'); ?>/images/bgcolor.jpg"); }	<?php /* Checks to see whether it needs a sidebar or not */ if ((! $withcomments) && (! is_single())) { ?>
			#page		{ background: url("<?php bloginfo('stylesheet_directory'); ?>/images/bg.jpg") repeat-y top; border: none; } <?php } else { // No sidebar ?>
			#page		{ background: url("<?php bloginfo('stylesheet_directory'); ?>/images/bgwide.jpg") repeat-y top; border: none; } <?php } ?>
			#header 	{ background: url("<?php bloginfo('stylesheet_directory'); ?>/images/header.jpg") no-repeat bottom center; }
			#footer 	{ background: url("<?php bloginfo('stylesheet_directory'); ?>/images/footer.jpg") no-repeat bottom; border: none;}
			
			
			/*	Because the template is slightly different, size-wise, with images, this needs to be set here
				If you don't want to use the template's images, you can also delete the following two lines. */
			
			#header 	{ margin: 0 !important; margin: 0 0 0 1px; padding: 1px; height: 198px; width: 758px; }
			#headerimg 	{ margin: 3px 3px 0px 4px; height: 192px; width: auto; } 
		/* END IMAGE CSS */
		
	
		

	</style>

	<?php wp_get_archives('type=monthly&format=link'); ?>

	<?php wp_head(); ?>
</head>
<body>

<div id="page">


<div id="header">
	<div id="headerimg">
		<h1><a href="<?php echo get_settings('home'); ?>"><?php bloginfo('name'); ?></a></h1>
		<div class="description"><span><?php bloginfo('description'); ?></span></div>
		<?php if (get_option('haxor_header_menu_enable', true)) : ?>
			<nav id="site-navigation" class="main-navigation" role="navigation">
				<?php wp_nav_menu( array( 'theme_location' => 'header-menu', 'sort_column' => 'menu_order', 'container_class' => 'menu-header'  ) ); ?>
			</nav><!-- #site-navigation -->
		<?php endif; ?>
	</div>
</div>
<?php 
	if (function_exists('themehacker_ip')){
		themehacker_ip(); 
	}
?>
<hr />
