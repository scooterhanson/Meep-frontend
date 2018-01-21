<?php

////
// If the session variable for 'user' is not set, bump back to login.php
///

session_start();
if(!isset($_SESSION['user']))
{
    header("Location: /login.php");
    exit;
}
