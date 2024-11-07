      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; <?php echo $this->User->get_account_copyright_msg(); ?></span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
          <a class="btn btn-primary btn-sm" href="{site_url}retailer/dashboard/logout">Logout</a>
        </div>
      </div>
    </div>
  </div>
  <!-- Logout Modal-->
  <div class="modal fade" id="instantLoanModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Instant Loan</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <?php $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID); ?>
        <?php $instantLoanText = $this->User->getInstantLoanText($loggedUser['id']); ?>
        <?php if($instantLoanText){ ?>
        <div class="modal-body" style="text-align: center;"><img src="https://chart.googleapis.com/chart?chs=400x400&cht=qr&chl=<?php echo urlencode($instantLoanText); ?>&choe=UTF-8"/></div>
        <?php } else { ?>
        <div class="modal-body">We are not able to offer Loan to you at this point. Please make daily transaction till 1 Lakh for 3 Months. We are working hard to include you soon.</div>
        <?php } ?>
        <div class="modal-footer">
          <a class="btn btn-primary btn-sm" href="#" data-dismiss="modal">Okay, Got it</a>
        </div>
      </div>
    </div>
  </div>
  <!-- Bootstrap core JavaScript-->
  <script src="{site_url}skin/admin/vendor/jquery/jquery.min.js"></script>
  <script src="{site_url}skin/admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <script src="{site_url}skin/admin/js/bootstrap-timepicker.min.js"></script>
  <script src="{site_url}skin/admin/js/jquery.datetimepicker.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="{site_url}skin/admin/vendor/jquery-easing/jquery.easing.min.js"></script>

  <!-- Custom scripts for all pages-->
  <script src="{site_url}skin/admin/js/sb-admin-2.min.js"></script>

  <!-- Page level plugins -->
  <script src="{site_url}skin/admin/vendor/chart.js/Chart.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="{site_url}skin/admin/js/demo/chart-area-demo.js"></script>
  <script src="{site_url}skin/admin/js/demo/chart-pie-demo.js"></script>

  <script src="{site_url}skin/admin/vendor/datatables/jquery.dataTables.min.js"></script>
  <script type="text/javascript" language="javascript" src="{site_url}skin/admin/vendor/datatables/dataTables.buttons.min.js"></script>
  <script type="text/javascript" language="javascript" src="{site_url}skin/admin/vendor/datatables/buttons.flash.min.js"></script>
  <script type="text/javascript" language="javascript" src="{site_url}skin/admin/vendor/datatables/jszip.min.js"></script>
  <script type="text/javascript" language="javascript" src="{site_url}skin/admin/vendor/datatables/pdfmake.min.js"></script>
  <script type="text/javascript" language="javascript" src="{site_url}skin/admin/vendor/datatables/buttons.html5.min.js"></script>
  <script src="{site_url}skin/admin/vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="{site_url}skin/admin/js/demo/datatables-demo.js"></script>
  <script src="{site_url}skin/admin/js/retailer-custom.js"></script>
  <?php
   if($content_block == 'aeps/capture' || $content_block == 'aeps/list'){
  ?>
   <script src="{site_url}skin/admin/js/retailer-aeps-custom.js"></script>
   
    <?php } elseif($content_block == 'newaeps/capture' || $content_block == 'newaeps/list'  || $content_block == 'newaeps/member-registration' || $content_block == 'newaeps/member-login' ) { ?>
    
    <script src="{site_url}skin/admin/js/retailer-new-aeps-custom.js"></script>

  <?php } elseif($content_block == 'fingpayaeps/capture'  || $content_block == 'fingpayaeps/capture' || $content_block == 'fingpayaeps/capture' || $content_block == 'fingpayaeps/list'  || $content_block == 'fingpayaeps/member-register' || $content_block == 'fingpayaeps/member-login' ) { ?>
    
    <script src="{site_url}skin/admin/js/retailer-fingpay-aeps-custom.js"></script>
  <?php } else { ?>

        <script src="{site_url}skin/admin/js/retailer-instantpay-aeps-custom.js"></script>

      <?php } ?>
  <script src="{site_url}skin/admin/js/retailer-datatable.js"></script>
  
  <script src="{site_url}skin/admin/js/bootstrap-select.js"></script>
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  <script src="{site_url}skin/admin/js/jquery.countdown.js"></script>

  <script type="text/javascript">
  var current1 = location.pathname.split("/").slice(-2)[0].replace(/^\/|\/$/g, '');
  var current2 = location.pathname.split("/").slice(-1)[0].replace(/^\/|\/$/g, '');
  var current = current1+'/'+current2;
  var siteUrl = $("#siteUrl").val();

    $('.navbar-nav li a').each(function() {
      var $this = $(this);
      if (current === "") {
        //for root url
        if ($this.attr('href').indexOf(siteUrl) !== -1) {
          $(this).parents('.nav-item').first().addClass('active');
          if ($(this).parents('.sub-menu').length) {
            $(this).closest('.collapse').addClass('show');
            $(this).addClass('active');
          }
        }
      } else {
        //for other url
        if ($this.attr('href').indexOf(current) !== -1) {
          $(this).parents('.nav-item').first().addClass('active');
          if ($(this).parents('.sub-menu').length) {
            $(this).closest('.collapse').addClass('show');
            $(this).addClass('active');
          }
        }
      }
    });


    $('.navbar-nav li').click(function(){
    $('.navbar-nav li').removeClass('active');
    $(this).addClass('active');
})
</script>

