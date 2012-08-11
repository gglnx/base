<?php
/**
 * WordPress Theme Scratch
 * by Dennis Morhardt
 * 
 * Version 0.2 (2012-06-29)
 */
  
/**
 * oEmbed SSL
 */
wp_oembed_add_provider('#https://(www\.)?youtube.com/watch.*#i', 'http://youtube.com/oembed?scheme=https&wmode=transparent', true);
wp_oembed_add_provider('#https://(www\.)?vimeo\.com/.*#i', 'https://vimeo.com/api/oembed.{format}?scheme=https', true);
wp_oembed_add_provider('https://youtu.be/*', 'http://youtube.com/oembed?scheme=https&wmode=transparent', false); 

/**
 * WMode Transparent on YouTube videos
 */
add_filter('embed_oembed_html', function($html, $url, $attr) {
	if ( false !== strpos ( $html, 'feature=oembed' ) ) return str_replace('feature=oembed', 'wmode=opaque', $html);
	return $html;
}, 10, 3);

/**
 * HTML5 captions
 */
function html5_img_caption_shortcode($attr, $content = null) {
	extract(shortcode_atts(array(
		'id' => '',
		'align' => 'alignnone',
		'width' => '',
		'caption' => ''
	), $attr));

	if ( 1 > (int) $width || empty($caption) )
		return $content;

	if ( $id ) $idtag = 'id="' . esc_attr($id) . '" ';

	return '<figure ' . $idtag . 'aria-describedby="figcaption_' . $id . '" class="clearfix">' . do_shortcode( $content ) . '<figcaption id="figcaption_' . $id . '">' . $caption . '</figcaption></figure>';
}
add_shortcode('wp_caption', 'html5_img_caption_shortcode');
add_shortcode('caption', 'html5_img_caption_shortcode');
 
/**
 * Helper function: check if current page is realy the front page
 */
function _is_front_page() {
	global $wp_query;

	if ( false == get_option( 'show_on_front' ) )
		return is_home() && !is_paged();
	
	if ( function_exists( 'is_front_page' ) )
		return is_front_page() && !is_paged();
	else
		return get_option('show_on_front') == 'page' && get_option('page_on_front') == $wp_query->get_queried_object_id();
}

/**
 * Display the page title
 */
function the_page_title($echo = true) {
	global $paged, $wp_query;
	
	$blogname = get_bloginfo('name');
	$title = trim(wp_title('', false));
	$pagecounter = is_paged() ? ' (Seite ' . $paged . ')' : '';
	
	if ( is_paged() && is_home() )
		$title = $blogname . " - Archiv" . $pagecounter;
	elseif ( _is_front_page() )
		$title = $blogname;
	elseif ( is_search() )
		$title = "Suchergebnis zu '" . get_search_query() . $pagecounter . "' - " . $blogname;
	elseif ( is_category() )
		$title = 'Artikel zum Thema ' .  $title . $pagecounter . ' - ' . $blogname;
	elseif ( is_tag() )
		$title = 'Artikel zum Stichwort \'' .  $title . '\'' . $pagecounter . ' - ' . $blogname;
	elseif ( is_author() )
		$title = 'Artikel von ' . $wp_query->queried_object->display_name . $pagecounter . ' - ' . $blogname;
	elseif ( is_day() )
		$title = 'Artikel vom ' . get_the_time('d. F Y') . $pagecounter . ' - ' . $blogname;
	elseif ( is_month() )
		$title = 'Artikel aus ' .  get_the_time('F Y') . $pagecounter . ' - ' . $blogname;
	elseif ( is_year() )
		$title = 'Artikel aus dem Jahr ' .  get_the_time('Y') . $pagecounter . ' - ' . $blogname;
	elseif ( is_singular() )
		$title = get_the_title() . ' - ' . $blogname;
	else
		$title = $title . ' - ' . $blogname;
		
	if ( $echo )
		echo $title;
		
	return $title;
}

/**
 * Display the meta title
 */
function the_meta_title($echo = true) {
	if ( is_singular() )
		$title = get_the_title();
	else
		$title = get_bloginfo('name');
		
	if ( $echo )
		echo $title;
		
	return $title;
}

