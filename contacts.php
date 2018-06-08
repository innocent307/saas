<?php 
	$page_title='Members';
	//INCLUDE THE HEADMASTER FILE WHICH HELPS TO PERFORM ROUTINE TASKS

	include('includes/inside_headmaster.php');

	//INCLUDE FILE TO HANDLE ACTION CONFIRMATION MESSAGES
	include('includes/action_success.php');


	?>
	<!--INCLUDE JAVASCRIPT FILE TO HANDLE DELETE CONFIRMATION -->

	<script src="includes/js/jresources.js"></script>


    <div class="row">
    	<div class="large-12 columns">
    <div class="medium-10 large-10 columns">
    &nbsp;
	</div>
	<div class="medium-2 large-2 columns">

    <a href="add_contact.php" class="thin radius button success">Add member</a>
    </div>
</div>
</div>
	</br>
	

	<?php
	//RETRIEVE CONTACTS FROM DATABASE
	$q = "SELECT id,fullname,email,phone,address FROM cms WHERE $tenant AND status='active' ORDER BY cms_id DESC";
    $r = mysqli_query($dbc,$q);
	if(@mysqli_num_rows($r) > 0)

	{ // IF ONE OR MORE ROWS ARE RETURNED DO FOLLOWING

    echo'<div class="row"><div class="large-12 columns">';
	echo'<table width="100%">
	<th width="2%" align="left"></th>
	<th width="23%" align="left">Name</th>
	<th width="17%" align="left">Phone</th>
	<th width="22%" align="left">Email</th>
	<th width="36%" align="left">Address</th>';
	echo'<form action="" method="POST" name="checkboxesform">';

	//USE WHILE LOOP TO PRINT OUT ALL THE RESULTS FROM THE DATABASE
	while($row = mysqli_fetch_array($r,MYSQLI_ASSOC))
	{

	//INCASE THE LENGTH OF THE NAME/PHONE ETC IS TOO LONG
	if(strlen($row['fullname'])>20){
	$row['fullname']=mb_substr($row['fullname'],0,20).'...';
	}
	if(strlen($row['address'])>40){
	$row['address']=mb_substr($row['address'],0,40).'...';
	}

	if(strlen($row['email'])>20){
	$row['email']=mb_substr($row['email'],0,20).'...';
	}


	if(strlen($row['phone'])>15){
	$row['phone']=mb_substr($row['phone'],0,15).'...';
	}
	if(empty($row['phone'])){ $row['phone']= '----';}
	if(empty($row['address'])){ $row['address']= '----';}



	echo '<tr>
	<td width="2%" class="borderright"><input type="checkbox"  name="checkbox[]" value="'. $row['id'].'"></td>
	<td><a href="view_contact.php?id=' . $row['id'] .'"><span class="nopaddingmargin">' . $row['fullname'] . '</span></a></td>
	
	<td><a href="view_contact.php?id=' . $row['id'] .'"><span class="nopaddingmargin"> '. $row['phone'].'</span></a></td>
	<td><a href="view_contact.php?id=' . $row['id'] .'"><span class="nopaddingmargin"> '. $row['email'].'</span></a></td>

	<td><a href="view_contact.php?id=' . $row['id'] .'"><span class="nopaddingmargin"> '.$row['address'] .'</span></a></td>
	</tr>';
	}//CLOSE WHILE LOOP


	echo'</table>';

	//DELETE CONTACTS


	if(isset($_POST['delete']))//CHECK IF THE DELETE BUTTON WAS CLICKED
	{
	if(isset($_POST['checkbox']))//CHECK IF THE ANYBOX WAS CLICKED
	{
	$checkbox=$_POST['checkbox'];//STORE THE VALUE OF THE CHECKBOX IN A VARIABLE
	$count=count($_POST['checkbox']);//COUNT THE NO OF VALUES STORED(NO OF CHECKBOXES CLICKED)
	for($i=0;$i<$count;$i++)//LOOP THROUGH ALL THE VALUES
	{
	$del_id = $checkbox[$i]; //STORE EACH ONE IN A VARIABLE
	$sql = "UPDATE cms SET status='del' WHERE (id=$del_id AND $tenant)";//CHANGE THE STATUS OF THE CONTACT TO SHOW THAT IT HAS BEEN DELETED
	$delete_result = mysqli_query($dbc,$sql);
	}
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
	}//CLOSE IF ISSET POST CHECKBOX
	}// CLOSE IF ISSET POST DELETE


	echo'<div class="row"><div class="large-12 columns">
	<div class="medium-2 large-2 columns">
	<input type="submit" name="delete"  class="thin radius button alert" value="Delete" onclick="return confirmdelete();">
	</div>
	<div class="medium-3 large-3 columns">';

	//CODE TO MOVE CONTACTS TO ANY SELECTED GROUP

	if(isset($_POST['movetogroup']))
	{
	if(isset($_POST['checkbox']))
	{
	$checkbox=$_POST['checkbox'];
	$count=count($_POST['checkbox']);
	if(!empty($_POST['group_id']))/// FROM SELECT BOX
	{
	$groupid=$_POST['group_id'];
	$result1 = mysqli_query($dbc,"SELECT group_name from groups WHERE group_id=$groupid AND $tenant LIMIT 1");//pull out catname
	if(@mysqli_num_rows($result1)>0)
	{
	list($group_name)=mysqli_fetch_row($result1);
	}
	for($i=0;$i<$count;$i++)
	{

	//STORE THE IDs OF THE CONTACTS TO BE MOVED IN A SESSION VARIABLE
	$_SESSION['contactstomove']= $checkbox[$i];

	//CHECK THAT THE CONTACT DOES NOT BELONG TO THE GROUP ALREADY.
	$result2=mysqli_query($dbc,"SELECT fkgroups_groupname from group_members WHERE fkgroups_groupid=$groupid AND fkcms_cid={$checkbox[$i]} AND $tenant LIMIT 1");
	
	if(@mysqli_num_rows($result2)<1)
    {
    //IF CONTACT DOES NOT BELONG TO THE GROUP, INSERT HIM ELSE DO NOTHING
	$moveresult = mysqli_query($dbc,"INSERT INTO group_members(fkgroups_groupid,fkgroups_groupname,fkcms_cid,fk_tenant_id,fk_tenant_token) VALUES($groupid,'$group_name',{$_SESSION['contactstomove']},$tenant_id,'$tenant_token')");
    }
	}//CLOSE FOR LOOP
	
	//print_r($_SESSION['contactstomove']);
	// IF SUCCESSFUL REFRESH THE PAGE
	if (mysqli_affected_rows($dbc)>0) 
	{
	$_SESSION['move_success']=1;
	unset($_SESSION['contactstomove']);
	header("Location:contacts.php");
	} 
	else 
	{ // IF THE QUERY DID NOT RUN OK.
	$error[]= 'Move Not successful.'; // 
	}
	}//CLOSE IF EMPTY POST GROUP ID
	}// CLOSE IF ISSET CHECK BOX
	}//CLOSE IF ISSET MOVE TO GROUP



	//PRINT THE LIST OF GROUPS FROM DATABASE

	$groupname_query=mysqli_query($dbc,"SELECT group_id,group_name from groups WHERE $tenant order by group_name ASC");
	if(@mysqli_num_rows($groupname_query)>0)
	{
	echo '<select name="group_id" class="dropdown">';
	echo '<option value="" value="selected"></option>';

	while($grow=mysqli_fetch_array($groupname_query,MYSQLI_NUM))
	{
	echo "<option value=".$grow[0].">".$grow[1]."</option>";
	}
	echo '</select></div>';
	}

	echo '<div class="medium-2 large-2 columns">
	<input type="submit" name="movetogroup"  class="thin radius button" value="Move">
	</div>
    <div class="medium-3 large-3 columns">&nbsp;</div>
	<div class="medium-2 large-2 columns">
	<input type="checkbox" name="checkboxall" value="yes" onClick="Check(document.checkboxesform.checkbox)"><span class="small_spacing">Select all</span>
	</div>
	</form>	</div></div>';
	}
	elseif(mysqli_num_rows($r)==0)
	{
	$notify[]='No contacts yet. Click Add contact to start adding contacts.';
	}
	else
	{
	$error[]='An error has occured. We apologize for any incovenience';
	exit;
	}

	include('includes/alert.php');
	include('includes/footer.html');

	
	