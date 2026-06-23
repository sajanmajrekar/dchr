<?php 
$role= 	"Called - Busy. Call back later, Called - but did not pick up call";
$role=explode(",",$role);
$finalstring = []; 
    foreach ($role as $key => $value) {
        //echo "splittedstring[".$key."] = ".$value."<br>";
            if(trim($value) =="Called - Busy. Call back later"){
               array_push($finalstring,'10' );
            }else if(trim($value) =="Called - but did not pick up call"){
               array_push($finalstring,'20' );
            }
            else{
                array_push($finalstring,'30' );
            }
    }
	echo implode(",",$finalstring);;
?>