<script type="text/JavaScript" language="JavaScript">

$('.datepick').each(function(){
$(this).datetimepicker({
formatTime:'H:i',
formatDate:'d.m.Y',
timepicker:false
});
});

$('.datetimepick').each(function(){
$(this).datetimepicker({
format:'Y-m-d H:i:s',
timepicker:true,
});
});
$("#start_date").datetimepicker({
      formatTime:'H:i',
      formatDate:'d.m.Y',
      timepicker:false
    });
       $("#end_date").datetimepicker({
            formatTime:'H:i',
            formatDate:'d.m.Y',
            timepicker:false
        });
$("#special_price_from").datetimepicker({
            formatTime:'H:i',
            format:'d-m-Y',
            timepicker:false
        });
    $("#special_price_to").datetimepicker({
            formatTime:'H:i',
            format:'d-m-Y',
            timepicker:false
        });

    $('[data-toggle="tooltip"]').tooltip();
    $('[data-countdown]').each(function() {
        var $this = $(this), finalDate = $(this).data('countdown');
        $this.countdown(finalDate).on('update.countdown', function(event) {
        $this.html(event.strftime('%D Day %H h %M m %S s'));
        }).on('finish.countdown', function(event) {
        
          $this.html(event.strftime('%D Day %H h %M m %S s'));
          var club_id = $("#club_id").val();
          var requestID = $("#requestID").val();
          var siteUrl = $("#siteUrl").val();
          $("#club-live-bid-btn").html('<a class="blink_me" href="'+siteUrl+'retailer/saving/clubChatLiveAuth/'+club_id+'/'+requestID+'"><i class="fa fa-comment"></i>View Live Bidding</a>');
         
        });
      });

</script>
  
<script type="text/javascript">
  
function hidemenu(val){

if(val == 1){
document.getElementById('parent_menu').style.display="none";
document.getElementById('menu_icon_class').style.display="block";
}

else{
document.getElementById('parent_menu').style.display="block";
document.getElementById('menu_icon_class').style.display="none";
}

}

</script>

<script type="text/javascript">
      
    $(document).on('change', 'input[name="color"]', function (e) {
        $("input[name='color_code']").val(this.value);
    });

    $("#colorCode").click(function (e) {
        document.querySelector('#colorCodeInput').select();
        document.execCommand('copy'); document.getElementById('copied').innerHTML= 'copied!';
        setTimeout(function() {
            $('#copied').css('visibility','hidden')
        }, 1000);
    });
    $(document).on('change', 'select[name="role_id"]', function (e) {
        $('#for-view').addClass('divDisable');
        var role = this.value;
        if(role == 6){
            $('#for-view').removeClass('divDisable');
        }
    });
countdownTimer($("#chat-countdown").data('endtime'));
function countdownTimer(finalDate)
{
  //console.log('Yes');
  var $this = $("#chat-countdown");
  //console.log(finalDate);
        
  $this.countdown(finalDate).on('update.countdown', function(event) {
  $this.html(event.strftime('<i class="fa fa-clock"></i> %M:%S'));
  }).on('finish.countdown', function(event) {
  
    $this.html(event.strftime('<i class="fa fa-clock"></i> %M:%S'));
    clubRoundSuperTimer();
    
   
  });
}

