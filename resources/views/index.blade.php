<!DOCTYPE html>
<html lang="en">
<head>
    <title>Map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
          integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
          crossorigin=""/>
    <style>
        #mapid {
            height: 100vh;
        }

        .legend {
            line-height: 18px;
            color: #555;
        }

        .legend i {
            width: 18px;
            height: 18px;
            float: left;
            margin-right: 8px;
            opacity: 0.7;
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
    </style>
</head>
<body>
    <div id="mapid"></div>

    <!-- Make sure you put this AFTER Leaflet's CSS -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
        integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
        crossorigin=""></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.21.4/axios.min.js" integrity="sha512-lTLt+W7MrmDfKam+r3D2LURu0F47a3QaW5nF0c6Hl0JDZ57ruei+ovbg7BrZ+0bjVJ5YgzsAWE+RreERbpPE1g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        function getColor(d) {
            return d > 14 * 10 ** 6 ? '#800026' :
                d > 12 * 10 ** 6  ? '#BD0026' :
                    d > 10 * 10 ** 6  ? '#E31A1C' :
                        d > 8 * 10 ** 6  ? '#FC4E2A' :
                            d > 6 * 10 ** 6   ? '#FD8D3C' :
                                d > 4 * 10 ** 6  ? '#FEB24C' :
                                    d > 2 * 10 ** 6   ? '#FED976' :
                                        '#FFEDA0';
        }

        function getIncomeClassColor(ic) {
            return ic === '4th' ? '#cb181d' :
                        ic === '3rd' ? '#fb6a4a  ' :
                            ic === '2nd' ? '#fcae91' :
                                '#fee5d9';
        }

        function style(feature) {
            return {
                fillColor: getColor(feature.properties.population),
                weight: 2,
                opacity: 1,
                color: 'white',
                dashArray: '3',
                fillOpacity: 0.7
            };
        }

        function styleIc(feature) {
            return {
                fillColor: getIncomeClassColor(feature.properties.income_class),
                weight: 1,
                opacity: 1,
                color: '#000',
                dashArray: '0',
                fillOpacity: 1
            };
        }

        function highlightFeature(e) {
            var layer = e.target;

            layer.setStyle({
                weight: 5,
                color: '#666',
                dashArray: '',
                fillOpacity: 1
            });

            info.update(layer.feature.properties);

            if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                layer.bringToFront();
            }
        }
    </script>

    <script>
        const mymap = L.map('mapid').setView([11.505, 121.09], 6);

        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
            maxZoom: 18,
            id: 'mapbox/streets-v11',
            tileSize: 512,
            zoomOffset: -1,
            accessToken: 'pk.eyJ1IjoibWxhYjgxNyIsImEiOiJja3Q1Z2dncjIwOGVnMnBxbmE2Y2IweW40In0.4oLFQhXLJWcrnJFLWwoGyw',
            renderer: L.svg()
        }).addTo(mymap);

        // L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        //     attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        //     maxZoom: 18,
        //     id: 'mapbox/streets-v11',
        //     tileSize: 512,
        //     zoomOffset: -1,
        //     accessToken: 'pk.eyJ1IjoibWxhYjgxNyIsImEiOiJja3Q1Z2dncjIwOGVnMnBxbmE2Y2IweW40In0.4oLFQhXLJWcrnJFLWwoGyw'
        // }).addTo(mymap);

        var geojson = null;

        axios.get('/api/provinces?type=centroid')
            .then(res => {
                // console.log(res.data.data);
                // res.data.data.forEach(region => {
                //     L.geoJSON(region)
                //         .addTo(mymap);
                // });
                // geojson = L.geoJSON(res.data.data, {
                //     style: styleIc,
                //     onEachFeature: onEachFeature
                // }).addTo(mymap)
                res.data.data.forEach(r => {
                    L.circle([r.geometry.coordinates[1],r.geometry.coordinates[0]], { radius: r.properties.population / 100 }).addTo(mymap)
                })
            })
            .catch(err => {
                alert(err.message);
            })

        function resetHighlight(e) {
            geojson.resetStyle(e.target);
            info.update(layer.feature.properties);
        }

        function zoomToFeature(e) {
            mymap.fitBounds(e.target.getBounds());
        }

        function onEachFeature(feature, layer) {
            layer.on({
                mouseover: highlightFeature,
                mouseout: resetHighlight,
                click: zoomToFeature
            });
        }

        var legend = L.control({position: 'bottomright'});

        legend.onAdd = function (map) {

            var div = L.DomUtil.create('div', 'info legend'),
                // grades = [0, 2000000, 4000000, 6000000, 8000000, 10000000, 12000000, 14000000],
                grades = ['4th','3rd','2nd','1st']
                // labels = [0, '2M', '4M', '6M', '8M', '10M', '12M', '14M'];

            // loop through our density intervals and generate a label with a colored square for each interval
            for (var i = 0; i < grades.length; i++) {
                div.innerHTML +=
                    '<i style="background:' + getIncomeClassColor(grades[i]) + '"></i> ' + grades[i] + '<br/>';
            }

            return div;
        };

        legend.addTo(mymap);

        var info = L.control();

        info.onAdd = function (map) {
            this._div = L.DomUtil.create('div', 'info'); // create a div with a class "info"
            this.update();
            return this._div;
        };

        // method that we will use to update the control based on feature properties passed
        info.update = function (props) {
            this._div.innerHTML = '<h4>Income Class</h4>' +  (props ?
                '<b>' + props.name + '</b><br />' + props.income_class + ' class</sup>'
                : 'Hover over a region');
        };

        info.addTo(mymap);
    </script>
</body>
</html>
