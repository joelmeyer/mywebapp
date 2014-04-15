<?php

//takes a username and password
//returns data about user in Goshen's ldap
//in format [successful, lastName, firstName,username]
//or [unsuccessful] if not successful

function getLdapData($user, $pass){

    if(empty($user) || empty($pass)){
        return [false, 'Enter a username and password'];
    }

    //LDAP stuff here
    
    $ldapconnection = ldap_connect($ldapServer);
    if($ldapconnection) {
       
        //bind to server
        try {
            $result = ldap_bind($ldapconnection, $userdistinguishedname, $pass);
        }
        catch(Exception $e){
            return [false, "Wrong username or password"];
        }
        if(!$result){
            return[false, "Wrong username or password"];
        }
    }

    //search for user
    $accounts_searchResult = ldap_search

    //gets first user with that username
    $entry = ldap_first_entry($ldapconnection, $accounts_searchResult);

    //gets attributes
    $attrs = ldap_get_attributes($ldapconnection, $entry);

    //assign
    $lastName = $attrs['sn'][0];
    $firstName = $attrs['givenName'][0];
    $username = $attrs['uid'][0];
    $group = $attrs['eduPersonPrimaryAffiliation'][0];

    ldap_free_result($accounts_searchResult);
    ldap_unbind($ldapconnection);
    
    return [true, $lastName, $firstName, $username, $group];
}

function checkForUser($lastName, $firstName, $username){
    $un = mysql_real_escape_string($username);

    $check= mysql_query("SELECT user_id FROM users WHERE username='$un'");
    if(!$check){
        return false;
    }
    if(mysql_num_rows($check) > 0){
        //user is in database
        return mysql_fetch_assoc($check)['user_id'];
    }
    //user is not in database
    return false;
}
?>
