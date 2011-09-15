<?php
date_default_timezone_set('GMT');
define('gregorian_interval', 'http://reference.data.gov.uk/id/gregorian-interval/');
define('rdf','http://www.w3.org/1999/02/22-rdf-syntax-ns#');
define('rdfs','http://www.w3.org/2000/01/rdf-schema#');
define('owl','http://www.w3.org/2002/07/owl#');
define('met','http://metoffice.dataincubator.org/vocabs/metoffice/');
define('meteo','http://purl.org/ns/meteo#');
define('dct','http://purl.org/dc/terms/');
define('spatialrelations','http://data.ordnancesurvey.co.uk/ontology/spatialrelations/');
define('foaf','http://xmlns.com/foaf/0.1/');
define('skos','http://www.w3.org/2004/02/skos/core#');
define('geo','http://www.w3.org/2003/01/geo/wgs84_pos#');
define('geonames','http://www.geonames.org/ontology#');
define('ov','http://open.vocab.org/terms/');
define('dbpedia','http://dbpedia.org/resource/');
define('xsd','http://www.w3.org/2001/XMLSchema#');
define('admingov','http://statistics.data.gov.uk/def/administrative-geography/');
define('metvis','http://metoffice.dataincubator.org/categories/visibility/');
define('metcat','http://metoffice.dataincubator.org/categories/');
define('metareas', 'http://metoffice.dataincubator.org/areas/');
define('metobv', 'http://metoffice.dataincubator.org/observation-sites/');
define('met_celc', 'http://metoffice.dataincubator.org/temperatures/celcius/');
define('compass','http://purl.org/net/compass#');
define('scv', 'http://purl.org/NET/scovo#');

require_once 'inc.php';
//define('MORIARTY_ARC_DIR', 'lib/arc/');
define('MORIARTY_HTTP_CACHE_DIR', 'cache/');

require_once 'lib/moriarty/simplegraph.class.php';
require_once 'generate-compass-rdf.php';



