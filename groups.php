<?php
	$page_title="Groups";
	include('includes/inside_headmaster.php');
	include('includes/action_success.php');

	?>

	<script src="includes/js/jresources.js"></script>

   </br>
    <div class="row">
    <div class="large-12 columns">
    <a href="add_group.php" class="thin radius button success">Add group</a>
    <h4>Groups</h4>
	<form action="groups.php" method="POST" name="checkboxesform">
	<?php

	//RETRIEVE  THE LIST OF GROUPS FROM DATABASE
	$q="SELECT group_id, group_name,date_created FROM groups WHERE $tenant";
	$r=mysqli_query($dbc,$q);
	if(@mysqli_num_rows($r)>0)
	{

	echo '<table border="0" width="100%" cellspacing="0" cellpadding="0" align="center">
	<tr >
	<td align="center" width="2%"  class="borderright" ></td>
	<td align="center" width="2%"  class="borderright" ><b>S/N</b></td>
	<td align="left" width="45%" class="borderbottom"><b>Group name</b></td>
	<td align="center" width="25%" class="borderbottom"><b>Members</b></td>
	<td align="center" width="25%" class="borderbottom"><b>Date created</b></td>

	</tr>';
	$i=0;
	while($row=mysqli_fetch_array($r, MYSQLI_ASSOC))
	{
	$i++;

	//GET THE NO OF CONTACTS IN EVERY GROUP

	$r2=mysqli_query($dbc,"SELECT COUNT(fkcms_cid) FROM group_members WHERE $tenant AND fkgroups_groupid={$row['group_id']} AND status='active'" ); 
	$get_count=mysqli_fetch_array($r2,MYSQLI_NUM);
	$contact_count=$get_count[0];
	
	//PRINT A DEFAULT GROUP DESCRIPTION IF ONE WAS NOT SPECIFIED
	if(empty($row['group_description']))
	{
	$row['group_description']='No description available';
	}

	echo '<tr>
	<td align="center" width="2%" class="borderright"><span class="deletecheckbox"><input type="checkbox"  name="checkbox[]" value="'. $row['group_id'].'"></span></td>
	<td align="center" width="2%" class="borderright"><a href="view_group.php?group_name='.$row['group_name'].'&group_id='.$row['group_id'].'">'.$i.'</a></td>
	<td align="left" width="2%" class="borderright"><a href="view_group.php?group_name='.$row['group_name'].'&group_id='.$row['group_id'].'">'.$row["group_name"].'</a></td>
	<td align="center" width="2%" class="borderright"><a href="view_group.php?group_name='.$row['group_name'].'&group_id='.$row['group_id'].'">'.$contact_count.'</a></td>
	<td align="center" width="2%" class="borderright"><a href="view_group.php?group_name='.$row['group_name'].'&group_id='.$row['group_id'].'">'.$row["date_created"].'</a></td>
	</tr>';
	
	}//CLOSE WHILE LOOP
	echo '</table>';
	
	//DELETE
	if(isset($_POST['delete']))
	{
	if(isset($_POST['checkbox']))
	{
	$checkbox=$_POST['checkbox'];
	$count=count($_POST['checkbox']);
	for($i=0;$i<$count;$i++)
	{
	$del_id = $checkbox[$i];
	$check=mysqli_query($dbc,"SELECT fkgroups_groupid FROM group_members WHERE ($tenant AND fkgroups_groupid='$del_id') AND status='active' LIMIT 1");
	
	if(@mysqli_num_rows($check)>0)
	{
	$error[]='Delete NOT successful. Make sure the group(s) contain no contacts and try again'	;
	}
	else
	{
	$sql = mysqli_query($dbc,"DELETE FROM groups WHERE $tenant AND group_id='$del_id'");
	
	if (mysqli_affected_rows($dbc) > 0) 
	{
	// IF IT RAN OK.
	$_SESSION['delete_success']=1;
	header("Location:groups.php");
	 // PRINT A MESSAGE:
	}
	else 
	{ // If the query dcid not run OK.
	$error[]='The group could not be deleted. Please ensure it contains no records then try again.</p>'; 
	}

	}//CLOSE ELSE
	}//CLOSE IF ISSET POST CHECKBOX
	}//CLOSE IF ISSET POST DELETE
	}//CLOSE FOR LOOP

	echo '</div></div></div>';
	echo '<div class="row"><div class="large-12 columns">';
	echo '<div class="medium-2 large-2 columns">
	<input type="submit" name="delete"  class="thin radius button" value="Delete" onclick="return confirmdelete();">
	</div>';
    echo '<div class="medium-8 large-8 columns">&nbsp;</div>
	<div class="medium-2 large-2 columns">
	<span class="selectall"><input type="checkbox" name="checkboxall" class="selectallcheckbox" value="yes" onClick="Check(document.checkboxesform.checkbox)">&nbsp;&nbsp;Select all</span>
	</div>'; 
		echo '</form></div></div>';
	}//CLOSE
	elseif(mysqli_num_rows($r)==0)

	{
	$notify[]= 'You have not added any group. Click <a href="add_group+.php">Add group</a>&nbsp;to add a new group.';
	}
	else{
	$error[]= 'Could not retrieve groups due to a system error. Sorry for the incovenienece. Please try again later';
	//echo mysqli_error($dbc);
	}
	
	include('includes/alert.php');
	?>
	</div><!--CLOSE DIV LARGE 12 COLUMNS-->
	</div><!--CLOSE DIV ROW-->

		<?php include('includes/footer.html');?>

	