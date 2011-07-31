<?php

require '5day_forecast.php';
require 'areas.inc.php';

if(isset($_GET['key'])){

  $turtle = scrape5DayForecast($_GET['key']);
  $metofficeStore->get_metabox()->submit_turtle($turtle);
  header("Content-type:text/turtle");
  echo $turtle;
  die;
} else {
?>
<!DOCTYPE HTML>
<body>
  <h1>5 Day Forecasts from Metoffice, UK</h1>
  <p> See <a href="http://metoffice.dataincubator.org/">MetOffice Dataincubator Page</a></p>
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
