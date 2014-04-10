<?php 
    require_once('./log.php');
    include("/var/www/app/dbquery.php");
    include("/var/www/app/ldap.php");
    $Log->request($_SERVER['PHP_SELF']);

    if($_GET['action']=='logIn'){
        $con = openConnection();

        $user = mysql_real_escape_string($_POST['un']);
        $pass = mysql_real_escape_string($_POST['pass']);
        
        if(empty($user) || empty($pass)){
            //empty field received 
            echo '{"confirmed": false, "status" : "empty data sent"}';
            return;
        }

        $key = generateSessionKey($user);
        //use ldap to user data
        $userdata = getLdapData($user, $pass);
        //check success
        if ($userdata[0] === false) {
            echo '{"confirmed": false, "status" : "wrong password or user"}';
            return;
        }
        
        //checks if user exists in database
        if(false === ($id = checkForUser($userdata[1], $userdata[2], $userdata[3]))) {
            //if not in database, insert them
            //first check if they are a student or not
            if($userdata[4]=='student'){
                insertUser($userdata[1], $userdata[2], $userdata[3]);
                $first_time = "true";
                $id= mysql_insert_id();
                //echo $id;
            }
            else{
                echo '{"confirmed": false, "status" : "not a student"}';
                mysql_close($con);
                return;
            }
        }
        else
        {
            $first_time = "false";
        }

        //existing user
        echo '{"confirmed": true, "status" : "user confirmed", "username": "'.$userdata[3].'", "id": "' . $id . '", "key" : "'. $key . '", "first_time" :  "'. $first_time .'" }';
        mysql_close($con);
    }
    elseif($_GET['action']=='logout'){
        $key = mysql_real_escape_string($_POST["key"]);
        deleteKey($key);
    }
    elseif($_GET['action']=='test'){
        //this is to test functionality of functions from dbquery
        $result = mysql_real_escape_string($_POST["un"]);
        //generateSessionKey($result);
         echo checkUserKey($result) ? "true" : "false";
    }
    elseif($_GET['action']=='getUser'){
        $key = mysql_real_escape_string($_POST["session_key"]);
        $user = getUserFromKey($key);
        echo json_encode($user);
    }
    elseif($_GET['action']=='addEvent'){
        echo $_POST['event_time'];
        $name = mysql_real_escape_string($_POST["event_name"]);
        $time = mysql_real_escape_string(date('Y-m-d H:i:s', strtotime($_POST["event_time"])));
        $location = mysql_real_escape_string($_POST["event_loc"]);
        $descript = mysql_real_escape_string($_POST["event_descript"]);
        $uID = mysql_real_escape_string($_POST["userID"]);
        $food = mysql_real_escape_string($_POST["food"]);
        $key = mysql_real_escape_string($_POST["session_key"]);
        if(empty($name) && empty($time) && empty($location) && empty($descript) && empty($uID) && empty($food) &&empty($key)){
            die('empty data sent');
        }
        echo addEvent($name, $time, $descript, $location, $uID, $food, $key);
        $event_id= mysql_insert_id();
        printf("Event added to database: %d\n", mysql_affected_rows());
        echo attending($uID, $event_id);
    }
    elseif($_GET['action']=="userCanPost"){
        $canPost = mysql_real_escape_string($_POST["uid"]);
        $cp = userCanPost($canPost);
        echo ($cp) ? "true" : "false";
    }

    elseif($_GET['action']=='checkUser'){
        $userID = mysql_real_escape_string($_POST["userid"]);
        $eventID = mysql_real_escape_string($_POST["eventid"]);

        if(empty($userID) && empty($eventID)){
            die('empty data sent');
        }
        $rp = checkIfUsersEvent($userID, $eventID);
        return $rp;
    }
    elseif($_GET['action']=='deleteEvent'){
        $uID = mysql_real_escape_string($_POST["userid"]);
        $eID = mysql_real_escape_string($_POST["eventid"]);
        $t = deleteEvent($uID, $eID);
        printf("Events deleted from database: %d\n", mysql_affected_rows());
        return $t;
    }
    elseif($_GET['action']=='updateEvent'){
        $n = mysql_real_escape_string($_POST["event_name"]);
        $t = mysql_real_escape_string($_POST["event_time"]);
        $d = mysql_real_escape_string($_POST["event_descript"]);
        $l = mysql_real_escape_string($_POST["event_loc"]);
        $eID = mysql_real_escape_string($_POST["event_id"]);
        $food = mysql_real_escape_string($_POST["food"]);

        $r= updateEvent($n, $t, $d, $l, $eID, $food);
        var_dump($n, $t, $d, $l, $eID, $food);
        printf("Events updated in database: %d\n", mysql_affected_rows());
        return $r;
    }
    elseif($_GET['action']=='attending'){
        $uID = mysql_real_escape_string($_POST["userid"]);
        $eID = mysql_real_escape_string($_POST["eventid"]);

        $e= attending($uID, $eID);
        printf("Attendance updated in database: %d\n", mysql_affected_rows());
        return $e;
    }
    elseif($_GET['action']=='notAttending'){
        $uID = mysql_real_escape_string($_POST["userid"]);
        $eID = mysql_real_escape_string($_POST["eventid"]);

        $e= notAttending($uID, $eID);
        var_dump($uID, $eID);
        printf("Attendance updated in database: %d\n", mysql_affected_rows());
        return $e;
    }
    elseif($_GET['action']=='getEvents'){
        $q= getEvents();
        $qu= queryToArray($q);
        echo(json_encode($qu));
    }
?>
