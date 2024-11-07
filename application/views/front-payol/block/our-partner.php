
        <!-- CONTENT START -->
        <div class="page-content">
 
			<!-- SLIDER START --> 
			<div class="inner_Banner_area p-t50 p-b50">
			 <div class="container">
			  <div class="row">
			   <div class="col-lg-12 col-md-12">	
			    <div class="inner_banner_content">
			   	 <h3>Become A Partner</h3>
			    </div>	
			  </div></div>	
			 </div>	
			</div>
			<!-- SLIDER END -->
<style type="text/css">
	.form_group_title h3 {font-size: 30px;font-weight: 600;}
.become_form_colm {padding: 30px;}
</style>
			<!-- Section -->
			<div class="about_area_Section p-t90 p-b90">
				<div class="container">
					<div class="row align-items-center">
					<div class="col-lg-5 col-md-5">
					<div class="become_panel"><img src="{site_url}skin/front-payol/images/our_services_box_image.jpg" class="img-fluid"></div>
					</div>	
					<div class="col-lg-7 col-md-7">	
						<div class="become_form_colm">
							<?php echo form_open_multipart('Page/auth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
				<div class="row">
					<div class="col-md-12">
									<div class="form_group_title mb-4">
										<h3>Become A Partner</h3>
									</div>
								</div>
                                <!-- COLUMNS 1 -->
								<div class="col-md-6">
									<div class="form-group aon-form-label">
										<label>Full Name</label>
										<input type="text" name="name" placeholder="Your Name" class="form-control gradi-line-1">
										<?php echo form_error('name', '<div class="error">', '</div>'); ?>
									</div>
								</div>
								<!-- COLUMNS 2 -->
								<div class="col-md-6">
									<div class="form-group aon-form-label">
										<label>Your Email</label>
										<input type="email" name="email" placeholder="Your Mail" class="form-control gradi-line-1">
										<?php echo form_error('email', '<div class="error">', '</div>'); ?>
									</div>
								</div>
								<!-- COLUMNS 3 -->
								<div class="col-md-12">
									<div class="form-group aon-form-label">
										<label>Your Mobile</label>
										<input type="text" name="mobile" placeholder="Your Mobile" class="form-control gradi-line-1">
										<?php echo form_error('mobile', '<div class="error">', '</div>'); ?>
									</div>
								</div>
								<!-- COLUMNS 4 -->
								<div class="col-md-6">
									<div class="form-group aon-form-label">
										<label>Partner Type</label>
										<select class="form-control gradi-line-1" name="partner_type" required="">
										<option value="">Select</option>
										<option value="retailer">Retailer</option>
										<option value="distributor">Distributor</option>
										<option value="master"> Master Distributor</option>
										<option value="api">API Partner</option>
										</select>
										
									</div>
								</div>
								<!-- COLUMNS 5 -->
								<div class="col-md-6">
									<div class="form-group aon-form-label">
										<label>Product Intrest</label>
										<select  class="form-control gradi-line-1 selectpicker" multiple aria-label="size 3 select example" name="product_interest[]" required="">
										<option value="UPI Collection">Upi Collection</option>
										<option value="Payout">Payout</option>
										<option value="Recharge">Recharge</option>
										<option value="Bill Payment">Bill Payment</option>
										
										</select>
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group aon-form-label">
										<label>Message</label>
										<input type="text" name="message" placeholder="Message" class="form-control gradi-line-1" required>
										
									</div>
								</div>

								<!-- COLUMNS 6 -->
								<div class="col-md-12">
									<div class="sf-contact-submit-btn">
								<button type="submit" class="site-button btn-animate-one">Submit Now </button>
							</div>
								</div>

							</div>
							<?php echo form_close(); ?>
						</div>
					</div></div>
				</div>
				</div>
			</div>
			<!-- Section End -->

			



			

