{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>API Circle</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('superadmin/api/saveApiCircle', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <input type="hidden" value="<?php echo $api_id;?>" name="api_id">

             
              <div class="row">
                
                        <div class="col-sm-12">
                          <table class="table table-bordered table-striped">
                            <tr>
                              <th>#</th>
                              <th>Circle</th>
                              <th>Code</th>
                            </tr>
                            <?php if($operatorList){ ?>
                              <?php $i = 1; foreach($operatorList as $list){ ?>
                              <tr>
                                <td><?php echo $i; ?>.</td>
                                <td>
                                  <?php echo $list['circle_name']; ?>
                                  
                                </td>
                                <td>
                                  <input type="hidden" value="<?php echo $list['id']; ?>" class="form-control" name="oprator_id[<?php echo $i; ?>]">
                                  <input type="text" value="<?php echo $list['code']; ?>" class="form-control" name="oprator_code[<?php echo $i; ?>]">
                                </td>
                                
                              </tr>
                              <?php $i++; } ?>
                            <?php } ?>
                          </table>
                        </div>
                      </div>
                
              
              

              
          </div>
        </div>
        <div class="card shadow">
        <div class="card-header py-3 text-right">
        <button type="submit" class="btn btn-success">Submit</button>
        <button onclick="window.history.back()" type="button" class="btn btn-secondary">Cancel</button>
        </div>    
        </div>    
 <?php echo form_close(); ?>     
    </div>




