<?php
define('MORIARTY_ARC_DIR', 'lib/arc/');
require_once 'lib/moriarty/simplegraph.class.php';

define('OV', 'http://open.vocab.org/terms/');
define('SKOS', 'http://www.w3.org/2004/02/skos/core#');
define('METEO', 'http://purl.org/ns/meteo#');
define('MET_DI', 'http://metoffice.dataincubator.org/');

 $data = json_decode(file_get_contents('weather-symbols.js'), true);
 $turtle = "";

$graph = new SimpleGraph();
 foreach($data as $uri => $label){
         $slug = str_replace(' ', '', ucwords($label));
         $slug = str_replace('(','_', $slug);
         $slug = str_replace(')','', $slug);
//         $turtle .= "\n metcat:{$slug} \n\t a meteo:Category ; \n \t rdfs:label \"{$label}\"@en ; \n\t skos:inScheme <http://metoffice.dataincubator.org/categories> ; \n \t ov:icon <http://www.metoffice.gov.uk{$uri}>  . \n";
         $caturi = MET_DI.'categories/'.$slug;
         $graph->add_resource_triple($caturi, RDF_TYPE, METEO.'Category');
         $graph->add_literal_triple($caturi, RDFS_LABEL, $label, 'en-gb');
         $graph->add_resource_triple($caturi, SKOS.'inScheme', MET_DI.'categories/');
         $graph->add_resource_triple($caturi, OV.'icon', "http://www.metoffice.gov.uk{$uri}");
 }

 echo $graph->to_turtle();
?>
