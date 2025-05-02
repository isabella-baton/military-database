<?php

    #start session
    session_start();

    #initialize the connection
    include '../includes/database-connection.php';

    #un-set all session variables
    $_SESSION = array();

    #destroy session
    session_destroy();

    #send back to login
    header("Location: login.php");
    exit;

?>