<?php include 'includes/doctype.php';?>
<html>
<head>
<?php include 'includes/head.php';?>

<style>
#page-content{color:#000;}
.loadingZone{width:75px;margin-left:auto;margin-right:auto;}
</style>

</head>

<body>
<?php include 'includes/nav.php';?>
    <div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar">
        <form role="search">
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Search">
            </div>
        </form>
        <ul class="nav menu">
            <li class="active"><a id="btn_campaigns" href="#"><span>My Campaigns</span></a></li>
            <li><a id="btn_organizations" href="#"><span>My Organizations</span></a></li>
            <li><a id="btn_accountInfo" href="#"><span>Account Info</span></a></li>
        </ul>
    </div><!--/.sidebar-->

    <div class="container"> 
        <div class="col-sm-9 col-sm-offset-3 col-lg-10 col-lg-offset-2 main"> 
            <div id="page-content">
            </div>
        </div>
    </div>

    <div style="display:none;">
        <div id="tableTemplate">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Date Created</th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    
    <script id="campaignTemplate" type="text/x-handlebars-template">
        <tr>
            <td>{{name}}</td>
            <td>{{description}}</td>
            <td>{{datecreated}}</td>
            <td><a href="#">View</a></td>
            <td><a href="#">Edit</a></td>
        </tr>
    </script>
    <script id="organizationTemplate" type="text/x-handlebars-template">
        <tr>
            <td>{{org_fullname}}</td>
            <td>{{description}}</td>
            <td>{{org_datejoined}}</td>
            <td><a href="#">View</a></td>
            <td><a href="#">Edit</a></td>
        </tr>
    </script>

    
    <?php include 'includes/footer.php';?>
    <?php include 'includes/modals.php';?>
    <script src="js/ajax.module.js"></script>
    <script src="js/campaign.module.js"></script>
    <script src="js/organization.module.js"></script>
    <script src="js/user.binding.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/3.0.3/handlebars.min.js"></script>
</body>

</html>