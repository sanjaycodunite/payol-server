<!--about-->
<section class="about_us_section">
<div class="container">
<div class="row">
<div class="col-lg-12 col-md-12 col-sm-12">
 <div class="up_about_textarea">
 <h3>Privacy & Policy</h3>  
    <?php if(isset($contactData['description']) && $contactData['description']){ ?>
    <p style="white-space: pre-line;"><?php echo $contactData['description']; ?></p>
  <?php } else { ?>
    <h1>Comming Soon !</h1>
  <?php } ?>

</div>  
 </div> 
 </div> 

</div>
</section>
<!--End about us -->