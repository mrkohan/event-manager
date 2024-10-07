<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Event_Shortcodes {

    /**
     * Initializes the shortcodes.
     */
    public static function init() {
        add_shortcode( 'event_list', array( __CLASS__, 'event_list_shortcode' ) );
        add_shortcode( 'event_filter_form', array( __CLASS__, 'event_filter_form_shortcode' ) );
    }

    /**
     * Shortcode to display a list of events.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output.
     */
    public static function event_list_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'type'      => '',
            'date_from' => '',
            'date_to'   => '',
        ), $atts, 'event_list' );

        $args = array(
            'post_type'      => 'event',
            'posts_per_page' => -1,
        );

        if ( ! empty( $atts['type'] ) ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'event_type',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field( $atts['type'] ),
                ),
            );
        }

        if ( ! empty( $atts['date_from'] ) || ! empty( $atts['date_to'] ) ) {
            $meta_query = array( 'relation' => 'AND' );

            if ( ! empty( $atts['date_from'] ) ) {
                $meta_query[] = array(
                    'key'     => '_event_date',
                    'value'   => sanitize_text_field( $atts['date_from'] ),
                    'compare' => '>=',
                    'type'    => 'DATE',
                );
            }

            if ( ! empty( $atts['date_to'] ) ) {
                $meta_query[] = array(
                    'key'     => '_event_date',
                    'value'   => sanitize_text_field( $atts['date_to'] ),
                    'compare' => '<=',
                    'type'    => 'DATE',
                );
            }

            $args['meta_query'] = $meta_query;
        }

        $query = new WP_Query( $args );
        ob_start();

        if ( $query->have_posts() ) {
            echo '<div class="event-listing">';
            while ( $query->have_posts() ) {
                $query->the_post();
                ?>
                <div class="event-item">
                    <h2 class="event-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <div class="event-meta">
                        <p><strong><?php _e( 'Date:', 'event-manager' ); ?></strong> <?php echo esc_html( get_post_meta( get_the_ID(), '_event_date', true ) ); ?></p>
                        <p><strong><?php _e( 'Location:', 'event-manager' ); ?></strong> <?php echo esc_html( get_post_meta( get_the_ID(), '_event_location', true ) ); ?></p>
                    </div>
                </div>
                <?php
            }
            echo '</div>';
        } else {
            _e( 'No events found.', 'event-manager' );
        }

        wp_reset_postdata();
        return ob_get_clean();
    }

    /**
     * Shortcode to display the event filter form.
     *
     * @return string HTML output.
     */
    public static function event_filter_form_shortcode() {
        ob_start();
        ?>

        <form method="get" action="<?php echo esc_url( get_post_type_archive_link( 'event' ) ); ?>" class="event-filter-form">
            <p>
                <label for="event_type"><?php _e( 'Event Type:', 'event-manager' ); ?></label>
                <?php
                wp_dropdown_categories( array(
                    'taxonomy'        => 'event_type',
                    'name'            => 'event_type',
                    'show_option_all' => __( 'All Types', 'event-manager' ),
                    'value_field'     => 'slug',
                ) );
                ?>
            </p>
            <p>
                <label for="date_from"><?php _e( 'From Date:', 'event-manager' ); ?></label>
                <input type="date" name="date_from" id="date_from">
            </p>
            <p>
                <label for="date_to"><?php _e( 'To Date:', 'event-manager' ); ?></label>
                <input type="date" name="date_to" id="date_to">
            </p>
            <p>
                <input type="submit" value="<?php _e( 'Filter', 'event-manager' ); ?>">
            </p>
        </form>

        <?php
        return ob_get_clean();
    }
}
