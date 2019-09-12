<?PHP
    ini_set('max_execution_time', 2000);
    include 'conexion.php';
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Mapa Detalle Votos</title>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
    crossorigin=""/>
        <link rel="stylesheet" type="text/css" href="css/font-awesome/css/all.css">
        <link rel="stylesheet" type="text/css" href="src/leaflet-panel-layers.css">
        <link rel="stylesheet" type="text/css" href="css/myStyle.css">
        <link rel="stylesheet" type="text/css" href="css/leaflet-search.css">
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css">

        <!-- Make sure you put this AFTER Leaflet's CSS -->
        <script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js" integrity="sha512-GffPMF3RvMeYyc1LWMHtK8EbPv0iNZ8/oTtHPx9/cc2ILxQ+u905qIwdpULaqDkyBKgOaB57QTMg7ztg8Jm2Og=="
    crossorigin=""></script>
        <script src="src/leaflet-panel-layers.js" type="text/javascript" ></script>
        <script src="https://code.jquery.com/jquery-1.12.4.min.js"
     type="text/javascript" ></script>
        <script src="js/leaflet-search.js" type="text/javascript"></script>

        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js" type="text/javascript"></script>
        <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js " type="text/javascript"></script>

        <!-- Plotly.js -->
        <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
        <!-- Numeric JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/numeric/1.2.6/numeric.min.js"></script>

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
            .modal-md {
                /*width: 700px;*/
            }
            #myModal.modal{
                left: auto;
            }
            #myModal .modal-dialog{
                margin: 0 17px 0 0;
            }
            canvas {
                -moz-user-select: none;
                -webkit-user-select: none;
                -ms-user-select: none;
            }
            table#datosElec{
                font-size: 12px;
            }
            .table > tbody > tr > td, .table > tbody > tr > th, .table > tfoot > tr > td, .table > tfoot > tr > th, .table > thead > tr > td, .table > thead > tr > th {

                padding: 5px;
                line-height: 1.42857143;
                vertical-align: top;
                border-top: 1px solid #ddd;

            }

            input[type="checkbox"], input[type="radio"] {
                margin: 1px 3px 0 0;
            }

            .leaflet-panel-layers.expanded.leaflet-control.leaflet-control-layers-expanded{
                margin: 3px 10px 3px 0;
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
    var poligono;
    var resintos2005;

    var osmBase;
    var humanitarian_layer;
    var mapnik_layer;
    var google;
    var osmLayer;
    var overlayers;

    var markerK = new Array();
    var markerBackup = new Array();
    var markerAux = new Array();

    var graf = new Array();

    function stylePolygon(feature) {
      return {
        weight: 1, // grosor de línea
        color: 'black', // color de línea
        opacity: 0.3, // tansparencia de línea
        fillColor: '#9C425C', // color de relleno
        fillOpacity: 0.4, // transparencia de relleno
        zindex: 0
      };
    };

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

/**
 * DISTRITOS
 */

var layerDistrito;

$.ajax({
    url: "distrito.geojson",
    dataType: 'json',
    async: false,
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
        layerDistrito = L.geoJson(datos, {
            color: 'blue',
            weight: 1, // grosor de línea
            fillColor: '#9C425C', // color de relleno
            onEachFeature: popup_
        });
    }
});

