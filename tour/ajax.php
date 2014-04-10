<?php  
    include("/var/www/tour/dbquery.php");
    include("/var/www/tour/ldap.php");
    if($_GET['action']=='insertStu'){
        $ln = $_POST["lastname"];
        $fn = $_POST["firstname"];
        $stu_id= $_POST["stu_id"];
        $user= $_POST["username"];
        $lname = mysql_real_escape_string($ln);
        $fname = mysql_real_escape_string($fn);
        $id = mysql_real_escape_string($stu_id);
        $un = mysql_real_escape_string($user);
        if(empty($lname) && empty($fname) && empty($id) && empty($un)){
            
            die('empty data sent');
        }
        
        echo insertStudent($lname, $fname, $id, $un);
        printf("Records added to students: %d\n", mysql_affected_rows());
    }
    elseif($_GET['action']=='recordLoc'){
        $id = $_POST["stu_id"];
        $loc = $_POST["loc_id"];
        $stu_id = mysql_real_escape_string($id);
        $loc_id = mysql_real_escape_string($loc);
        if(empty($stu_id) && empty($loc_id)){
            die('empty data sent');
        }
        echo recordLocation($stu_id, $loc_id); 
        printf("Records added to stu_loc_m2m: %d\n", mysql_affected_rows());
    }
    elseif($_GET['action']=='completionCheck'){
        $resp= checkCompletion();
        echo $resp;
    }
        
    elseif($_GET['action']=='numberDone'){
        $resp= studentsDone();
        echo $resp;
    }
    elseif($_GET['action']=='studentCheck'){
        $un = $_POST["username"];
        $user = mysql_real_escape_string($un);
        if(empty($user)){
            die('empty data sent');
        }
       echo checkStudent($user);
        printf("got down here");
    }
    elseif($_GET['action']=='logIn'){
        //Cleans user and pass
        $user =  mysql_real_escape_string($_POST['un']);
        $pass =  mysql_real_escape_string($_POST['pass']);
        //Check failure states
        if(empty($user) || empty($pass)){
            //Didn't put information in one of the fields
            echo '{"confirmed": false, "status" : "empty data sent"}';
            return;
        }

        //Uses ldap to get user data

        $studentdata = getLdapData($user, $pass);

        //Checks if successful

        if($studentdata[0] == false){
            echo '{"confirmed": false, "status" : "wrong password or user"}';
            //wrong password or user;
            return;
        }

        //Checks if student exists

        if(checkForStudent($studentdata[1], $studentdata[2], $studentdata[3], $studentdata[4]) == false){
            //Not in the database? Then insert 'em.

            insertStudent($studentdata[1], $studentdata[2], $studentdata[3], $studentdata[4]);
        }

        //Existing student

        echo '{"confirmed": true, "status" : "student confirmed", "student_id":'.$studentdata[3].'}';
        //return true;
                
    }
    elseif($_GET['action']=='insertCourse'){
        echo insertCourseData();
        printf("Courses Inserted:");
    }
    elseif($_GET['action']=='test'){
        $r= testDB();
        print_r($r);
    }
    elseif($_GET['action']=='insertUser'){
        echo insertUserData();
        printf("User Inserted: %d", mysql_affected_rows());
    }
    elseif($_GET['action']=='updateUser'){
        echo updateUserData();
        printf("User updated: %d", mysql_affected_rows());
    }
?>