/**
 * Display the meta description
 */
function the_meta_description($echo = true) {
	global $wp_query;

	if ( is_singular() )
		$description = substr(str_replace('"', "'", wp_strip_all_tags(get_post($wp_query->queried_object->ID)->post_content, true)), 0, 300) . '...';
	else
		$description = get_bloginfo('description');
		
	if ( $echo )
		echo $description;
		
	return $description;
}

/**
 * Display the rel="canonical"
 */
function the_rel_canonical() {
	global $wp_query;

	if ( is_404() || is_search() || is_tag() )
		return;
		
	$canonical = '';
	
	if ( _is_front_page() )
		$canonical = trailingslashit(get_option('home'));
	elseif ( is_home() && false == _is_front_page() && false == is_paged() )
		$canonical = get_permalink($wp_query->get_queried_object_id());
	elseif ( is_paged() )
		$canonical = get_pagenum_link(get_query_var('paged'));
	elseif ( is_singular() )
		$canonical = get_permalink(get_query_var('p'));
	elseif ( is_category() )
		$canonical = get_category_link(get_query_var('cat'));
	elseif ( is_day() )
		$canonical = get_day_link(get_query_var('year'), get_query_var('monthnum'), get_query_var('day'));
	elseif ( is_month() )
		$canonical = get_month_link(get_query_var('year'), get_query_var('monthnum'));
	elseif ( is_year() )
		$canonical = get_year_link(get_query_var('year'));
	elseif ( is_attachment() )
		$canonical = get_attachment_link(get_query_var('attachment_id'));
		
	if ( empty( $canonical ) )
		return;

	echo sprintf('<link rel="canonical" href="%s" />%s', $canonical, "\n");
}

/**
 * Display the robot meta tag
 */
function the_robots() {
	$robots = '';
	$noindex = array(0 => 'index, follow', 1 => 'index, follow', 2 => 'index, nofollow',
					 3 => 'noindex', 4 => 'noindex, follow', 5 => 'noindex, nofollow');
	$options = array('home' => 0, 'single' => 0, 'page' => 0, 'category' => 0,
					 'search' => 4, 'archive' => 4, 'tagging' => 4, '404' => 4, 'attachment' => 0);
	
	if ( ( is_date() || is_author() || is_paged() || is_attachment() ) && !is_attachment() )
		$area = 'archive';
	elseif ( _is_front_page() || ( is_home() && !_is_front_page() && !is_paged() ) )
		$area = 'home';
	elseif ( is_single() && !is_attachment() )
		$area = 'single';
	elseif ( is_page() )
		$area = 'page';
	elseif ( is_category() )
		$area = 'category';
	elseif ( is_search() )
		$area = 'search';
	elseif ( is_tag() )
		$area = 'tagging';
	elseif ( is_404() )
		$area = '404';
	elseif ( is_attachment() )
		$area = 'attachment';
	else
		$area = 'home';
			
	$option = $options[$area];
		
	if ( is_singular() && get_option( 'page_comments' ) && get_query_var( 'cpage' ) >= 1 && $option <= 2 )
		$robots = $noindex[$option + 3];
	else
		$robots = $noindex[$option];
			
	$robots.= ( $robots ? ', ' : '' ) . 'noodp';
	$robots.= ( $robots ? ', ' : '' ) . 'noarchive';
	$robots.= ( $robots ? ', ' : '' ) . 'noydir';
	
	if ( $robots )
		echo sprintf('<meta name="robots" content="%s" />%s', $robots, "\n");
}

/**
 * Display the count of found posts in this search
 */
function the_search_count($singular = 'Artikel', $plural = 'Artikel') {
	global $wp_query;

	if ( 1 == $wp_query->found_posts )
		echo $wp_query->found_posts . ' '. $singular;
	else
		echo $wp_query->found_posts . ' ' . $plural;
}

/**
 * Display a paging bar
 *
 * @see http://playground.ebiene.de/2554/wordpress-pagebar-pluginlos/
 */
