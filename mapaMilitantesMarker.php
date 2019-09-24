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
    <link rel="stylesheet" type="text/css" href="css/leaflet-search.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/lightbox.min.css">
    <link rel="stylesheet" type="text/css" href="css/myStyle.css">

    <!-- Make sure you put this AFTER Leaflet's CSS -->
    <script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js" integrity="sha512-GffPMF3RvMeYyc1LWMHtK8EbPv0iNZ8/oTtHPx9/cc2ILxQ+u905qIwdpULaqDkyBKgOaB57QTMg7ztg8Jm2Og=="
crossorigin=""></script>
    <script src="src/leaflet-panel-layers.js" type="text/javascript" ></script>
    <script src="https://code.jquery.com/jquery-1.12.4.min.js" type="text/javascript" ></script>
    <script src="js/leaflet-search.js" type="text/javascript"></script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js " type="text/javascript"></script>

    <!-- Plotly.js -->
    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>
    <!-- Numeric JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/numeric/1.2.6/numeric.min.js"></script>
    <!-- lightbox -->
    <script type="text/javascript" src="js/lightbox.js"></script>

    <style>


    </style>
</head>
<body>

    <div class="row">
        <div class="col-md-12 left">
            <button type="button" id="myButton" class="btn btn-primary" data-toggle="button" aria-pressed="false" autocomplete="off">
                Datos Demograficos Generales
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12" id = "map"></div>
    </div>

</body>
</html>
<script>

    $(document).ready(function() {
        //cargarMapa();
    });

    // Variables y Objetos globales.
    var mapa;
    var feature;
    var poligono;
    var resintos;
    var res2005;
    var layerPoligonos;

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

    var elecPor = new Array();
    var elecNum = new Array();

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
 * datos demograficos
 */

$('#myButton').on('click', function () {
    general();
})
function general(){
    $('#modalGeneral').modal({
        keyboard: true
    });
}

function circuns(num){
    $('#modal'+num).modal({
        keyboard: true
    });
}

/**
 * CIRCUNSCRIPCIONES
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
                var cir = feature.id;
                layer.bindPopup(info).on('click',
                    function(){
                        if (cir == 6) {
                            circuns(6);
                        }if (cir == 7) {
                            circuns(7);
                        }if (cir == 8) {
                            circuns(8);
                        }if (cir == 9) {
                            circuns(9);
                        }
                    }
                );
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
/**
 * ZONAS
 */
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
        layerPoligonos = L.geoJson(datos, {
            style: stylePolygon,
            onEachFeature: popup_,
            transparent: true
        });//.addTo(mapa);

    },
    error: function(msgj) {
        console.log("Error!!!");
    }
});

/**
 * puntos
 */

/*$.getJSON("server/consultaResintos.php?id=1", function(p_data_eventos){

    resintos = L.geoJson(p_data_eventos, {

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
            var idElec = feature.properties.idElec;

            layers.bindPopup(rec).on('click',
                function() {
                   onClick2005(elec, cir, zon, rec, por, id, idElec);
                });

        }

    }).addTo(mapa);
});*/

$.ajax({
    url: "server/consultaResintos.php?id=3",
    dataType: 'json',
    async: false,
    type: 'post',
    success: function(datos){
        resintos2009 = L.geoJson(datos, {
            pointToLayer: function(feature, latlng){
                return L.circleMarker(latlng, style(feature));
            },
            onEachFeature: function (feature, layers) {
                var elec   = feature.properties.elec;
                var cir    = feature.properties.circunscripcion;
                var zon    = feature.properties.zona;
                var rec    = feature.properties.recinto;
                var por    = feature.properties.porcentaje;
                var id     = feature.properties.id;
                var idElec = feature.properties.idElec;

                layers.bindPopup(rec).on('click',
                    function() {
                       onClick2009(elec, cir, zon, rec, por, id, idElec);
                    });
            }
        });//.addTo(mapa);
    },
    error: function(msgj) {
        console.log("Error!!!");
    }
});

