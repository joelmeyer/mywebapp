<?php
include("/var/www/html/tour/dbquery.php");
function getClasses()
{
    openConnection();
    $query = "SELECT class_name FROM classes";

    $q= mysql_query($query);
    return $q;
}

function classList($class)
{
    openConnection();
    $query = "SELECT username, (SELECT COUNT(loc_name) FROM locations
        WHERE loc_id NOT IN (SELECT loc_id FROM stu_loc_m2m
        WHERE stu_loc_m2m.student_id=students.student_id))
        AS notDone
        FROM students JOIN classes 
        ON classes.class_id=students.class_id
        WHERE class_name ='$class'";
    $q= mysql_query($query);
    return $q;


}