function the_paging_bar($range = 9) {
	global $wp_query, $paged;

	$count = $wp_query->max_num_pages;
	$page = $paged;
	$ceil = ceil($range / 2);

	if ( $count <= 1 )
		return false;

	if ( false == $page)
		$page = 1;

	if ($count > $range):
		if ( $page <= $range ):
			$min = 1;
			$max = $range + 1;
		elseif ( $page >= ( $count - $ceil ) ):
			$min = $count - $range;
			$max = $count;
		elseif ( $page >= $range && $page < ( $count - $ceil ) ):
			$min = $page - $ceil;
			$max = $page + $ceil;
		endif;
	else:
		$min = 1;
		$max = $count;
	endif;

	if ( false == empty( $min ) && false == empty( $max ) ):
		for ( $i = $min; $i <= $max; $i++ ):
			echo sprintf('<li><a href="%s"%s>%d</a></li>', get_pagenum_link($i), ( $i == $page ? ' class="active"' : '' ), $i);
		endfor;
	endif;
}

/**
 * Check if a paging bar sould be displayed
 */
function paging_bar_needed() {
   global $wp_query;
   
   return ( $wp_query->max_num_pages > 1 );
}

/**
 * wpautop for HTML5
 *
 * @author nicolas@nicolasgallagher.com
 * @see http://nicolasgallagher.com/using-html5-elements-in-wordpress-post-content/
 */
function html5wpautop($pee, $br = 1) {
	if ( trim($pee) === '' )
		return '';
	 
	$pee = $pee . "\n"; // just to make things a little easier, pad the end
	$pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
	// Space things out a little
	// *insertion* of section|article|aside|header|footer|hgroup|figure|details|figcaption|summary
	$allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|map|area|blockquote|address|math|style|input|p|h[1-6]|hr|fieldset|legend|section|article|aside|header|footer|hgroup|figure|details|figcaption|summary)';
	$pee = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
	$pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);
	$pee = str_replace(array("\r\n", "\r"), "\n", $pee); // cross-platform newlines
	if ( strpos($pee, '<object') !== false ) {
		$pee = preg_replace('|\s*<param([^>]*)>\s*|', "<param$1>", $pee); // no pee inside object/embed
		$pee = preg_replace('|\s*</embed>\s*|', '</embed>', $pee);
	}
	$pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
	// make paragraphs, including one at the end
	$pees = preg_split('/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY);
	$pee = '';
	foreach ( $pees as $tinkle )
		$pee .= '<p>' . trim($tinkle, "\n") . "</p>\n";
	$pee = preg_replace('|<p>\s*</p>|', '', $pee); // under certain strange conditions it could create a P of entirely whitespace
	// *insertion* of section|article|aside
	$pee = preg_replace('!<p>([^<]+)</(div|address|form|section|article|aside)>!', "<p>$1</p></$2>", $pee);
	$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee); // don't pee all over a tag
	$pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // problem with nested lists
	$pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
	$pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
	$pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
	$pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
	if ($br) {
		$pee = preg_replace_callback('/<(script|style).*?<\/\\1>/s', create_function('$matches', 'return str_replace("\n", "<WPPreserveNewline />", $matches[0]);'), $pee);
		$pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
		$pee = str_replace('<WPPreserveNewline />', "\n", $pee);
	}
	$pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
	// *insertion* of img|figcaption|summary
	$pee = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol|img|figcaption|summary)[^>]*>)!', '$1', $pee);
	if (strpos($pee, '<pre') !== false)
		$pee = preg_replace_callback('!(<pre[^>]*>)(.*?)</pre>!is', 'clean_pre', $pee );
	$pee = preg_replace( "|\n</p>$|", '</p>', $pee );

	return $pee;
}
remove_filter('the_excerpt', 'wpautop');
remove_filter('the_content', 'wpautop');
add_filter('the_excerpt', 'html5wpautop');
add_filter('the_content', 'html5wpautop');

/**
 * Add alignment styles to feeds
 */
