var map = L.map('map').setView([-16.49535, -68.13858], 12); // Málaga

var osmBase = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
  attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap<\/a> contributors'
}).addTo(map);

/// ---- Línea  ----
var geojsonFeatureLine = [
  {
    "type": "Feature",
    "properties": {},
    "geometry": {
      "type": "LineString",
      "coordinates": [
            [-68.16776, -16.49436],
            [-68.16055, -16.47296],
            [-68.1403, -16.47593],
            [-68.12296, -16.48778]
      ]
    }
  }
];

function styleLine(feature) {
  return {
    weight: 3.3,
    color: 'orange',
    opacity: 1.0,
    dashArray: '5, 5, 1, 5'
  };
};

var line = new L.geoJson(geojsonFeatureLine, {
  style: styleLine
}).addTo(map);

/// ---- Polígono  ----

var geojsonFeaturePolygon = [
  {
    "type": "Feature",
    "properties": {},
    "geometry": {
      "type": "Polygon",
      "coordinates": [
        [

            [-68.14443666261354,-16.50327552198497],
            [-68.14443956815121,-16.50326996667438],
            [-68.14471434121944,-16.50274466364949],
            [-68.14482430591013,-16.5025169214716],
            [-68.14490658281963,-16.50234652052072],
            [-68.14499925301237,-16.50215459556253],
            [-68.14510076668964,-16.50194435365463],
            [-68.14520275790119,-16.50200180401356],
            [-68.14554174932643,-16.50219152648218],
            [-68.14589710967552,-16.50239195255605],
            [-68.14619642081126,-16.50254692816046],
            [-68.14644890045679,-16.50269266759285],
            [-68.14671071479509,-16.50284748849246],
            [-68.1469725692078,-16.50299326558566],
            [-68.14730419223679,-16.50319240387028],
            [-68.14717601462957,-16.50363201381218],
            [-68.14702506774159,-16.50384846199488],
            [-68.14678967030777,-16.50408265088465],
            [-68.14655415327969,-16.50434397357385],
            [-68.14644074474643,-16.50455152906067],
            [-68.14589848169078,-16.50421467633372],
            [-68.14528122472261,-16.50387751565524],
            [-68.14482286112343,-16.5036495343621],
            [-68.1443106841504,-16.50351316038462],
            [-68.14443666261354,-16.50327552198497]

        ]
      ]
    }
  }
];

function stylePolygon(feature) {
  return {
    weight: 1.3, // grosor de línea
    color: 'black', // color de línea
    opacity: 1.0, // tansparencia de línea
    fillColor: 'red', // color de relleno
    fillOpacity: 0.3 // transparencia de relleno
  };
};

var polygon = new L.geoJson(geojsonFeaturePolygon, {
  style: stylePolygon
}).addTo(map);

polygon.bindPopup("I am a polygon.");

var baseMaps = {
  "OSM": osmBase
};

var overlayMaps = {
  "Línea": line,
  "Polígono": polygon
};

L.control.layers(baseMaps, overlayMaps,{
  position: 'topright', // 'topleft', 'bottomleft', 'bottomright'
  collapsed: false // true
}).addTo(map);


var popup = L.popup();

function onMapClick(e) {
    popup
        .setLatLng(e.latlng)
        .setContent("You clicked the map at " + e.latlng.toString())
        .openOn(map);
}

map.on('click', onMapClick);

var marker = L.marker([-16.48893, -68.1439]).addTo(map);
marker.bindPopup("<b>Hello world!</b><br>I am a popup.").openPopup();

