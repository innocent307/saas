<?php
$page_title='Create group';
include('includes/inside_headmaster.php');
?>
<div class="row">
<div class="large-12 columns">
<h2>Add a group</h2>
<div class="medium-4 large-4 columns">
&nbsp;
</div>
<div class="medium-4 large-4 columns">
<?php
echo '</br>';

//HANDLE THE POSTED FORM
if(isset($_POST['submit']))
{
if(isset($_POST['group_description']))
{
$group_description=mysqli_real_escape_string($dbc,sanitize($_POST['group_description']));	
}

if(empty($_POST['group_name']))
{
	$error[]='Enter group name';
}
else
{
$group_name = mysqli_real_escape_string($dbc,sanitize($_POST['group_name']));

//CHECK IF THE GROUP ALREADY EXISTS
$result = mysqli_query($dbc,"SELECT * from groups WHERE group_name='$group_name' AND $tenant");
if(@mysqli_num_rows($result)>0)
{
$notify[] = 'This group already exists.';
}
else
{
 //INSERT NEW GROUP INTO DB
$r= mysqli_query($dbc,"INSERT into groups(group_name,group_description,date_created,created_by,fk_tenant_id,fk_tenant_token) VALUES('$group_name','$group_description', NOW(),'$created_by',$tenant_id,'$tenant_token')");

if(mysqli_affected_rows($dbc)==1)
{

$success[] = 'Successful.';
}
else{
$error[]="Group NOT created. Please try again";
//echo mysqli_error($dbc);
}
}//CLOSE ELSE FOR MYSQLI NUM ROWS $RESULT
}//CLOSE ELSE FOR GROUP NAME AVAILABLE
}//CLOSE IF ISSET POST SUBMIT
include('includes/alert.php');
?>
<form method="POST" action="" id="addgroup">
<h5>Group Name:</h5>
<input type="text" name="group_name" size="40" maxlength="100">
<h5>Description:</h5>
<input type="text" name="group_description" size="40" maxlength="100">
<p ><input type="submit" value="Add group" name="submit" class="thin radius button"></p>
</form>
</div>

<div class="medium-4 large-4 columns">
<a href="groups.php" class="thin radius button">Go to groups</a>
</div>
</div>
</div>
<?php include('includes/footer.html');?>

