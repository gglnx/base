<!DOCTYPE html>
<!--[if lt IE 9]><html class="ie" lang="de"><![endif]-->
<!--[if gte IE 9]><!--><html lang="de"><!--<![endif]-->
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

	<title><?php the_page_title(); ?></title>

	<meta name="title" content="<?php the_meta_title(); ?>" />
	<meta name="description" content="<?php the_meta_description(); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php the_robots(); ?>
	
	<link rel="index" title="<?php bloginfo('name'); ?>" href="<?php echo home_url('/'); ?>" />
	<?php the_rel_canonical(); ?>
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" />
</head>

<body <?php body_class(); ?>>
	<header role="banner">
		<h1><a href="<?php echo home_url('/'); ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a></h1>
		<h2><?php bloginfo('description'); ?></h2>
		<nav><ul>
			<li><a href="<?php echo home_url('/'); ?>" title="Home">Home</a></li>
		</ul></nav>
	</header>
	