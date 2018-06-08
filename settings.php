<?php
$page_title = 'Settings';

include('includes/inside_headmaster.php');
?>
<script src="includes/js/jresources.js"></script>
<div class="row">
<div class="large-12 columns">
<p>Email address</p>
<hr/>
</div>
</div>

<div class="row">
<div class="large-12 columns">
<div class="medium-5 large-5 columns">
<?php
if(isset($_SESSION['tenant_email']))
{
	echo $_SESSION['tenant_email'];
}
?>
</div>
<div class="medium-2 large-2 columns">
<a href="change_email.php?e=1">Change</a>
</div>

<div class="medium-5 large-5 columns">
&nbsp;
</div>
</div>
</div>
</br></br>
<div class="row">
<div class="large-12 columns">
<p>Password  </p>
<hr/>
</div>
</div>

<div class="row">
<div class="large-12 columns">
<div class="medium-5 large-5 columns">
<p>xxxx</p>
</div>
<div class="medium-2 large-2 columns">
<a href="change_password.php">Change</a>
</div>

<div class="medium-5 large-5 columns">
&nbsp;
</div>
</div>
</div>








<?php
include('includes/footer.html');