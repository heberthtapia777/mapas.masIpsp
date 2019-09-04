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
        <link rel="stylesheet" type="text/css" href="css/font-awesome/css/all.css">
        <link rel="stylesheet" type="text/css" href="src/leaflet-panel-layers.css">
        <link rel="stylesheet" type="text/css" href="css/myStyle.css">
        <link rel="stylesheet" type="text/css" href="css/leaflet-search.css">
        <link rel="stylesheet" type="text/css" href="assets/sweetalert2/dist/sweetalert2.min.css">
        <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css">


        <!-- Make sure you put this AFTER Leaflet's CSS -->
        <script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js" integrity="sha512-GffPMF3RvMeYyc1LWMHtK8EbPv0iNZ8/oTtHPx9/cc2ILxQ+u905qIwdpULaqDkyBKgOaB57QTMg7ztg8Jm2Og=="
    crossorigin=""></script>
        <script src="src/leaflet-panel-layers.js" type="text/javascript" ></script>
        <script src="js/leaflet_.js" type="text/javascript"></script>
        <script src="https://code.jquery.com/jquery-1.12.4.min.js"
     type="text/javascript" ></script>
        <script src="js/leaflet-search.js" type="text/javascript"></script>
        <script src="assets/sweetalert2/dist/sweetalert2.all.min.js" type="text/javascript"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js" type="text/javascript"></script>
        <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js " type="text/javascript"></script>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>

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
                width: 700px;
            }
            #myModal.modal{
                left: auto;
            }
            #myModal .modal-dialog{
                margin-right: 17px;
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

        </style>
    </head>
<body>
    <div id = "map"></div>
</body>
</html>
<script>

    $(document).ready(function() {
        cargarMapa();
        /*$('#myModal').modal({

        })*/
    });

    // Variables y Objetos globales.
    var mapa;
    var feature;
    var poligono;

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
        fillOpacity: 0.4 // transparencia de relleno
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

 // variable global donde se carga la capa flotante
     var info;
     // variable global donde se guarda la ruta de la imagen
     var img;
     // capa flotante donde se muestra la info al darle click sobre un maker

var randomScalingFactor = function() {
            return Math.round(Math.random() * 100);
        };

window.chartColors = {
    red: 'rgb(255, 99, 132)',
    orange: 'rgb(255, 159, 64)',
    yellow: 'rgb(255, 205, 86)',
    green: 'rgb(75, 192, 192)',
    blue: 'rgb(54, 162, 235)',
    purple: 'rgb(153, 102, 255)',
    grey: 'rgb(201, 203, 207)'
};

var chartColors = window.chartColors;
        var color = Chart.helpers.color;
        var config = {
            data: {
                datasets: [{
                    data: [
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                        randomScalingFactor(),
                    ],
                    backgroundColor: [
                        color(chartColors.red).alpha(0.5).rgbString(),
                        color(chartColors.orange).alpha(0.5).rgbString(),
                        color(chartColors.yellow).alpha(0.5).rgbString(),
                        color(chartColors.green).alpha(0.5).rgbString(),
                        color(chartColors.blue).alpha(0.5).rgbString(),
                    ],
                    label: 'My dataset' // for legend
                }],
                labels: [
                    'Red',
                    'Orange',
                    'Yellow',
                    'Green',
                    'Blue'
                ]
            },
            options: {
                responsive: true,
                legend: {
                    position: 'right',
                },
                title: {
                    display: true,
                    text: 'GRAFICO'
                },
                scale: {
                    ticks: {
                        beginAtZero: true
                    },
                    reverse: false
                },
                animation: {
                    animateRotate: false,
                    animateScale: true
                }
            }
        };

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

                <div id="canvas-holder" style="width:100%" align="center">
                    <canvas id="chart-area"></canvas>
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

