<?php
/**
 * Uninstall Event Manager Plugin
 *
 * Deletes plugin data upon uninstall.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Delete custom post type posts.
$events = get_posts( array(
    'post_type'      => 'event',
    'numberposts'    => -1,
    'post_status'    => 'any',
    'fields'         => 'ids',
) );

foreach ( $events as $event_id ) {
    wp_delete_post( $event_id, true );
}

// Delete custom taxonomy terms.
$terms = get_terms( array(
    'taxonomy'   => 'event_type',
    'hide_empty' => false,
    'fields'     => 'ids',
) );

foreach ( $terms as $term_id ) {
    wp_delete_term( $term_id, 'event_type' );
}

// Delete post meta data.
global $wpdb;
$wpdb->query( "DELETE FROM $wpdb->postmeta WHERE meta_key IN ( '_event_date', '_event_location', '_event_rsvps' )" );
