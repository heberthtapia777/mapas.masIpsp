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
        <link rel="stylesheet" type="text/css" href="src/leaflet-panel-layers.css">
        <link rel="stylesheet" type="text/css" href="css/myStyle.css">
        <link rel="stylesheet" type="text/css" href="css/leaflet-search.css">
        <link rel="stylesheet" type="text/css" href="assets/sweetalert2/dist/sweetalert2.min.css">
        <link rel="stylesheet" type="text/css" href="css/bootstrap.css">

        <!-- Make sure you put this AFTER Leaflet's CSS -->
        <script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js" integrity="sha512-GffPMF3RvMeYyc1LWMHtK8EbPv0iNZ8/oTtHPx9/cc2ILxQ+u905qIwdpULaqDkyBKgOaB57QTMg7ztg8Jm2Og=="
    crossorigin=""></script>

        <script src="src/leaflet-panel-layers.js" type="text/javascript" ></script>
        <script src="https://code.jquery.com/jquery-1.12.4.min.js"
     type="text/javascript" ></script>
        <script src="js/leaflet-search.js" type="text/javascript"></script>
        <script src="assets/sweetalert2/dist/sweetalert2.all.min.js" type="text/javascript"></script>
        <script src="js/leaflet_.js" type="text/javascript"></script>
        <script src="js/bootstrap.js" type="text/javascript"></script>

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

    var iconDefault = "images/blue.png";
    var iconActive = "images/red.png";
    var defaultMarker = L.icon({
        iconUrl: iconDefault,
     });
    var activeMarker = L.icon({
        iconUrl: iconActive,
    });

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

function capa(data){

          if (info != undefined) { // se valida si existe informacion en la capa, si es borra la capa
              info.remove(mapa); // esta linea quita la capa flotante
          }

          info = L.control({position: 'bottomleft'});

          info.onAdd = function (mapa) {
              this._div = L.DomUtil.create('div', 'info');
              this.update(data);
              return this._div;
          };

          info.update = function (data) {

        };
          info.addTo(mapa);
      }

      // variable que guarda los detalles del marker al que se le dio click
      var eventBackup;
      // variable auxiliar que guarda el tag img con su src
      var x;
      // funcion que se ejecuta cuando se da click sobre un maker del mapa
function onClick(e) {
    var i = this.options;
        console.log(i);
        c='abre modal.....'+e;
        showModal(c);
      }

function showModal(c){
        //console.log(c);
        Swal.fire({
            position: 'top-end',
            title: '<strong>HTML <u>example</u></strong>',
            type: 'info',
            html: c,
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText:
                '<i class="fa fa-thumbs-up"></i> Great!',
            confirmButtonAriaLabel: 'Thumbs up, great!',
            cancelButtonText:
                '<i class="fa fa-thumbs-down"></i>',
            cancelButtonAriaLabel: 'Thumbs down'
        })
      };

</script>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Modal title</h4>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
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

    /******************/

    .modal.left .modal-dialog {
        position: fixed;
        margin: auto;
        width: 320px;
        height: 100%;
        -webkit-transform: translate3d(0%, 0, 0);
        -ms-transform: translate3d(0%, 0, 0);
        -o-transform: translate3d(0%, 0, 0);
        transform: translate3d(0%, 0, 0);
    }

    .modal.left .modal-content {
        height: 100%;
        overflow-y: auto;
    }

    .modal.left .modal-body {
        padding: 15px 15px 80px;
    }

    .modal.left.fade .modal-dialog {
        left: -320px;
        -webkit-transition: opacity 0.3s linear, left 0.3s ease-out;
        -moz-transition: opacity 0.3s linear, left 0.3s ease-out;
        -o-transition: opacity 0.3s linear, left 0.3s ease-out;
        transition: opacity 0.3s linear, left 0.3s ease-out;
    }

    .modal.left.fade.show .modal-dialog {
        left: 0;
    }

    /* ----- MODAL STYLE ----- */
    .modal-content {
        border-radius: 0;
        border: none;
    }

    .modal-header {
        border-bottom-color: #eeeeee;
        background-color: #fafafa;
    }

    /* ----- v CAN BE DELETED v ----- */
    body {
        background-color: #78909c;
    }

    .demo {
        padding-top: 60px;
        padding-bottom: 110px;
    }

    .btn-demo {
        margin: 15px;
        padding: 10px 15px;
        border-radius: 0;
        font-size: 16px;
        background-color: #ffffff;
    }

    .btn-demo:focus {
        outline: 0;
    }
</style>
