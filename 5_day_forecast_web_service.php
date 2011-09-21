<?php

require '5day_forecast.php';
require 'areas.inc.php';
require 'request.class.php';
$Request = new Request();


function serveTurtle($turtle){
  header("Content-type:text/turtle");
  echo $turtle;
  die;
}

function serveJson($json){
  header("Content-type:application/json");
  echo $json;
  die;
}

if(isset($_GET['key'])){

  $turtle = scrape5DayForecast($_GET['key'], 'turtle');
  $metofficeStore->get_metabox()->submit_turtle($turtle);

  foreach($Request->getAcceptTypes() as $mimetype){
    if(strpos($mimetype, 'turtle')){
      serveTurtle($turtle);
    } else if(strpos($mimetype, 'json')){
      $graph = new SimpleGraph();
      $graph->add_turtle($turtle);
      $json = $graph->to_json(); 
      serveJson($json);
    }
  }

  header("Content-Type: text/plain");
  echo $turtle;
  die;

} else {
?>
<!DOCTYPE HTML>
<body>
  <h1>5 Day Forecasts from Metoffice, UK</h1>
  <p> See <a href="http://metoffice.dataincubator.org/">MetOffice Dataincubator Page</a></p>
<p>Usage: <a href="http://kwijibo.talis.com/metoffice/os/lerwick"> http://kwijibo.talis.com/metoffice/os/lerwick</a> is RDF version of <a href="http://www.metoffice.gov.uk/weather/uk/os/lerwick_forecast_weather.html">usage: http://kwijibo.talis.com/metoffice/os/lerwick is RDF version of http://www.metoffice.gov.uk/weather/uk/os/lerwick_forecast_weather.html</a> .  See metoffice.gov.uk pages, or the source of this web form, for place codes to use.</p>

  <form action="" method="GET">
    <label for="key">Place:</label>
    <select name="key" id="key">
      <?php foreach($areas as $key => $name):?>
      <option value="<?php echo $key?>"><?php echo htmlentities($name)?></option>
      <?php endforeach ?>
    </select>
    <input type="submit"/>
  </form>
</body>


<?php
}

?>
