<?php 

if (session_status() === PHP_SESSION_NONE) {
    if (!@session_start()) {
        error_log('core.php: session_start failed.');
    }
}

require_once 'db_connect.php';
date_default_timezone_set('Asia/Kolkata');
function time_ago($time_ago)
{
    if($time_ago!=null){
        $time_ago     = strtotime($time_ago);
        $cur_time     = time();
        $time_elapsed = $cur_time - $time_ago;
        $seconds      = $time_elapsed;
        $minutes      = round($time_elapsed / 60);
        $hours        = round($time_elapsed / 3600);
        $days         = round($time_elapsed / 86400);
        $weeks        = round($time_elapsed / 604800);
        $months       = round($time_elapsed / 2600640);
        $years        = round($time_elapsed / 31207680);
        // Seconds
        if ($seconds <= 60) {
            return "Just now";
        }
        //Minutes
        elseif ($minutes <= 60) {
            if ($minutes == 1) {
                return "One minute ago";
            }

            return $minutes.' minutes ago';
        }
        //Hours
        elseif ($hours <= 24) {
            if ($hours == 1) {
                return "an hour ago";
            }

            return $hours." hours ago";
        }
        //Days
        elseif ($days <= 7) {
            if ($days == 1) {
                return "Yesterday";
            }

            return $days." days ago";
        }
        //Weeks
        elseif ($weeks <= 4.3) {
            if ($weeks == 1) {
                return "a week ago";
            }

            return $weeks." weeks ago";
        }
        //Months
        elseif ($months <= 12) {
            if ($months == 1) {
                return "a month ago";
            }

            return $months." months ago";
        }
        //Years

        if ($years == 1) {
            return "one year ago";
        }

        return $years." years ago";
    }
    else
    {
         return "Not yet contacted.";
    }
}

function CheckEmail($email){
    $checkemail = $email;
    global $connect;
    if (filter_var($checkemail, FILTER_VALIDATE_EMAIL)) {
       $sql1 = "SELECT * FROM `tblleads` where email='".$checkemail."'";
        $result = $connect->query($sql1);
        if($result->num_rows == 0) { 
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }
}
function getemail($id){
    $checkemail = $id;
    global $connect;
       $sql1 = "SELECT * FROM `tblleads` where id='".$checkemail."'";
        $result = $connect->query($sql1);
        if($result->num_rows > 0) { 
         while($row = $result->fetch_array()) {
            return $row['email'];
        }
        }else{
            return '';
        }
}
function getstatus($status){
    if($status =="Called - Busy. Call back later"){
        return 10;
    }else if($status =="Called - but did not pick up call"){
        return 2;
    }
    else if($status =="Called - candidate not interested, comment reason"){
        return 9;
    }
    else if($status =="Called - candidate rejected by us, comment reason"){
        return 8;
    }
    else if($status =="Interview - Completed"){
        return 18;
    }
    else if($status =="Interview - Did not turn up"){
        return 14;
    }
    else if($status =="Interview - Need to schedule"){
        return 11;
    }
    else if($status =="Interview - Scheduled, comment date of interview"){
        return 12;
    }
    else if($status =="Status - Joined"){
        return 27;
    }
    else if($status =="Status - Offer accepted, comment date of joining"){
        return 26;
    }
    else if($status =="Status - Offer given, comment date of offer"){
        return 24;
    }
    else if($status =="Status - Offer rejected"){
        return 25;
    }
    else if($status =="Status - Rejected, comment reason"){
        return 22;
    }
    else if($status =="Status - Shortlisted "){
        return 23;
    }else{
        return 20;
    }
}
function getrole($role){  
    $role=explode(",",$role);
    $finalstring = []; 
    foreach ($role as $key => $value) {
        //echo "splittedstring[".$key."] = ".$value."<br>";
            if(trim($value) =="Business Development"){
               array_push($finalstring,'10' );
            }else if(trim($value) =="Client Servicing / Project Management / Brand Associate"){
               array_push($finalstring,'9' );
            }
            else if(trim($value) =="Copy Writing / Content Writing"){
               array_push($finalstring,'11' );
            }
            else if(trim($value) =="Google Ads / Facebook Ads / Instagram Ads"){
               array_push($finalstring,'4' );
            }
            else if(trim($value) =="Graphic Design / Motion Graphics"){
               array_push($finalstring,'6' );
            }
            else if(trim($value) =="Human Resource / Recruitment"){
               array_push($finalstring,'8' );
            }
            else if(trim($value) =="I'm a fresher! Any role will do"){
               array_push($finalstring,'12' );
            }
            else if(trim($value) =="Search Engine Optimisation"){
               array_push($finalstring,'3' );
            }
            else if(trim($value) =="Social Media Marketing / Social Media Campaigns"){
               array_push($finalstring,'5' );
            }
            else if(trim($value) =="Website Development (front end / back end)"){
               array_push($finalstring,'7' );
            }
    }
    return implode(",",$finalstring);
}
function getroletext($role){  
    $role=explode(",",$role);
    $finalstring = []; 
    foreach ($role as $key => $value) {
        //echo "splittedstring[".$key."] = ".$value."<br>";
            if(trim($value) =="10"){
               array_push($finalstring,' Business Development' );
            }else if(trim($value) =="9"){
               array_push($finalstring,' Client Servicing' );
            }
            else if(trim($value) =="11"){
               array_push($finalstring,' Copy Writing / Content Writing' );
            }
            else if(trim($value) =="4"){
               array_push($finalstring,' Performance Marketing/Paid Media' );
            }
            else if(trim($value) =="6"){
               array_push($finalstring,' Graphic Design' );
            }
            else if(trim($value) =="8"){
               array_push($finalstring,' Human Resource' );
            }
            else if(trim($value) =="12"){
               array_push($finalstring," Internship" );
            }
            else if(trim($value) =="3"){
               array_push($finalstring,' Search Engine Optimisation' );
            }
            else if(trim($value) =="5"){
               array_push($finalstring,' Social Media Marketing / Social Media Campaigns' );
            }
            else if(trim($value) =="7"){
               array_push($finalstring,' Website Development' );
            }
    }
    return implode(",",$finalstring);
}
?>
