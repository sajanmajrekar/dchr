<?php 
$role= 	"Called - Busy. Call back later, Called - but did not pick up call";
$role=explode(",",$role));
    $finalstring[]="";
    foreach ($role as $key => $value) {
        echo "splittedstring[".$key."] = ".$value."<br>";
            if($status =="Called - Busy. Call back later"){
               array_push($finalstring,'10' );
            }else if($status =="Called - but did not pick up call"){
               array_push($finalstring,'20' );
            }
            else{
                array_push($finalstring,'30' );
            }
    }
?>