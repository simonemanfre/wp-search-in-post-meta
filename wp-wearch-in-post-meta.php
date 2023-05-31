<?php
/**
 * Plugin Name: Wp Search in Post Meta
 * Description: Enable WordPress search in the post metadata in the frontend or site dashboard as part of the search criteria.
 * Version: 1.0.0
 * Requires at least: 4.6
 * Requires PHP: 5.5
 * Tested up to: 6.2
 * Author: Simone Manfredini
 * Author URI: https://simonemanfre.it
 * License: GPLv2 or later
 * 
 * Text Domain: search-post-meta
 * Domain Path: /languages/
 */

/*
	Copyright 2019 Simone Manfredini

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

/*Enque scripts and styles*/
add_action( 'wp_enqueue_scripts','fwpd_enqueue_scripts_styles' );
function fwpd_enqueue_scripts_styles(){
	wp_enqueue_script( 'my-custom-script',untrailingslashit( plugins_url( '', __FILE__ ) ).'/assets/js/wp-search-in-post-meta.js' );
	wp_enqueue_style( 'my-custom-style',untrailingslashit( plugins_url( '', __FILE__ ) ).'/assets/css/wp-search-in-post-meta.css' );
}
 
//ABILITO RICERCA BACKEND IN POST META
function trp_search_join( $join ) {
    global $wpdb;

    if ( is_search() ) {    
        $join .=' LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
    }

    return $join;
}
add_filter('posts_join', 'trp_search_join' );

function trp_search_where( $where ) {
    global $pagenow, $wpdb;

    if ( is_search() ) {
        $where = preg_replace(
            "/(s*".$wpdb->posts.".post_titles LIKEs*('[^'] ')s*)/",
            "(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1)", $where );
    }

    return $where;
}
add_filter( 'posts_where', 'trp_search_where' );

function trp_search_distinct( $where ) {
    global $wpdb;

    if ( is_search() ) {
        return "DISTINCT";
    }

    return $where;
}
add_filter( 'posts_distinct', 'trp_search_distinct' );