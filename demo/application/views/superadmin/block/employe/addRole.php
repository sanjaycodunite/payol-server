<div class="row">
{system_message}    
{system_info}
<div class="col-sm-4">
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Add Role</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('superadmin/employe/saveRole', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row"  id="before_user_row">

                <div class="col-sm-12">
                <div class="form-group">
                <label><b>Title*</b></label>
                <input type="text" class="form-control" name="title" placeholder="Title" value="<?php echo set_value('title') ?>">
                <?php echo form_error('title', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>

                
                <div class="col-sm-12">
                <div class="form-group">
                <label><b>Status</b></label>
                <select class="form-control" name="status">
                    <option value="1">Active</option>
                    <option value="0">Deactive</option>
                </select>
                <?php echo form_error('status', '<div class="error">', '</div>'); ?>  
                </div>
                  
                </div>                
                  

			  
			   
              </div>
          </div>
        <div class="card-footer py-3 text-right">
        <button type="submit" class="btn btn-success">Submit</button>
        <button onclick="window.history.back()" type="button" class="btn btn-secondary">Cancel</button>
        </div>    
        </div>    
  
    </div>

    <div class="col-sm-8">
      <div class="row">  
        <?php if($menuList){ ?>
      <?php foreach($menuList as $list){ ?>
    <div class="col-md-6">
                <div class="box box-success">                    
                    
          <div class="box-body">                                                           
                                
             
                          
                          <div class="form-group">
                              <label><b><?php echo $list['title']; ?> Permission:</b></label> <br />
                              
                                  <input type="checkbox" name="menu_id[]" value="<?php echo $list['id']; ?>" id="menu<?php echo $list['id']; ?>">
                                  <label for="menu<?php echo $list['id']; ?>"><?php echo $list['title']; ?></label>   <br />                                 
                    
                              
                              <?php if($list['subMenu']){ ?>
                                <?php foreach($list['subMenu'] as $slist){ ?>
                              
                                      <input type="checkbox" name="sub_menu_id[]" value="<?php echo $slist['id']; ?>" id="submenu<?php echo $slist['id']; ?>">
                                      <label for="submenu<?php echo $slist['id']; ?>"><?php echo $slist['title']; ?></label>                       <br />             
                        
                              
                                <?php } ?>
                              <?php } ?>
                
                          </div>
                          
                    
                </div><!-- /.box -->
            </div> 
    </div>
    <?php } ?>
    <?php } ?>
</div>

    </div>
    <?php echo form_close(); ?>    
</div>
</div>



