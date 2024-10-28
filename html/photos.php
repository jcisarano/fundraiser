<?php include 'includes/doctype.php';?>
<html>
<head>
<?php include 'includes/head.php';?>
</head>

<body>
<?php include 'includes/nav.php';?>
<?php    
$page = 'photos';
include('includes/sidebar.php');
?>

<div class="container">		
<div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">			
	            <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
                <button type="button" class="btn btn-default" style="display: inline-block;">save changes</button>
                <hr style="display: block;">
                <h3 style="display: block;">Photos</h3>
                <p style="display: block;">Upload photos or images for your funder</p>
                <button type="button" class="btn btn-default" style="display: inline-block;" data-toggle="modal" data-target="#modalFPhoto">upload photos</button>  
                <p style="display: block;"><br></p>
                <div class="row" style="display: block;">
                    <div class="col-md-4" style="display: block;">
                        <img src="images/1.jpg" width="200" class="img-responsive">
                        <p style="display: block;">one.jpg</p> 
                    </div>
                    <div class="col-md-4" style="display: block;">
                        <img src="images/2.jpg" width="200" class="img-responsive">
                        <p style="display: block;">one.jpg</p> 
                    </div>
                    <div class="col-md-4" style="display: block;">
                        <img src="images/3.jpg" width="200" class="img-responsive">
                        <p style="display: block;">three.jpg</p> 
                    </div>
                </div>
                <hr />
            </div>
</div><!--/.main-->
</div><!--conatiner-->

<?php include 'includes/footer.php';?>
<?php include 'includes/modals.php';?>
</body>

</html>