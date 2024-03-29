      <!DOCTYPE html>
<html>
  <head>
    <title>Multilayer | CARTO</title>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,600,700|Open+Sans:300,400,600" rel="stylesheet">
    <!-- Include Leaflet -->
    <script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js"></script>
    <link href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css" rel="stylesheet">
    <!-- Include CARTO.js -->
    <script src="https://libs.cartocdn.com/carto.js/v4.1.11/carto.min.js"></script>
    <link href="https://carto.com/developers/carto-js/examples/maps/public/style.css" rel="stylesheet">
  </head>
  <body>
    <div id="map"></div>
    <!-- Description -->
    <aside class="toolbox">
      <div class="box">
        <header>
          <h1>Add more layers</h1>
          <button class="github-logo js-source-link"></button>
        </header>
        <section>
          <p class="description open-sans">Add multiple CARTO layers to your map.</p>
        </section>
        <footer class="js-footer"></footer>
      </div>
    </aside>

    <script>
      const map = L.map('map').setView([-16.48349760264812, -68.13858032226562], 12);
      map.scrollWheelZoom.disable();

      L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager_nolabels/{z}/{x}/{y}.png', {
        maxZoom: 18
      }).addTo(map);

      const client = new carto.Client({
        apiKey: 'default_public',
        username: 'cartojs-test'
      });

      const spainCitiesSource = new carto.source.SQL(`
        SELECT *
          FROM ne_10m_populated_places_simple
          WHERE adm0name = \'Spain\'
      `);
      const spainCitiesStyle = new carto.style.CartoCSS(`
        #layer {
          marker-width: 7;
          marker-fill: #EE4D5A;
          marker-line-color: #FFFFFF;
        }
      `);
      const spainCitiesLayer = new carto.layer.Layer(spainCitiesSource, spainCitiesStyle);

      const europeCountriesSource = new carto.source.Dataset('ne_adm0_europe');
      const europeCountriesStyle = new carto.style.CartoCSS(`
        #layer {
          polygon-fill: #826DBA;
          polygon-opacity: 0.8;
          ::outline {
            line-width: 1;
            line-color: #FFFFFF;
            line-opacity: 0.8;
          }
        }
      `);
      const europeCountriesLayer = new carto.layer.Layer(europeCountriesSource, europeCountriesStyle);

      client.addLayers([europeCountriesLayer, spainCitiesLayer]);
      client.getLeafletLayer().addTo(map);
    </script>
  </body>
</html>

