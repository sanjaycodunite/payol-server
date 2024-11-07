<div class="login_footer">
<div class="container">
  <div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="aon-foo-copyright-2 text-center">
                            © 2023 Payol Digital Technologies Private Limited. All Right Reserved | <a href="{site_url}about">About Payol</a>
                        </div>
    </div>  
  </div>
</div>
</div>   
    <!-- jquery
    ============================================ -->    
        <script src="{site_url}skin/front/login/js/vendor/jquery-1.11.3.min.js"></script>
        
        <!-- popper JS
    ============================================ -->    
        <script src="{site_url}skin/front/login/js/popper.min.js"></script>
        
    <!-- bootstrap JS
    ============================================ -->    
        <script src="{site_url}skin/front/login/js/bootstrap.min.js"></script>
        
       <!-- wow JS
    ============================================ -->    
        <script src="{site_url}skin/front/login/js/wow.min.js"></script>
      
    <!-- scrollUp JS
    ============================================ -->    
        <script src="{site_url}skin/front/login/js/jquery.scrollUp.min.js"></script>
        
        
    <!-- plugins JS
    ============================================ -->    
        <script src="{site_url}skin/front/login/js/plugins.js"></script>
        
    <!-- main JS
    ============================================ -->    
        <script src="{site_url}skin/front/login/js/main.js"></script>

        <script src="{site_url}skin/front/login/js/custom-ajax.js"></script>
    <script type="text/javascript">
         $('.toggleOtp').click(function(){
  $(this).toggleClass('active');
  
  if($('.toggleOtp').hasClass('active')) {
    $(this).closest('.digit-group-inner').find('input[type="password"]').each( function( index, value ) {
      value.setAttribute('type', 'text');
    });
  }else{
    $(this).closest('.digit-group-inner').find('input[type="text"]').each( function( index, value ) {
      value.setAttribute('type', 'password');
    });
  }
  

  
})
</script>
    </body>
</html>