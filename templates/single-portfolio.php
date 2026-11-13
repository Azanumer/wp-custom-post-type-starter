<?php
/**
 * Single template fallback for the Portfolio post type.
 * Copy this file into your theme as single-portfolio.php to override it.
 */

get_header();
?>

<main class="cpts-single">
    <?php while ( have_posts() ) : the_post(); ?>
        <article>
            <h1><?php the_title(); ?></h1>

            <?php if ( has_post_thumbnail() ) : ?>
                <div class="cpts-featured"><?php the_post_thumbnail( 'large' ); ?></div>
            <?php endif; ?>

            <div class="cpts-content"><?php the_content(); ?></div>

            <?php
            $terms = get_the_terms( get_the_ID(), 'portfolio_category' );
            if ( $terms && ! is_wp_error( $terms ) ) :
                ?>
                <p class="cpts-terms">
                    <?php esc_html_e( 'Categories:', 'cpt-starter' ); ?>
                    <?php echo esc_html( implode( ', ', wp_list_pluck( $terms, 'name' ) ) ); ?>
                </p>
            <?php endif; ?>
        </article>
    <?php endwhile; ?>
</main>

<?php
get_footer();
