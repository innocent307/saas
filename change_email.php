<?php 
	$page_title='Change email';
	include('includes/inside_headmaster.php');
	?>
	
	<div class="row">
	<div class="large-12 columns">
	<div class="medium-3 large-3 columns">&nbsp;</div>
	<div class="medium-6 large-6 columns">
	<?php
	
	if(isset($_POST['submit']))
	{
	if(empty($_POST['email']))
	{
	$error[]=' Please enter your email';
	}
	else{
	$email=mysqli_real_escape_string($dbc,sanitize($_POST['email']));
	}
	
	if(empty($error))
	{
	//CHECK IF THE NEW EMAIL IS ALREADY IN USE
	$r = mysqli_query($dbc, "SELECT tenant_email  FROM tenants WHERE tenant_email='$email' and tenant_id != $tenant_id ");
	// GET THE NUMBER OF ROWS RETURNED:
	$rows = @mysqli_num_rows($r);
	if ($rows === 0) 
	{ // NO TENANT EXISTS WITH SAME EMAIL
	$update=mysqli_query($dbc,"UPDATE tenants SET tenant_email='$email' WHERE tenant_id=$tenant_id LIMIT 1");
	}
	else
	{
	$error[]='This email already exists';	
	}

	if($update)
	{
	//UPDATE THE EMAIL STORED IN THE SESSION
	$_SESSION['tenant_email']=$email;
	$success[] = 'Update successful. New email:&nbsp;'.$email;
	mysqli_close($dbc);
	}
	else{
	$error[]='Update NOT successful';
	////echo mysqli_error($dbc);
	}
	}//CLOSE IF EMPTY ERROR
	}//CLOSE IF ISSET POST SUBMIT
	include('includes/alert.php');
	?>
	

	<form method="POST" action="" >
	<p><label>Email</label></p>
	<input type="text" name="email" size="40" maxlength="50" value="<?php if(!empty($_SESSION['tenant_email'])){ echo trim($_SESSION['tenant_email']);}?>">
	<p><input type="submit" value="SAVE" name="submit" class="thin radius button"></p>
	</form>
	</div>
	<div class="medium-3 large-3 columns">&nbsp;</div>
	</div></div>
	<?php include('includes/footer.html');?>





