<?php

////
// Kill the session and bump back to login.php
////

session_start();
if(session_destroy())
{
	header("Location: login.php");
}
?>
