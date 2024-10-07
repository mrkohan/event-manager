<?php
class Event_Manager_Tests extends WP_UnitTestCase {
    public function test_event_cpt_registration() {
        $this->assertTrue( post_type_exists( 'event' ) );
    }

    public function test_event_taxonomy_registration() {
        $this->assertTrue( taxonomy_exists( 'event_type' ) );
    }

    public function test_event_metaboxes() {
        global $wp_meta_boxes;
        $this->assertArrayHasKey( 'event_details', $wp_meta_boxes['event']['normal']['high'] );
    }

    public function test_event_shortcode_output() {
        $output = do_shortcode( '[event_list]' );
        $this->assertNotEmpty( $output );
    }

    public function test_rsvp_functionality() {
        // Simulate RSVP submission.
        $user_id = $this->factory->user->create();
        wp_set_current_user( $user_id );

        $event_id = $this->factory->post->create( array( 'post_type' => 'event' ) );

        $_POST['event_id'] = $event_id;
        $_POST['nonce'] = wp_create_nonce( 'event_rsvp_nonce' );

        ob_start();
        Event_RSVP::handle_rsvp();
        $response = ob_get_clean();

        $this->assertStringContainsString( 'RSVP successful', $response );
    }
}
