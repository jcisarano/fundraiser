<?php include 'includes/doctype.php';?>
<html>
<head>
<?php include 'includes/head.php';?>
</head>

<body>
<?php include 'includes/nav.php';?>
<?php    
$page = 'fundraising';
include('includes/sidebar.php');
?>
		
<div class="container">		
            <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main"> 
                <button type="button" class="btn btn-default" style="display: inline-block;">save changes</button>
                <hr style="display: block;">
                <h3 style="display: block;">Fundraising Items</h3>
                <p style="display: block;">Hit the checkbox to enable or disable</p>
                <hr />
                <div class="panel-group" id="fundpanels">
                    <div class="panel panel-default" id="skilled"> 
                        <div class="panel-heading"> 
                            <h4 class="panel-title"> <a data-toggle="collapse" data-parent="#panels1" href="#collapse1">                            Skilled Services                            </a> </h4> 
                        </div>                         
                        <div id="collapse1" class="panel-collapse collapse in"> 
                            <div class="panel-body">
                                <div class="col-md-6 skilles-left">
                                    <img src="images/soccer1.jpg" width="150" class="pull-left skills">
                                    <h3>Soccer Training</h3> 
                                    <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
                                    <img src="images/enabled.png" class="pull-left" />
                                    <p style="display: block;" class="p-space">enabled</p>
                                </div>
                                <div class="col-md-6 skilles-left">
                                    <img src="images/sing1.jpg" width="150" class="pull-left skills">
                                    <h3>Singing Lessons</h3> 
                                    <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
                                    <img src="images/disabled.png" class="pull-left" />
                                    <p style="display: block;" class="p-space">disabled</p>
                                </div>
                                <div class="col-md-6 skilles-left">
                                    <img src="images/soccer1.jpg" width="150" class="pull-left skills">
                                    <h3>Soccer Training</h3> 
                                    <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
                                    <img src="images/enabled.png" class="pull-left" />
                                    <p style="display: block;" class="p-space">enabled</p>
                                </div>
                                <div class="col-md-6 skilles-left">
                                    <img src="images/sing1.jpg" width="150" class="pull-left skills">
                                    <h3>Singing Lessons</h3> 
                                    <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
                                    <img src="images/disabled.png" class="pull-left" />
                                    <p style="display: block;" class="p-space">disabled</p>
                                </div>                                 
                            </div>                             
                        </div>                         
                    </div>
                    <div class="panel panel-default" id="labor"> 
                        <div class="panel-heading"> 
                            <h4 class="panel-title"> <a data-toggle="collapse" data-parent="#panels1" href="#collapse2">                                Labor Services                               </a> </h4> 
                        </div>                         
                        <div id="collapse2" class="panel-collapse collapse"> 
                            <div class="panel-body">
                                <div class="col-md-6 skilles-left">
                                    <img src="images/soccer1.jpg" width="150" class="pull-left skills">
                                    <h3>Soccer Training</h3> 
                                    <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
                                    <img src="images/enabled.png" class="pull-left" />
                                    <p style="display: block;" class="p-space">enabled</p>
                                </div>
                                <div class="col-md-6 skilles-left">
                                    <img src="images/sing1.jpg" width="150" class="pull-left skills">
                                    <h3>Singing Lessons</h3> 
                                    <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
                                    <img src="images/disabled.png" class="pull-left" />
                                    <p style="display: block;" class="p-space">disabled</p>
                                </div>                                                                  
                            </div>                             
                        </div>                         
                    </div>                     
                    <div class="panel panel-default" id="raffles"> 
                        <div class="panel-heading"> 
                            <h4 class="panel-title"> <a data-toggle="collapse" data-parent="#panels1" href="#collapse3">                               Raffles                                </a> </h4> 
                        </div>                         
                        <div id="collapse3" class="panel-collapse collapse"> 
                            <div class="panel-body">
                                <div class="col-md-6 skilles-left">
                                    <img src="images/soccer1.jpg" width="150" class="pull-left skills">
                                    <h3>Soccer Training</h3> 
                                    <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
                                    <img src="images/enabled.png" class="pull-left" />
                                    <p style="display: block;" class="p-space">enabled</p>
                                </div>
                                <div class="col-md-6 skilles-left">
                                    <img src="images/sing1.jpg" width="150" class="pull-left skills">
                                    <h3>Singing Lessons</h3> 
                                    <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
                                    <img src="images/disabled.png" class="pull-left" />
                                    <p style="display: block;" class="p-space">disabled</p>
                                </div>                                                                  
                            </div>                             
                        </div>                         
                    </div>
                    <div class="panel panel-default" id="donations"> 
                        <div class="panel-heading"> 
                            <h4 class="panel-title"><a data-toggle="collapse" data-parent="#panels1" href="#collapse4">                                Donations                                </a> </h4> 
                        </div>                         
                        <div id="collapse4" class="panel-collapse collapse"> 
                            <div class="panel-body">
                                <div class="col-md-6 skilles-left">
                                    <img src="images/soccer1.jpg" width="150" class="pull-left skills">
                                    <h3>Soccer Training</h3> 
                                    <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
                                    <img src="images/enabled.png" class="pull-left" />
                                    <p style="display: block;" class="p-space">enabled</p>
                                </div>
                                <div class="col-md-6 skilles-left">
                                    <img src="images/sing1.jpg" width="150" class="pull-left skills">
                                    <h3>Singing Lessons</h3> 
                                    <p>Cras justo odio, dapibus ac facilisis in, egestas eget quam. Donec id elit non mi porta gravida at eget metus. Nullam id dolor id nibh ultricies vehicula ut id elit.</p>
                                    <img src="images/disabled.png" class="pull-left" />
                                    <p style="display: block;" class="p-space">disabled</p>
                                </div>                                                                  
                            </div>                             
                        </div>                         
                    </div>                     
                </div>
            </div>
            <!--/.main-->    
</div><!--conatiner-->

<?php include 'includes/footer.php';?>
<?php include 'includes/modals.php';?>
</body>

</html>