function clubRoundSuperTimer()
{

  var siteUrl = $("#siteUrl").val();
  var club_id = $("#club_id").val();
  var roundNo = $("#roundNo").val();
  var requestID = $("#requestID").val();
  $.ajax({                
    url:siteUrl+'retailer/saving/clubRoundSuperTimer/'+club_id+'/'+requestID+'/'+roundNo,                        
    success:function(r){
      
      var data = JSON.parse($.trim(r));
      if(data["status"] == 2){
        
        $("#chat-countdown").attr("data-endtime",data['end_datetime']);
        countdownTimer(data['end_datetime']);
      }
      else if(data["status"] == 3){
        
        //$("#club-bid-btn").css('display','none');
      }
      

      
    }
  });
  
}
function getClubLiveMember()
{

  var siteUrl = $("#siteUrl").val();
  var club_id = $("#club_id").val();
  var requestID = $("#requestID").val();
  $.ajax({                
    url:siteUrl+'retailer/saving/getClubLiveMembers/'+club_id+'/'+requestID,                        
    success:function(r){
      
      var data = JSON.parse($.trim(r));
      if(data["status"] == 1){
        $("#club-live-member-block").html(data['str']);
      }

      
    }
  });
  
}
function getClubChatList()
{

  var siteUrl = $("#siteUrl").val();
  var club_id = $("#club_id").val();
  var roundNo = $("#roundNo").val();
  var requestID = $("#requestID").val();
  $.ajax({                
    url:siteUrl+'retailer/saving/getClubChatList/'+club_id+'/'+requestID+'/'+roundNo,                        
    success:function(r){
      
      var data = JSON.parse($.trim(r));
      if(data["status"] == 1){
        $("#club-chat-block").html(data['str']);
        if(data['isNewRound'] == 1)
        {
          $("#roundNo").val(data['lastRoundNo']);
          $("#chat-countdown").attr("data-endtime",data['end_datetime']);
          countdownTimer(data['end_datetime']);
        }
        var lastChatDatetime = $("#lastChatDatetime").val();
        $("#lastChatDatetime").val(data['lastChatDatetime']);
        $("#club-last-member-name").html(data['lastMemberName']+' Bid for ');
        $("#club-total-round-amount").html('<i class="fa fa-rupee"></i> '+data['totalBidAmount']);
        if(data['lastChatDatetime'] != lastChatDatetime)
        {
          $("#club-chat-block").animate({ scrollTop: $('#club-chat-block').prop("scrollHeight")}, 100);
        }

      }
      

      
    }
  });
  
}
function getClubRoundStatus()
{

  var siteUrl = $("#siteUrl").val();
  var club_id = $("#club_id").val();
  var roundNo = $("#roundNo").val();
  var requestID = $("#requestID").val();
  $.ajax({                
    url:siteUrl+'retailer/saving/getClubRoundStatus/'+club_id+'/'+requestID+'/'+roundNo,                        
    success:function(r){
      
      var data = JSON.parse($.trim(r));
      if(data["status"] == 1){
        if(data['isLive'] == 1)
        {
          $("#club-live-header").css('display','flex');
        }
        else
        {
          getClubRoundStatus();
        }
      }
      else
      {
        getClubRoundStatus();
      }
      

      
    }
  });
  
}
$('#update-task-message').keypress(function(event) {
    if (event.keyCode == 13) {
        event.preventDefault();
        var siteUrl = $("#siteUrl").val();
        var club_id = $("#club_id").val();
        var roundNo = $("#roundNo").val();
        var requestID = $("#requestID").val();
        if($("#update-task-message").val() != '')
        {
          $("#update-task-comment-loader").html('<img src="'+siteUrl+'skin/admin/images/small-loading.gif" />');
          $.ajax({                
            type:'POST',
            url:siteUrl+'retailer/saving/clubChatAuth/'+club_id+'/'+requestID+'/'+roundNo,
            data:{'message':$("#update-task-message").val()},
            success:function(r){
              
              var data = JSON.parse($.trim(r));
              if(data["status"] == 1){
                $("#club-chat-block").append(data['str']);
                $("#update-task-comment-loader").html('');
              }
              else
              {
                $("#update-task-comment-loader").html('<font color="red">'+data['msg']+'</font>');
              }
              $("#update-task-message").val('');
              

              
            }
          });
        }
        else
        {
          $("#update-task-message").focus();
        }
    }
});
$("#update-task-comment-btn").click(function(){
  var siteUrl = $("#siteUrl").val();
  var club_id = $("#club_id").val();
  var roundNo = $("#roundNo").val();
  var requestID = $("#requestID").val();
  if($("#update-task-message").val() != '')
  {
    $("#update-task-comment-loader").html('<img src="'+siteUrl+'skin/admin/images/small-loading.gif" />');
    $.ajax({                
      type:'POST',
      url:siteUrl+'retailer/saving/clubChatAuth/'+club_id+'/'+requestID+'/'+roundNo,
      data:{'message':$("#update-task-message").val()},
      success:function(r){
        
        var data = JSON.parse($.trim(r));
        if(data["status"] == 1){
          $("#club-chat-block").append(data['str']);
          $("#update-task-comment-loader").html('');
        }
        else
        {
          $("#update-task-comment-loader").html('<font color="red">'+data['msg']+'</font>');
        }
        $("#update-task-message").val('');
        

        
      }
    });
  }
  else
  {
    $("#update-task-message").focus();
  }

});
$("#club-bid-btn").click(function(){
  var siteUrl = $("#siteUrl").val();
  var club_id = $("#club_id").val();
  var roundNo = $("#roundNo").val();
  var requestID = $("#requestID").val();
  if($("#update-task-message").val() != '')
  {
    $("#update-task-comment-loader").html('<img src="'+siteUrl+'skin/admin/images/small-loading.gif" />');
    $.ajax({                
      type:'POST',
      url:siteUrl+'retailer/saving/clubBidAuth/'+club_id+'/'+requestID+'/'+roundNo,
      data:{'message':$("#update-task-message").val()},
      success:function(r){
        
        var data = JSON.parse($.trim(r));
        if(data["status"] == 1){
          $("#club-chat-block").append(data['str']);
          $("#update-task-comment-loader").html('');
        }
        else
        {
          $("#update-task-comment-loader").html('<font color="red">'+data['msg']+'</font>');
        }
        $("#update-task-message").val('');
        

        
      }
    });
  }
  else
  {
    $("#update-task-message").focus();
  }

});
    function getDashboardSummary()
{

  var siteUrl = $("#siteUrl").val();
  $.ajax({                
    url:siteUrl+'retailer/dashboard/getDashboardSummary',                        
    success:function(r){
      
      var data = JSON.parse($.trim(r));
      if(data["status"] == 1){
        $("#totalSuccessBlock").html(data['successAmount']+' / '+data['successRecord']);
        $("#totalPendingBlock").html(data['pendingAmount']+' / '+data['pendingRecord']);
        $("#totalFailedBlock").html(data['failedAmount']+' / '+data['failedRecord']);

          $("#totalBbpsSuccessBlock").html(data['successBbpsAmount']+' / '+data['successBbpsRecord']);
        $("#totalBbpsPendingBlock").html(data['pendingBbpsAmount']+' / '+data['pendingBbpsRecord']);
        $("#totalBbpsFailedBlock").html(data['failedBbpsAmount']+' / '+data['failedBbpsRecord']);

        $("#totalAepsSuccessBlock").html(data['successAepsAmount']+ ' / ' + data['successAepsRecord']);
        $("#totalAepsFailedBlock").html(data['failedAepsAmount'] + ' / ' + data['successAepsRecord']);


        $("#totalMoneySuccessBlock").html(data['successMoneyAmount']+' / '+data['successMoneyRecord']);
        $("#totalMoneyPendingBlock").html(data['pendingMoneyAmount']+' / '+data['pendingMoneyRecord']);
        $("#totalMoneyFailedBlock").html(data['failedMoneyAmount']+' / '+data['failedMoneyRecord']);


        $("#totalMoneyTransferSuccessBlock").html(data['successMoneyTransferAmount']+' / '+data['successMoneyTransferRecord']);
        $("#totalMoneyTransferPendingBlock").html(data['pendingMoneyTransferAmount']+' / '+data['pendingMoneyTransferRecord']);
        $("#totalMoneyTransferFailedBlock").html(data['failedMoneyTransferAmount']+' / '+data['failedMoneyTransferRecord']);


        //setTimeout( getDashboardSummary(), 30000 );
      }
      
    }
  });
  
}
<?php if(isset($content_block) && $content_block == 'dashboard'){ ?>
setTimeout( getDashboardSummary(), 3000 );
<?php } ?>
<?php if(isset($content_block) && $content_block == 'saving/club-chat-live'){ ?>
  getClubRoundStatus();
  getClubLiveMember();
  getClubChatList();
  setInterval( getClubChatList, 1000 );
setInterval( getClubLiveMember, 3000 );
<?php } ?>
  </script>
 

</body>

</html>
