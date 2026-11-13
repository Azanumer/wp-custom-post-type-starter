<?php
/**
 * Archive template fallback for the Portfolio post type.
 * Copy this file into your theme as archive-portfolio.php to override it.
 */

get_header();
?>

<main class="cpts-archive">
    <h1><?php post_type_archive_title(); ?></h1>

    <?php if ( have_posts() ) : ?>
        <div class="cpts-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.5rem">
            <?php while ( have_posts() ) : the_post(); ?>
                <article class="cpts-item">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium' ); ?></a>
                    <?php endif; ?>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <?php the_excerpt(); ?>
                </article>
            <?php endwhile; ?>
        </div>

        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <p><?php esc_html_e( 'No items found.', 'cpt-starter' ); ?></p>
    <?php endif; ?>
</main>

<?php
get_footer();
