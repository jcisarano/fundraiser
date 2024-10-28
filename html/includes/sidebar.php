	<div id="sidebar-collapse" class="col-sm-3 col-lg-2 sidebar">
		<form role="search">
			<div class="form-group">
				<input type="text" class="form-control" placeholder="Search">
			</div>
		</form>
		<ul class="nav menu">
			<li class="<?php if($page=='overview'){echo 'active';}?>" ><a href="overview.php"><span>Overview</span></a></li>
			<li class="<?php if($page=='general'){echo 'active';}?>" ><a href="general.php"><span>General Settings</span></a></li>
			<li class="<?php if($page=='photos'){echo 'active';}?>" ><a href="photos.php"><span>Photos</span></a></li>
			<li class="<?php if($page=='fundraising'){echo 'active';}?>" ><a href="fundraising.php"><span>Fundraising</span></a></li>
			<li class="<?php if($page=='participants'){echo 'active';}?>" ><a href="participants.php"><span>Participants</span></a></li>
			<li class="<?php if($page=='account'){echo 'active';}?>" ><a href="account-info.php"><span>Account Info</span></a></li>
		</ul>
	</div><!--/.sidebar-->