$.ajax({
    url: "server/consultaResintos.php?id=2",
    dataType: 'json',
    async: false,
    type: 'post',
    success: function(datos){
        resintos2005 = L.geoJson(datos, {
            pointToLayer: function(feature, latlng){
                return L.circleMarker(latlng, style(feature));
            },
            onEachFeature: function (feature, layers) {
                var elec   = feature.properties.elec;
                var cir    = feature.properties.circunscripcion;
                var zon    = feature.properties.zona;
                var rec    = feature.properties.recinto;
                var por    = feature.properties.porcentaje;
                var id     = feature.properties.id;
                var idElec = feature.properties.idElec;

                layers.bindPopup(rec).on('click',
                    function() {
                       onClick2005(elec, cir, zon, rec, por, id, idElec);
                    });
            }
        });//.addTo(mapa);
    },
    error: function(msgj) {
        console.log("Error!!!");
    }
});

$.ajax({
    url: "server/consultaResintos.php?id=1",
    dataType: 'json',
    async: false,
    type: 'post',
    success: function(datos){
        resintos2014 = L.geoJson(datos, {
            pointToLayer: function(feature, latlng){
                return L.circleMarker(latlng, style(feature));
            },
            onEachFeature: function (feature, layers) {
                var elec   = feature.properties.elec;
                var cir    = feature.properties.circunscripcion;
                var zon    = feature.properties.zona;
                var rec    = feature.properties.recinto;
                var por    = feature.properties.porcentaje;
                var id     = feature.properties.id;
                var idElec = feature.properties.idElec;

                layers.bindPopup(rec).on('click',
                    function() {
                       onClick2014(elec, cir, zon, rec, por, id, idElec);
                    });
            }
        });//.addTo(mapa);
    },
    error: function(msgj) {
        console.log("Error!!!");
    }
});

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
        group: "Mostrar",
        collapsed: false,
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
        collapsed: false,
        layers:[
            {
                active: false,
                name: "Resintos 2005",
                layer: resintos2005
            },
            {
                active: false,
                name: "Resintos 2009",
                layer: resintos2009
            },
            {
                active: false,
                name: "Resintos 2014",
                layer: resintos2014
            }
        ]
    }
];

/**
 * [overlayers input's]
 * @type {CHECKBOX}
 */
var overlayers = [];

/*var baseLayers3 = [
    {
        group: "Datos Demograficos",
        collapsed: false,
        layers:[
            {
                active: false,
                name: "Datos Generales",
                layer: general
            }
        ]
    }
];*/


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
    compact: false,
    //collapsed: true,
    collapsibleGroups: true
});
mapa.addControl(panelLayers1);

var panelLayers2 = new L.Control.PanelLayers(baseLayers2, overlayers, {
    compact: false,
    //collapsed: true,
    collapsibleGroups: true
});
mapa.addControl(panelLayers2);

/*var panelLayers3 = new L.Control.PanelLayers(baseLayers3, overlayers, {
    compact: false,
    //collapsed: true,
    collapsibleGroups: true
});
mapa.addControl(panelLayers3);*/

//function cargarMapa(){
    // Hacer llamada ajax.

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


