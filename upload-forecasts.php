<?php



require '5day_forecast.php';

foreach($areas as $key => $label){
  $turtle = scrape5DayForecast($key);
$response =  $metofficeStore->get_metabox()->submit_turtle($turtle);
  var_dump(
    array($key => $label,
    $response->status_code,
    $response->body,
  )
  );
}
?>
