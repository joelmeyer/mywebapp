<?php
function openConnection()
{
    include("/var/www/html/tour/config.php");
	if(!$con) {
	die('Could not connect: ' . mysql_error());
	}
	mysql_select_db("raspi", $con);
	return $con;
}


function queryToArray($qs)
{
	$con=openConnection();
	$result = $qs;
	$rows=array();
	
	$row = mysql_fetch_array($result);
	while($row) {
		$rows[]=$row;
		$row = mysql_fetch_array($result);
	}

	mysql_close($con);
	return $rows;
}


function insertStudent($lastName, $firstName, $studentID, $username)
{
	
	$con=openConnection();
	$ln=mysql_real_escape_string($lastName, $con);
	$fn=mysql_real_escape_string($firstName, $con);
	$stu_id=mysql_real_escape_string($studentID, $con);;
	$un=mysql_real_escape_string($username, $con);

		
	//check and see if its already in there
	$check= mysql_query("SELECT last_name, first_name, student_id, username
			 FROM students 
				WHERE last_name='$ln' AND first_name='$fn'
				AND student_id= '$stu_id' AND username='$un'");
	if(!$check){
		
	error_log('query failed to execute');
    die();

	}

	if(mysql_num_rows($check)>0){

	echo "Entry already exists";

	}
	//if not in there, insert new student
    else {
        $ch= mysql_query("SELECT student_id, username
			 FROM students 
				WHERE student_id= '$stu_id' AND username='$un'");
	    if(!$ch){
		
	    error_log('query failed to execute');
        die();
	    }   

	    if(mysql_num_rows($ch)>0){
            $re = "UPDATE students SET last_name='$ln', first_name='$fn'
                WHERE student_id='$stu_id' AND username='$un'";

            $req=mysql_query($re);
            return $req;
        }
        else{
            $request = "INSERT INTO students (last_name, first_name, student_id, username)
                VALUES ('$ln', '$fn','$stu_id', '$un')";

            $r=mysql_query($request);
            return $r;
        }
    }
}
function recordLocation($studentID, $locationID){
	
	$con=openConnection();

	$stu=mysql_real_escape_string($studentID, $con);
	$loc=mysql_real_escape_string($locationID, $con);
	
	//check and see if its already in there
	$check= mysql_query("SELECT student_id, loc_id FROM stu_loc_m2m 
				WHERE student_id= '$stu' AND loc_id='$loc'");
	if(!$check){
		
	die('query failed to execute');

	}

	if(mysql_num_rows($check)>0){

	echo "Entry already exists";
	$u = mysql_fetch_array($check);
	print_r($u);		

	}

	else {
	$qs= "INSERT INTO stu_loc_m2m (student_id, loc_id)
		VALUES('$stu','$loc')";

	$q=mysql_query($qs);
	return $q;
	}

}

function checkCompletion(){

    //check and see if student has finished more than 15 locations
    //not used currently
    //other query instead
    openConnection();

	$query = "SELECT last_name, first_name
	FROM stu_loc_m2m
	LEFT JOIN students
	ON stu_loc_m2m.student_id=students.student_id
	RIGHT JOIN locations
	ON stu_loc_m2m.loc_id=locations.loc_id
	GROUP BY stu_loc_m2m.student_id
	HAVING COUNT(DISTINCT stu_loc_m2m.loc_id)>=15";

	$q=mysql_query($query);
	return $q;

}

function numberDone(){
    
    //name of student and number finshed
    //not used because uses names
	openConnection();
    $query= "SELECT last_name, first_name, COUNT(DISTINCT stu_loc_m2m.loc_id) 
    AS numberComplete FROM stu_loc_m2m
	LEFT JOIN students
	ON stu_loc_m2m.student_id=students.student_id
	GROUP BY stu_loc-m2m.student_id
	ORDER BR numberCompleted DESC";
	
	$q= mysql_query($query);
	return $q;
}

function checkStudent($un){

	//finds just the location name completed by a username

    openConnection();
    $uni= mysql_real_escape_string($un);
	$query = "SELECT loc_name AS loc
	FROM stu_loc_m2m
	LEFT JOIN students
	ON stu_loc_m2m.student_id=students.student_id
	RIGHT JOIN locations
	ON stu_loc_m2m.loc_id=locations.loc_id
	WHERE username='$uni'";

    $q= mysql_query($query);
    return $q;

}

function studentNot($un){

    //returns location names for uncompleted sites for a username
    openConnection();
    $uni= mysql_real_escape_string($un);
    $query = "SELECT loc_name FROM locations WHERE loc_name
        NOT IN(SELECT loc_name FROM locations JOIN stu_loc_m2m
        ON locations.loc_id=stu_loc_m2m.loc_id
        JOIN students
        ON stu_loc_m2m.student_id=students.student_id
        WHERE username='$uni')";
    
    $q= mysql_query($query);
    return $q;



}
function queriesTests($stu_id){

    ///unused queries that I didn't want to delete quite yet
    openConnection();
    $uni= mysql_real_escape_string($stu_id);
    $que= "SELECT loc_name FROM locations 
        WHERE loc_id not in (SELECT loc_id FROM stu_loc_m2m 
        WHERE student_id=$uni)";

    $qu= "SELECT username, (SELECT count(loc_name) FROM locations 
        WHERE loc_id NOT IN (SELECT loc_id FROM stu_loc_m2m WHERE student_id=$uni))
        AS notDone FROM students WHERE student_id=$uni";
}
function allStu($class){
    
    //displays all students' usernames and location counts
    openConnection();
    if($class=='default' || empty($class)){
    $query= "SELECT username,(SELECT COUNT(loc_name) FROM locations
        WHERE loc_id NOT IN (SELECT loc_id FROM stu_loc_m2m WHERE stu_loc_m2m.student_id=students.student_id)) 
        AS notDone from students";
    }
    else{
     $query= "SELECT username,(SELECT COUNT(loc_name) FROM locations
        WHERE loc_id NOT IN (SELECT loc_id FROM stu_loc_m2m WHERE stu_loc_m2m.student_id=students.student_id)) 
        AS notDone from students 
        WHERE class_id=(SELECT class_id FROM classes WHERE class_name= '$class')";
        }
    $q = mysql_query($query);
    return $q;

}

