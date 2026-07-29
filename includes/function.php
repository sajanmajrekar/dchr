<?php 
    function query($query){

        global $conn;

        $result = $conn->query($query);

        return $result; 

    }
    function insert($table,$data){

        global $conn;

       // echo "<pre>";print_r($conn);die();

        //echo "<pre>";print_r($table);die();

        //echo "<pre>";print_r($data);die();

        $key =  "`" . implode("`,`",array_keys($data))."`" ;

        //echo $key;die();

        $value = "'" . implode("','", $data) . "'";

   $query="insert into $table($key) values($value)";

     //echo $query;die();

      $sql = $conn->query("insert into $table($key) values($value)");

        return true;

    }



    function update($table,$data,$where){

        global $conn;

        $qry = '';

        $condition = '';

        foreach ($data as $key => $value) {

            $qry .= "`" . $key ."`" .  " = '" . $value . "', ";

        }

        $qry = substr($qry, 0, -2); 



        foreach ($where as $key => $value) {

            $condition .= $key . " = '" . $value . "', ";

        }

        $condition = substr($condition, 0, -2);   

        $query="Update $table set $qry where $condition";

      //echo $query;die();

       $sql = $conn->query("Update $table set $qry where $condition");



    }

  

    function redirect($link,$alert=''){

        $redirect = '<script>';

        if($alert != ''){

            $redirect .= 'alert("'.$alert.'");';    

        }

        $redirect .= 'window.location.href="'.$link.'";</script>';

        echo $redirect;

    }

     function getname($id,$table){

        global $conn;

        $q = $conn->query("select * from $table where id=$id");

        if($q->num_rows != 0){

            $r = $q->fetch_object();

            $value = $r->name;

        }else{

            $value = "";

        }

        return $value;

    }



    

     function getcor($id,$table){

        global $conn;

        $q = $conn->query("select * from $table where register_id=$id");

        if($q->num_rows != 0){

            $r = $q->fetch_object();

            $value = $r->company_person_name;



        }else{

            $value = "";

        }

        return $value;

    }

       function getindustry($id,$table){

        global $conn;

        $q = $conn->query("select * from $table where id=$id");

        if($q->num_rows != 0){

            $r = $q->fetch_object();

            $value = $r->name;



        }else{

            $value = "";

        }

        return $value;

    }

      function getindustries($id,$table){

        global $conn;

        $q = $conn->query("select * from $table where brand_id=$id");

        if($q->num_rows != 0){

            $r = $q->fetch_object();

            $value = $r->industry_id;



        }else{

            $value = "";

        }

        return $value;

    }

    function escape_string($string){

        global $conn;

        $value = mysqli_real_escape_string($conn,$string);

        return $value;

    }

    

    function last_insert_id(){

        global $conn;

        $value = mysqli_insert_id($conn);

        return $value;

    }

     function last_update_id(){

        global $conn;

        $value = mysqli_update_id($conn);

        return $value;

    }

    function mysqlerror($string){

        global $conn;

        $value = mysqli_error($conn);

        return $value;

    }



    function adminemail(){

        global $conn;

        $q = $conn->query("select * from admin_users LIMIT 1");

        $r = $q->fetch_object();

        $value = $r->email;

        return $value;

    }

    

    function getmethod($payment){

        switch ($payment) {

            case '1':

                $value = 'Cash&nbsp;on&nbsp;Delivery';

                break;

            case '2':

                $value = 'Online Payment';

                break;

            default:

                $value = 'Cash&nbsp;on&nbsp;Delivery';

                break;

        }

        return $value;

    }

	 

	function rightsofemp($emp,$pageactive){

		$value = 1;

		global $conn;

		$q = $conn->query("select type,rights from admin_users where username = '$emp'");

		$rr = $q->fetch_object();

		if($rr->type != 1){

		if($rr->rights != ''){

			$arr = explode(',',$rr->rights);

			$value = (in_array($pageactive,$arr))?1:0;

		}else{

			$value = 0;

		}

		}else{

			$value = 1;

		}

		return $value;

	}

	

	function arrayselected($arr,$value1){

        $value = (in_array($value1,$arr))?'checked="Yes"':'';

        return $value;

    }

	/* Dashboard Function End */

    function cleanurl($string){

        $url = str_replace("'", '', $string);

        $url = str_replace('%20', ' ', $url);

        $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url); // substitutes anything but letters, numbers and '_' with separator

        $url = trim($url, "-");

        $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);  // you may opt for your own custom character map for encoding.

        $url = strtolower($url);

        $url = preg_replace('~[^-a-z0-9_]+~', '', $url); // keep only letters, numbers, '_' and separator

        return $url;

    }

    class cart{



    function add_to_cart($cid){

        $q="INSERT INTO cart (cid,qty) VALUES('$cid','1') ";

        $r=mysql_query($q) or die(mysql_error());

        header("Location:cart.php");

    

    }

    

    function remove_cart($cart_id){

        $q="DELETE FROM cart WHERE cart_id='$cart_id'";

        $r=mysql_query($q) or die(mysql_error());

        header("Location:cart.php");

    

    }





}

// function getbrand($id,$table){

//         global $conn;

//         $q = $conn->query("select * from $table where id=$id");

//         if($q->num_rows != 0){

//             $r = $q->fetch_object();

//             $value = $r->name;

//         }else{

//             $value = "";

//         }

//         return $value;

//     }
