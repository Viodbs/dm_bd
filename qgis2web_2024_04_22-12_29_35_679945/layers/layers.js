var wms_layers = [];

var format_communes_0 = new ol.format.GeoJSON();
var features_communes_0 = format_communes_0.readFeatures(json_communes_0, 
            {dataProjection: 'EPSG:4326', featureProjection: 'EPSG:3857'});
var jsonSource_communes_0 = new ol.source.Vector({
    attributions: ' ',
});
jsonSource_communes_0.addFeatures(features_communes_0);
var lyr_communes_0 = new ol.layer.Vector({
                declutter: false,
                source:jsonSource_communes_0, 
                style: style_communes_0,
                popuplayertitle: "communes",
                interactive: true,
                title: '<img src="styles/legend/communes_0.png" /> communes'
            });
var format_commune_cessions_susp4_1 = new ol.format.GeoJSON();
var features_commune_cessions_susp4_1 = format_commune_cessions_susp4_1.readFeatures(json_commune_cessions_susp4_1, 
            {dataProjection: 'EPSG:4326', featureProjection: 'EPSG:3857'});
var jsonSource_commune_cessions_susp4_1 = new ol.source.Vector({
    attributions: ' ',
});
jsonSource_commune_cessions_susp4_1.addFeatures(features_commune_cessions_susp4_1);
var lyr_commune_cessions_susp4_1 = new ol.layer.Vector({
                declutter: false,
                source:jsonSource_commune_cessions_susp4_1, 
                style: style_commune_cessions_susp4_1,
                popuplayertitle: "commune_cessions_susp4",
                interactive: true,
                title: '<img src="styles/legend/commune_cessions_susp4_1.png" /> commune_cessions_susp4'
            });
var format_cessions_2 = new ol.format.GeoJSON();
var features_cessions_2 = format_cessions_2.readFeatures(json_cessions_2, 
            {dataProjection: 'EPSG:4326', featureProjection: 'EPSG:3857'});
var jsonSource_cessions_2 = new ol.source.Vector({
    attributions: ' ',
});
jsonSource_cessions_2.addFeatures(features_cessions_2);
var lyr_cessions_2 = new ol.layer.Vector({
                declutter: false,
                source:jsonSource_cessions_2, 
                style: style_cessions_2,
                popuplayertitle: "cessions",
                interactive: true,
                title: '<img src="styles/legend/cessions_2.png" /> cessions'
            });

lyr_communes_0.setVisible(true);lyr_commune_cessions_susp4_1.setVisible(true);lyr_cessions_2.setVisible(true);
var layersList = [lyr_communes_0,lyr_commune_cessions_susp4_1,lyr_cessions_2];
lyr_communes_0.set('fieldAliases', {'fid': 'fid', 'nom_commune': 'nom_commune', 'insee_com': 'insee_com', 'departement': 'departement', 'surface_com': 'surface_com', });
lyr_commune_cessions_susp4_1.set('fieldAliases', {'fid': 'fid', 'nom_commun': 'nom_commun', 'insee_com': 'insee_com', 'departemen': 'departemen', 'surface_co': 'surface_co', });
lyr_cessions_2.set('fieldAliases', {'statut_cession': 'statut_cession', 'id_unique': 'id_unique', });
lyr_communes_0.set('fieldImages', {'fid': '', 'nom_commune': '', 'insee_com': '', 'departement': '', 'surface_com': '', });
lyr_commune_cessions_susp4_1.set('fieldImages', {'fid': '', 'nom_commun': '', 'insee_com': '', 'departemen': '', 'surface_co': '', });
lyr_cessions_2.set('fieldImages', {'statut_cession': 'TextEdit', 'id_unique': 'TextEdit', });
lyr_communes_0.set('fieldLabels', {'fid': 'no label', 'nom_commune': 'no label', 'insee_com': 'no label', 'departement': 'no label', 'surface_com': 'no label', });
lyr_commune_cessions_susp4_1.set('fieldLabels', {'fid': 'no label', 'nom_commun': 'no label', 'insee_com': 'no label', 'departemen': 'no label', 'surface_co': 'no label', });
lyr_cessions_2.set('fieldLabels', {'statut_cession': 'no label', 'id_unique': 'no label', });
lyr_cessions_2.on('precompose', function(evt) {
    evt.context.globalCompositeOperation = 'normal';
});