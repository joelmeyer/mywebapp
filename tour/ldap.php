<?php

    //Takes a username and password
    //Returns data about a user in Goshen's ldap
    //in format [successful, lastName,firstName,studentID,username]
    //or [unsuccessful] if not successful
    function getLdapData($user, $pass){
        //Check failure states
        if(empty($user) || empty($pass)){
            return [false, 'Enter a username and password']; //Send some data next time
        }

        //LDAP stuff here; hold on to your butts
        $ldapServer = 'openldap.goshen.edu';
        $ldapconnection = ldap_connect($ldapServer);
        if($ldapconnection) {
            $userdistinguishedname = "uid=".$user.",ou=people,dc=goshen,dc=edu";
            //Try to bind to server
            try{
                $result = ldap_bind( $ldapconnection, $userdistinguishedname, $pass );
            }
            catch(Exception $e){
                return [false, "Wrong username or password"];
            }
            //echo $result;
            if(!$result){
               // echo "incorrect password / username";
                //Unbind connection
             //   ldap_unbind( $ldapconnection );
                return [false, "Wrong username or password"];
            }
        }

        //Searches for user with given username
        $accounts_searchResult = ldap_search( $ldapconnection, "ou=people,dc=goshen,dc=edu", "uid=".$user );
        
        //gets first user with that username
        $entry = ldap_first_entry( $ldapconnection, $accounts_searchResult );
        
        //gets attributes from that user
        $attrs = ldap_get_attributes($ldapconnection, $entry);

        //Assigns some 
        $lastName = $attrs['sn'][0];
        $firstName = $attrs['givenName'][0];
        $studentID = $attrs['gcid'][0];
        $username = $attrs['uid'][0];

        //Housecleaning
        ldap_free_result( $accounts_searchResult );
        ldap_unbind( $ldapconnection );

        //Returns array containing true and data needed for inserting a student 
        return [true, $lastName,$firstName,$studentID,$username];
    }

    //Check to see if given student is already in the database
    //Returns boolean
    function checkForStudent($lastName, $firstName, $studentID, $username){

        $con=openConnection();
        $ln=mysql_real_escape_string($lastName, $con);
        $fn=mysql_real_escape_string($firstName, $con);
        $stu_id=mysql_real_escape_string($studentID, $con);;
        $un=mysql_real_escape_string($username, $con);

        //check and see if its already in there
        $check= mysql_query("SELECT last_name, first_name, student_id, username
                FROM students 
                WHERE last_name='$ln' AND first_name='$fn' AND student_id= '$stu_id' AND username='$un'");
        if(!$check){
            // echo "error in executing query";
            return false;
        }

        if(mysql_num_rows($check)>0){
            //Student is in database
            return true;}

        //student is not in database
        return false;
        }
?>
