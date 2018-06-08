
<?php 
 	$page_title='Forgot Your Password';
 	include ('includes/outside_headmaster.php');
 	echo'<div class="row">

 	<div class="large-12 columns">';
 	if ($_SERVER['REQUEST_METHOD'] == 'POST') 
 	{

 	//INITALISE TENANT ID
 	$tid=false;
 
 	// CHECK THE EMAIL
 	if (!empty($_POST['email'])) 
 	{
     $e=mysqli_real_escape_string($dbc,sanitize($_POST['email']));
	
	// CHECK IF THE SUPPLIED MAIL ADDRESS EXISTS
 	$q = "SELECT tenant_id FROM tenants WHERE tenant_email='$e'";
	$r = mysqli_query ($dbc, $q);

	if (@mysqli_num_rows($r) == 1) 
	 { 
	//RETRIEVE THE TENANT ID:
	 list($tid) = mysqli_fetch_row($r);
	 } 
	 else 
	 { // NO DATABASE MATCH MADE.
	 $error[]='The submitted email address does not match any on file';
	 }
	 } //CLOSE IF NOT EMPTY POST EMAIL
	 else 
	 { // NO EMAIL!
	 $error[]= 'You forgot to enter your email address!';
	 } // END OF IF EMPTY($_POST['EMAIL']).

	 if ($tid) 
	 { // IF EVERYTHING'S OK.

	 // CREATE A NEW, RANDOM PASSWORD:
	$p = substr (md5(uniqid(rand(),true)), 3, 10);

	 // UPDATE THE RECORD:
	 $q = "UPDATE tenants SET tenant_pass=SHA1('$p') WHERE tenant_id=$tid LIMIT 1";
	 $r = mysqli_query ($dbc, $q);

	 if (mysqli_affected_rows($dbc) ==1) 

	 	{ // IF THE UPDATE WAS SUCCESSFUL

		 // SEND AN EMAIL:
		$body = "Your password has been temporarily changed to '$p'. Please sign in using this password and this email address. We strongly advise that you change your passoword afterwards.";
	 	if(LIVE)
	 	{
		mail ($e, 'Your temporary password.', $body,'From: admin@rainbow.com');
	    }
	    else
	    {
		echo $body;
		}
		 // PRINT A MESSAGE INFORMING THE USER OF THE SUCCESSFUL PASSWORD CHANGE
		echo 'Your password has been changed. You will receive the new, temporary password at the email address with which
		you registered. Once you have logged in with this password, you may change it by clicking on  "Account=>Settings"
		link.';
		 mysqli_close($dbc);
		 include('includes/alert.php');
		 include ('includes/footer.html');
		 exit;
		} 
		
     else 
	 { // IF THE UPDATE WAS NOT SUCCESSFUL
	$error[]='Your password could not be changed due to a system error. We apologize for any inconvenience.';
	 }
	 } //CLOSE IF TID
	 else 
	 { // NO VALID TENANT ID.
	 $error[]='Please try again.';
	 }
      include('includes/alert.php');


	 } // END OF IF ISSET POST SUBMIT
	 ?>
	<div class="medium-4 large-4 columns">&nbsp;<!--THIS AND NO BREAKS SPACE IS VERY NECESSARY BECAUSE OF A BUG WITH FOUNTAIN 5-->
	</div>
	<div class="medium-4 large-4 columns">

	<h4>Reset Your Password</h4>
	<p>Enter your email address below and your password will be reset.</p>
	<form action="forgot_password.php" method="post">
	</br>
	<p> 
	<label >Your email </label>
	<input id="email" name="email" required="required" type="text" 
	value="<?php if(isset($_POST['email'])){ echo $_POST['email'];} ?>" placeholder="Your email"/>
	</p>
	<input type="submit" name="submit" value="Reset My Password" class="thin radius button"/>
	</form>
	</div>
		<div class="medium-4 large-4 columns">
	</div>
	</div>
	</div>
	

	<?php include ('includes/footer.html');
	?>