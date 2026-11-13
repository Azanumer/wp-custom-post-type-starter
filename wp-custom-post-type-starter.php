<?php
/**
 * Plugin Name: Custom Post Type Starter
 * Plugin URI:  https://github.com/Azanumer/wp-custom-post-type-starter
 * Description: Boilerplate for registering a custom post type with a taxonomy, REST support, archive templates and a listing shortcode. Edit the CPT_* constants to make it yours.
 * Version:     1.0.0
 * Author:      Azan Umer
 * Author URI:  https://azanumer.cv
 * License:     MIT
 * Text Domain: cpt-starter
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
 * ---------------------------------------------------------------------------
 * EDIT THESE — everything else adapts.
 * ---------------------------------------------------------------------------
 */
define( 'CPTS_POST_TYPE', 'portfolio' );       // Post type slug (max 20 chars).
define( 'CPTS_TAXONOMY', 'portfolio_category' ); // Taxonomy slug.
define( 'CPTS_SINGULAR', 'Portfolio Item' );
define( 'CPTS_PLURAL', 'Portfolio Items' );
define( 'CPTS_TAX_SINGULAR', 'Portfolio Category' );
define( 'CPTS_TAX_PLURAL', 'Portfolio Categories' );

/**
 * Register the custom post type.
 */
function cpts_register_post_type() {
    $labels = array(
        'name'               => CPTS_PLURAL,
        'singular_name'      => CPTS_SINGULAR,
        'add_new'            => sprintf( __( 'Add New %s', 'cpt-starter' ), CPTS_SINGULAR ),
        'add_new_item'       => sprintf( __( 'Add New %s', 'cpt-starter' ), CPTS_SINGULAR ),
        'edit_item'          => sprintf( __( 'Edit %s', 'cpt-starter' ), CPTS_SINGULAR ),
        'new_item'           => sprintf( __( 'New %s', 'cpt-starter' ), CPTS_SINGULAR ),
        'view_item'          => sprintf( __( 'View %s', 'cpt-starter' ), CPTS_SINGULAR ),
        'search_items'       => sprintf( __( 'Search %s', 'cpt-starter' ), CPTS_PLURAL ),
        'not_found'          => sprintf( __( 'No %s found', 'cpt-starter' ), strtolower( CPTS_PLURAL ) ),
        'not_found_in_trash' => sprintf( __( 'No %s found in Trash', 'cpt-starter' ), strtolower( CPTS_PLURAL ) ),
        'all_items'          => sprintf( __( 'All %s', 'cpt-starter' ), CPTS_PLURAL ),
        'archives'           => sprintf( __( '%s Archives', 'cpt-starter' ), CPTS_SINGULAR ),
    );

    register_post_type(
        CPTS_POST_TYPE,
        array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_icon'          => 'dashicons-portfolio',
            'menu_position'      => 5,
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions' ),
            'has_archive'        => true,
            'rewrite'            => array( 'slug' => CPTS_POST_TYPE, 'with_front' => false ),
            'show_in_rest'       => true, // Block editor + REST API.
            'taxonomies'         => array( CPTS_TAXONOMY ),
        )
    );
}
add_action( 'init', 'cpts_register_post_type' );

/**
 * Register the companion taxonomy.
 */
