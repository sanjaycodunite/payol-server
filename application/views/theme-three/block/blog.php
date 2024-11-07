

			
			<!-- Main Banner -->
			<div id="theme-banner-two" class="service_side-ar">
				<div class="bg-round-one wow zoomIn animated" data-wow-duration="5s"></div>
				<div class="bg-round-two wow zoomIn animated" data-wow-duration="5s"></div>
				<div class="bg-round-three wow zoomIn animated" data-wow-duration="5s"></div>
				<div class="bg-round-four wow zoomIn animated" data-wow-duration="5s"></div>
				<div class="bg-round-five wow zoomIn animated" data-wow-duration="5s"></div>
				<div class="container">
				<div class="row">
			<div class="m-auto col-lg-6 col-md-6">	
					<div class="inner_slide_content text-center">
              <h2>Blog</h2> 
              <ul>
               <li><a href="{site_url}">Home</a></li>
               <li>/</li>
               <li>Blog</li> 
              </ul>
             </div>
					</div>
					</div> <!-- /.main-wrapper -->
				</div> <!-- /.container -->
			</div> <!-- /#theme-banner-two -->



			
			
			
			


    <div class="about-us-section">
    	
    <div class="container">
    <div class="row">

    	<?php foreach($blog as $list) { ?>
    <div class="col-lg-4 col-md-4">
    <div class="single-blog-post">
	<div class="img-holder"><img src="{site_url}<?php echo $list['image'] ?>" alt=""></div>
	<div class="post-data">
	<h5 class="blog-title-one title"><a href="{site_url}Blogdetail/index/<?php echo $list['id']; ?>"><?php echo $list['title'] ?></a></h5>
	<p><?php echo $list['description'] ?></p>
	</div> <!-- /.post-data -->
	</div>	
    </div>

    <?php } ?>

    
    
    </div>	
   
    </div>	
    </div>
 