function add_alignment_styles($matches) {
	$explicit_alignment_styles = array(
		'alignleft' => 'float: left;',
		'alignright' => 'float: right;',
		'aligncenter' => 'display: block; margin-right: auto; margin-left: auto;',
		'centered' => 'display: block; margin-right: auto; margin-left: auto;',
		'img.alignleft' => 'padding: 4px; margin: 0 7px 2px 0;',
		'img.alignright' => 'padding: 4px; margin: 0 0 2px 7px;',
		'wp-caption' => 'border: 1px solid #dddddd; background-color: #f3f3f3; padding-top: 4px; margin: 10px; text-align:center;',
		'p.wp-caption-text' => 'padding: 0 4px 5px; margin: 0;',
	);
	
	$tag = strtolower($matches[1]);
	$style = "";
	
	if ( preg_match( '/class=(["\'])(.+?)\1/i', $matches[0], $classdata ) )
		$classes = preg_split('/\s+/', $classdata[2]);
	else
		return $matches[0];
	
	foreach ( $classes as $class ):
		if ( isset( $explicit_alignment_styles[$class] ) )
			$style.= " " . $explicit_alignment_styles[$class];
		if ( isset( $explicit_alignment_styles[$tag . '.' . $class] ) )
			$style.= " " . $explicit_alignment_styles[$tag.'.'.$class];
	endforeach;
	
	if ( empty( $style ) )
		return $matches[0];
	
	if ( preg_match( '/style=(["\'])(.*?)\1/i', $matches[0], $styledata ) )
		$result = str_replace($styledata[0], "style={$styledata[1]}{$styledata[2]}; {$style}{$styledata[1]}", $matches[0]);
	else
		$result = str_replace('<'.$matches[1], "<$tag style='$style' ", $matches[0]);
	
	return $result;
}

add_filter('the_content', function($content) {
	if ( is_feed() ) $content = preg_replace_callback('/<(div|img|p|span).*?>/i', 'add_alignment_styles', $content);
	return $content;
}, 10000);

/**
 * Get number of pingbacks and trackbacks
 */
function get_pings_number($post_id = 0) {
	global $id, $wpdb;
	
	$post_id = (int) $post_id;

	if ( false == $post_id )
		$post_id = (int) $id;

	$count = $wpdb->get_var($wpdb->prepare("SELECT count(comment_id) FROM $wpdb->comments WHERE comment_type IN ('pingback', 'trackback') AND comment_approved = 1 AND comment_post_id = %d", $post_id));

	return apply_filters('get_pings_number', $count, $post_id);
}

/**
 * Get number of tweets for post
 */
function get_twitter_count($post_id = 0) {
	global $id;
	
	$post_id = (int) $post_id;

	if ( false == $post_id )
		$post_id = (int) $id;
	
	if ( false === ( $count = get_transient( 'twittercount' . $post_id ) ) ):
		$url = rawurlencode(get_permalink($post_id));
		
		if ( false === ( $json = _curl_get_file_contents( 'http://urls.api.twitter.com/1/urls/count.json?url=' . $url ) ) )
			return 0;
			
		$count = json_decode($json)->count;
		set_transient('twittercount_' . $post_id, $count, 60 * 5);
	endif;
	
	return $count;
}

/**
 * Get number of shares and likes for post
 */
function get_facebook_count($post_id = 0) {
	global $id;
	
	$post_id = (int) $post_id;

	if ( false == $post_id )
		$post_id = (int) $id;

	if ( false === ( $count = get_transient( 'facebookcount' . $post_id ) ) ):
		$url = rawurlencode(get_permalink($post_id));
		
		if ( false === ( $xml = _curl_get_file_contents( 'http://api.facebook.com/restserver.php?return=json&method=links.getStats&urls=' . $url ) ) )
			return 0;
		
		$count = (int) simplexml_load_string($xml)->link_stat->share_count + (int) simplexml_load_string($xml)->link_stat->like_count;
		set_transient('facebookcount_' . $post_id, $count, 60 * 5);
	endif;
	
	return $count;
}

/**
 * Helper function for facebook and twitter counts
 */
function _curl_get_file_contents($url) {
	$c = curl_init();
	curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($c, CURLOPT_URL, $url);
	$contents = curl_exec($c);
	curl_close($c);

	if ( $contents )
		return $contents;

	return false;
}
