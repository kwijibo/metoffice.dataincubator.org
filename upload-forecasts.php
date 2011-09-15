<?php



require '5day_forecast.php';

foreach($areas as $key => $label){
  $turtle = scrape5DayForecast($key);
  $url = "http://www.metoffice.gov.uk/weather/uk/{$key}_forecast_weather.html";
  
$response =  $metofficeStore->mirror_from_url($url, $turtle);
  var_dump(
    array($key => $label,
    $response->status_code,
    $response->body,
  )
  );
}
?>
