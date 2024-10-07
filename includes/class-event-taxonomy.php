<?php
class Event_Taxonomy {
    public static function register() {
        $labels = array(
            'name'              => __( 'Event Types', 'event-manager' ),
            'singular_name'     => __( 'Event Type', 'event-manager' ),
            'search_items'      => __( 'Search Event Types', 'event-manager' ),
            'all_items'         => __( 'All Event Types', 'event-manager' ),
            'parent_item'       => __( 'Parent Event Type', 'event-manager' ),
            'parent_item_colon' => __( 'Parent Event Type:', 'event-manager' ),
            'edit_item'         => __( 'Edit Event Type', 'event-manager' ),
            'update_item'       => __( 'Update Event Type', 'event-manager' ),
            'add_new_item'      => __( 'Add New Event Type', 'event-manager' ),
            'new_item_name'     => __( 'New Event Type Name', 'event-manager' ),
            'menu_name'         => __( 'Event Types', 'event-manager' ),
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'rewrite'           => array( 'slug' => 'event-type' ),
        );

        register_taxonomy( 'event_type', array( 'event' ), $args );
    }
}
