
<div class="container-fluid">
<div class="col-sm-8">
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Change Theme</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row">
              <div class="col-sm-12">
              <div class="form-group">
              <div  class="color_theme">
              <ul style="margin: 0px !important;
    padding: 0px !important;">
                <li <?php if($themeData['theme_id'] == 0 || $themeData['theme_id'] == ''){ ?>  class="active" <?php } ?>><button type="button" class="color_btn color_defult" onclick="location.href='{site_url}superadmin/setting/updateTheme/0'"></button></li>
                <li <?php if($themeData['theme_id'] == 1){ ?>  class="active" <?php } ?>><button type="button" class="color_btn" onclick="location.href='{site_url}superadmin/setting/updateTheme/1'"></button></li>
                <li <?php if($themeData['theme_id'] == 2){ ?>  class="active" <?php } ?>><button type="button" class="color_btn" onclick="location.href='{site_url}superadmin/setting/updateTheme/2'"></button></li>
                <li <?php if($themeData['theme_id'] == 3){ ?>  class="active" <?php } ?>><button type="button" class="color_btn" onclick="location.href='{site_url}superadmin/setting/updateTheme/3'"></button></li>
                <li <?php if($themeData['theme_id'] == 4){ ?>  class="active" <?php } ?>><button type="button" class="color_btn" onclick="location.href='{site_url}superadmin/setting/updateTheme/4'"></button></li>
                <li <?php if($themeData['theme_id'] == 5){ ?>  class="active" <?php } ?>><button type="button" class="color_btn" onclick="location.href='{site_url}superadmin/setting/updateTheme/5'"></button></li>
                <li <?php if($themeData['theme_id'] == 6){ ?>  class="active" <?php } ?>><button type="button" class="color_btn" onclick="location.href='{site_url}superadmin/setting/updateTheme/6'"></button></li>

              </ul></div>
              <!-- <select class="form-control" name="theme_id" onchange="location.href=this.value">
                <option value="{site_url}admin/setting/updateTheme/0">Default Theme</option>
                <option value="{site_url}admin/setting/updateTheme/1" <?php if($themeData['theme_id'] == 1){ ?> selected="" <?php } ?>>Dark Black</option>
                <option value="{site_url}admin/setting/updateTheme/2" <?php if($themeData['theme_id'] == 2){ ?> selected="" <?php } ?>>Dark Blue</option>
                <option value="{site_url}admin/setting/updateTheme/3" <?php if($themeData['theme_id'] == 3){ ?> selected="" <?php } ?>>Dark Red</option>
                <option value="{site_url}admin/setting/updateTheme/4" <?php if($themeData['theme_id'] == 4){ ?> selected="" <?php } ?>>Grey</option>
                <option value="{site_url}admin/setting/updateTheme/5" <?php if($themeData['theme_id'] == 5){ ?> selected="" <?php } ?>>Gradient Red</option>
                <option value="{site_url}admin/setting/updateTheme/6" <?php if($themeData['theme_id'] == 6){ ?> selected="" <?php } ?>>Gradient Blue</option>
              </select> -->
              </div>
              </div>
              
              </div>

              

                

          </div>
        </div>
      </div>
    </div>
            
    </div>