function scrape5DayForecast($key){
  // items for use in metadata
  $url = "http://www.metoffice.gov.uk/weather/uk/{$key}_forecast_weather.html";
  $dateTimeNow = date('c');
  $locationURI = metareas.$key;
  require 'areas.inc.php';
  $locationName = $areas[$key];


  $page_content = file_get_contents($url);
  $dom = new DomDocument();
  @$dom->loadHTML($page_content);
  $xpath = new DomXpath($dom);


  
  $lastUpdated = $xpath->query('//div[@class="tableWrapper"]//table//tr[position()=last()]')->item(0)->textContent;
  $modified = str_replace(' on ', ' ', str_replace('Last updated: ','',$lastUpdated));
  $modified = preg_replace('/(\d\d)(\d\d)(.+)/','$3 $1$2',$modified);
  $modified = date('c', strtotime($modified));

  $forecastURI_base = $locationURI.'/forecast/from/'.$modified.'/for/';

  $graph = new SimpleGraph();


  $siteInfo = $xpath->query('//div[@id="siteInfo"]')->item(0)->textContent;
  preg_match('/Latitude: (.+?); Longitude: ([\-0-9\.]+)/', $siteInfo, $siteInfoRegexMatches);

  $latitude = $siteInfoRegexMatches[1];
  $longitude = $siteInfoRegexMatches[2];

  $nearestObservationSiteLink = $xpath->query('//div[@id="siteInfo"]/a[position()=last()]')->item(0);
  $href = $nearestObservationSiteLink->getAttribute('href');
 $shortName = str_replace('_latest_weather.html','',$href);
  $areaCode  = array_shift(explode('/',$key));
  $distance = trim(preg_replace('/\(([\d\.]+) km\).+/', '$1', $nearestObservationSiteLink->nextSibling->textContent));
  $nearestObservationSiteUri = metobv.$areaCode.'/'.$shortName;
  $distanceUri = $nearestObservationSiteUri.'/distance-to-forecast-location/'.array_pop(explode('/',$key));  
  $graph->add_resource_triple($locationURI, met.'nearestObservationSite', $nearestObservationSiteUri);
  $graph->add_literal_triple($nearestObservationSiteUri, RDFS_LABEL, $nearestObservationSiteLink->textContent, 'en');
  $graph->add_resource_triple($nearestObservationSiteUri, foaf.'page', "http://www.metoffice.gov.uk/weather/uk/{$areaCode}/{$shortName}_latest_weather.html" );
  $graph->add_resource_triple($nearestObservationSiteUri, RDF_TYPE, met.'MetOfficeObservationSite' );
  $graph->add_literal_triple($locationURI, geo.'lat', $latitude, false, xsd.'float');
  $graph->add_literal_triple($locationURI, geo.'long', $longitude, false, xsd.'float');
  $graph->add_resource_triple($distanceUri, RDF_TYPE, ov.'Distance');
  $graph->add_resource_triple($distanceUri, ov.'distancePoint', $nearestObservationSiteUri);
  $graph->add_resource_triple($distanceUri, ov.'distancePoint', $locationURI);
  $graph->add_literal_triple($distanceUri, ov.'kilometres', $distance, null, xsd.'decimal');
  $graph->add_resource_triple($locationURI, ov.'distance', $distanceUri);
  $graph->add_resource_triple($nearestObservationSiteUri, ov.'distance', $distanceUri);


  foreach($xpath->query('//td[@scope="row"]') as $no => $td){
    $date = date('Y-m-d', strtotime($td->textContent));

    $sunrise = date_sunrise(strtotime($date), SUNFUNCS_RET_STRING, $latitude, $longitude);
    $sunset = date_sunset(strtotime($date), SUNFUNCS_RET_STRING, $latitude, $longitude);
    $secondsOfDaylight = strtotime($sunset) - strtotime($sunrise);
    $hoursOfDaylight = intval($secondsOfDaylight / 3600);
    $hoursOfNight = 24-$hoursOfDaylight;

    $tds = $td->parentNode->getElementsByTagName('td');
    $tr = $td->parentNode;
    $rowNo = 1;
    $continue = true;
    while($continue){ #iterate over rows until next day
    $forecastURI= $forecastURI_base.$date;
      if($rowNo==1)//the row saying the day
      {
        $start = 1;
      } else {
        $start = 0;
      }    
      $rowNo++;
      $time = trim($tds->item($start)->nodeValue);
      $time = preg_replace('/(\d\d)(\d\d)/','$1:$2',$time);
      if(preg_match('/^\d\d:\d\d$/', $time)) $time.=':00';

      if($time=='Day'){
        $forecastURI.='/Day';
        $timeURI =  gregorian_interval.$date.'T'.$sunrise.'/PT'.$hoursOfDaylight.'H';
        $graph->add_literal_triple($timeURI, scv.'min', $date.'T'.$sunrise, false, xsd.'dateTime');
        $graph->add_literal_triple($timeURI, scv.'max', $date.'T'.$sunset, false, xsd.'dateTime');
      }
      else if($time=='Night'){
        $forecastURI.'/Night';
        $timeURI =  gregorian_interval.$date.'T'.$sunset.'/PT'.$hoursOfNight.'H';
        $nextDayDate = date('Y-m-d', strtotime($date)+(60*60*24));
        $graph->add_literal_triple($timeURI, scv.'min', $date.'T'.$sunset, false, xsd.'dateTime');
        $graph->add_literal_triple($timeURI, scv.'max', $nextDayDate.'T'.$sunrise, false, xsd.'dateTime');
      }
      else {
        $forecastURI.='T'.$time;
        $timeURI =  gregorian_interval.$date.'T'.$time.'/PT3H';
        $dateTime3Hrs = date('Y-h-mTH:m:s', strtotime($date.'T'.$time)+(3*60*60));
        $graph->add_literal_triple($timeURI, scv.'min', $date.'T'.$time, false, xsd.'dateTime');
        $graph->add_literal_triple($timeURI, scv.'max', $dateTime3Hrs, false, xsd.'dateTime');
      }
    

//      var_dump($tds->length, $tds->item($start+1));
    $categoryURI = metcat.slug($tds->item($start+1)->firstChild->getAttribute('title'));
    $temperature = intval($tds->item($start+2)->nodeValue);
    $windDirectionCode = $tds->item($start+3)->nodeValue;
    $windDirection = ($windDirectionCode=='VRB')? met.'variable-wind-direction' : Compass::get_slug($windDirectionCode);
    $windSpeed = intval($tds->item($start+4)->nodeValue);
    $gustSpeed = intval($tds->item($start+5)->nodeValue);
    $visibility = $tds->item($start+6)->nodeValue;
    $windURI =  $forecastURI.'/wind';
    $graph->add_resource_triple($forecastURI, RDF_TYPE, meteo.'Forecast');
    $graph->add_literal_triple($forecastURI, RDFS_LABEL, "Forecast for {$locationName} for {$time} on {$date}, forecast at $modified", 'en-gb');
    $graph->add_resource_triple($forecastURI, meteo.'location', $locationURI); 
    $graph->add_literal_triple($forecastURI, dct.'created',$modified, false, xsd.'dateTime');
    $graph->add_resource_triple($forecastURI, meteo.'time', $timeURI);
    $graph->add_resource_triple($forecastURI, meteo.'category', $categoryURI);
    $graph->add_resource_triple($forecastURI, meteo.'visibility', metvis.slug($visibility));
    $graph->add_resource_triple($forecastURI, meteo.'temperature', met_celc.slug(($temperature)));
    $graph->add_literal_triple(met_celc.slug($temperature), meteo.'celsius', ($temperature), false, xsd.'integer');
    $graph->add_resource_triple($forecastURI, meteo.'wind',$windURI);
    $graph->add_resource_triple('http://metoffice.dataincubator.org/areas/'.$key.'/forecast-channel', FOAF.'topic', $forecastURI);
    $graph->add_resource_triple($windURI, compass.'comingFrom', compass.slug($windDirection));
    $graph->add_resource_triple($windURI, meteo.'speed', $windURI.'/speed');
    $graph->add_resource_triple($windURI, meteo.'gust-speed', $windURI.'/gust-speed');
    $graph->add_literal_triple($windURI.'/speed', meteo.'milesPerHour', $windSpeed, false, xsd.'integer');
    $graph->add_literal_triple($windURI.'/gust-speed', meteo.'milesPerHour', $gustSpeed, false, xsd.'integer');
    $graph->add_resource_triple(metvis.slug($visibility), RDF_TYPE, met.'VisibilityAssessment');
    $graph->add_literal_triple(metvis.slug($visibility), RDFS_LABEL, $visibility, 'en-gb');

      if($tr->nextSibling && $tr->nextSibling->hasChildNodes() && $tr->nextSibling->childNodes->length > 3 && !$tr->nextSibling->firstChild->hasAttribute('scope')){
          $tr = $tr->nextSibling;
          $tds = $tr->getElementsByTagName('td');
          $td= $tds->item(0);
//          echo "\n Continuing from {$date} {$time}\n";
      } else {
  //                  echo "\n Breaking while loop after {$date} {$time}\n";

          $continue = false;
        }
    }
   


//    var_dump(date('c', strtotime($time.' '.$date)), $time, date('c',strtotime($time)));
  
  }
  $graph->add_resource_triple('http://metoffice.dataincubator.org/areas/'.$key.'/forecast-channel', DCT.'source', $url);
  $graph->add_literal_triple(met.'variable-wind-direction' , RDFS_LABEL , 'Variable Wind Direction', 'en-gb');
  $graph->add_literal_triple(met.'variable-wind-direction' , RDFS_COMMENT , 'Variable Wind Direction; when the wind does not prevail from one particular direction.', 'en-gb');
  return $graph->to_turtle();

}

 //echo scrape5DayForecast('os/kirkwall');

?>
