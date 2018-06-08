<?php 
$page_title = 'Log out';
require ('includes/inside_headmaster.php');

$_SESSION = array( ); // DELETE ALL THE SESSION VARIABLES.
session_destroy( ); // DESTROY THE SESSION ITSELF.


//BUILD URL TO REDIRECT USER.

$redirect = SITE_URL . 'signin.php'; 

//CLEAN THE BUFFER

ob_end_clean( ); 

//REDIRECT THE USER.

header("Location: $redirect");

//EXIT THE SCRIPT

exit; 
 ?>