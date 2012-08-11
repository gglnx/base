<?php get_header(); ?>

	<section role="main">
		<?php if ( is_category() ): ?>
		<h1>Artikel zum Thema '<?php single_cat_title(); ?>'</h1>
		<?php elseif( is_tag() ): ?>
		<h1>Artikel zum Stichwort '<?php single_tag_title(); ?>'</h1>
		<?php elseif ( is_author() ): ?>
		<h1>Artikel von <?php echo $wp_query->queried_object->display_name; ?></h1>
		<?php elseif ( is_day() ): ?>
		<h1>Artikel vom <?php the_time('d. F Y'); ?></h1>
		<?php elseif ( is_month() ): ?>
		<h1>Artikel aus <?php the_time('F Y'); ?></h1>
		<?php elseif ( is_year() ): ?>
		<h1>Artikel aus dem Jahr <?php the_time('Y'); ?></h1>
		<?php elseif ( is_search() ): ?>
		<h1>Suchergebnis zu '<?php the_search_query(); ?>' (<?php the_search_count(); ?>)</h1>
		<?php elseif ( is_paged() ): ?>
		<h1>Archiv (Seite <?php global $paged; echo $paged; ?>)</h1>
		<?php endif; ?>
		
		<?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header>
				<h1><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
				<p><time datetime="<?php the_time('c') ?>"><?php the_time('j. F Y') ?></time></p>
			</header>

			<?php the_content('mehr &raquo;'); ?>
		</article>
		<?php endwhile; ?>
		
		<?php if ( paging_bar_needed() ): ?> 
		<footer id="pager"><ul>
			<li><?php previous_posts_link('&laquo; Neuere Beiträge') ?></li>
			<?php the_paging_bar() ?>
			<li><?php next_posts_link('Ältere Beiträge &raquo;') ?></li>
		</ul></footer>
		<?php endif; ?>
			
		<?php else: ?>
		<p>Es konnten leider keine Artikel gefunden werden.</p>
		<?php endif; ?>
	</section>

<?php get_footer(); ?>