<?PHP
    ini_set('max_execution_time', 2000);
    include 'conexion.php';
?>

<!DOCTYPE html>
<html>
<head>
  <title>Leaflet sample</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
crossorigin=""/>
  <link rel="stylesheet" type="text/css" href="css/myStyle.css">
  <link rel="stylesheet" type="text/css" href="css/leaflet-search.css">
  <!-- Make sure you put this AFTER Leaflet's CSS -->
<script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js" integrity="sha512-GffPMF3RvMeYyc1LWMHtK8EbPv0iNZ8/oTtHPx9/cc2ILxQ+u905qIwdpULaqDkyBKgOaB57QTMg7ztg8Jm2Og=="
crossorigin=""></script>
  <script src="js/jquery-3.2.1.min.js" type="text/javascript" ></script>
  <script src="js/leaflet-search.js" type="text/javascript"></script>

  <style>
    .search-input {
        font-family:Courier
    }
    .search-input,
    .leaflet-control-search {
        max-width:400px;
    }

    ul {
        font-size:.85em;
        margin:0;
        padding:0;
    }
    li {
        margin:0 0 2px 18px;
    }
    </style>
</head>
<body>
    <div id = "map"></div>
</body>
</html>
<script>

    $(document).ready(function() {
        cargarMapa();
    });

    // Variables y Objetos globales.
    var mapa;
    var feature;

    var layerPoligonos;

    function stylePolygon(feature) {
      return {
        weight: 1, // grosor de línea
        color: 'black', // color de línea
        opacity: 0.3, // tansparencia de línea
        fillColor: '#9C425C', // color de relleno
        fillOpacity: 0.4 // transparencia de relleno
      };
    };

    function cargarMapa(){
        // Asuncion - Paraguay.
        var longitud = -68.11305255006089;
        var latitud = -16.50350005994209;
        var zoom = 12;

        var osmBase = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap<\/a> contributors'
        })

        // Humanitarian layer.
        var humanitarian_layer = L.tileLayer('http://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
            attribution: 'Data \u00a9 <a href="http://www.openstreetmap.org/copyright">' +
              'OpenStreetMap Contributors </a> Tiles \u00a9 HOT'
        });

        // Mapnik layer.
        var mapnik_layer = L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Data \u00a9 <a href="http://www.openstreetmap.org/copyright">' +
              'OpenStreetMap Contributors </a>'
        });

        var google = L.tileLayer('http://www.google.cn/maps/vt?lyrs=s@189&gl=cn&x={x}&y={y}&z={z}', {
                attribution: 'google'
            });

        // Se instancia el objeto mapa.
        mapa = L.map('map', {
            center: new L.LatLng(latitud, longitud),
            zoom: zoom,
            layers: [osmBase]
        });

        // Hacer llamada ajax.
        $.ajax({
            url: "coordenadas.geojson",
            dataType: 'json',
            type: 'post',
            success: function(datos){
                // Funcion que devuelve el estilo de un poligono.
                function popup_(feature, layer) {
                    if (feature.name) {
                        var info = feature.name;
                        layer.bindPopup(info);
                    }else if(feature){
                        layer.bindPopup(feature);
                    }
                }
                layerPoligonos = L.geoJson(datos, {
                    style: stylePolygon,
                    onEachFeature: popup_
                }).addTo(mapa);

                var baseMaps = {
                  "OSM": osmBase,
                  "Mapnik": mapnik_layer,
                  "Humanitarian": humanitarian_layer,
                  "google": google
                };

                var overlayMaps = {
                  "Polígono": layerPoligonos
                };

                L.control.layers(baseMaps, overlayMaps,{
                  position: 'topright', // 'topleft', 'bottomleft', 'bottomright'
                  collapsed: false // true
                }).addTo(mapa);
            },
            error: function(msgj) {
                console.log("Error!!!");
            }
        });

        /**
         * Insertar Buscador al Mapa
         */
        mapa.addControl( new L.Control.Search({
            url: 'https://nominatim.openstreetmap.org/search?format=json&q={s}',
            jsonpParam: 'json_callback',
            propertyName: 'display_name',
            propertyLoc: ['lat','lon'],
            marker: L.circleMarker([0,0],{radius:30}),
            autoCollapse: true,
            autoType: false,
            minLength: 2
        }) );

        /*var popup = L.popup();

        function onMapClick(e) {
            popup
                .setLatLng(e.latlng)
                .setContent("You clicked the map at " + e.latlng.toString())
                .openOn(mapa);
        }

        mapa.on('click', onMapClick);*/

        mapa.on("click", function(e){
            new L.Marker([e.latlng.lat, e.latlng.lng]).addTo(mapa);
        })


    }

</script>
<style type="text/css" media="screen">
   body {
        margin: 0;
        padding: 0;
    }
    #map {
        position: absolute;
        top: 0;
        bottom: 0;
        width: 100%;
    }
</style>
