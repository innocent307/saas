<?php 

$page_title='Edit group';
include('includes/inside_headmaster.php');

	?>
	<div class="row">
	<div class="large-12 columns">
	<h3>Edit group</h3>
	<div class="medium-4 large-4 columns">&nbsp;</div>
	<div class="medium-4 large-4 columns">
	<?php
	if(isset($_GET['editgroupid']))
	{

	$edit_groupid=$_GET['editgroupid'];
	//STORE THE GROUP IN A SESSION VARIABLE
	$_SESSION['editgroup']=$edit_groupid;
	
	$result=mysqli_query($dbc,"SELECT group_id,group_name,group_description FROM groups WHERE group_id='$edit_groupid' AND $tenant LIMIT 1");
	
	if(mysqli_num_rows($result)==1)
	{
	$row=mysqli_fetch_array($result,MYSQLI_ASSOC);
	}
	else
	{
	$error[]= 'Could could retrieve group information';
	}
	}// CLOSE IF ISSET GET['EDITGROUPID']
	else
	{
	$error[]= 'You have accessed this page in error';
	include('includes/alert.php');
	exit;
	}
	if(isset($_POST['submit']))
	{
	if(empty($_POST['group_name']))
	{
	$error[]='Please enter a group name';
	}
	else
	{
	$group_name=mysqli_real_escape_string($dbc,sanitize($_POST['group_name']));
	}
	if(isset($_POST['group_description']))
	{
	$group_description=mysqli_real_escape_string($dbc,sanitize($_POST['group_description']));

	}

	if(empty($error))//IF EVERYTHING IS FINE
	{
	$update=mysqli_query($dbc,"UPDATE groups SET group_name='$group_name',group_description='$group_description',last_modified_by='$last_modified_by' WHERE group_id='{$_SESSION['editgroup']}' AND $tenant LIMIT 1");
	
	if($update)
	{
	$update2=mysqli_query($dbc,"UPDATE group_members SET fkgroups_groupname='$group_name' WHERE fkgroups_groupid='{$_SESSION['editgroup']}' AND $tenant LIMIT 1");

	$success[] = 'Update successful. <a href="groups.php" class="whitestrong">Go to Groups</a>';
	include('includes/alert.php');
		echo'</div>
	<div class="medium-4 large-4 columns">&nbsp;</div>
	</div>';
	exit;
	}
	else{
	$error[]='Update was NOT successful';
	////echo mysqli_error($dbc);
	}}}
	include('includes/alert.php');?>
	<form method="POST" action="" id="addgroup">
	<p>
	<span class="formlabel">Group Name:</span></p><input type="text" name="group_name" size="40" maxlength="50" 
		value="<?php if(isset($row['group_name'])){ echo trim($row['group_name']);}?>">
	<p>
	<span class="formlabel">Description:</span></p><input type="text" name="group_description" size="40" maxlength="200" 
		value="<?php if(isset($row['group_description'])){ echo trim($row['group_description']);}?>" >
	
	<p><input type="submit" value="SAVE" name="submit" class="thin radius button"></p>
	</form>
	</div>
	<div class="medium-4 large-4 columns">&nbsp;</div>
	</div>
	</div>
	<?php include('includes/footer.html');?>

	</body>
	</html>




