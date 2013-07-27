<?
	session_start();
	
	session_unset();
	
	session_destroy();
	$return = $_GET['return'];
	header("location: $return");

?>