function onClick2005(elec, cir, zon, rec, por, id, idElec) {
    $('#myModal2005').on('show.bs.modal', function() {
        $tabla = $('#datosElec2005').dataTable({
            "aProcessing": true,
            "aServerSide": true,
            "paging":   false,
            "ordering": false,
            "info":     false,
            "searching": false,
            "scrollCollapse": true,
            "paging":         false,
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
                {   "mDataProp": "9"},
                {   "mDataProp": "10"},
                {   "mDataProp": "11"},
                {   "mDataProp": "12"}
            ],"ajax":
                {
                   url: 'server/consultaDatos2005.php',
                    type : "POST",
                    async: false,
                    dataType : "json",
                    data: { id: id, idElec: idElec },
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
                        //alert(graf[c]);
                        c++;

                    $(column.footer()).html(sum);
                });
                $('#datosElec2005 tfoot tr th:first').html('TOTAL');
            },
            "bDestroy": true

        }).DataTable();
        $('.c').css('width', '45px');
        $('.d').css('width', '50px');
    });

    $('#myModal2005').modal({
        keyboard: true
    });

    $('#myModal2005').find('.modal-title').html(elec);
    $('#myModal2005').find('#cir').html(cir);
    $('#myModal2005').find('#zon').html(zon);
    $('#myModal2005').find('#rec').html(rec);
    $('#myModal2005').find('#por').html(por+'%');

    var ultimateColors = [
        [
            'rgb(0, 38, 255)',
            'rgb(255, 0, 0)',
            'rgb(255, 255, 255)',
            'rgb(128, 0, 255)',
            'rgb(255, 215, 0)',
            'rgb(128, 0, 128)',
            'rgb(255, 192, 203)',
            'rgb(165, 42, 42)'
        ]
    ];

    var data = [{
        values: [
            ((graf[1]*100)/graf[9]),
            ((graf[2]*100)/graf[9]),
            ((graf[3]*100)/graf[9]),
            ((graf[4]*100)/graf[9]),
            ((graf[5]*100)/graf[9]),
            ((graf[6]*100)/graf[9]),
            ((graf[7]*100)/graf[9]),
            ((graf[8]*100)/graf[9])
        ],
        labels: [
            'MAS-IPSP',
            'PODEMOS',
            'FREPAB',
            'NFR',
            'UN',
            'USTB',
            'MNR',
            'MIP'
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
        title: 'Grafica',
        height: 400,
        width:  570
    };
    Plotly.newPlot('grafico2005', data, layout, {showSendToCloud:false});

    $.ajax({
        url: 'server/comparacion.php',
        type: "post",
        dataType: "json",
        data: {id: id},
    })
    .done(function(data) {

        for (var i = 0 ; i < data.num; i++) {
            elecPor[i] = data.por[i];
            if (data.elec[i] == 2) {
                elecNum[i] = '2005';
            }
            if (data.elec[i] == 3) {
                elecNum[i] = '2009';
            }
            if (data.elec[i] == 1) {
                elecNum[i] = '2014';
            }

        }

        var colors = [
        [
            'rgb(0, 38, 255)',
            'rgb(116, 191, 4)',
            'rgb(242, 116, 5)'
        ]
    ];

        var trace1 = {
            type: 'bar',
            x: elecNum,
            y: elecPor,
            marker: {
                color: colors[0],
                line: {
                    width: 1.5
                }
            }
        };

        var data = [ trace1 ];

        var layout1 = {
          title: 'Comparación (%)',
          font: {size: 18}
        };

        Plotly.newPlot('com2005', data, layout1, {responsive: true});

    })

}

function onClick2009(elec, cir, zon, rec, por, id, idElec) {
    $('#myModal2009').on('show.bs.modal', function() {
        $tabla = $('#datosElec2009').dataTable({
            "aProcessing": true,
            "aServerSide": true,
            "paging":   false,
            "ordering": false,
            "info":     false,
            "searching": false,
            "scrollCollapse": true,
            "paging":         false,
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
                {   "mDataProp": "9"},
                {   "mDataProp": "10"},
                {   "mDataProp": "11"},
                {   "mDataProp": "12"}
            ],"ajax":
                {
                   url: 'server/consultaDatos2009.php',
                    type : "POST",
                    async: false,
                    dataType : "json",
                    data: { id: id, idElec: idElec },
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
                $('#datosElec2009 tfoot tr th:first').html('TOTAL');
            },
            "bDestroy": true

        }).DataTable();
        $('.c').css('width', '45px');
        $('.d').css('width', '50px');
    });

    $('#myModal2009').modal({
        keyboard: true
    });

    $('#myModal2009').find('.modal-title').html(elec);
    $('#myModal2009').find('#cir').html(cir);
    $('#myModal2009').find('#zon').html(zon);
    $('#myModal2009').find('#rec').html(rec);
    $('#myModal2009').find('#por').html(por+'%');

    var ultimateColors = [
        [
            'rgb(128, 128, 128)',
            'rgb(0, 38, 255)',
            'rgb(255, 165, 0)',
            'rgb(139, 0, 0)',
            'rgb(255, 215, 0)',
            'rgb(255, 0, 0)',
            'rgb(255, 192, 203)',
            'rgb(0, 128, 0)'
        ]
    ];

    var data = [{
        values: [
            ((graf[1]*100)/graf[9]),
            ((graf[2]*100)/graf[9]),
            ((graf[3]*100)/graf[9]),
            ((graf[4]*100)/graf[9]),
            ((graf[5]*100)/graf[9]),
            ((graf[6]*100)/graf[9]),
            ((graf[7]*100)/graf[9]),
            ((graf[8]*100)/graf[9])
        ],
        labels: [
            'BSD',
            'MAS-IPSP',
            'MUSPA',
            'PULSO',
            'UN',
            'PPB-CN',
            'GENTE',
            'AS'
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
        title: 'Grafica',
        height: 400,
        width:  570
    };
    Plotly.newPlot('grafico2009', data, layout, {showSendToCloud:false});

    $.ajax({
        url: 'server/comparacion.php',
        type: "post",
        dataType: "json",
        data: {id: id},
    })
    .done(function(data) {

        for (var i = 0 ; i < data.num; i++) {
            elecPor[i] = data.por[i];
            if (data.elec[i] == 2) {
                elecNum[i] = '2005';
            }
            if (data.elec[i] == 3) {
                elecNum[i] = '2009';
            }
            if (data.elec[i] == 1) {
                elecNum[i] = '2014';
            }

        }

        var colors = [
        [
            'rgb(0, 38, 255)',
            'rgb(116, 191, 4)',
            'rgb(242, 116, 5)'
        ]
    ];

        var trace1 = {
            type: 'bar',
            x: elecNum,
            y: elecPor,
            marker: {
                color: colors[0],
                line: {
                    width: 1.5
                }
            }
        };

        var data = [ trace1 ];

        var layout1 = {
          title: 'Comparación (%)',
          font: {size: 18}
        };

        Plotly.newPlot('com2009', data, layout1, {responsive: true});
    })
}

function onClick2014(elec, cir, zon, rec, por, id, idElec) {
    $('#myModal2014').on('show.bs.modal', function() {
        $tabla = $('#datosElec2014').dataTable({
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
                   url: 'server/consultaDatos2014.php',
                    type : "POST",
                    async: false,
                    dataType : "json",
                    data: { id: id, idElec: idElec },
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
                $('#datosElec2014 tfoot tr th:first').html('TOTAL');
            },
            "bDestroy": true

        }).DataTable();
        $('.c').css('width', '45px');
        $('.d').css('width', '50px');
    });

    $('#myModal2014').modal({
        keyboard: true
    });

    $('#myModal2014').find('.modal-title').html(elec);
    $('#myModal2014').find('#cir').html(cir);
    $('#myModal2014').find('#zon').html(zon);
    $('#myModal2014').find('#rec').html(rec);
    $('#myModal2014').find('#por').html(por+'%');

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
        title: 'Grafica',
        height: 400,
        width:  570
    };

    Plotly.newPlot('grafico2014', data, layout, {showSendToCloud:false});

    $.ajax({
        url: 'server/comparacion.php',
        type: "post",
        dataType: "json",
        data: {id: id},
    })
    .done(function(data) {

        for (var i = 0 ; i < data.num; i++) {
            elecPor[i] = data.por[i];
            if (data.elec[i] == 2) {
                elecNum[i] = '2005';
            }
            if (data.elec[i] == 3) {
                elecNum[i] = '2009';
            }
            if (data.elec[i] == 1) {
                elecNum[i] = '2014';
            }

        }

        var colors = [
        [
            'rgb(0, 38, 255)',
            'rgb(116, 191, 4)',
            'rgb(242, 116, 5)'
        ]
    ];

        var trace1 = {
            type: 'bar',
            x: elecNum,
            y: elecPor,
            marker: {
                color: colors[0],
                line: {
                    width: 1.5
                }
            }
        };

        var data = [ trace1 ];

        var layout1 = {
          title: 'Comparación (%)',
          font: {size: 18}
        };

        Plotly.newPlot('com2014', data, layout1, {responsive: true});
    })
}

