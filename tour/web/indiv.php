<!doctype html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div id="header">
    <div class="headfont">
<a class="head" href="tstudents.php"> Goshen College</a>
    </div>
</div>
<?php
include("/var/www/tour/dbquery.php");

$username=$_GET['user'];
$un= mysql_real_escape_string($username);
$q=checkStudent($un);
$num_results = mysql_num_rows($q);
echo"<h2>Student: $un </h2>";
      echo" <table class='left'>";
      echo" <tr align='center'>";

      echo "<th>Locations Done: ".$num_results."</th>";
      echo" </tr>";
 
      for ($i=0; $i<$num_results; $i++)
             {
                $row = mysql_fetch_array($q);
                echo "<tr><td>";
               /* echo stripslashes($row["username"]);
                if($row["notDone"]>='6'){
                echo "</a></td><td style='background-color:rgb(205,0,0)'>";
                }
                else
                {
                    echo "</a></td><td style='background-color:rgb(0,205,0)'>";
                }
                echo "</td><td>";*/
                echo stripslashes($row["loc"]);
                echo "</td> </tr>";
             }
      echo"</table>";





$qu=studentNot($un);
$num_result = mysql_num_rows($qu);
           
      echo" <table class='right'>";
      echo" <tr>";
      echo "<th>Locations Not Done: ".$num_result."</th>";
      echo" </tr>";
 
      for ($i=0; $i<$num_result; $i++)
             {
                $rows = mysql_fetch_array($qu);
                echo "<tr><td>";
               /* if($row["notDone"]>='6'){
                echo "</a></td><td style='background-color:rgb(205,0,0)'>";
                }
                else
                {
                    echo "</a></td><td style='background-color:rgb(0,205,0)'>";
                }*/
                echo stripslashes($rows["loc_name"]);
                echo "</td> </tr>";
             }
      echo"<table>";




?>

</body>
<html>