function cpts_register_taxonomy() {
    $labels = array(
        'name'              => CPTS_TAX_PLURAL,
        'singular_name'     => CPTS_TAX_SINGULAR,
        'search_items'      => sprintf( __( 'Search %s', 'cpt-starter' ), CPTS_TAX_PLURAL ),
        'all_items'         => sprintf( __( 'All %s', 'cpt-starter' ), CPTS_TAX_PLURAL ),
        'edit_item'         => sprintf( __( 'Edit %s', 'cpt-starter' ), CPTS_TAX_SINGULAR ),
        'add_new_item'      => sprintf( __( 'Add New %s', 'cpt-starter' ), CPTS_TAX_SINGULAR ),
        'new_item_name'     => sprintf( __( 'New %s Name', 'cpt-starter' ), CPTS_TAX_SINGULAR ),
        'menu_name'         => CPTS_TAX_PLURAL,
    );

    register_taxonomy(
        CPTS_TAXONOMY,
        CPTS_POST_TYPE,
        array(
            'labels'            => $labels,
            'public'            => true,
            'hierarchical'      => true, // Category-like. Set false for tag-like.
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rewrite'           => array( 'slug' => CPTS_TAXONOMY, 'with_front' => false ),
        )
    );
}
add_action( 'init', 'cpts_register_taxonomy' );

/**
 * Flush rewrite rules on activation / deactivation so the new URLs work.
 */
function cpts_activate() {
    cpts_register_post_type();
    cpts_register_taxonomy();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'cpts_activate' );

function cpts_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'cpts_deactivate' );

/**
 * Fall back to the bundled templates when the theme has none.
 * Copy templates/archive-portfolio.php or templates/single-portfolio.php
 * into your theme to override them.
 *
 * @param string $template Theme template path.
 * @return string
 */
function cpts_template_fallback( $template ) {
    if ( is_post_type_archive( CPTS_POST_TYPE ) && ! locate_template( 'archive-' . CPTS_POST_TYPE . '.php' ) ) {
        $fallback = plugin_dir_path( __FILE__ ) . 'templates/archive-' . CPTS_POST_TYPE . '.php';
        if ( file_exists( $fallback ) ) {
            return $fallback;
        }
    }
    if ( is_singular( CPTS_POST_TYPE ) && ! locate_template( 'single-' . CPTS_POST_TYPE . '.php' ) ) {
        $fallback = plugin_dir_path( __FILE__ ) . 'templates/single-' . CPTS_POST_TYPE . '.php';
        if ( file_exists( $fallback ) ) {
            return $fallback;
        }
    }
    return $template;
}
add_filter( 'template_include', 'cpts_template_fallback' );

/**
 * [portfolio_list] shortcode — grid of latest items.
 *
 * Attributes: count (default 6), category (taxonomy slug), columns (default 3).
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function cpts_list_shortcode( $atts ) {
    $atts = shortcode_atts(
        array(
            'count'    => 6,
            'category' => '',
            'columns'  => 3,
        ),
        $atts,
        'portfolio_list'
    );

    $args = array(
        'post_type'      => CPTS_POST_TYPE,
        'post_status'    => 'publish',
        'posts_per_page' => max( 1, (int) $atts['count'] ),
        'no_found_rows'  => true,
    );
    if ( '' !== $atts['category'] ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => CPTS_TAXONOMY,
                'field'    => 'slug',
                'terms'    => sanitize_text_field( $atts['category'] ),
            ),
        );
    }

    $query = new WP_Query( $args );
    if ( ! $query->have_posts() ) {
        return '<p>' . esc_html__( 'No items found.', 'cpt-starter' ) . '</p>';
    }

    $columns = max( 1, min( 6, (int) $atts['columns'] ) );
    $out     = '<div class="cpts-grid" style="display:grid;grid-template-columns:repeat(' . $columns . ',1fr);gap:1.5rem">';

    while ( $query->have_posts() ) {
        $query->the_post();
        $out .= '<article class="cpts-item">';
        if ( has_post_thumbnail() ) {
            $out .= '<a href="' . esc_url( get_permalink() ) . '">' . get_the_post_thumbnail( get_the_ID(), 'medium' ) . '</a>';
        }
        $out .= '<h3><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';
        if ( has_excerpt() ) {
            $out .= '<p>' . esc_html( get_the_excerpt() ) . '</p>';
        }
        $out .= '</article>';
    }
    wp_reset_postdata();

    $out .= '</div>';
    return $out;
}
add_shortcode( 'portfolio_list', 'cpts_list_shortcode' );