</script>

<!-- Modal -->
<div class="modal fade" id="myModal2005" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Modal title</h4>
            </div>
            <div class="modal-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#vot2005" aria-controls="vot2005" role="tab" data-toggle="tab">Votación</a></li>
                    <li role="presentation"><a href="#com2005" aria-controls="com2005" role="tab" data-toggle="tab">Comparar</a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="vot2005">
                        <br>
                        <p><strong>CIRCUNSCRIPCION: </strong><span id="cir"></span></p>
                        <p><strong>ZONA: </strong><span id="zon"></span></p>
                        <p><strong>RECINTO: </strong><span id="rec"></span></p>
                        <p><strong>PORCENTAJE MAS-IPSP: </strong><span id="por"></span></p>

                        <table id="datosElec2005" class="table table-striped table-bordered" cellpadding="0" cellspacing="0" width="100%" >
                            <thead>
                                <tr>
                                    <th id="m">MESA</th>
                                    <th class="c">MAS-IPSP</th>
                                    <th>PODEMOS</th>
                                    <th>FREPAB</th>
                                    <th>NFR</th>
                                    <th>UN</th>
                                    <th>USTB</th>
                                    <th>MNR</th>
                                    <th>MIP</th>
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
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>

                        <div id="grafico2005" align="center"></div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="com2005" align="center">

                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal2009" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Modal title</h4>
            </div>
            <div class="modal-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#vot2009" aria-controls="vot2009" role="tab" data-toggle="tab">Votación</a></li>
                    <li role="presentation"><a href="#com2009" aria-controls="com2009" role="tab" data-toggle="tab">Comparar</a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="vot2009">
                        <br>
                        <p><strong>CIRCUNSCRIPCION: </strong><span id="cir"></span></p>
                        <p><strong>ZONA: </strong><span id="zon"></span></p>
                        <p><strong>RECINTO: </strong><span id="rec"></span></p>
                        <p><strong>PORCENTAJE MAS-IPSP: </strong><span id="por"></span></p>


                        <table id="datosElec2009" class="table table-striped table-bordered" cellpadding="0" cellspacing="0" width="100%" >
                            <thead>
                                <tr>
                                    <th id="m">MESA</th>
                                    <th class="c">BSD</th>
                                    <th>MAS-IPSP</th>
                                    <th>MUSPA</th>
                                    <th>PULSO</th>
                                    <th>UN</th>
                                    <th>PPB-CN</th>
                                    <th>GENTE</th>
                                    <th>AS</th>
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
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>

                        <div id="grafico2009" align="center"></div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="com2009" align="center"></div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal2014" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Modal title</h4>
            </div>
            <div class="modal-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#vot2014" aria-controls="vot2014" role="tab" data-toggle="tab">Votación</a></li>
                    <li role="presentation"><a href="#com2014" aria-controls="com2014" role="tab" data-toggle="tab">Comparar</a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="vot2014">
                        <br>
                        <p><strong>CIRCUNSCRIPCION: </strong><span id="cir"></span></p>
                        <p><strong>ZONA: </strong><span id="zon"></span></p>
                        <p><strong>RECINTO: </strong><span id="rec"></span></p>
                        <p><strong>PORCENTAJE MAS-IPSP: </strong><span id="por"></span></p>

                        <table id="datosElec2014" class="table table-striped table-bordered" cellpadding="0" cellspacing="0" width="100%" >
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

                        <div id="grafico2014" align="center"></div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="com2014" align="center"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Datos Demograficos Circunscripciones -->
<?php
    require_once 'modal/circunscripcion.php';
?>

