<?php include 'includes/doctype.php';?>
<html>
<head>
<?php include 'includes/head.php';?>
</head>

<body>
<?php include 'includes/nav.php';?>
<?php    
$page = 'general';
include('includes/sidebar.php');
?>
		
         <div class="container"> 
            <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main"> 
                <button type="button" class="btn btn-default">save changes</button>                 
                <hr>                 
                <h3>Title of funder</h3> 
                <p>Title that displays on top of your page.</p> 
                <div class="form-group"> 
                    <input type="text" class="form-control" id="formInput15" placeholder="This is my funder">                     
                </div>                 
                <hr>                 
                <h3>Header Image</h3> 
                <p>1000px or wider is recommended</p> 
                <img src="images/camp-main.jpg" width="400" class="img-responsive">                 
                <p>Kids.jpg</p> 
                <button type="button" class="btn btn-default change-buttons" data-target="#modalHImage" data-toggle="modal">change</button>                 
                <hr>                 
                <h3>Logo/Profile Image</h3> 
                <p>A square image is recommended</p> 
                <img src="images/profilepic.jpg" width="150" class="img-responsive">                 
                <p>olivia-portrait.jpg</p> 
                <button type="button" class="btn btn-default change-buttons" data-target="#modalPImage" data-toggle="modal">change</button>                 
                <hr>                 
                <h3>Color Scheme</h3> 
                <p>Primary Color</p> 
                <div class="row"> 
                    <div class="col-md-3"> 
                        <div class="form-group color-box"> 
                            <input type="text" class="form-control color-box" id="formInput33">                             
                        </div>                         
                    </div>                     
                    <div class="col-md-1"> 
                        <button type="button" class="btn btn-default dropper-btn" data-target="#modalPColor" data-toggle="modal"> 
                            <i class="fa fa-eyedropper fa-2x" style="color:#2F53BA;"></i> 
                        </button>                         
                    </div>                     
                </div>                 
                <p>Secondary Color</p> 
                <div class="row"> 
                    <div class="col-md-3"> 
                        <div class="form-group color-box-two"> 
                            <input type="text" class="form-control color-box-two" id="formInput33">                             
                        </div>                         
                    </div>                     
                    <div class="col-md-1"> 
                        <button type="button" class="btn btn-default dropper-btn" data-target="#modalSColor" data-toggle="modal"> 
                            <i class="fa fa-eyedropper fa-2x" style="color:#2F53BA;"></i> 
                        </button>                         
                    </div>                     
                </div>                 
                <hr>                 
                <h3>About</h3> 
                <p>Write some information about your funder.</p> 
                <textarea class="form-control ib" rows="4" id="textboxabout"></textarea>                 
                CONTENT
            </div>             
            <!--/.main-->             
        </div>         
        <!--conatiner-->           

<?php include 'includes/footer.php';?>
<?php include 'includes/modals.php';?>
</body>

</html>