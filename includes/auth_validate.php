<?php

//If User is logged in the session['user_logged_in'] will be set to true
include('../utils.php');
//if user is Not Logged in, redirect to login.php page.
if (!isset($_SESSION['user_logged_in'])) {
	// echo "<script>window.location.href='login.php';</script>";
    redirect("login.php");
    exit;
}

 ?>