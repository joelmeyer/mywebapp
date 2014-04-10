<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="style.css">
<title>Events</title>
</head>
<body>
<div id="header">
    <div class="headfont">
<a class="head" href="events.php"> Goshen College</a>
    </div>
</div>
    <div class="left">
<form action="events.php" method="get">
<table class='form'>
<tr>
   <td> First Name</td>
   <td> <input name="first_name" type=text value=""></td>
</tr>   
<tr>
   <td> Last Name</td>
   <td> <input name="last_name" type=text value=""></td>
</tr>
<tr>
   <td> Username</td>
   <td> <input name="username" type=text value=""></td>
</tr>
<tr>
   <td> UserID</td>
   <td> <input name="userid" type=text value=""></td>
</tr>
<tr>
 <td> <input type=submit value="Go"></td>
</tr>
</table>
</form> 
</div>

<div class="result">
<?php
    include("/var/www/app/dbquery.php");
   /* if($_REQUEST['Go']){*/
        $first = mysql_real_escape_string($_GET['first_name']);
        $last = mysql_real_escape_string($_GET['last_name']);
        $username = mysql_real_escape_string($_GET['username']);
       $userID = mysql_real_escape_string($_GET['userid']); 

      
          
        $search_fields = array("$first", "$last", "$username", "$userID");
  $conditions = array();
      // define the fields that are searchable (we assumed that the table field 
        // names match the form input names)
    if(!empty($first)){
                array_push($conditions, "first_name LIKE '%".$first."%'");
    }
  if(!empty($last)){
                array_push($conditions, "`last_name` LIKE '%".$last."%'");
    }
   if(!empty($username)){
                array_push($conditions, "`username` LIKE '%".$username."%'");
   }
    if(!empty($userID)){
                array_push($conditions, "`user_id` LIKE '%".$userID."%'");
    }  
    // if there are conditions defined
    if(count($conditions) > 0)
        {
          $em = "(SELECT user_id FROM users WHERE ". implode(" AND ", $conditions).")";
          $q = "SELECT * FROM events JOIN users ON users.user_id=events.user_id 
            WHERE users.user_id IN $em";
          
        }
    
        else
        {
            $q= "SELECT * FROM events";
        }
        openConnection();
        var_dump($q);
        $qu = mysql_query($q);
        var_dump($qu);
        $num_results = mysql_num_rows($qu);
        //get user_id to first and last name
        $que = mysql_query($q);
        $rw = mysql_fetch_array($que);    
        $uid = $rw['user_id'];
        $t = mysql_query("SELECT last_name, first_name FROM users WHERE user_id='$uid'");
        $tw= mysql_fetch_array($t);
        $ln = $tw['last_name'];
        $fn = $tw['first_name'];
        echo "<table>";
       echo "<tr><td class='clear'>Events Found: ".$num_results."</td>";
      echo "<td class='clear'>For: ".$fn." ". $ln."</td></tr><tr>";
      echo "</table>";
        echo "<table border='1'>";
      echo "<td>Name</td>";
      echo "<td>EventID</td>";
      echo "<td>Time</td>";
      echo "<td>Description</td>";
      echo "<td>Location</td>";
      echo "<td>Date Created</td>";
      echo "<td>Last Updated</td>";
      echo "<td>User ID</td>";
       echo "<td>Last name</td>";
    echo "</tr>";
      /* var_dump(mysql_fetch_array($qu));*/
    for ($i=0; $i<$num_results; $i++)
    {   
        $row = mysql_fetch_array($qu);
        echo "<tr><td>";
        echo stripslashes($row["event_name"]);
         echo "</td>";
        echo "<td>";
        echo stripslashes($row["event_id"]);
        echo "</td>";
        echo "<td>";
        echo stripslashes($row["event_time"]);
        echo "</td>";
        echo "<td>";
        echo stripslashes($row["event_descript"]);
        echo "</td>";
        echo "<td>";
        echo stripslashes($row["event_location"]);
        echo "</td>";
        echo "<td>";
        echo stripslashes($row["dateCreated"]);
        echo "</td>";
        echo "<td>";
        echo stripslashes($row["lastUpdated"]);
        echo "</td><td>";
        echo stripslashes($row["user_id"]);
        echo "</td><td>";
        echo stripslashes($row["last_name"]);
        echo "</td></tr>";
    }
    echo "</table>";
     
?>
</div>
    </body>
        </html>





