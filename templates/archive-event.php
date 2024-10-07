<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

get_header();
?>

<h1 class="archive-title"><?php post_type_archive_title(); ?></h1>

<?php if ( have_posts() ) : ?>
    <div class="event-listing">
        <?php while ( have_posts() ) : the_post(); ?>
            <div class="event-item">
                <h2 class="event-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <div class="event-meta">
                    <p><strong><?php _e( 'Date:', 'event-manager' ); ?></strong> <?php echo esc_html( get_post_meta( get_the_ID(), '_event_date', true ) ); ?></p>
                    <p><strong><?php _e( 'Location:', 'event-manager' ); ?></strong> <?php echo esc_html( get_post_meta( get_the_ID(), '_event_location', true ) ); ?></p>
                </div>
                <div class="event-excerpt">
                    <?php the_excerpt(); ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <?php the_posts_pagination(); ?>

<?php else : ?>
    <p><?php _e( 'No events found.', 'event-manager' ); ?></p>
<?php endif; ?>

<?php
get_footer();
