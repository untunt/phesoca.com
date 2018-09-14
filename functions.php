<?php
/**
 * https://codex.wordpress.org/Customizing_the_Read_More#Prevent_Page_Scroll_When_Clicking_the_More_Link
 */
function remove_more_link_scroll( $link ) {
	$link = preg_replace( '|#more-[0-9]+|', '', $link );
	return $link;
}
add_filter( 'the_content_more_link', 'remove_more_link_scroll' );

function chinese_punctuations( $content ) {
	$prefix = '<span class="cn">';
	$suffix = '</span>';

	$from = array('“', '”', '‘', '’', '…', '—', '·', '·');
	$to = $from;
	foreach ($to as &$punct) {
		$punct = $prefix . $punct . $suffix;
	}
	$except = $to;
	foreach ($except as &$punct) {
		$punct = '\\' . $punct;
	}
	
	$content = str_replace($from, $to, $content);
	$content = str_replace($except, $from, $content);
	return $content;
}
add_filter( 'the_content', 'chinese_punctuations' );

function replace_empty_p( $content ) {
	$content = str_replace('<p></p>', '<br>', $content);
	return $content;
}
add_filter( 'the_content', 'replace_empty_p' );
