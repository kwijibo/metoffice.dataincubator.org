<?php
// $data = json_decode(file_get_contents('weather-symbols.js'), true);
// $turtle = "";
// foreach($data as $uri => $label){
//         $slug = str_replace(' ', '', ucwords($label));
//         $slug = str_replace('(','_', $slug);
//         $slug = str_replace(')','', $slug);
//         $turtle .= "\n metcat:{$slug} \n\t a meteo:Category ; \n \t rdfs:label \"{$label}\"@en ; \n\t skos:inScheme <http://metoffice.dataincubator.org/categories> ; \n \t ov:icon <http://www.metoffice.gov.uk{$uri}>  . \n";
// }
// 
// echo $turtle ;

class Compass {
var $dirs = array(
"N",  
"NNE",
"NE",
"ENE",
"E",
"ESE",
"SE",
"SSE",
"S",
"SSW",
"SW",
"WSW",
"W",
"WNW",
"NW",
"NNW"
);


    function to_turtle(){
        $turtle = '';

        foreach($this->dirs as $k){

            $label = $this->get_label($k);
            $slug = $this->get_slug($k);
            $opposite = $this->get_slug($this->getOpposite($k));
            $clock = $this->get_slug($this->getClockwise($k));
            $anticlock = $this->get_slug($this->getAntiClockwise($k));
                    var_dump($k, $label, $slug);
            $turtle .= <<<_N3_

compass:{$slug} 
        rdfs:label "{$label}"@en-gb ;
        a compass:Direction ;
        dct:identifier "{$k}"@en-gb ;
        compass:opposite compass:{$opposite} ;
        compass:clockwise compass:{$clock} ;
        compass:anti-clockwise compass:{$anticlock}
 .
_N3_;
        }
        return $turtle;
    }

    function get_label($k){
        $labels = array(
            'N' => 'North',
            'E' => 'East',
            'S' => 'South' ,
            'W' => 'West'
        );
        $return = '';
        for ($i=0; $i < strlen($k); $i++) { 
            $l = $k[$i];
            $return .=$labels[$l].' ';
            if(strlen($k) ==3 AND $i===0){
                $return.='by ';
            }
        }
        return rtrim($return);
    }

    function get_slug($k){
        $labels = array(
            'N' => 'North',
            'E' => 'East',
            'S' => 'South' ,
            'W' => 'West'
        );
        $return = '';
        for ($i=0; $i < strlen($k); $i++) { 
             $l = $k[$i];
            $return .=$labels[$l];
        }
        return $return;
    }

    
    function getOpposite($k){
        $nk = $k;
        for ($i=0; $i < 8; $i++) { 
            $k = $this->getClockwise($k);
        }
        return $k;
    }
    
    function getClockwise($k){
        $index = array_search($k, $this->dirs);
        if(isset($this->dirs[$index+1])){
            return $this->dirs[$index+1];
        } else {
            return $this->dirs[0];
        }
    }
    
    function getAntiClockwise($k){
        $index = array_search($k, $this->dirs);
        if(isset($this->dirs[$index-1])){
            return $this->dirs[$index-1];
        } else {
            return array_pop($this->dirs);
        }
        
    }

}
//$com = new Compass();
//echo $com->get_label('NNW');
//echo $com->to_turtle();
?>
