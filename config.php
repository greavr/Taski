<?php
    // Connection's Parameters
    $db_host="taskus.db.7613008.hostedresource.com";
    $db_name="taskus";
    $username="taskus";
    $password="S0ldner02";
    $db_con=mysql_connect($db_host,$username,$password);
    $connection_string=mysql_select_db($db_name);
    // Connection
    $db_handle = mysql_connect($db_host,$username,$password);
    $db_found = mysql_select_db($db_name,$db_handle);
    
    //Attachment Options
    $filepath= "http://" . $_SERVER["SERVER_NAME"] . dirname($_SERVER["REQUEST_URI"]). "/files/";
?>