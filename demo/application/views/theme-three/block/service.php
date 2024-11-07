      <!-- Main Banner -->
      <div class="inner_banner">
        <div class="container">
        <div class="row">
        <div class="m-auto col-lg-6 col-md-6">  
          <div class="inner_slide_content text-center">
              <h2>Services</h2> 
              <ul>
               <li><a href="index.html">Home</a></li>
               <li>/</li>
               <li>Services</li> 
              </ul>
             </div>
          </div> <!-- /.main-wrapper -->
        </div> <!-- /.container -->
      </div></div> <!-- /#theme-banner-two -->


    <div class="about-us-section pt-70 pb-70">
    	
    <div class="container">
    <div class="row">
  
   <?php
   foreach($service as $list){
    ?>
    <div class="col-lg-4">
    <div class="project_item_service">
      <div class="project-img">
      <a href="#"><img src="{site_url}<?php echo $list['image']; ?>" alt="images"></a>
       </div>
      <div class="project_sevice_content">
      <div class="portfolio-inner">
      <h3 class="title"><?php echo $list['title']; ?></h3>
      <p>Morning pay API portal providing Business to Business services. Join us to earn additional benefits. We offer attractive margins all other booking services. </p>
      </div>
       </div>
      </div>                         
  </div>  
 <?php } ?>    

    </div>	
    </div>	
    </div>
 


			