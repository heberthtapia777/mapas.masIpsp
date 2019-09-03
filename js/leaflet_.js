function cargarMapa(){

    /*$.ajax({
        url: 'server/consultaResintos.php',
        type: 'POST',
        dataType: 'json',
        success: function(data) {

            $.each(data.features, function(index, val) {
                 /* iterate through array or object */
                // console.log(val);
                 //$.each(val.properties, function(key, valor) {
                    /* iterate through array or object */
                    //alert(key+'==>'+valor);
                   /* lat = val.geometry['coordinates'][1];
                    lng = val.geometry['coordinates'][0];

                    id = val.properties['idRes'];

                    var LamMarker = L.marker([lat, lng]);
                    markerK.push(LamMarker);
                    markerK[id].on('click', onClick);
                    mapa.addLayer(markerK[id]);
                 //});
            });
            /*row = data.features;
            console.log(row['properties']);
            alert(row[properties].idRes);
        },
        error: function(msgj) {
            console.log("Error!!!");
        }
    });*/


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
                onEachFeature: popup_
            }).addTo(mapa);

            $.getJSON("server/consultaResintos.php", function(p_data_eventos){
                var resintos = L.geoJson(p_data_eventos, {


                    pointToLayer: function(feature, latlng){
                        return L.circleMarker(latlng, style(feature));
                        /*lamMarker = L.marker(latlng,{
                            //icon: defaultMarker,
                            zona : feature.properties.zona,
                            resinto : feature.properties.resinto
                        });

                        return (lamMarker);*/

                        //L.geoJson(statesData, {style: style}).addTo(mapa);
                    },
                    onEachFeature: function (feature, layers) {

                        /*markerK.push(lamMarker);
                        key = feature.properties.id;
                        //alert(key);
                        markerK[key] = on('click', onClick());
                        mapa.addLayer(markerK[key]);*/
                        var elec = feature.properties.elec;
                        var cir = feature.properties.circunscripcion;
                        var zon = feature.properties.zona;
                        var rec = feature.properties.recinto;
                        var por = feature.properties.porcentaje;
                        var id = feature.properties.id;
                        //alert(id);
                        layers.bindPopup(rec).on('click',
                            function() {
                               onClick(elec, cir, zon, rec, por, id);
                            }).addTo(mapa);

                    }
                }).addTo(mapa);

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
                        grades = [00 , 20 , 40 , 60 , 80 ],
                        labels = ['ROJO', 'NARANJA','AMARILLO','VERDE','AZUL'];

                    // loop through our density intervals and generate a label with a colored square for each interval
                    for (var i = 0; i < grades.length; i++) {
                        div.innerHTML +=
                            '<i style="background:' + getColor(grades[i] + 1) + '"></i> ' +
                            grades[i] + (grades[i + 1] ? '&ndash;' + grades[i + 1] + '<br>' : '+');
                    }

                    return div;
                };

                legend.addTo(mapa);

                /**
                 * MENU SUPERIOR
                 */

                var baseMaps = {
                  "OSM": osmBase,
                  "Mapnik": mapnik_layer,
                  "Humanitarian": humanitarian_layer,
                  "google": google
                };
                overlayMaps = {
                    "Polígono": layerPoligonos,
                    "Resintos": resintos
                };

                L.control.layers(baseMaps, overlayMaps, {
                  position: 'topright', // 'topleft', 'bottomleft', 'bottomright'
                  collapsed: true // true
                }).addTo(mapa);

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
function onClick(elec, cir, zon, rec, por, id) {

    $('#myModal').on('show.bs.modal', function() {

        $tabla = $('#datosElec').dataTable(
            {
                "aProcessing": true,
                "aServerSide": true,
                "paging":   false,
                "ordering": false,
                "info":     false,
                "searching": false,
                scrollY:        400,
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
                "bDestroy": true

            }).DataTable();

            $('.table').css('width', '834px');
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

    var chart = $('#chart-area');
    Chart.PolarArea(chart, config);


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


