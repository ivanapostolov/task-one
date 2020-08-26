<?php
    /*Template Name: Maps*/
    get_header();
?>

<div id="googleMap" style="width:100%;height:400px"></div>

<script><?php

/*function console_log($output, $with_script_tags = false) {
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . ');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}

$csv = file_get_contents("/home/ivanapostolov/Downloads/Meteorite_Landings.csv");//"https://data.nasa.gov/api/views/gh4g-9sfh/rows.csv");
$records = array_map("str_getcsv", explode("\n", $csv));
array_shift($records);
foreach($records as $record) {
    echo doubleval($record[8]) . ' ';
}*/
//echo $array_data[1][0]; //json_encode(str_getcsv(explode("\n", $csv_data)[1]));
?></script>

<script type="application/json" id="meteorites"><?php
    $table_name = $wpdb->prefix . 'meteorites';
    $result = $wpdb->get_results("SELECT * FROM $table_name;", 'ARRAY_A');
    if ($result === false) {
        echo '{"error":' . $wpdb->last_error . '}';
    } else {
        echo json_encode($result);
    }
?></script>

<script>
function loadMap() {
    let mapProp= {
        center: new google.maps.LatLng(43,23),
        zoom: 5,
    };

    let map = new google.maps.Map(document.getElementById("googleMap"), mapProp);

    let data = JSON.parse(document.getElementById('meteorites').innerHTML);

    data.forEach(e => {
        new google.maps.Marker({ 
            position: new google.maps.LatLng(e.meteorite_lat, e.meteorite_lng), 
            map: map 
        });
    });
}
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD91wVBu5lcSQty3BYUKfGVlpRalES89uA&callback=loadMap"></script>

<?php
    get_footer();
?>