<?php
/**
 * Plugin Name:       Event Manager
 * Plugin URI:        https://kohan.com.tr
 * Description:       A plugin to manage events with custom post types, taxonomies, and more.
 * Version:           1.0.0
 * Author:            Reza Kohan
 * Author URI:        https://kohan.com.tr
 * Text Domain:       event-manager
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Define plugin constants.
define( 'EM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'EM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include required files.
require_once EM_PLUGIN_DIR . 'includes/class-event-cpt.php';
require_once EM_PLUGIN_DIR . 'includes/class-event-taxonomy.php';
require_once EM_PLUGIN_DIR . 'includes/class-event-metaboxes.php';
require_once EM_PLUGIN_DIR . 'includes/class-event-shortcodes.php';
require_once EM_PLUGIN_DIR . 'includes/class-event-notifications.php';
require_once EM_PLUGIN_DIR . 'includes/class-event-rest-api.php';
require_once EM_PLUGIN_DIR . 'includes/class-event-rsvp.php';

// Initialize the plugin.
function em_initialize_plugin() {
    // Load text domain for translations.
    load_plugin_textdomain( 'event-manager', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

    // Register custom post type and taxonomy.
    Event_CPT::register();
    Event_Taxonomy::register();

    // Add metaboxes.
    Event_Metaboxes::init();

    // Initialize shortcodes.
    Event_Shortcodes::init();

    // Initialize notifications.
    Event_Notifications::init();

    // Initialize REST API endpoints.
    Event_REST_API::init();

    // Initialize RSVP functionality.
    Event_RSVP::init();
}
add_action( 'init', 'em_initialize_plugin' );

// Enqueue scripts and styles.
function em_enqueue_assets() {
    wp_enqueue_style( 'em-style', EM_PLUGIN_URL . 'assets/css/style.css', array(), '1.0.0' );
    wp_enqueue_script( 'em-script', EM_PLUGIN_URL . 'assets/js/script.js', array( 'jquery' ), '1.0.0', true );
}
add_action( 'wp_enqueue_scripts', 'em_enqueue_assets' );

// Activation and deactivation hooks.
function em_activate_plugin() {
    // Flush rewrite rules to register custom post types and taxonomies.
    em_initialize_plugin();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'em_activate_plugin' );

function em_create_sample_data() {
    // Ensure the custom post type and taxonomy are registered.
    Event_CPT::register();
    Event_Taxonomy::register();

    // Flush rewrite rules to avoid 404 errors.
    flush_rewrite_rules();

    // Create sample event types.
    $event_types = array(
        'Conference',
        'Workshop',
        'Webinar',
        'Meetup',
    );

    foreach ( $event_types as $event_type ) {
        if ( ! term_exists( $event_type, 'event_type' ) ) {
            wp_insert_term( $event_type, 'event_type' );
        }
    }

    // Create sample events.
    $sample_events = array(
        array(
            'title'       => 'WordPress Conference 2024',
            'content'     => 'Join us for the annual WordPress Conference where you can meet developers and enthusiasts from around the world.',
            'date'        => date( 'Y-m-d', strtotime( '+1 month' ) ),
            'location'    => 'New York City, USA',
            'event_type'  => 'Conference',
        ),
        array(
            'title'       => 'Online SEO Workshop',
            'content'     => 'An interactive workshop focused on the latest SEO techniques and best practices.',
            'date'        => date( 'Y-m-d', strtotime( '+2 weeks' ) ),
            'location'    => 'Online',
            'event_type'  => 'Workshop',
        ),
        array(
            'title'       => 'Marketing Strategies Webinar',
            'content'     => 'Learn effective marketing strategies in our free webinar.',
            'date'        => date( 'Y-m-d', strtotime( '+3 weeks' ) ),
            'location'    => 'Online',
            'event_type'  => 'Webinar',
        ),
        array(
            'title'       => 'Local WordPress Meetup',
            'content'     => 'A casual meetup for WordPress users in the local area.',
            'date'        => date( 'Y-m-d', strtotime( '+5 days' ) ),
            'location'    => 'San Francisco, USA',
            'event_type'  => 'Meetup',
        ),
    );

    foreach ( $sample_events as $event_data ) {
        // Check if an event with the same title already exists.
        $existing_event = get_page_by_title( $event_data['title'], OBJECT, 'event' );
        if ( $existing_event ) {
            continue; // Skip if the event already exists.
        }

        // Create the event.
        $event_id = wp_insert_post( array(
            'post_title'   => wp_strip_all_tags( $event_data['title'] ),
            'post_content' => $event_data['content'],
            'post_status'  => 'publish',
            'post_type'    => 'event',
        ) );

        if ( $event_id && ! is_wp_error( $event_id ) ) {
            // Set event meta data.
            update_post_meta( $event_id, '_event_date', $event_data['date'] );
            update_post_meta( $event_id, '_event_location', $event_data['location'] );

            // Assign event type.
            $term = get_term_by( 'name', $event_data['event_type'], 'event_type' );
            if ( $term ) {
                wp_set_post_terms( $event_id, $term->term_id, 'event_type' );
            }
        }
    }
}
register_activation_hook( __FILE__, 'em_create_sample_data' );

function em_deactivate_plugin() {
    // Flush rewrite rules on deactivation.
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'em_deactivate_plugin' );

// Deactivation hook to remove sample data.
register_deactivation_hook( __FILE__, 'em_remove_sample_data' );

/**
 * Removes sample data upon plugin deactivation.
 */
