<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

get_header();

while ( have_posts() ) : the_post();
    $event_date = get_post_meta( get_the_ID(), '_event_date', true );
    $event_location = get_post_meta( get_the_ID(), '_event_location', true );
    ?>

    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <h1 class="event-title"><?php the_title(); ?></h1>
        <div class="event-meta">
            <p><strong><?php _e( 'Date:', 'event-manager' ); ?></strong> <?php echo esc_html( $event_date ); ?></p>
            <p><strong><?php _e( 'Location:', 'event-manager' ); ?></strong> <?php echo esc_html( $event_location ); ?></p>
            <p><strong><?php _e( 'Type:', 'event-manager' ); ?></strong> <?php the_terms( get_the_ID(), 'event_type' ); ?></p>
        </div>
        <div class="event-content">
            <?php the_content(); ?>
        </div>
        <?php Event_RSVP::render_form( get_the_ID() ); ?>
    </article>

    <?php
endwhile;

get_footer();
