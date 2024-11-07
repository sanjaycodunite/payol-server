<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="{meta_description}">
  <meta name="author" content="">

  <?php
    $account_id = $this->User->get_domain_account();
    $accountData = $this->User->get_account_data($account_id);
    $get_theme = $this->db->get_where('theme',array('account_id'=>$account_id))->row_array();
  ?>
  <title><?php echo $accountData['title']; ?></title>

  <!-- Favicon Icon -->
  <link rel="shortcut icon" href="{site_url}skin/front/assets/images/logo/marwarcare icon.png" />
  <!-- Custom fonts for this template-->
  <link href="{site_url}skin/admin/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <link href="{site_url}skin/admin/css/sb-admin-2.css" rel="stylesheet">
  
  <link href="{site_url}skin/admin/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

<body style="background: #f5eeee">
  <input type="hidden" id="siteUrl" value="{site_url}">
<div class="container-fluid mt-5">
<div class="card shadow mb-4">
            <div class="card-body">
              <div class="table-responsive" id="liveRecharge">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                  <thead>
                    <tr style="background: black;color: white;">
                      <th>#</th>
                      <th>RechargeID</th>
                      <th>MemberID</th>
                      <th>Name</th>
                      <th>Mobile</th>
                      <th>Operator</th>
                      <th>API ID</th>
                      <th>Amount</th>
                      <th>Date Time</th>
                      
                    </tr>
                  </thead>

                  <tbody>
                    <?php
                    $i=$totalRecord;
                    foreach($rechargeList as $list){
                    ?>


                    
                    <?php
                    if($list['status'] == 1){
                    ?>
                    <tr style="background: #dc8f01;color: white;">
                    <?php } elseif($list['status'] == 2){ ?>
                    <tr style="background: green;color: white;">  
                    <?php } elseif($list['status'] == 3 || $list['status'] == 4){ ?>
                    <tr style="background: #ca0303;color: white;">  
                    <?php } else{ ?>
                    <tr>  
                    <?php } ?>  

                      <td><?php echo $i; ?></td>
                      <td><?php echo $list['recharge_display_id']; ?></td>
                      <td><?php echo $list['user_code']; ?></td>
                      <td><?php echo $list['name']; ?></td>
                      <td><?php echo $list['mobile']; ?></td>
                      <td><?php echo $list['operator_name']; ?></td>
                      <td><?php echo $list['api_id']; ?></td>
                      <td><?php echo number_format($list['amount'],2); ?></td>
                      <td><?php echo date('d-M-Y h:i:s',strtotime($list['created'])); ?></td>
                    </tr>

                  <?php $i--;} ?>
                  </tbody>

                  
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
</div>


 <!-- Bootstrap core JavaScript-->
  <script src="{site_url}skin/admin/vendor/jquery/jquery.min.js"></script>
  <script src="{site_url}skin/admin/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="{site_url}skin/admin/vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="{site_url}skin/admin/vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <script src="{site_url}skin/admin/js/demo/datatables-demo.js"></script>
  
  <script type="text/javascript">
   //$('#liveRechargeDataTable').DataTable().ajax.reload();

   setInterval( function () {
   $(document).ready(function() { 

    var siteUrl = $("#siteUrl").val();
    var memberID = $("#selMemberID").val();
    $(".recharge-comm-loader").html("<img src='"+siteUrl+"skin/images/loading2.gif' alt='loading' />");
    $.ajax({                
      url:siteUrl+'admin/report/getLiveRechargeData',                        
      success:function(r){
        
        var data = JSON.parse($.trim(r));
        if(data["status"] == 1){
          
          $("#liveRecharge").html(data['str']);
          
        }
        else
        {
          $(".liveRecharge").html(data['str']);
        }
      }
    
    });

   })}, 3000);

   
  </script>


   
</body>

</html>