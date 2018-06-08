<?php 
	$page_title='View Contact';
	include('includes/inside_headmaster.php');
	include('includes/action_success.php');

  	?>
	<script src="includes/js/jresources.js"></script>
	</br>
    <div class="row">
    <div class="large-12 columns">
    <form action="" method="POST">
    &nbsp;
    </div>
	<?php
	// CHECK FOR A VALID CID , THROUGH GET OR POST:
	if (isset($_GET['id']) && is_numeric($_GET['id']) ) 
	{
	$id = $_GET['id'];
	} 
	elseif (isset($_POST['id']) && is_numeric($_POST['id']) ) 
	{ // Form submission.
	$id = $_POST['id'];
	} 
	else 
	{ // NO VALID ID, PRINT MESSAGE AND EXIT.
	$error[]='You have accessed this page in error';
	include('includes/alert.php');
	exit;
	}
	?>

   </div></div>
	<?php

	// RETRIEVE THE CONTACT'S INFORMATION:
	$result=mysqli_query($dbc,"SELECT * FROM cms WHERE id='$id' AND $tenant  LIMIT 1");
	if (@mysqli_num_rows($result) == 1) 
	{
	// VALIDATE CONTACT ID, SHOW THE FORM.
	// GET THE USER'S INFORMATION:
	$row = mysqli_fetch_array ($result,MYSQLI_ASSOC);

	echo '<div class="row"><div class="large-12 columns">
	<div class="medium-4 large-4 columns">
	<a href="edit_contact.php?id='.$row['id'] .'" class="thin radius button">Edit</a>
	</div>
	<div class="medium-7 large-7 columns">
		&nbsp;

	
	</div>
	<div class="medium-1 large-1 columns">
	<input type="submit" name="delete"  class="thin radius button alert" value="Delete" onclick="return confirmdelete();">

	
	</div>


	</div></div>';


	echo'<div class="row">
    <div class="large-12 columns">';



	//DELETE CONTACT

	if(isset($_POST['delete']))
	{

	$sql = "UPDATE cms SET status='del' WHERE id=$id AND $tenant";
	$delete_result = mysqli_query($dbc,$sql) ;
	if ($delete_result) 
	{
	$sql2 = "UPDATE group_members SET status='del' WHERE (fkcms_cid=$del_id AND $tenant)";//DONT FORGET TO UPDATE THE GROUP_MEMBERS TABLE AS WELL
	$delete_result2 = mysqli_query($dbc,$sql2);

	$_SESSION['delete_success']=1;		//WE USE THIS VARIABLE TO ECHO A SUCCESS MESSAGE
	header("Location:contacts.php");	// IF SUCCESSFUL REDIRECT TO CONTACTS PAGE
	} 
	else 
	{ 
	$error[]= 'Delete NOT successful .';
     //echo mysqli_error($dbc);
	}
	}//CLOSE IF ISSET POST DELETE

	//CLOSE DELETE CONTACT
    echo '</form>';
	
	echo '<div class="medium-1 large-1 columns">
		&nbsp;
		</div>';

    

   
	echo '<div class="medium-5 large-5 columns">
	

	<h5 class="red"><strong>Full Name:</strong></h5>
	<p class="text">';
	if(!empty($row['fullname']))
	{echo '<h4><strong>'.$row['fullname'].'</strong></h4>';} else {echo '---';}
	
	echo'</p>

	<h5 class="red"><strong>Phone Number</strong></h5>
	<p class="text">';
	//IF ONE OR MORE PHONE NUMBERS WERE PROVIDED, SEPARATE THEM AT THE COMMA
	if(!empty($row['phone'])){$pieces=explode(',',$row['phone']);
	foreach($pieces as $phones)
	{
	echo $phones;
	echo '</br>';}
	}
	else{ echo '---';}
	echo '</p>

	<h5 class="red"><strong>Address</strong></h5>
	<p class="text">';
	//IF ONE OR MORE ADDRESSES WERE PROVIDED, SEPARATE THEM AT THE COMMA
	if(!empty($row['address']))
	{
	$piecesaddress=explode('    ',$row['address']);
	foreach($piecesaddress as $address){
    echo $address;
	echo '</br>';}}
	else {echo '---';}
	echo'</p>



    <h5 class="red"><strong>Groups</strong></h5>';

    $group_result=mysqli_query($dbc,"SELECT fkgroups_groupname FROM group_members where fkcms_cid='$id' AND $tenant ");
	echo'<p class="text">';

	if(@mysqli_num_rows($group_result)>0)
    {
    while(list($group_name)=mysqli_fetch_row($group_result))
    {
    	echo $group_name;
    	echo '</br>';
    }

	}
	
     else {echo 'No group';}
     echo'</div>';


	echo'<div class="medium-5 large-5 columns">
	
	<h5 class="red"><strong>Email</strong></h5>
	<p class="text">';
	if(!empty($row['email']))
	//IF MORE THAN ONE EMAIL WAS PROVIDED, SEPARATE THEM AT THE COMMA
	{$piecesemail=explode(',',$row['email']);
	foreach($piecesemail as $email)
	{echo $email;
	echo '</br>';
	}
	} 
	else {
	echo '---';
		}
	echo'</p>
	
	<h5><strong>Date created</strong></h5>
	<p class="text">';
	if(!empty($row['date_entered'])){echo $row['date_entered'];} else {echo '---';}
	echo'</p>

	<input type="hidden" name="cid" value="' .$id . '" />
	</form>
    <div class="medium-1 large-1 columns">
		&nbsp;
		</div>';
	} 
	else 
	{ // NOT A VALID ID
	$error[]='This page has been accessed in error. It may be you tried to access a previously deleted record. ';
	    echo'<br/><br/><br/>';

	}
	mysqli_close($dbc);
	include('includes/alert.php');

	echo'</div>
	


	</div>
	</div>';
	include('includes/footer.html');?>

	
