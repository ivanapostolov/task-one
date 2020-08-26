<?php
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {
    $parenthandle = 'parent-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.
    $theme = wp_get_theme();
    wp_enqueue_style( $parenthandle, get_template_directory_uri() . '/style.css', 
        array(),  // if the parent theme code has a dependency, copy it to here
        $theme->parent()->get('Version')
    );
    wp_enqueue_style( 'child-style', get_stylesheet_uri(),
        array( $parenthandle ),
        $theme->get('Version') // this only works if you have Version in the style header
    );
}

function custom_post_type_map() {
    $supports = array(
    'title', // post title
    'editor', // post content
    'author', // post author
    'thumbnail', // featured images
    'excerpt', // post excerpt
    'custom-fields', // custom fields
    'comments', // post comments
    'revisions', // post revisions
    'post-formats', // post formats
    );

    $labels = array(
    'name' => _x('maps', 'plural'),
    'singular_name' => _x('map', 'singular'),
    'menu_name' => _x('maps', 'admin menu'),
    'name_admin_bar' => _x('maps', 'admin bar'),
    'add_new' => _x('Add New', 'add new'),
    'add_new_item' => __('Add New Map'),
    'new_item' => __('New Map'),
    'edit_item' => __('Edit Map'),
    'view_item' => __('View Map'),
    'all_items' => __('All Maps'),
    'search_items' => __('Search Maps'),
    'not_found' => __('No Maps Found.'),
    );

    $args = array(
    'supports' => $supports,
    'labels' => $labels,
    'public' => true,
    'query_var' => true,
    'rewrite' => array('slug' => 'maps'),
    'has_archive' => true,
    'hierarchical' => false,
    );
    register_post_type('maps', $args);
}
add_action('init', 'custom_post_type_map');

?>