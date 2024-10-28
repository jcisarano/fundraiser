<?php include 'includes/doctype.php';?>
<html>
<head>
<?php include 'includes/head.php';?>
</head>

<body>
<?php include 'includes/nav.php';?>
<?php    
$page = 'overview';
include('includes/sidebar.php');
?>
		
<div class="container">		
            <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main">
                <button type="button" class="btn btn-default" style="display: inline-block;">save changes</button>
                <hr style="display: block;">
                <h3 style="display: block;">Funding Overview</h3> 
                <div class="progress"> 
                    <div class="progress-bar" role="progressbar" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"> 
                        <span class="sr-only">30% Complete</span> 
                    </div>                     
                </div>
            </div><!--/.main-->
</div><!--conatiner-->

<?php include 'includes/footer.php';?>
<?php include 'includes/modals.php';?>
</body>

</html>