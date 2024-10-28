<?php include 'includes/doctype.php';?>
<html>
<head>
<?php include 'includes/head.php';?>
</head>

<body>
<?php include 'includes/nav.php';?>
<?php    
$page = 'participants';
include('includes/sidebar.php');
?>
		
<div class="container">		
            <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main"> 
                <button type="button" class="btn btn-default" style="display: inline-block;">save changes</button>
                <hr style="display: block;">
                <h3 style="display: block;">Participants</h3>
                <p style="display: block;">This funder has (4) participants</p>
                <hr />
                <div class="col-md-6 skilles-left pull-left">
                    <button type="button" class="close" aria-hidden="true">&times;</button>
                    <img src="images/jasons.jpg" width="150" class="pull-left skills">
                    <h3>Jason Sudeikis</h3>
                    <h4 class="participant-h4">Highest Donation: $200.00</h4>
                    <div class="progress"> 
                        <div class="progress-bar" role="progressbar" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"> 
                            <span class="sr-only">30% Complete</span> 
                        </div>                         
                    </div>                     
                </div>
                <div class="col-md-6 skilles-left pull-left">
                    <button type="button" class="close" aria-hidden="true">&times;</button>
                    <img src="images/justint.jpg" width="150" class="pull-left skills">
                    <h3>Justin Timberlake</h3>
                    <h4 class="participant-h4">Highest Donation: $200.00</h4>
                    <div class="progress"> 
                        <div class="progress-bar" role="progressbar" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"> 
                            <span class="sr-only">30% Complete</span> 
                        </div>                         
                    </div>                     
                </div>
                <div class="container pg-empty-placeholder"></div>
                <hr />
                <div class="col-md-6 skilles-left pull-left">
                    <button type="button" class="close" aria-hidden="true">&times;</button>
                    <img src="images/jasons.jpg" width="150" class="pull-left skills">
                    <h3>Jason Sudeikis</h3>
                    <h4 class="participant-h4">Highest Donation: $200.00</h4>
                    <div class="progress"> 
                        <div class="progress-bar" role="progressbar" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"> 
                            <span class="sr-only">30% Complete</span> 
                        </div>                         
                    </div>                     
                </div>
                <div class="col-md-6 skilles-left pull-left">
                    <button type="button" class="close" aria-hidden="true">&times;</button>
                    <img src="images/justint.jpg" width="150" class="pull-left skills">
                    <h3>Justin Timberlake</h3>
                    <h4 class="participant-h4">Highest Donation: $200.00</h4>
                    <div class="progress"> 
                        <div class="progress-bar" role="progressbar" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"> 
                            <span class="sr-only">30% Complete</span> 
                        </div>                         
                    </div>                     
                </div>
            </div>             
            <div class="row">
                <div class="col-sm-offset-2 col-md-10">
                    <hr />                     
                </div>
            </div>
            <!--/.main-->    
</div><!--conatiner-->

<?php include 'includes/footer.php';?>
<?php include 'includes/modals.php';?>
</body>

</html>