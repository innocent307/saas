<?php 
$page_title='RAINBOW - Sign in';
include('includes/outside_headmaster.php');
?>
<?php 
// CHECK FOR A FORM SUBMISSION:
if (isset($_POST['submit']) && $_SERVER['REQUEST_METHOD'] === 'POST') 
{
// VALIDATE THE EMAIL ADDRESS:
if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) 
{
$e =  mysqli_real_escape_string($dbc,sanitize($_POST['email']));
} 
else 
{
$error[] = 'Please enter a valid email address!';
}
// VALIDATE THE PASSWORD:
if (!empty($_POST['pass'])) 
{
$p = $_POST['pass'];
}
else 
{
$error[] = 'Please enter your password!';
}

if (empty($error)) 
{

//QUERY FOR TENANT
$r = mysqli_query($dbc,"SELECT tenant_id,tenant_status,tenant_email,tenant_expiry_date FROM tenants 
	WHERE (tenant_email='$e' AND tenant_pass=SHA1('$p'))");	

//IF QUERY FOR TENANT RETURNED A RESULT
if (@mysqli_num_rows($r) === 1) 
{ 
$_SESSION = mysqli_fetch_array($r, MYSQLI_ASSOC);//STORE LOGIN INFO IN A SESSION

$r2=mysqli_query($dbc,"SELECT tenant_token FROM tenant_tokens WHERE fktk_tid={$_SESSION['tenant_id']}");
if(@mysqli_num_rows($r2)===1)
{
$_SESSION['tenant_token']=mysqli_fetch_array($r2,MYSQLI_NUM);
mysqli_close($dbc);

   
// REDIRECT THE tenant:
$redirect = SITE_URL . 'contacts.php';
// DEFINE THE URL.
ob_end_clean( ); // CLEAN THE BUFFER.
header("Location: $redirect");
exit(); 
}//CLOSE @MYSQLI_NUM_ROWS($R2) === 1
else{

$error[]='Sign in NOT successful';
}
}//CLOSE @MYSQLI_NUM_ROWS($R) === 1
else{

$error[]='Could not verify this account. The email address and password entered do not match those 
on file.&nbsp;<a href="forgot_password.php" class="white">Forgot password?</a>';
echo mysqli_error($dbc);

}

}//CLOSE IF EMPTY ERROR
mysqli_close($dbc);
include('includes/alert.php');
} // END OF SUBMIT CONDITIONAL.
?>




<div class="row">
<div class="large-12 columns">
<div id="customform">
<div class="medium-4 large-4 columns">
&nbsp;
</div>
<div class="medium-4 large-4 columns">
<form action="" method="POST">
<p class="blue">Welcome back! Sign in</p>
<fieldset>

<label>Email</label>
<input type="text"  required placeholder="Enter email" value="" name="email">
<label>Password</label>
<input type="password" required placeholder="Enter password" value="" name="pass">

<input type="submit" value="Sign in" name="submit" class="tiny radius button"/> 

</div>
<div class="medium-4 large-4 columns">&nbsp;</div>
</fieldset>

</form>

</div>
</div>
</div>

<?php 
include ('includes/footer.html');
?>