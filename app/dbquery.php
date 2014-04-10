<?php
///starting functions
function openConnection()
{
    include("/var/www/app/config.php");
    if(!$con) {
        die('Could not connect: ' . mysql_error());
    }
    mysql_select_db("app", $con);
    return $con;
}

function queryToArray($q)
{
    $result = $q;
    $rows = array();
    $row = mysql_fetch_assoc($result);
    while($row) {
        $rows[]=$row;
        $row = mysql_fetch_assoc($result);
    }
    return $rows;
}

function query($sql) {
    global $Log;
    $Log->query(preg_replace("/\s+/", ' ', $sql));

    return mysql_query($sql);
}

///User interaction functions

function storeSessionKey($key, $un){
    $con = openConnection();
    $query = query("UPDATE users SET session_key = '$key'
        where username='$un';");
}


function generateSessionKey($un)
{
    //generates a session key with the username + hashme
    //this key is then stored
    //note: this is after mysql_real_escape_string() has been called on un
    $key =  sha1($un . "hashme");
    storeSessionKey($key, $un);
    return $key;
}

function getUserFromKey($key){
    $con=openConnection();
    $un=queryToArray(query("SELECT user_id from users
        where session_key='$key'"));
    return  $un[0];
    } 

function checkUserKey($un, $key)
{
    $con =openConnection();
    $result=queryToArray(query("SELECT session_key from users
        where username='$un'"));
    return ($result[0]["session_key"] == $key);
}

function getUserKey($un)
{
    //check if user already has a key, return the key if so
    $con=openConnection();
    $result=queryToArray(query("SELECT session_key from users
        where username='$un'"));
    return $result[0]["session_key"];
}

function deleteKey($key)
{
    $con=openConnection();
    query("UPDATE users SET session_key=NULL WHERE session_key='$key'");
}

function userCanPost($uid)
{
    $con=openConnection();
    $canPost=queryToArray(query("SELECT can_post from users
        WHERE user_id='$uid'"));
    mysql_close($con);
    return $canPost[0]["can_post"] == "1";
}

function insertUser($lastName, $firstName, $username)
{
    $con=openConnection();
    $ln=mysql_real_escape_string($lastName, $con);
    $fn=mysql_real_escape_string($firstName, $con);
    $un=mysql_real_escape_string($username, $con);

    $check= query("SELECT last_name, first_name, username
        FROM users
        WHERE last_name='$ln' AND first_name='$fn' AND username='$un'");
    if(!$check){
        error_log('query failed to execute');
        die();
    }

    if(mysql_num_rows($check)>0){
        echo "entry already exists";
    }

    else {
        $request = "INSERT INTO users (last_name, first_name, username)
            VALUES ('$ln', '$fn', '$un')";
        $r= query($request);
        return $r;
    }
}

/*function sendEventID($un, $name)
{
    openConnection();
    $user= query("SELECT user_id FROM users WHERE username='$un'");
    $request = "SELECT event_id FROM events WHERE event_name='$name' AND user_id='$user'" 
    $r= query($request);
    return $r;
}*/

function is_timestamp($timestamp)
{
    return ((string) (int) $timestamp === $timestamp)
        && ($timestamp <= PHP_INT_MAX)
        && ($timestamp >= ~PHP_INT_MAX);
}
///Event creation, editing, and deletion functions
function addEvent($name, $time, $descript, $location, $uID, $food, $key)
{
   //sanitize data received?
   if(empty($uID))
   {
       echo 'No user ID';
       return;
   }
   if(strlen($descript)>250)
    {
        echo 'Description more than 250 characters';
        return;
    }
   if(strlen($name)>30)
    {
        echo 'Name more than 30 characters';
        return;
    }
   if(strlen($location)>50)
    {
        echo 'Location more than 50 characters';
        return;
    }
  /* if(is_timestamp($time)== false)
   {
       echo 'not a timestamp';
       return;
   }
     
   if($time<time())
   {
       echo 'timestamp earlier than now ';
       return;
   }*/
  
   //connect to the database
   
   if (getUserFromKey($key)["user_id"] != $uID)
   {
       echo "user num: ", getUserKey($key)["user_id"], "num from db: ",$uID;
       return;
   }
   if (!userCanPost($uID))
   {
       echo "User cannot post";
       return;
   } 
   openConnection();
   
   //get the user id from users table

   //check to see if it exists already
   $check= query("SELECT user_id, event_name FROM events
       WHERE user_id='$uID' AND event_name='$name' AND event_time='$time'
      AND event_location='$location' AND event_descript='$descript' AND food=$food");

   
    if(!$check){
        echo "something";
        error_log('query failed to execute');
        die();
        return;
    }
    if(mysql_num_rows($check)>0){
        echo "entry already exists";
    }
    //if it doesn't exist, insert it into the database
    else{
    $request = "INSERT INTO events (event_name, event_time, event_descript,
        event_location, user_id, food) VALUES ('$name', '$time', '$descript',
        '$location', '$uID', '$food')";
    $r= query($request);
    return $r;
}
}
/*function checkUser($uID){
    open*/
function checkIfUsersEvent($uID, $event_id)
{
    openConnection();
    $userID= mysql_real_escape_string($uID);
    $eID= mysql_real_escape_string($event_id);
    //check and see if event is user's
    $request = "SELECT user_id, event_id FROM events
        WHERE event_id='$eID' AND user_id='$userID'";
    $check= query($request);
    if(!$check){
        error_log('query failed to execute');
        die();
    }
    if(mysql_num_rows($check)>0){
        return true;
    }
    else{
        return false;
    }
}
function deleteEvent($uID, $eID)
{
    openConnection();
    $request = "DELETE FROM events WHERE user_id='$uID' AND event_id='$eID'";
    $r= query($request);
    return $r;
   
    $rq = "DELETE FROM attendance WHERE event_id='$id'";
    $rt= query($rq);
    return $rt;   
}

function updateEvent($name, $time, $descript, $location, $eID, $food)
{
   //sanitize data received?
   if(strlen($descript)>250)
    {
        echo 'Description more than 250 characters';
        return;
    }
   if(strlen($name)>30)
    {
        echo 'Name more than 30 characters';
        return;
    }
   if(strlen($location)>50)
    {
        echo 'Location more than 50 characters';
        return;
    }
  /* if(is_timestamp($time)== false)
   {
       echo 'not a timestamp';
       return;
   }
   if($time<time())
   {
       echo 'timestamp earlier than now ';
       return;
   }*/
   
   $food = (bool)$food;
 openConnection();
    $request = "UPDATE events SET event_name='$name', event_time='$time',
        event_descript='$descript', event_location='$location', food=$food
        WHERE event_id='$eID'";
    $r= query($request);
    return $r;
}

//////Work on this function
function getEvents()
{
    openConnection();
    //sanitize time somehow
    date_default_timezone_set('America/New_York');

    $time= date('Y-m-d H:i:s', strtotime('-3 hours'));
    //query events based on time? maybe do this at the app level?
    #$request = "SELECT event_name, event_id, event_time, event_descript, event_location, food, users.user_id, last_name, first_name
    #    FROM events JOIN users ON users.user_id= events.user_id WHERE event_time >='$time' ORDER BY event_time"; 
    $request = "SELECT event_name, event_id, event_time, event_descript, event_location, 
                    food, last_name, first_name, username,
                    (SELECT COUNT(*) FROM attendance as a WHERE a.event_id = e.event_id) as attendance
                FROM events as e 
                    JOIN users as u 
                    ON u.user_id= e.user_id 
                    WHERE event_time >='$time' 
                    ORDER BY event_time";
    $r = query($request);
    return $r;
}


///Attendance functions


function attending($uID, $event_id)
{
    openConnection();
    //make sure data is sane 
    if(strlen($event_id)>10){
        echo 'event_id too long';
        return;
    }

   $un= mysql_real_escape_string($uID);
   $eID= mysql_real_escape_string($event_id);

   //insert into table
   $request = "INSERT INTO attendance (user_id, event_id) VALUES ('$un','$eID')";
   $r= query($request);
   return $r;
}

function notAttending($uID, $event_id)
{
    openConnection();
    //make sure data is sane 
    if(strlen($event_id)>10){
        echo 'event_id too long';
        return;
    }  

   $un= mysql_real_escape_string($uID);
   $eID= mysql_real_escape_string($event_id);

   //delete from table
   $request = "DELETE FROM attendance WHERE event_id='$eID' AND user_id='$un'";
   
   $r= query($request);
   
   return $r;
}

?>