function em_remove_sample_data() {
    // Delete sample events.
    $sample_titles = array(
        'WordPress Conference 2024',
        'Online SEO Workshop',
        'Marketing Strategies Webinar',
        'Local WordPress Meetup',
    );

    foreach ( $sample_titles as $title ) {
        $event = get_page_by_title( $title, OBJECT, 'event' );
        if ( $event ) {
            wp_delete_post( $event->ID, true );
        }
    }

    // Optionally, delete sample event types.
    $event_types = array(
        'Conference',
        'Workshop',
        'Webinar',
        'Meetup',
    );

    foreach ( $event_types as $event_type ) {
        $term = get_term_by( 'name', $event_type, 'event_type' );
        if ( $term ) {
            wp_delete_term( $term->term_id, 'event_type' );
        }
    }

    // Flush rewrite rules.
    flush_rewrite_rules();
}


function em_modify_event_archive_query( $query ) {
    if ( is_post_type_archive( 'event' ) && $query->is_main_query() && ! is_admin() ) {
        if ( isset( $_GET['event_type'] ) && $_GET['event_type'] != 0 ) {
            $query->set( 'tax_query', array(
                array(
                    'taxonomy' => 'event_type',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field( $_GET['event_type'] ),
                ),
            ) );
        }

        $meta_query = array();

        if ( ! empty( $_GET['date_from'] ) ) {
            $meta_query[] = array(
                'key'     => '_event_date',
                'value'   => sanitize_text_field( $_GET['date_from'] ),
                'compare' => '>=',
                'type'    => 'DATE',
            );
        }

        if ( ! empty( $_GET['date_to'] ) ) {
            $meta_query[] = array(
                'key'     => '_event_date',
                'value'   => sanitize_text_field( $_GET['date_to'] ),
                'compare' => '<=',
                'type'    => 'DATE',
            );
        }

        if ( ! empty( $meta_query ) ) {
            $query->set( 'meta_query', $meta_query );
        }
    }
}
add_action( 'pre_get_posts', 'em_modify_event_archive_query' );


function em_add_rsvps_submenu() {
    add_submenu_page(
        'edit.php?post_type=event',
        __( 'Event RSVPs', 'event-manager' ),
        __( 'Event RSVPs', 'event-manager' ),
        'manage_options',
        'event-rsvps',
        'em_render_rsvps_page'
    );
}
add_action( 'admin_menu', 'em_add_rsvps_submenu' );


function em_render_rsvps_page() {
    ?>
    <div class="wrap">
        <h1><?php __( 'Event RSVPs', 'event-manager' ); ?></h1>
        <?php
        // Fetch all events
        $events = get_posts( array(
            'post_type'   => 'event',
            'numberposts' => -1,
        ) );

        if ( $events ) {
            foreach ( $events as $event ) {
                $rsvps = get_post_meta( $event->ID, '_event_rsvps', true );
                echo '<h2>' . esc_html( $event->post_title ) . '</h2>';

                if ( ! empty( $rsvps ) && is_array( $rsvps ) ) {
                    echo '<ul>';
                    foreach ( $rsvps as $user_id ) {
                        $user_info = get_userdata( $user_id );
                        if ( $user_info ) {
                            echo '<li>' . esc_html( $user_info->display_name ) . ' (' . esc_html( $user_info->user_email ) . ')</li>';
                        }
                    }
                    echo '</ul>';
                } else {
                    _e( 'No RSVPs yet.', 'event-manager' );
                }
            }
        } else {
            _e( 'No events found.', 'event-manager' );
        }
        ?>
    </div>
    <?php
}
