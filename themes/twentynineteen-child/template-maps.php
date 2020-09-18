<?php 
    get_header();
?>

<div class="filter-container">
    <div>
        <label>City Name</label>
        <div class="input-container">
            <input id="city_name" class="input" type="text">
        </div>
    </div>
    <div>
        <label>From Year</label>
        <input id="from_year" class="input" type="number">
    </div>
    <div>
        <label>To Year</label>
        <input id="to_year" class="input" type="number">
    </div>
    <div>
        <label>From Point</label>
        <div class="coordinates-container">
            <input id="lat_one" class="input" type="number" step="0.000001">
            <input id="lng_one" class="input" type="number" step="0.000001">
        </div>
    </div>
    <div>
        <label>To Point</label>
        <div class="coordinates-container">
            <input id="lat_two" class="input" type="number" step="0.000001">
            <input id="lng_two" class="input" type="number" step="0.000001">
        </div>
    </div>
    <div>
        <button id="btn_apply" class="btn-filter">Apply</button>
    </div>
</div>

<div id="googleMap" style="width:100%;height:400px"></div>

<script>
function loadMap() {
    let mapProp = {
        center: new google.maps.LatLng(43,23),
        zoom: 5,
    };

    let map = new google.maps.Map(document.getElementById("googleMap"), mapProp);

    let markers = [];

    document.getElementById("btn_apply").addEventListener("click", (e) => {
        getPoints().then(points => {
            markers = points;
            loadMarkers();
        });
    });

    function loadMarkers() {
        markers.forEach(marker => {
            new google.maps.Marker({ 
                position: new google.maps.LatLng(marker.meteorite_lat, marker.meteorite_lng), 
                map: map,
            });
        });
    }
}
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD91wVBu5lcSQty3BYUKfGVlpRalES89uA&callback=loadMap"></script>

<?php
    get_footer();
?>