<?php
class Event_Metaboxes {
    public static function init() {
        add_action( 'add_meta_boxes', array( __CLASS__, 'add_metaboxes' ) );
        add_action( 'save_post', array( __CLASS__, 'save_metaboxes' ) );
    }

    public static function add_metaboxes() {
        add_meta_box(
            'event_details',
            __( 'Event Details', 'event-manager' ),
            array( __CLASS__, 'render_metaboxes' ),
            'event',
            'normal',
            'high'
        );
    }

    public static function render_metaboxes( $post ) {
        wp_nonce_field( 'event_details_nonce', 'event_details_nonce_field' );

        $event_date = get_post_meta( $post->ID, '_event_date', true );
        $event_location = get_post_meta( $post->ID, '_event_location', true );

        ?>
        <p>
            <label for="event_date"><?php __( 'Event Date:', 'event-manager' ); ?></label><br>
            <input type="date" id="event_date" name="event_date" value="<?php echo esc_attr( $event_date ); ?>">
        </p>
        <p>
            <label for="event_location"><?php __( 'Event Location:', 'event-manager' ); ?></label><br>
            <input type="text" id="event_location" name="event_location" value="<?php echo esc_attr( $event_location ); ?>">
        </p>
        <?php
    }

    public static function save_metaboxes( $post_id ) {
        // Verify nonce.
        if ( ! isset( $_POST['event_details_nonce_field'] ) || ! wp_verify_nonce( $_POST['event_details_nonce_field'], 'event_details_nonce' ) ) {
            return;
        }

        // Check autosave.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check permissions.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Sanitize and save data.
        if ( isset( $_POST['event_date'] ) ) {
            $event_date = sanitize_text_field( $_POST['event_date'] );
            update_post_meta( $post_id, '_event_date', $event_date );
        }

        if ( isset( $_POST['event_location'] ) ) {
            $event_location = sanitize_text_field( $_POST['event_location'] );
            update_post_meta( $post_id, '_event_location', $event_location );
        }
    }
}
