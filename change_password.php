<?php 


		 $page_title = 'Change Your Password';
		 include ('includes/inside_headmaster.php');
 		echo '<div class="row">
 		<div class="large-12 columns">';
        
		 // REDIRECT IF NO USER ID

		 if (!isset($_SESSION['tenant_token']) && !isset($_SESSION['fk_tenant_token']) ) 
		 {
		 $redirect = SITE_URL . 'signin.php';		// DEFINE THE URL.
		 ob_end_clean( ); // DELETE THE BUFFER.
		 header("Location: $redirect");
		 exit( ); // QUIT THE SCRIPT.
		 }
		if (isset($_POST['submit']) && $_SERVER['REQUEST_METHOD'] === 'POST') {

		// CHECK FOR A NEW PASSWORD AND MATCH AGAINST THE CONFIRMED PASSWORD:
		$p = FALSE;
		
		//PASSWORD MUST BE ALPHANUMERIC AND HAVE A LENGTH BETWEEN 6 AND 20 CHARACTERS

		if( preg_match('~(?=.*[0-9])(?=.*[a-z])^[a-z0-9]{6,20}$~', $_POST['password1']) ) 
		{
		if ($_POST['password1'] == $_POST['password2']) 
		{
		 $p = mysqli_real_escape_string($dbc, sanitize($_POST['password1']));
		} 
		else 
		{
		$error[]='Your password did not match the confirmed password!';
		}
		} 
		else 
		{
		 $error[]='Please enter a valid password!';
		}
		
		if ($p) 
		{ // IF EVERYTHING'S OK.
		// UPDATE THE RECORD
		$update=mysqli_query($dbc,"UPDATE tenants SET tenant_pass=SHA1('$p') WHERE tenant_id=$tenant_id LIMIT 1");
		}
		if ($update) 
		{ 
				
		$success[]='Password changed successfully.&nbsp';
		include('includes/alert.php');
		mysqli_close($dbc); // CLOSE THE DATABASE CONNECTION.
		//echo "<meta http-equiv=\"refresh\" content=\"0;URL='settings.php'\">";
		} 
		else 
		{ // IF IT DID NOT RUN OK.

		$error[]='Password change NOT successful.';
		 include('includes/alert.php');
		}
		} // END OF THE MAIN SUBMIT CONDITIONAL.
		?>
		 <div class="row">
		 <div class="large-12 columns">
		 <div class="medium-4 large-4 columns">&nbsp;</div>

		<div class="medium-4 large-4 columns">
		<h3>Change Your Password</h3>
		<form action="change_password.php" method="post">
		<fieldset></br>
		<input type="password" name="password1" size="20" maxlength="20" placeholder="New password"/> </p>
		<input type="password" name="password2" size="20" maxlength="20" placeholder="Confirm new password"/></p>
		</fieldset>
		</br>
		<input type="submit" name="submit" class="thin radius button" value="Change My Password" />
		</form>
		</div>
		<div class="medium-4 large-4 columns">
		&nbsp;
		</div>
		</div>
		</div>


		<?php include ('includes/footer.html');
		?>		