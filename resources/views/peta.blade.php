<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laravel - Leaflet - Lanjut</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        html, body, #mymap {
            height: 100%;
        }
    </style>
</head>
<body>
    <div id="mymap"></div>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://unpkg.com/rbush@2.0.2/rbush.min.js"></script>
    <script src="https://unpkg.com/labelgun@6.1.0/lib/labelgun.min.js"></script>
    <script>
        var hideLabel = function(label) {
            label.labelObject.style.opacity = 0;
            label.labelObject.style.transition = 'opacity 0s';
        };
        
        var showLabel = function(label) {
            label.labelObject.style.opacity = 1;
            label.labelObject.style.transition = 'opacity 1s';
        };
        
        labelEngine = new labelgun.default(hideLabel, showLabel);

        var id = 0;
        var labels = [];
        var totalMarkers = 0;

        function resetLabels(markers) {
            console.log(markers.length);
            labelEngine.reset();
            var i = 0;
            for (var j = 0; j < markers.length; j++) {
                markers[j].eachLayer(function(label){
                    addLabel(label, ++i);
                });
            }
        labelEngine.update();
        }

        function addLabel(layer, id) {

        // This is ugly but there is no getContainer method on the tooltip :(
            if (layer.getTooltip()) {
                var label = layer.getTooltip()._source._tooltip._container;
                if (label) {

                    // We need the bounding rectangle of the label itself
                    var rect = label.getBoundingClientRect();

                    // We convert the container coordinates (screen space) to Lat/lng
                    var bottomLeft = mymap.containerPointToLatLng([rect.left, rect.bottom]);
                    var topRight = mymap.containerPointToLatLng([rect.right, rect.top]);
                    var boundingBox = {
                        bottomLeft : [bottomLeft.lng, bottomLeft.lat],
                        topRight   : [topRight.lng, topRight.lat]
                    };

                    // Ingest the label into labelgun itself
                    labelEngine.ingestLabel(
                    boundingBox,
                    id,
                    parseInt(Math.random() * (5 - 1) + 1), // Weight
                    label,
                    "Test " + id,
                    false
                    );

                    // If the label hasn't been added to the map already
                    // add it and set the added flag to true
                    if (!layer.added) {
                    layer.addTo(mymap);
                    layer.added = true;
                    }
                }
            }
        }
    </script>
    <script>
        let mymap = L.map('mymap').setView([-7.9008559,110.4345703],12);

        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
            maxZoom: 18,
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, ' +
                'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
            id: 'mapbox/streets-v11',
            tileSize: 512,
            zoomOffset: -1
        }).addTo(mymap);

        let geoJsonDesa = {
            "type": "FeatureCollection",
            "name": "batasdesa",
            "crs": {
                "type": "name",
                "properties": {
                    "name": "urn:ogc:def:crs:OGC:1.3:CRS84"
                }
            },
            "features": [
                @foreach($desa as $item)
                    {
                        "type": "Feature",
                        "properties": {
                            "Desa": "{{ $item->nama_desa }}"
                        },
                        "geometry": {
                            "type": "MultiPolygon",
                            "coordinates": [ {!! $item->area !!} ]
                        }
                    },
                @endforeach
            ]
        };

        mymap.createPane("pane_batasdesa");
        mymap.getPane("pane_batasdesa").style.zIndex = 302;

        let batasDesa = L.geoJson(null, {
            pane: "pane_batasdesa",
            style: function(feature) {
                return {
                    fillColor: "red",
                    fillOpacity: 1,
                    color: "#50d900",
                    weight: 2,
                    opacity: 1,
                    interactive: false
                }
            },
            onEachFeature: function (feature, layer){
                let content = 'Desa ' + layer.feature.properties.Desa.toString();
                layer.bindTooltip(content, {
                    direction: 'center',
                    permanent: true
                });
            }
        });

        batasDesa.addData(geoJsonDesa);

        let geoJsonKecamatan = {
            "type": "FeatureCollection",
            "name": "bataskecamatan",
            "crs": {
                "type": "name",
                "properties": {
                    "name": "urn:ogc:def:crs:OGC:1.3:CRS84"
                }
            },
            "features": [
                @foreach($kecamatan as $item)
                    {
                        "type": "Feature",
                        "properties": {
                            "cat": 15,
                            "Kecamatan": "{{ $item->nama_kecamatan }}"
                        },
                        "geometry": {
                            "type": "MultiPolygon",
                            "coordinates": [ {!! $item->area !!} ]
                        }
                    },
                @endforeach
            ]
        };

        mymap.createPane("pane_bataskecamatan");
        mymap.getPane("pane_bataskecamatan").style.zIndex = 302;
        let batasKecamatan = L.geoJson(null, {
            pane: "pane_bataskecamatan",
            style: function(feature){
                return {
                    fillOpacity: 0,
                    color: "yellow",
                    weight: 5,
                    opacity: 1,
                    interactive: false
                };
            },
            onEachFeature: function(feature, layer) {
                let content = "Kec. " + layer.feature.properties.Kecamatan.toString();
                layer.bindTooltip(content, {
                    direction: 'center',
                    permanent: true
                })
            }
        });

        batasKecamatan.addData(geoJsonKecamatan);
        mymap.addLayer(batasKecamatan);

        mymap.on("zoomend", function(){
            if (mymap.getZoom() <= 13) {
                mymap.removeLayer(batasDesa);
                resetLabels([batasKecamatan]);
            } else if (mymap.getZoom() > 13) {
                mymap.addLayer(batasDesa);
                resetLabels([batasDesa, batasKecamatan]);
            }
        });

        L.control.scale({
            maxWidth: 150,
            imperial: false,
        }).addTo(mymap);
  
        var legend_div = new L.Control({position: 'bottomright'});
        legend_div.onAdd = function (map) {
            this._div = L.DomUtil.create('div', 'legend');
            this._div.innerHTML = '<div id="legend-title">Legenda</div><hr><svg width="32" height="16"><line x1="0" y1="11" x2="32" y2="11" style="stroke-width:4;stroke:rgb(255, 255, 0);" /></svg> Batas Kecamatan<br><svg width="32" height="16"><line x1="0" y1="11" x2="32" y2="11" style="stroke-width:1;stroke:rgb(80, 217, 0);" /></svg> Batas Desa';
            return this._div;
        };
        legend_div.addTo(mymap);
    </script>
</body>
</html>