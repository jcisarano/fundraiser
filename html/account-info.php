<?php include 'includes/doctype.php';?>
<html>
<head>
<?php include 'includes/head.php';?>
</head>

<body>
<?php include 'includes/nav.php';?>
<?php    
$page = 'account';
include('includes/sidebar.php');
?>
		<div class="container">
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">		
<br />	

	<button type="submit" class="btn btn-primary">save changes</button>
	<hr/>
	<h3>Account Information</h3> 
	<p>This is displayed after the title of your funder.</p>
		<div class="form-group col-md-6">
			<input class="form-control ib" placeholder="Enter First Name"> 
		</div>
		<div class="form-group col-md-6">
			<input class="form-control ib" placeholder="Enter Last Name">
		</div>
		<br/><br/>
	<hr/>

</div><!--/.main-->
</div><!--container-->
<?php include 'includes/footer.php';?>
<?php include 'includes/modals.php';?>
</body>

</html>