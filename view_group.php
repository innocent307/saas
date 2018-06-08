<?php
	$page_title="View group";
	if(isset($_GET['group_id']) || isset($_GET['group_name']))
	{

	$group_id=$_GET['group_id'];
	$group_title=$_GET['group_name'];
	include('includes/inside_headmaster.php');
	include('includes/action_success.php');

	}
	else
	{
	if(file_exists('includes/404.php'))
	{
	include('includes/404.php');
	}
	exit;
	}
	?>
	
	<script src="includes/js/jresources.js"></script>


	</br>
	<div class="row">
	<div class="large-12 columns">
	<?php
	 ////PULL OUT THE GROUP DESCRIPTION TO ECHO ON THE GROUP PAGE
	$r=mysqli_query($dbc,"SELECT group_id,group_name,group_description FROM groups WHERE $tenant AND group_id='$group_id' LIMIT 1");
	if(mysqli_num_rows($r)>0)
	{
	$rowres=mysqli_fetch_array($r, MYSQLI_ASSOC);
	echo'<div class="medium-2 large-2 columns">
	&nbsp;
    </div>
	<div class="medium-8 large-8 columns">&nbsp;</div>
	<div class="medium-2 large-2 columns">
	<form action="edit_group.php" method="GET" ><input type="hidden" name="editgroupid"  value="'.$rowres['group_id'].'" />
	<input type="submit" name="edit_group" class="thin radius button"  value="Edit group"/>
	</form>
	</div></div></div>

	<div class="row">
	<div class="large-12 columns">
	<form action="" method="POST" name="checkboxesform">';
	echo '<h4>Group:&nbsp;'.strtoupper($rowres['group_name']).'</h4>';
	}

	//RETRIEVE THE MEMBERS OF THE GROUP
	$result=mysqli_query($dbc,"SELECT group_members.fkgroups_groupname,group_members.fkcms_cid,cms.id,cms.fullname,cms.phone,cms.address,cms.email from group_members
	JOIN cms on cms.id=group_members.fkcms_cid WHERE group_members.fk_tenant_id=$tenant_id AND group_members.fk_tenant_token='$tenant_token' AND group_members.fkgroups_groupid='$group_id' AND group_members.fkcms_cid!=0 AND group_members.status='active'");


	if(@mysqli_num_rows($result)>0)
	{
	//PRINT A FORMATTED GROUP DESCRIPTION
	if(!empty($rowres['group_description']))
	{
	echo ' <p class="small"><strong>Description:</strong> '.ucfirst($rowres['group_description']).'</p>';

	}
	echo'<div class="row">
	<div class="large-12 columns">
	<table width="100%" class="group_table"><th width="2%" align="left"></td><th width="23%" align="left">Name</th><th width="17%" align="left">Phone</th><th width="22%" align="left">Email</th><th width="36%" align="left">Address</th>
	<form action="" method="POST" name="checkboxesform">';

	$i=0;
	while($row=mysqli_fetch_array($result, MYSQLI_ASSOC))
	{

	$i++;

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
	}


	echo'</table>';
	echo '<div class="row">
	<div class="large-12 columns">	
	<div class="medium-1 large-1 columns">
	<input type="submit" name="delete" class="thin radius button" value="Delete" onclick="return confirmdelete();">
    </div>
    <div class="medium-9 large-9 columns">&nbsp;</div>
    <div class="medium-2 large-2 columns">';
	echo '<span class="selectall"><input type="checkbox" name="checkboxall" value="yes" onClick="Check(document.checkboxesform.checkbox)">&nbsp;&nbsp;Select all</span>'; 
	echo '</div>';
	}
	elseif(@mysqli_num_rows($result)==0)
	{
	echo'<div class="row">
	<div class="large-12 columns">';	
	$notify[]= 'There are no contacts in this group. Go to contacts page, select the contacts you want to group, select the group and click Move.</a>';
		//echo mysqli_error($dbc);

	}
	else
	{
	$error[]= 'You have accessed this page in error';
	//echo mysqli_error($dbc);
	}
	// DELETE SINGLE OR MULTIPLE RECORDS FROM DATABASE
	if(isset($_POST['delete']))
	{
	if(isset($_POST['checkbox']))
	{
	$checkbox=$_POST['checkbox'];
	$count=count($_POST['checkbox']);
	for($i=0;$i<$count;$i++)
	{
	$del_id = $checkbox[$i];
	$sql = "DELETE FROM group_members WHERE ($tenant AND fkcms_cid=$del_id AND fkgroups_groupid={$rowres['group_id']} AND fkcms_cid!=0)";
	$deleteresult = mysqli_query($dbc,$sql) ;
	}
	// IF SUCCESSFUL REFRESH THE PAGE 
	if (mysqli_affected_rows($dbc) >0) {
	$success[]='Delete successful.</p>';
	header('Location:view_group.php?group_id='.$group_id);
	} 
	else { // IF THE DELETE WAS NOT SUCCESSFUL
	//echo mysqli_error($dbc);
	$error[]= 'Delete NOT successful.'; 
	}}}
	echo '</form></div></div>';
	include('includes/alert.php');
	include('includes/footer.html')

	?>
	

	