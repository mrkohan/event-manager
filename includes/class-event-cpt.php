<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Event_CPT {

    /**
     * Registers the 'event' custom post type and hooks into admin customizations.
     */
    public static function register() {
        $labels = array(
            'name'                  => __( 'Events', 'event-manager' ),
            'singular_name'         => __( 'Event', 'event-manager' ),
            'menu_name'             => __( 'Events', 'event-manager' ),
            'name_admin_bar'        => __( 'Event', 'event-manager' ),
            'add_new'               => __( 'Add New', 'event-manager' ),
            'add_new_item'          => __( 'Add New Event', 'event-manager' ),
            'new_item'              => __( 'New Event', 'event-manager' ),
            'edit_item'             => __( 'Edit Event', 'event-manager' ),
            'view_item'             => __( 'View Event', 'event-manager' ),
            'all_items'             => __( 'All Events', 'event-manager' ),
            'search_items'          => __( 'Search Events', 'event-manager' ),
            'parent_item_colon'     => __( 'Parent Events:', 'event-manager' ),
            'not_found'             => __( 'No events found.', 'event-manager' ),
            'not_found_in_trash'    => __( 'No events found in Trash.', 'event-manager' ),
        );

        $args = array(
            'labels'             => $labels,
            'description'        => __( 'A custom post type for events.', 'event-manager' ),
            'public'             => true,
            'menu_icon'          => 'dashicons-calendar',
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
            'has_archive'        => true,
            'rewrite'            => array( 'slug' => 'events' ),
            'show_in_rest'       => true,
            'capability_type'    => 'post',
        );

        register_post_type( 'event', $args );

        // Hook into admin list view customizations
        add_filter( 'manage_event_posts_columns', array( __CLASS__, 'add_custom_columns' ) );
        add_action( 'manage_event_posts_custom_column', array( __CLASS__, 'manage_custom_columns' ), 10, 2 );
        add_filter( 'manage_edit-event_sortable_columns', array( __CLASS__, 'make_columns_sortable' ) );
        add_action( 'pre_get_posts', array( __CLASS__, 'sort_custom_columns' ) );
    }

    /**
     * Adds custom columns to the event post type in the admin list view.
     *
     * @param array $columns Existing columns.
     * @return array Modified columns.
     */
    public static function add_custom_columns( $columns ) {
        // Remove the date column
        unset( $columns['date'] );

        // Add custom columns
        $columns['event_date']     = __( 'Event Date:', 'event-manager' );        ;
        $columns['event_location'] = __( 'Location', 'event-manager' );
        $columns['date']           = __( 'Date', 'event-manager' ); // Re-add the date column at the end

        return $columns;
    }

    /**
     * Populates custom columns with data.
     *
     * @param string $column  Column name.
     * @param int    $post_id Post ID.
     */
    public static function manage_custom_columns( $column, $post_id ) {
        switch ( $column ) {
            case 'event_date':
                $event_date = get_post_meta( $post_id, '_event_date', true );
                echo esc_html( $event_date );
                break;
            case 'event_location':
                $event_location = get_post_meta( $post_id, '_event_location', true );
                echo esc_html( $event_location );
                break;
        }
    }

    /**
     * Makes custom columns sortable.
     *
     * @param array $columns Existing sortable columns.
     * @return array Modified sortable columns.
     */
    public static function make_columns_sortable( $columns ) {
        $columns['event_date'] = 'event_date';
        return $columns;
    }

    /**
     * Handles sorting by custom columns.
     *
     * @param WP_Query $query The current WP_Query instance.
     */
    public static function sort_custom_columns( $query ) {
        if ( ! is_admin() || ! $query->is_main_query() ) {
            return;
        }

        if ( 'event_date' === $query->get( 'orderby' ) ) {
            $query->set( 'meta_key', '_event_date' );
            $query->set( 'orderby', 'meta_value' );
        }
    }
}