function notDone($class){        
    
    //displays all students that have more than 3 locations left to 
    //complete
    openConnection();
    if($class == 'default'){
    $query= "SELECT username,(SELECT COUNT(loc_name) FROM locations
        WHERE loc_id NOT IN (SELECT loc_id FROM stu_loc_m2m 
        WHERE stu_loc_m2m.student_id=students.student_id)) 
        AS notDone from students
        HAVING notDone>3";
    }
    else{
    $query= "SELECT username,(SELECT COUNT(loc_name) FROM locations
        WHERE loc_id NOT IN (SELECT loc_id FROM stu_loc_m2m 
        WHERE stu_loc_m2m.student_id=students.student_id)) 
        AS notDone from students WHERE class_id=(SELECT class_id FROM classes WHERE class_name= '$class')
        HAVING notDone>3";
    }
    $q = mysql_query($query);
    return $q;

}
function orderUNd(){

    //displays username and count desc
    openConnection();
    $query=" SELECT username,(SELECT COUNT(loc_name) FROM locations
             WHERE loc_id NOT IN (SELECT loc_id FROM stu_loc_m2m WHERE stu_loc_m2m.student_id=students.student_id))
             AS notDone from students ORDER BY username DESC";
    
    $q = mysql_query($query);
    return $q;

}
function orderLH($class){

    //displays username and count with count from Low to High
   openConnection();
   if($class == 'default'){
   $query=" SELECT username,(SELECT COUNT(loc_name) FROM locations
            WHERE loc_id NOT IN (SELECT loc_id FROM stu_loc_m2m WHERE stu_loc_m2m.student_id=students.student_id))
            AS notDone from students ORDER BY notDone";
   }
   else{
   $query=" SELECT username,(SELECT COUNT(loc_name) FROM locations
            WHERE loc_id NOT IN (SELECT loc_id FROM stu_loc_m2m WHERE stu_loc_m2m.student_id=students.student_id))
            AS notDone from students WHERE class_id=(SELECT class_id FROM classes WHERE class_name= '$class') 
            ORDER BY notDone";
   }
   $q = mysql_query($query);
   return $q;
}
function orderHL($class){

    //displays username and count with count from high to low
    openConnection();
if($class == 'default')
{   $query=" SELECT username,(SELECT COUNT(loc_name) FROM locations
             WHERE loc_id NOT IN (SELECT loc_id FROM stu_loc_m2m WHERE stu_loc_m2m.student_id=students.student_id))
             AS notDone from students ORDER BY notDone DESC";
}
else{ $query=" SELECT username,(SELECT COUNT(loc_name) FROM locations
             WHERE loc_id NOT IN (SELECT loc_id FROM stu_loc_m2m WHERE stu_loc_m2m.student_id=students.student_id))
             AS notDone from students 
             WHERE class_id=(SELECT class_id FROM classes WHERE class_name= '$class') ORDER BY notDone DESC";
}   
    $q = mysql_query($query);
    return $q;

}
function complete($class){

    //displays users that have 3 or less locations to complete
    openConnection();
if($class == 'default'){
	$query ="SELECT username,(SELECT COUNT(loc_name) FROM locations
            WHERE loc_id NOT IN (SELECT loc_id FROM stu_loc_m2m WHERE stu_loc_m2m.student_id=students.student_id))
            AS notDone from students
	        HAVING notDone<=3";
}
else{
	$query ="SELECT username,(SELECT COUNT(loc_name) FROM locations
            WHERE loc_id NOT IN (SELECT loc_id FROM stu_loc_m2m WHERE stu_loc_m2m.student_id=students.student_id))
            AS notDone from students WHERE class_id=(SELECT class_id FROM classes WHERE class_name= '$class')
	        HAVING notDone<=3";
}
	$q=mysql_query($query);
	return $q;
}
function openCon(){
   include("/var/www/tour/connect.php"); 
    if(!$con) {
        die('Could not connect:');
    }
	return $con;
}
function getCourseData(){
    //connect to data base
    //run peters query
   openCon();
   $q="select mu.username as username, mc.id as courseid, mc.fullname as coursename, mu.idnumber as student_id
        from mdl_user as mu
        join mdl_user_enrolments mue
        on mu.id = mue.userid
        join mdl_enrol as me
        on mue.enrolid = me.id
        join mdl_course as mc
        on me.courseid = mc.id
        where mc.fullname like 'CORE100-%-FA1314%'";
    $qu= pg_query($q);
    return $qu;
}
function testDB(){
  $con=  openCon();
   $q=pg_query("select mu.username as username, mc.id as courseid, mc.fullname as coursename, mu.idnumber as studetn_id
        from mdl_user as mu
        join mdl_user_enrolments mue
        on mu.id = mue.userid
        join mdl_enrol as me
        on mue.enrolid = me.id
        join mdl_course as mc
        on me.courseid = mc.id
        where mc.fullname like 'CORE100-%-FA1314%'");

	$result = $q;
	$rows=array();
	
	$row = pg_fetch_array($result);
	while($row) {
		$rows[]=$row;
		$row = pg_fetch_array($result);
	}

	pg_close($con);
	return $rows;
}




function insertCourseData(){
    $con= openConnection();
    $data = getCourseData();
    $num = pg_num_rows($data);
    for($i=0; $i<$num; $i++){
       $row= pg_fetch_array($data);
       $cid= $row['courseid'];
       $cn= $row['coursename'];
       $check= mysql_query("SELECT class_name, class_id
           FROM classes 
				WHERE class_name='$cn' AND class_id='$cid'", $con);
       if(!$check){	
           die('query failed to execute');
       }

       if(mysql_num_rows($check)>0){
           echo "Entry already exists";
       }
       else {
           $qs= "INSERT INTO classes (class_name, class_id)
		VALUES('$cn','$cid')";

           mysql_query($qs, $con);
       }
    }
    var_dump($row);
}
function insertUserData(){
    $con= openConnection();
    $data = getCourseData();
    $num = pg_num_rows($data);
    
    for($i=0; $i<$num; $i++){
       $row= pg_fetch_array($data);
       $cid= $row['courseid'];
       $un= $row['username'];
       $stu_id= $row['student_id'];
       $check= mysql_query("SELECT username
           FROM students 
				WHERE username='$un' AND class_id='$cid' AND student_id='$stu_id'", $con);
       if(!$check){	
           die('query failed to execute');
       }

       if(mysql_num_rows($check)>0){
           echo "Entry already exists";
       }
       else {
           $ch= mysql_query("SELECT student_id
               FROM students 
               WHERE student_id= '$stu_id'");
           if(!$ch){		
               die('query failed to execute ch');
           }   
           if(mysql_num_rows($ch)>0){
               $re = "UPDATE students SET username='$un', class_id='$cid'
                WHERE student_id='$stu_id'";

               $req=mysql_query($re);
               return $req;
           }
           else{
               $qs= "INSERT INTO students (username, class_id, student_id)
                   VALUES('$un','$cid','$stu_id')";
               echo $qs;       
               mysql_query($qs, $con);
               echo mysql_error();
           }
       }
    }
}

function updateUserData(){
    $con= openConnection();
    $data = getCourseData();
    $num = pg_num_rows($data);
   var_dump($num); 
    for($i=0; $i<$num; $i++){
       $row= pg_fetch_array($data);
       $cid= $row['courseid'];
       $un= $row['username'];
       $stu_id= $row['student_id'];
       var_dump($cid);
       var_dump($un);
     var_dump($stu_id);  
       mysql_query("UPDATE students SET class_id='$cid'
                WHERE student_id='$stu_id'", $con);
       
      
    }
}

?>

