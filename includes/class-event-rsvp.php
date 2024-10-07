<?php
class Event_RSVP {
    public static function init() {
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
        add_action( 'wp_ajax_event_rsvp', array( __CLASS__, 'handle_rsvp' ) );
        add_action( 'wp_ajax_nopriv_event_rsvp', array( __CLASS__, 'handle_rsvp' ) );
    }

    public static function enqueue_scripts() {
        wp_enqueue_script( 'em-rsvp-script', EM_PLUGIN_URL . 'assets/js/script.js', array( 'jquery' ), '1.0.0', true );
        wp_localize_script( 'em-rsvp-script', 'em_rsvp_obj', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'event_rsvp_nonce' ),
        ) );
    }

    public static function render_form( $event_id ) {
        if ( is_user_logged_in() ) {
            ?>
            <form id="event-rsvp-form" data-event-id="<?php echo esc_attr( $event_id ); ?>">
                <input type="submit" value="<?php _e( 'RSVP to this event', 'event-manager' ); ?>">
            </form>
            <div id="rsvp-response"></div>
            <?php
        } else {
            _e( 'Please log in to RSVP.', 'event-manager' );
        }
    }

    public static function handle_rsvp() {
        check_ajax_referer( 'event_rsvp_nonce', 'nonce' );

        $event_id = absint( $_POST['event_id'] );
        $user_id  = get_current_user_id();

        if ( ! $event_id || ! $user_id ) {
            wp_send_json_error( __( 'Invalid request.', 'event-manager' ) );
        }

        // Record RSVP (for simplicity, i'll save as post meta).
        $existing_rsvps = get_post_meta( $event_id, '_event_rsvps', true );
        $existing_rsvps = $existing_rsvps ? $existing_rsvps : array();

        if ( in_array( $user_id, $existing_rsvps ) ) {
            wp_send_json_error( __( 'You have already RSVPed.', 'event-manager' ) );
        }

        $existing_rsvps[] = $user_id;
        update_post_meta( $event_id, '_event_rsvps', $existing_rsvps );

        // Send confirmation email.
        $user_info = get_userdata( $user_id );
        $event_title = get_the_title( $event_id );

        wp_mail(
            $user_info->user_email,
            __( 'Event RSVP Confirmation', 'event-manager' ),
            sprintf( __( 'You have successfully RSVPed to the event "%s".', 'event-manager' ), $event_title )
        );

        wp_send_json_success( __( 'RSVP successful!', 'event-manager' ) );
    }
}
