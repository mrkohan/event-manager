<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Event_REST_API {

    /**
     * Initializes the class by registering REST API routes.
     */
    public static function init() {
        add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
    }

    /**
     * Registers custom REST API routes.
     */
    public static function register_routes() {
        register_rest_route( 'event-manager/v1', '/events', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_events' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( 'event-manager/v1', '/events/(?P<id>\d+)', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_event' ),
            'permission_callback' => '__return_true',
            'args'                => array(
                'id' => array(
                    'validate_callback' => 'is_numeric',
                ),
            ),
        ) );
    }

    /**
     * Retrieves a collection of events.
     *
     * @param WP_REST_Request $request The REST request.
     * @return WP_REST_Response
     */
    public static function get_events( $request ) {
        $args = array(
            'post_type'      => 'event',
            'posts_per_page' => -1,
        );

        // Optional filtering by event type
        if ( $request->get_param( 'event_type' ) ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'event_type',
                    'field'    => 'slug',
                    'terms'    => $request->get_param( 'event_type' ),
                ),
            );
        }

        // Optional filtering by date range
        $meta_query = array();

        if ( $request->get_param( 'date_from' ) ) {
            $meta_query[] = array(
                'key'     => '_event_date',
                'value'   => $request->get_param( 'date_from' ),
                'compare' => '>=',
                'type'    => 'DATE',
            );
        }

        if ( $request->get_param( 'date_to' ) ) {
            $meta_query[] = array(
                'key'     => '_event_date',
                'value'   => $request->get_param( 'date_to' ),
                'compare' => '<=',
                'type'    => 'DATE',
            );
        }

        if ( ! empty( $meta_query ) ) {
            $args['meta_query'] = $meta_query;
        }

        $events = get_posts( $args );
        $data   = array();

        foreach ( $events as $event ) {
            $data[] = self::prepare_event_data( $event );
        }

        return rest_ensure_response( $data );
    }

    /**
     * Retrieves a single event.
     *
     * @param WP_REST_Request $request The REST request.
     * @return WP_REST_Response
     */
    public static function get_event( $request ) {
        $id = (int) $request['id'];
        $event = get_post( $id );

        if ( empty( $event ) || $event->post_type !== 'event' ) {
            return new WP_Error( 'event_not_found', __( 'Event not found', 'event-manager' ), array( 'status' => 404 ) );
        }

        $data = self::prepare_event_data( $event );

        return rest_ensure_response( $data );
    }

    /**
     * Prepares event data for REST response.
     *
     * @param WP_Post $event The event post object.
     * @return array
     */
    private static function prepare_event_data( $event ) {
        $event_date     = get_post_meta( $event->ID, '_event_date', true );
        $event_location = get_post_meta( $event->ID, '_event_location', true );
        $event_types    = wp_get_post_terms( $event->ID, 'event_type', array( 'fields' => 'names' ) );

        return array(
            'id'          => $event->ID,
            'title'       => $event->post_title,
            'content'     => apply_filters( 'the_content', $event->post_content ),
            'excerpt'     => get_the_excerpt( $event ),
            'date'        => $event_date,
            'location'    => $event_location,
            'event_types' => $event_types,
            'link'        => get_permalink( $event ),
        );
    }
}
