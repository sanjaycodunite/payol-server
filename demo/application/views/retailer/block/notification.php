<div class="container-fluid">
 <div class="row">
 <div class="m-auto col-lg-8">
 <div class="card shadow py-2">
 	<h2 class="ml-5">All Notification</h2>
 <div class="card-body">
  

<?php  if($notification_list) {
				foreach ($notification_list as  $list) {
				
	?>
<div class="card_notification_list mt-3">
 <div class="d-flex align-items-center">
  <div class="flex-shrink-0">
   <div class="notify-icon">
    <img class="img-profile rounded-circle" src="{site_url}skin/admin/images/bell.png">
     </div>
     </div>
   <div class="flex-grow-1 text-truncate ml-2">
    <h5 class="noti-item-title fw-medium fs-14"><?php echo $list['title'] ?></h5>
  <small class="noti-item-subtitle text-muted"><?php echo $list['message'] ?></small>
      </div>
      </div>
</div>

<?php } } else {  ?>

	<div class="card_notification_list">
 <div class="d-flex align-items-center">
  <div class="flex-shrink-0">
   <div class="notify-icon">
    <img class="img-profile rounded-circle" src="{site_url}skin/admin/images/bell.png">
     </div>
     </div>
   <div class="flex-grow-1 text-truncate ml-2">
    <h5 class="noti-item-title fw-medium fs-14">Datacorp</h5>
  <small class="noti-item-subtitle text-muted">No Data Found</small>
      </div>
      </div>
</div>

<?php } ?>


 </div>	
 </div> 	
 </div>	
 </div>	
</div>



</div>