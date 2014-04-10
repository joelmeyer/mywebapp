<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="style.css">
<title>Students</title>
</head>
<body>
<div id="header">
    <div class="headfont">
<a class="head" href="tstudents.php"> Goshen College</a>
    </div>
</div>
    <div id="headform">
<form action="tstudents.php" method="get">
<table class='form'>
<tr>
   <td> Class:</td>
   <td> <select name="class">
<option value="default">-
<?php
    include("/var/www/tour/web/classes.php");
    $que= getClasses();
    $num=mysql_num_rows($que);
    $i=0;
        for($i; $i<$num; $i++){
            $rows= mysql_fetch_array($que);
            $un= $rows['class_name'];
            echo"<option value='$un'>$un ";
        }
   ?>
    </select>
   <td> Filter Results:</td>
   <td> <select name="by">
        <option value="default">-
        <option value="HL">High to Low
        <option value="LH">Low to High
        <option value="DONE">Completed
        <option value="NOTDONE">Not Completed
    </select>
</td>
 <td> <input type=submit value="Go"></td>
</tr>
</table>
</form> 
</div>

<div class="left">
 <?php
    $uby= $_GET['by'];
    $by=mysql_real_escape_string($uby);
    $uclass= $_GET['class'];
    $class= mysql_real_escape_string($uclass);
    if($by=="NOTDONE"){
        $q= notDone($class);
    }
    elseif($by=="HL"){
        $q= orderHL($class);
    }
    elseif($by=="LH"){
        $q= orderLH($class);
    }
    elseif($by=="DONE"){
        $q= complete($class);
        
    }
    elseif(!$by || $by="default"){
        $q=allStu($class);
    }
    $num_results = mysql_num_rows($q);
    
   
   echo" <table border='1'>";
       echo" <tr><td class='clear'>Number of Students Found: ".$num_results."</td></tr><tr>";
      echo "<td>Student</td>";
      echo "<td>Left to Complete</td>";
     echo" </tr>";
    for ($i=0; $i<$num_results; $i++)
    {   
        $row = mysql_fetch_array($q);
        $uname=$row['username'];
        echo "<tr><td><a href='indiv.php?user=$uname'>";
        echo stripslashes($row["username"]);
        if($row["notDone"]>='3'){
            echo "</a></td><td class='notdone'>";
        }
        else
        {
            echo "</a></td><td class='done'>";
        }
        echo stripslashes($row["notDone"]);
        echo "</td> </tr>";
    }
    echo"</table>";
?>
</div>
    </body>
        </html>
