<?php

/*----------Add style.css----------*/

function add_stylesheet() {
    wp_enqueue_style( 'custom-style', get_stylesheet_directory_uri() . '/style.css', false, '1.0', 'all' );
}

add_action('wp_enqueue_scripts', 'add_stylesheet');

/*----------Add script.js----------*/

function add_script() {
    wp_enqueue_script('custom-js', get_stylesheet_directory_uri() . '/js/script.js', array(), '1.0', true );
}

add_action('wp_enqueue_scripts', 'add_script');

/*----------Custom REST APIs----------*/

/*------------------------------
Get By Boundaries
------------------------------*/

function get_endpoint_name($request) {
    $args = array(
        'b1' => $request['b1'],
        'b2' => $request['b2'],
        'b3' => $request['b3'],
        'b4' => $request['b4'],
    );

    global $wpdb;
    $table_name = $wpdb->prefix . 'meteorites';
        
    $result = $wpdb->get_results("SELECT meteorite_id, meteorite_lat, meteorite_lng FROM $table_name WHERE meteorite_lat BETWEEN $args[b1] AND $args[b3] AND meteorite_lng BETWEEN $args[b2] AND $args[b4];", 'ARRAY_A');
        
    if ($result === false) {
        return new WP_Error( 'no_such_points', 'there are no points in these boundaries', array('status' => 404) );
    } else {
        $data = json_encode($result);
        
        $response = new WP_REST_Response($data);
        $response->set_status(200);
        
        return $response;
    }
}

add_action('rest_api_init', function () {
    register_rest_route( 'meteorites/v1', 'bounds/(?P<b1>-?\d*\.?\d*)&(?P<b2>-?\d*\.?\d*)&(?P<b3>-?\d*\.?\d*)&(?P<b4>-?\d*\.?\d*)', array(
        'methods'  => 'GET',
        'callback' => 'get_endpoint_name',
        'permission_callback' => '__return_true',
    ));
});

/*------------------------------
Get All City Names
------------------------------*/

function get_city_names($request) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'meteorites';
        
    $result = $wpdb->get_results("SELECT DISTINCT meteorite_city_name FROM $table_name WHERE meteorite_city_name IS NOT NULL", 'ARRAY_A');

    if($result === false) {
        return new WP_Error( 'empty_database', 'database is empty', array('status' => 404) );
    } else {
        $data = json_encode($result);
        
        $response = new WP_REST_Response($data);
        $response->set_status(200);
        
        return $response;
    }
}

add_action('rest_api_init', function () {
    register_rest_route( 'meteorites/v1', 'city_names', array(
        'methods'  => 'GET',
        'callback' => 'get_city_names',
        'permission_callback' => '__return_true',
    ));
});

/*------------------------------
Get By Filter Args
------------------------------*/

function get_by_filter_args($request) {
    $args = array(
        'city_name' => $request['city_name'],
        'from_year' => $request['from_year'],
        'to_year' => $request['to_year'],
        'b1' => $request['b1'] < $request['b3'] ? $request['b1'] : $request['b3'],
        'b2' => $request['b2'] < $request['b4'] ? $request['b2'] : $request['b4'],
        'b3' => $request['b3'] > $request['b1'] ? $request['b3'] : $request['b1'],
        'b4' => $request['b4'] > $request['b2'] ? $request['b4'] : $request['b2'],
    );

    global $wpdb;
    $table_name = $wpdb->prefix . 'meteorites';

    $query = "SELECT meteorite_id, meteorite_lat, meteorite_lng FROM $table_name WHERE" . 
    " meteorite_city_name" . ($args[city_name] != 'null' ? "='" . "$args[city_name]" . "'" : " IS NOT NULL") . 
    " AND meteorite_year" . ($args[from_year] != 'null' ? ">=" . "$args[from_year]" : " IS NOT NULL") . 
    " AND meteorite_year" . ($args[to_year] != 'null' ? "<=" . "$args[to_year]" : " IS NOT NULL") . 
    ($args[b1] != 'null' && $args[b2] != 'null' && $args[b3] != 'null' && $args[b4] != 'null' ? " AND meteorite_lat BETWEEN $args[b1] AND $args[b3] AND meteorite_lng BETWEEN $args[b2] AND $args[b4]" : "");

    //return $query;
    $result = $wpdb->get_results($query,  'ARRAY_A');

    if ($result === false) {
        return new WP_Error( 'no_results_found', 'There is no result that matches the filters!', array('status' => 404) );
    } else {
        $response = new WP_REST_Response(json_encode($result));
        $response->set_status(200);
        
        return $response;
    }
}

add_action('rest_api_init', function () {
    register_rest_route( 'meteorites/v1', 'filter/(?P<city_name>[a-zA-Z0-9- ]+)&(?P<from_year>\d+|null)&(?P<to_year>\d+|null)&(?P<b1>-?\d+\.?\d*|null)&(?P<b2>-?\d+\.?\d*|null)&(?P<b3>-?\d+\.?\d*|null)&(?P<b4>-?\d+\.?\d*|null)', array(
        'methods'  => 'GET',
        'callback' => 'get_by_filter_args',
        'permission_callback' => '__return_true',
    ));
});

add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {
    $parenthandle = 'parent-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.
    $theme = wp_get_theme();
    wp_enqueue_style( $parenthandle, get_template_directory_uri() . '/style.css', 
        array(),  // if the parent theme code has a dependency, copy it to here
        $theme->parent()->get('Version')
    );
}

function custom_post_type_map() {
    $supports = array(
    'title',
    'editor',
    'author',
    'thumbnail',
    'excerpt',
    'custom-fields',
    'comments',
    'revisions',
    'post-formats',
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