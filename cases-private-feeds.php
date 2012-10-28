<?php
/*
Plugin Name: Cases. Kernel. Private Feeds
Plugin URI: http://wpcases.com/
Description: Ограничивает доступ к RSS-лентам.
Author: Sergey Biryukov
Author URI: http://profiles.wordpress.org/sergeybiryukov/
Version: 0.1.1
*/ 

function cpf_disable_feed() {
	if ( isset( $_GET['uid'] ) && isset( $_GET['key'] ) ) {
		$user = get_userdata( (int) $_GET['uid'] );
		if ( isset( $user->user_email ) && wp_hash( $user->user_email ) == $_GET['key'] )
			return;
	}

	wp_die( sprintf( 'RSS-ленты нет. Пожалуйста, посетите <a href="%s">главную страницу</a>.', home_url( '/' ) ) );
}  
add_action( 'do_feed',      'cpf_disable_feed', 1 );
add_action( 'do_feed_rdf',  'cpf_disable_feed', 1 );
add_action( 'do_feed_rss',  'cpf_disable_feed', 1 );
add_action( 'do_feed_rss2', 'cpf_disable_feed', 1 );
add_action( 'do_feed_atom', 'cpf_disable_feed', 1 );

function cpf_add_key_to_feed_links( $url ) {
	$user = wp_get_current_user();
	if ( isset( $user->user_email ) )
		$link = esc_url( add_query_arg( array( 'uid' => $user->ID, 'key' => wp_hash( $user->user_email ) ), $url ) );

	return $link;
}
add_filter( 'feed_link',                   'cpf_add_key_to_feed_links' );
add_filter( 'post_comments_feed_link',     'cpf_add_key_to_feed_links' );
add_filter( 'author_feed_link',            'cpf_add_key_to_feed_links' );
add_filter( 'category_feed_link',          'cpf_add_key_to_feed_links' );
add_filter( 'category_tag_link',           'cpf_add_key_to_feed_links' );
add_filter( 'taxonomy_feed_link',          'cpf_add_key_to_feed_links' );
add_filter( 'search_feed_link',            'cpf_add_key_to_feed_links' );
add_filter( 'post_type_archive_feed_link', 'cpf_add_key_to_feed_links' );

function cpf_restore_default_feed_links() {
	remove_action( 'roots_head', 'roots_feed_link' );

	add_action( 'wp_head', 'feed_links', 2 );
	add_action( 'wp_head', 'feed_links_extra', 3 );

	add_theme_support( 'automatic-feed-links' );
}
add_action( 'init', 'cpf_restore_default_feed_links', 11 );
?>