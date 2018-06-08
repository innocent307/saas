<?php
//EDIT CONTACT PAGE
$page_title='Edit Contact';
include('includes/inside_headmaster.php');
?>
<script src="includes/js/jresources.js"></script>

<div class="row">
<div class="large-12 columns">
<?php

// CHECK FOR A VALID ID , THROUGH GET OR POST:
	if ( (isset($_GET['id'])) && (is_numeric($_GET['id'])) ) 
	{ // FROM VIEW_CONTACTS.PHP
	$id = $_GET['id'];
	} 
	elseif ( (isset($_POST['id'])) && (is_numeric($_POST['id'])) ) { // Form submission.
	$id = $_POST['id'];
	} else { // NO VALID ID, PRINT ERROR MESSAGE.
	$error[]='You have accessed this page in error';
	include('includes/alert.php');

	exit();
	}

	// RETRIEVE THE CONTACT INFO
	$result=mysqli_query($dbc,"SELECT * from cms WHERE $tenant AND id='$id' LIMIT 1");
	if (mysqli_affected_rows($dbc) == 1) 
	{
	$row = mysqli_fetch_array ($result,MYSQLI_ASSOC);
	}
	else{

	$error[]='You have accessed this page in error';
	include('includes/alert.php');
	exit;
	}


	
	if(isset($_POST['save']))
	{
	$fullname=$jobtitle=$website=$address=$email=$phone='';
	if(!empty($_POST['fullname']))
	{$fullname=sanitize($_POST['fullname']);}
	else
	{
	$error[]='Please enter a name';
	}

	if(!empty($_POST['phone']))
	{$phone=sanitize($_POST['phone']);}

	if(!empty($_POST['address']))
	{$address=sanitize($_POST['address']);}

	if(!empty($_POST['email']))
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
	}


if(empty($error))
{
$update=mysqli_query($dbc,"UPDATE cms SET fullname='$fullname', phone='$phone', email='$email', address='$address' WHERE (id=$id AND cms_id={$row['cms_id']} AND $tenant) LIMIT 1");

if($update)
{
$success[]= '<span class="whitestrong">Edit successful.</span> <a href="view_contact.php?id='.$id.'" class="whitestrong">&nbsp;
Go to edited contact</span></a>&nbsp;|&nbsp;<a href="contacts.php" class="whitestrong">Go to all contacts</span></a></span>';
	include('includes/alert.php');
	exit;
}
else{
$error[]= 'Edit NOT successful';
//echo mysqli_error($dbc);
}
}// CLOSE IF EMPTY ERROR
}// CLOSE IF ISSET POST SUBMIT

if(isset($_POST['cancel']))
{
$_POST=array();
}
include('includes/alert.php');
?>
</div></div>
<div class="row">
<div class="large-12 columns">
<div class="medium-6 large-6 columns">
<form></form><!--CREATE A DUMMY FORM BECAUSE THE JRESOURCES SCRIPT FILE REFERENCES FORM 1-->
<form action="" method="POST">
<div id="namesection">
<h5>Name</h5>
<input type="text" name="fullname" placeholder="Add a name" size="50" maxlength="50"
value="<?php if(isset($row['fullname'])){ echo $row['fullname'];} ?>" />
</div>
<div id="phonesection">
<h5>Phone</h5>
<p class="clone"><input type="text" placeholder="Add a phone number" id="phone" name="phone"  size="50" maxlength="20" value="<?php if(isset($row['phone'])) {echo $row['phone'];} ?>" /></p>
</div>
<div id="emailsection">
<h5>Email</h5>
<p class="email"><input type="text" placeholder="Add an email" id="email" name="email"  size="50" maxlength="50" value="<?php if(isset($row['email'])) {echo $row['email'];}?>" /></p>
</div>
<div id="addresssection">
<h5>Address</h5>
<p class="address"><input type="text" placeholder="Add an address" id="address" name="address"  value="<?php if(isset($row['address'])) { echo $row['address'];} ?>" /></p>
</div>
</div>
<!--COLUMN 2-->
<div class="medium-6 large-6 columns">
<div id="submitsection">
<input type="submit" name="save" class="thin radius button" value="SAVE" style="margin-left:0; width:30%" />
<input type="submit" name="cancel" class="thin radius button" value="CANCEL" style="margin-left:7px; width:30%"; />
</form>
</div>
</div>



</div>
</div>

<?php include('includes/footer.html');?>

