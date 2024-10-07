<?php
class Event_Notifications {
    public static function init() {
        add_action( 'publish_event', array( __CLASS__, 'send_event_notification' ), 10, 2 );
    }

    public static function send_event_notification( $ID, $post ) {
        // Get subscribers (for simplicity, i'll assume all users are subscribers).
        $users = get_users( array( 'fields' => array( 'user_email' ) ) );

        $emails = wp_list_pluck( $users, 'user_email' );

        $subject = __( 'New Event Published', 'event-manager' );
        $message = sprintf(
            __( 'A new event "%s" has been published. View it here: %s', 'event-manager' ),
            $post->post_title,
            get_permalink( $ID )
        );

        // Send email to all subscribers.
        wp_mail( $emails, $subject, $message );
    }
}
