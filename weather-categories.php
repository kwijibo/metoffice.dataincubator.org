<?php
define('MORIARTY_ARC_DIR', 'lib/arc/');
require_once 'lib/moriarty/simplegraph.class.php';

define('OV', 'http://open.vocab.org/terms/');
define('SKOS', 'http://www.w3.org/2004/02/skos/core#');
define('METEO', 'http://purl.org/ns/meteo#');
define('MET_DI', 'http://metoffice.dataincubator.org/');

 $data = json_decode(file_get_contents('weather-symbols.js'), true);
 $turtle = "";
$codeToUri = array();
$graph = new SimpleGraph();
 foreach($data as $uri => $label){
         $slug = str_replace(' ', '', ucwords($label));
         $slug = str_replace('(','_', $slug);
         $slug = str_replace(')','', $slug);

         $caturi = MET_DI.'categories/'.$slug;
         if(preg_match('/w\d+/',$uri,$m)){
            $codeToUri[$m[0]]=$caturi;
         }
         $graph->add_resource_triple($caturi, RDF_TYPE, METEO.'Category');
         $graph->add_literal_triple($caturi, RDFS_LABEL, $label, 'en-gb');
         $graph->add_resource_triple($caturi, SKOS.'inScheme', MET_DI.'categories/');
         $graph->add_resource_triple($caturi, OV.'icon', "http://www.metoffice.gov.uk{$uri}");

         if(strpos($slug, 'Shower_')){
           $broaderUri = preg_replace('/_.+/','', $caturi);
           $broaderLabel = preg_replace('/ \(.+\)/', '', $label);
           $graph->add_resource_triple($broaderUri, SKOS.'narrower', $caturi);
           $graph->add_resource_triple($caturi, SKOS.'broader', $broaderUri);
           $graph->add_resource_triple($broaderUri, RDF_TYPE, METEO.'Category');
           $graph->add_literal_triple($broaderUri, RDFS_LABEL, $broaderLabel, 'en-gb');
           $graph->add_resource_triple($broaderUri, SKOS.'inScheme', MET_DI.'categories/');
           $evenBroaderUri = preg_replace('/Shower_.+/','', $caturi);
           $graph->add_resource_triple($broaderUri, SKOS.'broader', $evenBroaderUri);
           $graph->add_resource_triple($evenBroaderUri, SKOS.'narrower', $broaderUri);
         }
 }
file_put_contents('weather-categories.codes.serialised.php', serialize($codeToUri));
 echo $graph->to_turtle();
?>
