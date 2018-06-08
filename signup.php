<?php 

$page_title = 'RAINBOW - Sign Up Now';
include('includes/outside_headmaster.php');

?>
<div class="row">
<div class="large-12 columns">


<?php

// CHECK FOR A FORM SUBMISSION:
if (isset($_POST['submit']) && $_SERVER['REQUEST_METHOD'] === 'POST') 
{
	
// CHECK FOR A VALID EMAIL ADDRESS:
if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === $_POST['email']) 
{
$email = mysqli_real_escape_string($dbc,sanitize($_POST['email']));
} 
else 
{
$error[] = 'Please enter a valid email address!';
}

// CHECK FOR A VALID PASSWORD 
if (!empty($_POST['pass'])) 
{
if( !preg_match('~(?=.*[0-9])(?=.*[a-z])^[a-z0-9]{6,20}$~', $_POST['pass']) ) 
{
$error[] = 'Password must include at least one number,one letter and must be between 6 and 20 characters!';
}
else
{
$p = mysqli_real_escape_string($dbc,$_POST['pass']);
}
}

//IF THERE ARE NO ERRORS LET US GO AHEAD AND INSERT THE NEW USER INTO THE DATABASE
if (empty($error)) 
{ 
$status='TRIAL';

// MAKE SURE THE EMAIL ADDRESS IS AVAILABLE:
$r = mysqli_query($dbc, "SELECT tenant_email  FROM tenants WHERE tenant_email='$email' ");

// GET THE NUMBER OF RECORDS RETURNED:
// USE @ TO SUPPRESS ANY ERROR MESSAGES

$no_of_rows = @mysqli_num_rows($r);

if ($no_of_rows === 0) // NO TENANT EXISTS WITH SAME EMAIL
{


//BEGIN TRANSACTION - WE WANT TO REVERT ALL DATABASE INSERTS IF ANY FAILS
mysqli_autocommit($dbc,FALSE);

//INSERT THE NEW RECORD TENANT INTO THE DATABASE
//WE WANT EVERY NEW TENANT TO USE OUR APP FREE OF CHARGE FOR 15 DAYS
$r = mysqli_query($dbc, "INSERT INTO tenants (tenant_email,tenant_pass,tenant_status,tenant_registration_date,tenant_expiry_date) VALUES ('$email', '"  .  SHA1($p) .  "', '$status',NOW(), DATE_ADD(NOW(), INTERVAL 15 DAY))");
if (mysqli_affected_rows($dbc) === 1) 
{ 

	//RETRIEVE THE ID OF THE LAST INSERTED TENANT RECORD

	$lastinsertid=mysqli_insert_id($dbc);


	// GENERATE AN 18 CHARACTER TENANT TOKEN 

	$a = md5(uniqid(rand( ), true));
	$newa=substr($a,0,12);
	$newa=$newa.mt_rand(100000,200000);
	$tenant_token=sanitize(mysqli_real_escape_string($dbc,$newa));


//INSERT TENANT TOKEN

$r2=mysqli_query($dbc,"INSERT INTO tenant_tokens (fktk_tid,tenant_token,date_entered) VALUES ('$lastinsertid','$tenant_token',NOW())");

if(mysqli_affected_rows($dbc)==1)
{

//IF THE INSERT INTO TENANT TOKEN TABLE WAS SUCCESSFUL, COMPLETE (COMMIT) THE TRANSACTION
mysqli_commit($dbc);


//LOG THE NEW TENANT IN AT ONCE

$r3 = mysqli_query($dbc,"SELECT tenant_id, tenant_email,tenant_status,tenant_expiry_date FROM tenants
	WHERE (tenant_email='$email' AND tenant_pass=SHA1('$p'))");		

if (@mysqli_num_rows($r3) === 1) 
{ 

//STORE LOGIN INFO IN A SESSION
$_SESSION = mysqli_fetch_array($r3, MYSQLI_ASSOC);


$r4 = mysqli_query($dbc,"SELECT tenant_token FROM tenant_tokens WHERE fktk_tid={$_SESSION['tenant_id']} LIMIT 1");
if (@mysqli_num_rows($r4) === 1) 
{ 
//STORE TENANT TOKEN INFO IN A SESSION
//WE USE $_SESSION['TENANT_TOKEN'] INSTEAD OF SESSION TO AVOID OVERWRITING THE CONTENTS SESSION VARIABLE
$_SESSION['tenant_token'] = mysqli_fetch_array($r4, MYSQLI_NUM);
$_SESSION['welcome_message']=TRUE;
mysqli_close($dbc);

if(LIVE)
{

//STORE THE SIGN UP SUCCESS MESSAGE TO BE SHOWN TO THE USER.

$body='Thank you for signing up. You can use RAINBOW to manage your contacts easily and more effectively.
 If you have ANY trouble getting started with RAINBOW let us know. We will be delighted to help
 you get started.';

 // MAIL THE USER A WELCOME MESSAGE
mail($email, 'Welcome to RAINBOW', $body, 'From: admin@rainbow.com');  
} 


// REDIRECT THE TENANT TO THE FIRST PAGE OF THE APPLICATION:
$redirect = SITE_URL . 'contacts.php';

// CLEAN THE BUFFER.
ob_end_clean( ); 

header("Location: $redirect");

exit;
} 
else
{
//IF THE AUTOMATIC SIGN IN WAS NOT SUCCESSFUL, DESTROY THE SESSION VARIABLE
session_destroy();

//STORE ERROR MESSAGE IN A VARIABLE

$error[]='You could NOT be signed in automatically. You can &nbsp;<a href="signin.php">Click here</a>&nbsp; to sign in';

//ECHO ANY MYSQL ERRORS

//echo mysqli_error($dbc);
}
}//CLOSE IF @MYSQLI_NUM_ROWS($R3) === 1
else
{
session_destroy();

$error[]='Error! You could NOT be signed in automatically. You can<a href="signin.php">Click here</a>&nbsp; to sign in';

}
} // IF INSERT tenant token
else
{
$error[]='Sign up NOT successful';
//echo mysqli_error($dbc);

//ROLL BACK ANY INSERTS THAT MAY HAVE SUCCEEDED BECAUSE THE WHOLE SIGN UP PROCESS WAS NOT SUCCESSFUL
mysqli_rollback($dbc);
}
}// CLOSE IF(MYSQLI_AFFECTED_ROWS($DBC)==1) FOR TENANT TOKEN INSERT

else
{
$error[]='Sign up NOT successful';
//echo mysqli_error($dbc);
mysqli_rollback($dbc);
}
}// CLOSE IF(MYSQLI_AFFECTED_ROWS($DBC)==1) FOR TENANT INSERT
else
{// IF THE EMAIL IS NOT AVAILABLE
$notify[] = '&nbsp;This email address has already been &nbsp;registered. &nbsp;<a href="forgot_password">Forgot Password?</a>';	
}
}//CLOSE IF EMPTY ERROR

include('includes/alert.php');

} //CLOSE IF ISSET POST SUBMIT
?> 
</div></div>





<form  action="" autocomplete="on" method="POST"> 
<div id="customform">
<div class="medium-4 large-4 columns">
&nbsp;
</div>
<div class="medium-4 large-4 columns">
<br/><br/><br/>
<h3>Create a FREE account.</h3>

<fieldset>

<label>Email</label>
<input type="text" placeholder="Enter email" value="" required name="email" >
<label>Password <span class="smallred">Min.6 characters</span></label>
<input type="password" placeholder="Enter password" required value="" name="pass" maxlength="20">

<input type="submit" value="CREATE FREE ACCOUNT" name="submit" class="tiny radius button"/> </br>
Returning user?
<a href="signin.php" class=""> Sign in </a>
</fieldset>
</div>
<div class="medium-4 large 4 columns">
&nbsp;
</div>
</form>
</div></div>	
</div>
</div>  
<?php 
include ('includes/footer.html');
?>