function cargarMapa(){
    // Hacer llamada ajax.
    $.ajax({
        url: "coordenadas.geojson",
        dataType: 'json',
        async: false,
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
            var layerPoligonos = L.geoJson(datos, {
                style: stylePolygon,
                onEachFeature: popup_,
                transparent: true
            }).addTo(mapa);

            $.getJSON("server/consultaResintos.php", function(p_data_eventos){
                var resintos = L.geoJson(p_data_eventos, {

                    pointToLayer: function(feature, latlng){
                        return L.circleMarker(latlng, style(feature));
                    },
                    onEachFeature: function (feature, layers) {
                        var elec = feature.properties.elec;
                        var cir = feature.properties.circunscripcion;
                        var zon = feature.properties.zona;
                        var rec = feature.properties.recinto;
                        var por = feature.properties.porcentaje;
                        var id = feature.properties.id;

                        layers.bindPopup(rec).on('click',
                            function() {
                               onClick(elec, cir, zon, rec, por, id);
                            });

                    }
                });//.addTo(mapa);

                /**
                 * GESTION DE COLORES
                 */

                function getColor(d) {
                return d > 79.9   ? '#1F90FF' :
                       d > 59.9   ? '#1CE867' :
                       d > 39.9   ? '#FBFF2C' :
                       d > 19.9   ? '#E8941C' :
                                    '#FF2B31';
                }

                function style(feature) {
                    return {
                        fillColor: getColor(feature.properties.porcentaje),
                        weight: 1,
                        opacity: 1,
                        color: 'white',
                        dashArray: '2',
                        fillOpacity: 0.8
                    };
                }

                /**
                 * LEYENDA
                 */

                var legend = L.control({position: 'bottomright'});

                legend.onAdd = function (map) {
                    var div = L.DomUtil.create('div', 'info legend'),
                        grades = [0 , 20 , 40 , 60 , 80 ],
                        labels = ['ROJO','NARANJA','AMARILLO','VERDE','AZUL'];
                    // loop through our density intervals and generate a label with a colored square for each interval
                    for (var i = 0; i < grades.length; i++) {
                        div.innerHTML +=
                            '<i style="background:' + getColor(grades[i] + 1) + '"></i> ' +
                            grades[i] + (grades[i + 1] ? ' % &ndash; ' + grades[i + 1] + ' %<br>' : ' % +');
                    }
                    return div;
                };
                legend.addTo(mapa);

                /**
                 * MENU SUPERIOR
                 */

                /**
                 * [baseLayers input's ]
                 * @type {RADIO}
                 */
                var baseLayers = [
                    {
                        group: "Tipo de Mapa",
                        collapsed: true,
                        layers:[
                            {
                                active: true,
                                name: "Open Street Map",
                                layer: osmBase
                            },
                            {
                                active: false,
                                name: "Humanitarian",
                                layer: humanitarian_layer
                            },
                            {
                                active: false,
                                name: "Mapnik",
                                layer: mapnik_layer
                            },
                            {
                                active: false,
                                name: "GoogleMaps",
                                layer: google
                            }
                        ]
                    }
                ];

                var baseLayers1 = [
                    {
                        group: "Poligonos",
                        layers:[
                            {
                                active: true,
                                name: "Zonas",
                                layer: layerPoligonos
                            },
                            {
                                active: false,
                                name: "Circuncripciones",
                                layer: layerDistrito
                            }
                        ]
                    }
                ];

                var baseLayers2 = [
                    {
                        group: "Datos Elecciones",
                        layers:[
                            {
                                active: true,
                                name: "Resintos 2014",
                                layer: resintos
                            },
                            {
                                active: false,
                                name: "Resintos 2005",
                                layer: (
                                    /**
                                     * Elecciones 2005
                                     */
                                    function(){
                                        var r = L.geoJson(p_data_eventos, {

                                            pointToLayer: function(feature, latlng){
                                                return L.circleMarker(latlng, style(feature));
                                            },
                                            onEachFeature: function (feature, layers) {
                                                var elec = feature.properties.elec;
                                                var cir = feature.properties.circunscripcion;
                                                var zon = feature.properties.zona;
                                                var rec = feature.properties.recinto;
                                                var por = feature.properties.porcentaje;
                                                var id = feature.properties.id;

                                                layers.bindPopup(rec).on('click',
                                                    function() {
                                                       onClick('2005', cir, zon, rec, por, id);
                                                    });
                                            }

                                        });//.addTo(mapa);
                                        $.getJSON("server/consultaResintos.php", function(j){
                                            r.addData(j);
                                        });
                                        return r;
                                    }())
                            }
                        ]
                    }
                ];

                /**
                 * [overlayers input's]
                 * @type {CHECKBOX}
                 */
                var overlayers = [];

                var overlayers1 = [
                    {
                        active: true,
                        name: "Resintos",
                        layer: resintos
                    }
                ];

                var overlayers2 = [
                    {
                        active: true,
                        name: "Resintos",
                        layer: resintos
                    }
                ];


                /**
                 *
                 */

                var baseMaps = {
                    /*"OSM": osmBase,*/
                    "Resintos": resintos,
                    "Zonas": layerPoligonos,
                    "Distritos": layerDistrito


                  /*"Mapnik": mapnik_layer,
                  "Humanitarian": humanitarian_layer,
                  "google": google*/
                };
                overlayMaps = {
                    "Resintos": resintos
                };

                /*L.control.layers(baseMaps, overlayMaps, {
                    position: 'topright', // 'topleft', 'bottomleft', 'bottomright'
                    collapsed: false// true

                }).addTo(mapa);*/

                var panelLayers = new L.Control.PanelLayers(baseLayers, overlayers, {
                    compact: true,
                    //collapsed: true,
                    collapsibleGroups: true
                });
                mapa.addControl(panelLayers);

                var panelLayers1 = new L.Control.PanelLayers(baseLayers1, overlayers, {
                    //compact: true,
                    //collapsed: true,
                    collapsibleGroups: true
                });
                mapa.addControl(panelLayers1);

                var panelLayers2 = new L.Control.PanelLayers(baseLayers2, overlayers, {
                    //compact: true,
                    //collapsed: true,
                    collapsibleGroups: true
                });
                mapa.addControl(panelLayers2);

            });

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
}

function onClick(elec, cir, zon, rec, por, id) {
    $('#myModal').on('show.bs.modal', function() {
        $tabla = $('#datosElec').dataTable({
            "aProcessing": true,
            "aServerSide": true,
            "paging":   false,
            "ordering": false,
            "info":     false,
            "searching": false,

            scrollCollapse: true,
            paging:         false,
            "aoColumns":[
                    {   "mDataProp": "0"},
                    {   "mDataProp": "1"},
                    {   "mDataProp": "2"},
                    {   "mDataProp": "3"},
                    {   "mDataProp": "4"},
                    {   "mDataProp": "5"},
                    {   "mDataProp": "6"},
                    {   "mDataProp": "7"},
                    {   "mDataProp": "8"},
                    {   "mDataProp": "9"}

            ],"ajax":
                {
                   url: 'server/consultaDatos.php',
                    type : "POST",
                    async: false,
                    dataType : "json",
                    data: { id: id },
                    error: function(data){
                        console.log(data.responseText);
                    }
                },
            'initComplete': function (settings, json){
                var c = 0;
                this.api().columns().every(function(){
                    var column = this;

                    var sum = column
                        .data()
                        .reduce(function (a, b) {
                           a = parseInt(a, 10);
                           if(isNaN(a)){ a = 0; }

                           b = parseInt(b, 10);
                           if(isNaN(b)){ b = 0; }

                           return a + b;
                        });

                        graf[c] = sum;
                        c++;

                    $(column.footer()).html(sum);
                });
                $('tfoot tr th:first').html('TOTAL');
            },
            "bDestroy": true

        }).DataTable();
        $('.c').css('width', '45px');
        $('.d').css('width', '50px');
    });

    $('#myModal').modal({
        keyboard: true
    });

    $('#myModal').find('.modal-title').html(elec);
    $('#myModal').find('#cir').html(cir);
    $('#myModal').find('#zon').html(zon);
    $('#myModal').find('#rec').html(rec);
    $('#myModal').find('#por').html(por+'%');

    var ultimateColors = [
        [
            'rgb(0, 38, 255)',
            'rgb(253, 202, 56)',
            'rgb(252, 0, 0)',
            'rgb(132, 254, 2)',
            'rgb(55, 93, 50)'
        ]
    ];

    var data = [{
        values: [
            ((graf[3]*100)/graf[6]),
            ((graf[5]*100)/graf[6]),
            ((graf[4]*100)/graf[6]),
            ((graf[2]*100)/graf[6]),
            ((graf[1]*100)/graf[6])
        ],
        labels: [
            'MAS-IPSP',
            'UD',
            'PDC',
            'MSM',
            'PVB-IEP'
            ],
      domain: {column: 0},
      name: '',
      hoverinfo: 'label+percent+name',
      hole: .4,
      type: 'pie',
      marker: {
        colors: ultimateColors[0]
      }
    }];

    var layout = {
        title: 'Grafico',
        height: 400,
        width:  570
    };

    Plotly.newPlot('grafico', data, layout, {showSendToCloud:false});

}
</script>


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Modal title</h4>
            </div>
            <div class="modal-body">
                <p><strong>CIRCUNSCRIPCION: </strong><span id="cir"></span></p>
                <p><strong>ZONA: </strong><span id="zon"></span></p>
                <p><strong>RECINTO: </strong><span id="rec"></span></p>
                <p><strong>PORCENTAJE MAS-IPSP: </strong><span id="por"></span></p>


                <table id="datosElec" class="table table-striped table-bordered" cellpadding="0" cellspacing="0" width="100%" >
                    <thead>
                        <tr>
                            <th id="m">MESA</th>
                            <th class="c">PVB-IEP</th>
                            <th>MSM</th>
                            <th class="d">MAS-IPSP</th>
                            <th>PDC</th>
                            <th>UD</th>
                            <th>VALIDOS</th>
                            <th>BLANCOS</th>
                            <th>NULOS</th>
                            <th>EMITIDOS</th>
                        </tr>
                    </thead>

                    <tfoot>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>

                <div id="grafico">

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

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

    .info {
        padding: 6px 8px;
        font: 14px/16px Arial, Helvetica, sans-serif;
        background: white;
        background: rgba(255,255,255,0.8);
        box-shadow: 0 0 15px rgba(0,0,0,0.2);
        border-radius: 5px;
    }
    .info h4 {
        margin: 0 0 5px;
        color: #777;
    }

    .legend {
        line-height: 18px;
        color: #555;
        background-color: white;
    }
    .legend i {
        width: 18px;
        height: 18px;
        float: left;
        margin-right: 8px;
        opacity: 0.7;
    }

</style>
