<?php

	// Error Reporting

	ini_set('display_errors', 'On');
	error_reporting(E_ALL);


	$sessionUser = '';
	$sessionAvatar = '';
	
	if (isset($_SESSION['user'])) {
		$sessionUser = $_SESSION['user'];
		$sessionAvatar = $_SESSION['avatar'];
	}

	// Routes

	$css 	= 'layout/css/'; // Css Directory

	// Include The Important Files
