<?php
/**
 * Plugin Name: Meteorites Table Initializer
 * Plugin URI: http://www.mywebsite.com/meteorites-plugin
 * Description: The Meteorites Plugin
 * Version: 1.0
 * Author: Ivan Apostolov
 * Author URI: http://www.mywebsite.com
 */

function populate_table($wpdb, $table_name) {
    $url = 'https://data.nasa.gov/api/views/gh4g-9sfh/rows.csv';

    $csv = file_get_contents($url);

    $records = array_map("str_getcsv", explode("\n", $csv));

    if ($records !== false) {
        array_shift($records);

        foreach($records as $record) {
            $year = intval(explode('/', explode(' ', $record[6], 2)[0], 3)[2]);

            $inserted = $wpdb->insert($table_name, array(
                'meteorite_city_name' => $record[0],
                'meteorite_recclass' => $record[3],
                'meteorite_year' => $year,
                'meteorite_lat' => doubleval($record[7]),
                'meteorite_lng' => doubleval($record[8]),
            ), array('%s', '%s', '%d', '%f', '%f'));

            if ($inserted === false) {
                trigger_error($wpdb->last_error,E_USER_ERROR);
            }
        }
    }
}

function create_db() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $table_name = $wpdb->prefix . 'meteorites';
    $sql = "CREATE TABLE $table_name (
        meteorite_id INTEGER AUTO_INCREMENT,
        meteorite_city_name VARCHAR(255),
        meteorite_recclass VARCHAR(255),
        meteorite_year INTEGER,
        meteorite_lat DOUBLE,
        meteorite_lng DOUBLE,
        PRIMARY KEY (meteorite_id)
    ) $charset_collate;";
    dbDelta( $sql );

    if (!$wpdb->get_var("SELECT COUNT(*) FROM $table_name;")) {
        populate_table($wpdb, $table_name);
    }
}

register_activation_hook( __FILE__, 'create_db' );

?>