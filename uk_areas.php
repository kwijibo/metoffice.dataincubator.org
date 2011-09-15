<?php
require 'inc.php';

require_once 'lib/moriarty/simplegraph.class.php';
require_once 'lib/moriarty/constants.inc.php';

$geonamesStore = new Store('http://api.talis.com/stores/geonames');

$graph = new SimpleGraph();
foreach($regions as $key => $label){
    $uri = AREAS.$key;
    $graph->add_resource_triple($uri, RDF_TYPE, MET.'MetOfficeRegion');
    $graph->add_resource_triple($uri, FOAF.'page', "http://www.metoffice.gov.uk/weather/uk/{$key}/{$key}_forecast_weather.html");
    $graph->add_resource_triple($uri, FOAF.'depiction', "http://www.metoffice.gov.uk/lib/images/base_{$key}.gif");
//    $graph->add_resource_triple($uri, WEATHER.'forecastPage', $uri.'/forecast-channel');
    $graph->add_literal_triple($uri, MET.'identifier', $key);
    $graph->add_literal_triple($uri, RDFS_LABEL, $label, 'en-gb');
}

$areasGeonames = array();
$failedSameAs = array();

foreach($areas as $key => $label){
    $uri = AREAS.$key;
    $graph->add_resource_triple($uri, RDF_TYPE, MET.'MetOfficeLocation');
    $graph->add_resource_triple($uri, FOAF.'page', "http://www.metoffice.gov.uk/weather/uk/{$key}_forecast_weather.html");
    $graph->add_resource_triple($uri, WEATHER.'forecastPage', $uri.'/forecast-channel');
    $graph->add_literal_triple($uri, DCT.'identifier', $key);
    $graph->add_literal_triple($uri, RDFS_LABEL, $label, 'en-gb');
    $regionUri = AREAS.substr($key,0,2);
    $graph->add_resource_triple($regionUri, OS.'contains', $uri);
    $graph->add_resource_triple($uri, OS.'within', $regionUri);
    echo "\n Searching for {$label} : {$key}\n";
 
    $response = $geonamesStore->get_contentbox()->search($label, 20);
         if($response->is_success()){
 
            $searchGraph = new SimpleGraph();
            $searchGraph->from_rdfxml($response->body);
            $searchIndex = $searchGraph->get_index();
            foreach($searchIndex as $itemUri => $properties){
                if(isset($properties[GN.'inCountry'])){
                    if("http://www.geonames.org/countries/#GB"==$properties[GN.'inCountry'][0]['value']
                    AND
                    "http://www.geonames.org/ontology#P.PPL" == $properties[GN.'featureCode'][0]['value']
                    ){
                        if(metaphone($searchGraph->get_label($itemUri)) == metaphone($label)
                        OR metaphone($searchGraph->get_first_literal($itemUri, GN.'alternateName')) == metaphone($label)
                        ){ 
                            echo "\t found match $itemUri \n";
                            $areasGeonames[$uri][]=$itemUri;
                        } 
                    }
                }
            } 
    }         
    else {
        echo "\n searched Failed for {$label} . \n";
    }
    
    if(isset($areasGeonames[$uri])){
        if(count($areasGeonames[$uri])==1){
             $graph->add_resource_triple($uri, OWL_SAMEAS, $areasGeonames[$uri][0]);
        } else if(count($areasGeonames[$uri])==0){
            $failedSameAs['noMatch'][][$key]=$label;
        } else {
            $record = array( $key=>$label, 'matches' => $areasGeonames[$uri]);
            $failedSameAs['ambiguous'][] = $record;
        }
    } else {
        $failedSameAs['noMatch'][][$key]=$label;
    }
    
}
$rdf = $graph->to_turtle();
file_put_contents('uk_areas.ttl', $rdf);
file_put_contents('ambiguous_places.json', json_encode($failedSameAs));
?>
