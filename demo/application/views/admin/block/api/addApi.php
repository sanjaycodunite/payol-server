{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Add New API</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('admin/api/saveApiAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <div class="row">
               <div class="col-sm-2">
                <div class="form-group">
              <label><b>Provider*</b></label>
              <input type="text" class="form-control" name="provider" id="provider" placeholder="Provider">
              <?php echo form_error('provider', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Access Key</b></label>
              <input type="text" class="form-control" name="access_key" id="access_key" placeholder="Access Key">
              <?php echo form_error('access_key', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-2">
              <div class="form-group">
              <label><b>Username</b></label>
              <input type="text" class="form-control" name="username" id="username" placeholder="Username">
              <?php echo form_error('username', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              
              <div class="col-sm-2">
                <div class="form-group">
              <label><b>Password/PIN</b></label>
              <input type="text" class="form-control" name="password" id="password" placeholder="Password/PIN">
              <?php echo form_error('password', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <?php if($isInstantPayApiAllow){ ?>
              <div class="col-sm-3">
                <div class="form-group">
                  <br /><br />
                  <input type="checkbox" name="is_instant_pay_api" value="1" id="is_instant_pay_api">
                  <label for="is_instant_pay_api"><b>Is api belongs to Instantpay ?</b></label>
              </div>
              </div>
              <?php } ?>

              </div>

              <div class="row">
                <div class="col-sm-10">
                  <div class="form-group">
                  <label><b>Request Base URL*</b></label>
                  <input type="text" class="form-control" name="request_base_url" id="request_base_url" placeholder="Request Base URL">
                  <?php echo form_error('request_base_url', '<div class="error">', '</div>'); ?>  
                  <p>Short Code : {AMOUNT}, {OPERATOR}, {CIRCLE}, {TXNID}, {MOBILE}, {MEMBERID}</p>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                  <label><b>Request Type*</b></label>
                  <select class="form-control" name="request_type">
                  <option value="">Select Type</option>
                  <?php if($requestTypeList){ ?>
                    <?php foreach($requestTypeList as $list){ ?>
                      <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                    <?php } ?>
                  <?php } ?>
                  </select>
                  <?php echo form_error('request_type', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
              </div> 
              <div class="row">
                <div class="col-sm-12">
                  <h5>Header Parameters</h5>
                  <hr />
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" name="header_is_access_key" value="1" id="header_is_access_key">
                          <label for="header_is_access_key">Access Key</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" name="header_access_key">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" name="header_is_username" value="1" id="header_is_username">
                          <label for="header_is_username">Username</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" name="header_username">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" name="header_is_password" value="1" id="header_is_password">
                          <label for="header_is_password">Password/PIN</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" name="header_password">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      
                    </div>
                    
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <h5>Request Parameters</h5>
                  <hr />
                  <div class="row">
                    <div class="col-sm-6">
                      <h5>GET Method</h5>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" name="get_is_access_key" value="1" id="get_is_access_key">
                          <label for="get_is_access_key">Access Key</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" name="get_access_key">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" name="get_is_username" value="1" id="get_is_username">
                          <label for="get_is_username">Username</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" name="get_username">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" name="get_is_password" value="1" id="get_is_password">
                          <label for="get_is_password">Password/PIN</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" name="get_password">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-12">
                          <table class="table table-bordered table-striped">
                            <tr>
                              <th>#</th>
                              <th>Key</th>
                              <th>Value</th>
                              <th>Key Value</th>
                            </tr>
                            <?php for($i=1; $i<=10; $i++){ ?>
                              <tr>
                                <td><?php echo $i; ?>.</td>
                                <td>
                                  <input type="text" class="form-control" name="get_para_key[<?php echo $i; ?>]">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="get_para_val[<?php echo $i; ?>]">
                                  <p style="font-size: 11px; margin-bottom: 0px;">Put # for dynamic data</p>
                                  <p style="font-size: 11px; margin-bottom: 0px;">If key has multi value, please enter "|" seperated values see example</p>
                                  <p style="font-size: 11px; margin-bottom: 0px;">Prepaid|Special|DTH|Postpaid</p>
                                </td>
                                <td>
                                  <select class="form-control" name="get_para_key_val[<?php echo $i; ?>]">
                                    <option value="0">Select</option>
                                    <?php if($paraValueList){ ?>
                                      <?php foreach($paraValueList as $list){ ?>
                                        <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                                      <?php } ?>
                                    <?php } ?>
                                  </select>
                                </td>
                              </tr>
                            <?php } ?>
                          </table>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <h5>POST Method</h5>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" name="post_is_access_key" value="1" id="post_is_access_key">
                          <label for="post_is_access_key">Access Key</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" name="post_access_key">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" name="post_is_username" value="1" id="post_is_username">
                          <label for="post_is_username">Username</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" name="post_username">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" name="post_is_password" value="1" id="post_is_password">
                          <label for="post_is_password">Password/PIN</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" name="post_password">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-12">
                          <table class="table table-bordered table-striped">
                            <tr>
                              <th>#</th>
                              <th>Key</th>
                              <th>Value</th>
                              <th>Key Value</th>
                            </tr>
                            <?php for($i=1; $i<=10; $i++){ ?>
                              <tr>
                                <td><?php echo $i; ?>.</td>
                                <td>
                                  <input type="text" class="form-control" name="post_para_key[<?php echo $i; ?>]">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="post_para_val[<?php echo $i; ?>]">
                                  <p style="font-size: 11px; margin-bottom: 0px;">Put # for dynamic data</p>
                                  <p style="font-size: 11px; margin-bottom: 0px;">If key has multi value, please enter "|" seperated values see example</p>
                                  <p style="font-size: 11px; margin-bottom: 0px;">Prepaid|Special|DTH|Postpaid</p>
                                </td>
                                <td>
                                  <select class="form-control" name="post_para_key_val[<?php echo $i; ?>]">
                                    <option value="0">Select</option>
                                    <?php if($paraValueList){ ?>
                                      <?php foreach($paraValueList as $list){ ?>
                                        <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                                      <?php } ?>
                                    <?php } ?>
                                  </select>
                                </td>
                              </tr>
                            <?php } ?>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <h5>Response Type</h5>
                  <hr />
                </div>
                <div class="col-sm-3">
                   <div class="form-group">
                    <label><b>Type</b></label>
                    <select class="form-control" name="response_type" id="response_type">
                      <option value="">Select</option>
                      <?php if($responseTypeList){ ?>
                        <?php foreach($responseTypeList as $list){ ?>
                          <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>  
                    </div>
                </div>
                <div class="col-sm-3" id="seperator_block" style="display: none;">
                   <div class="form-group">
                    <label><b>Seperator</b></label>
                    <input type="text" class="form-control" name="response_seperator">
                    </div>
                </div>
              </div>
              <div class="row" id="str_res_block" style="display: none;">
                <div class="col-sm-6">
                  <table class="table table-bordered table-striped">
                      <tr>
                        <th>#</th>
                        <th>Value</th>
                      </tr>
                      <?php for($i=1; $i<=8; $i++){ ?>
                        <tr>
                          <td><?php echo $i; ?>.</td>
                          <td>
                            <select class="form-control" onchange="showStrResponseStatus(<?php echo $i; ?>,this.value)" name="str_res_type[<?php echo $i; ?>]">
                              <option value="0">Select</option>
                              <?php if($resValueList){ ?>
                                <?php foreach($resValueList as $list){ ?>
                                  <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                                <?php } ?>
                              <?php } ?>
                            </select>
                            <table class="table table-striped table-bordered" id="str_res_status_<?php echo $i; ?>" style="display: none;">
                              <tr>
                                <td>Success <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Failed <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Pending <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                              </tr>
                              <tr>
                                <td>
                                  <input type="text" class="form-control" name="str_res_status_val[<?php echo $i; ?>][0]">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="str_res_status_val[<?php echo $i; ?>][1]">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="str_res_status_val[<?php echo $i; ?>][2]">
                                </td>
                              </tr>
                            </table>
                          </td>
                         
                          
                        </tr>
                      <?php } ?>
                    </table>
                </div>
              </div>

              <div class="row" id="xml_res_block" style="display: none;">
                <div class="col-sm-6">
                  <table class="table table-bordered table-striped">
                      <tr>
                        <th>#</th>
                        <th>Key</th>
                        <th>Value</th>
                      </tr>
                      <?php for($i=1; $i<=8; $i++){ ?>
                        <tr>
                          <td><?php echo $i; ?>.</td>
                          <td>
                             <input type="text" class="form-control" name="xml_res_key[<?php echo $i; ?>]">
                          </td>
                          <td>
                            <select class="form-control" onchange="showXMLResponseStatus(<?php echo $i; ?>,this.value)" name="xml_res_type[<?php echo $i; ?>]">
                              <option value="0">Select</option>
                              <?php if($resValueList){ ?>
                                <?php foreach($resValueList as $list){ ?>
                                  <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                                <?php } ?>
                              <?php } ?>
                            </select>
                            <table class="table table-striped table-bordered" id="xml_res_status_<?php echo $i; ?>" style="display: none;">
                              <tr>
                                <td>Success <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Failed <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Pending <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                              </tr>
                              <tr>
                                <td>
                                  <input type="text" class="form-control" name="xml_res_status_val[<?php echo $i; ?>][0]">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="xml_res_status_val[<?php echo $i; ?>][1]">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="xml_res_status_val[<?php echo $i; ?>][2]">
                                </td>
                              </tr>
                            </table>
                          </td>
                         
                          
                        </tr>
                      <?php } ?>
                    </table>
                </div>
              </div>

              <div class="row" id="json_res_block" style="display: none;">
                <div class="col-sm-6">
                  <table class="table table-bordered table-striped">
                      <tr>
                        <th>#</th>
                        <th>Key</th>
                        <th>Value</th>
                      </tr>
                      <?php for($i=1; $i<=8; $i++){ ?>
                        <tr>
                          <td><?php echo $i; ?>.</td>
                          <td>
                             <input type="text" class="form-control" name="json_res_key[<?php echo $i; ?>]">
                          </td>
                          <td>
                            <select class="form-control" onchange="showJsonResponseStatus(<?php echo $i; ?>,this.value)" name="json_res_type[<?php echo $i; ?>]">
                              <option value="0">Select</option>
                              <?php if($resValueList){ ?>
                                <?php foreach($resValueList as $list){ ?>
                                  <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                                <?php } ?>
                              <?php } ?>
                            </select>
                            <table class="table table-striped table-bordered" id="json_res_status_<?php echo $i; ?>" style="display: none;">
                              <tr>
                                <td>Success <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Failed <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Pending <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                              </tr>
                              <tr>
                                <td>
                                  <input type="text" class="form-control" name="json_res_status_val[<?php echo $i; ?>][0]">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="json_res_status_val[<?php echo $i; ?>][1]">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="json_res_status_val[<?php echo $i; ?>][2]">
                                </td>
                              </tr>
                            </table>
                          </td>
                         
                          
                        </tr>
                      <?php } ?>
                    </table>
                </div>
              </div>

              <!-- START GET BALANCE API -->
              <div class="row">
                <div class="col-sm-10">
                  <div class="form-group">
                  <label><b>Get Balance Base URL</b></label>
                  <input type="text" class="form-control" name="get_balance_base_url" id="get_balance_base_url" placeholder="Get Balance Base URL">
                  <?php echo form_error('get_balance_base_url', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                  <label><b>Request Type</b></label>
                  <select class="form-control" name="get_balance_request_type">
                  <option value="">Select Type</option>
                  <?php if($requestTypeList){ ?>
                    <?php foreach($requestTypeList as $list){ ?>
                      <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                    <?php } ?>
                  <?php } ?>
                  </select>
                  <?php echo form_error('get_balance_request_type', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
              </div> 
              <div class="row">
                <div class="col-sm-12">
                  <h5>Get Balance Header Parameters</h5>
                  <hr />
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" name="get_balance_header_is_access_key" value="1" id="get_balance_header_is_access_key">
                          <label for="get_balance_header_is_access_key">Access Key</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" name="get_balance_header_access_key">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" name="get_balance_header_is_username" value="1" id="get_balance_header_is_username">
                          <label for="get_balance_header_is_username">Username</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" name="get_balance_header_username">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" name="get_balance_header_is_password" value="1" id="get_balance_header_is_password">
                          <label for="get_balance_header_is_password">Password/PIN</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" name="get_balance_header_password">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      
                    </div>
                    
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <h5>Get Balance Request Parameters</h5>
                  <hr />
                  <div class="row">
                    <div class="col-sm-6">
                      <h5>GET Method</h5>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" name="get_balance_get_is_access_key" value="1" id="get_balance_get_is_access_key">
                          <label for="get_balance_get_is_access_key">Access Key</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" name="get_balance_get_access_key">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" name="get_balance_get_is_username" value="1" id="get_balance_get_is_username">
                          <label for="get_balance_get_is_username">Username</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" name="get_balance_get_username">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" name="get_balance_get_is_password" value="1" id="get_balance_get_is_password">
                          <label for="get_balance_get_is_password">Password/PIN</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" name="get_balance_get_password">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-12">
                          <table class="table table-bordered table-striped">
                            <tr>
                              <th>#</th>
                              <th>Key</th>
                              <th>Value</th>
                              <th>Key Value</th>
                            </tr>
                            <?php $x = 0; for($i=1; $i<=10; $i++){ ?>
                              <tr>
                                <td><?php echo $i; ?>.</td>
                                <td>
                                  <input type="text" class="form-control" name="get_balance_get_para_key[<?php echo $i; ?>]">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="get_balance_get_para_val[<?php echo $i; ?>]">
                                  <p style="font-size: 11px;">Put # for dynamic data</p>
                                </td>
                                <td>
                                  <select class="form-control" name="get_balance_get_para_key_val[<?php echo $i; ?>]">
                                    <option value="0">Select</option>
                                    <?php if($paraValueList){ ?>
                                      <?php foreach($paraValueList as $list){ ?>
                                        <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                                      <?php } ?>
                                    <?php } ?>
                                  </select>
                                </td>
                              </tr>
                            <?php $x++; } ?>
                          </table>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <h5>POST Method</h5>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" name="get_balance_post_is_access_key" value="1" id="get_balance_post_is_access_key">
                          <label for="get_balance_post_is_access_key">Access Key</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" name="get_balance_post_access_key">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" name="get_balance_post_is_username" value="1" id="get_balance_post_is_username">
                          <label for="get_balance_post_is_username">Username</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" name="get_balance_post_username">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" name="get_balance_post_is_password" value="1" id="get_balance_post_is_password">
                          <label for="get_balance_post_is_password">Password/PIN</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" name="get_balance_post_password">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-12">
                          <table class="table table-bordered table-striped">
                            <tr>
                              <th>#</th>
                              <th>Key</th>
                              <th>Value</th>
                              <th>Key Value</th>
                            </tr>
                            <?php $x = 0; for($i=1; $i<=10; $i++){ ?>
                              <tr>
                                <td><?php echo $i; ?>.</td>
                                <td>
                                  <input type="text" class="form-control" name="get_balance_post_para_key[<?php echo $i; ?>]">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="get_balance_post_para_val[<?php echo $i; ?>]">
                                  <p style="font-size: 11px;">Put # for dynamic data</p>
                                </td>
                                <td>
                                  <select class="form-control" name="get_balance_post_para_key_val[<?php echo $i; ?>]">
                                    <option value="0">Select</option>
                                    <?php if($paraValueList){ ?>
                                      <?php foreach($paraValueList as $list){ ?>
                                        <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                                      <?php } ?>
                                    <?php } ?>
                                  </select>
                                </td>
                              </tr>
                            <?php $x++; } ?>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <h5>Response Type</h5>
                  <hr />
                </div>
                <div class="col-sm-3">
                   <div class="form-group">
                    <label><b>Type</b></label>
                    <select class="form-control" name="get_balance_response_type" id="get_balance_response_type">
                      <option value="">Select</option>
                      <?php if($responseTypeList){ ?>
                        <?php foreach($responseTypeList as $list){ ?>
                          <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>  
                    </div>
                </div>
                <div class="col-sm-3" id="get_balance_seperator_block" style="display: none;">
                   <div class="form-group">
                    <label><b>Seperator</b></label>
                    <input type="text" class="form-control" name="get_balance_response_seperator">
                    </div>
                </div>
              </div>
              <div class="row" id="get_balance_str_res_block" style="display: none;">
                <div class="col-sm-6">
                  <table class="table table-bordered table-striped">
                      <tr>
                        <th>#</th>
                        <th>Value</th>
                      </tr>
                      <?php $x = 0; for($i=1; $i<=8; $i++){ ?>
                        <tr>
                          <td><?php echo $i; ?>.</td>
                          <td>
                            <select class="form-control" onchange="showGetBalanceStrResponseStatus(<?php echo $i; ?>,this.value)" name="get_balance_str_res_type[<?php echo $i; ?>]">
                              <option value="0">Select</option>
                              <?php if($resValueList){ ?>
                                <?php foreach($resValueList as $list){ ?>
                                  <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                                <?php } ?>
                              <?php } ?>
                            </select>
                            <table class="table table-striped table-bordered" id="get_balance_str_res_status_<?php echo $i; ?>" style="display: none;">
                              <tr>
                                <td>Success <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Failed <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Pending <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                              </tr>
                              <tr>
                                <td>
                                  <input type="text" class="form-control" name="get_balance_str_res_status_val[<?php echo $i; ?>][0]">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="get_balance_str_res_status_val[<?php echo $i; ?>][1]">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="get_balance_str_res_status_val[<?php echo $i; ?>][2]">
                                </td>
                              </tr>
                            </table>
                          </td>
                         
                          
                        </tr>
                      <?php $x++; } ?>
                    </table>
                </div>
              </div>

              <div class="row" id="get_balance_xml_res_block" style="display: none;">
                <div class="col-sm-6">
                  <table class="table table-bordered table-striped">
                      <tr>
                        <th>#</th>
                        <th>Key</th>
                        <th>Value</th>
                      </tr>
                      <?php $x = 0; for($i=1; $i<=8; $i++){ ?>
                        <tr>
                          <td><?php echo $i; ?>.</td>
                          <td>
                             <input type="text" class="form-control" name="get_balance_xml_res_key[<?php echo $i; ?>]">
                          </td>
                          <td>
                            <select class="form-control" onchange="showGetBalanceXMLResponseStatus(<?php echo $i; ?>,this.value)" name="get_balance_xml_res_type[<?php echo $i; ?>]">
                              <option value="0">Select</option>
                              <?php if($resValueList){ ?>
                                <?php foreach($resValueList as $list){ ?>
                                  <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                                <?php } ?>
                              <?php } ?>
                            </select>
                            <table class="table table-striped table-bordered" id="get_balance_xml_res_status_<?php echo $i; ?>" style="display: none;">
                              <tr>
                                <td>Success <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Failed <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Pending <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                              </tr>
                              <tr>
                                <td>
                                  <input type="text" class="form-control" name="get_balance_xml_res_status_val[<?php echo $i; ?>][0]">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="get_balance_xml_res_status_val[<?php echo $i; ?>][1]">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="get_balance_xml_res_status_val[<?php echo $i; ?>][2]">
                                </td>
                              </tr>
                            </table>
                          </td>
                         
                          
                        </tr>
                      <?php $x++; } ?>
                    </table>
                </div>
              </div>

              <div class="row" id="get_balance_json_res_block" style="display: none;">
                <div class="col-sm-6">
                  <table class="table table-bordered table-striped">
                      <tr>
                        <th>#</th>
                        <th>Key</th>
                        <th>Value</th>
                      </tr>
                      <?php for($i=1; $i<=8; $i++){ ?>
                        <tr>
                          <td><?php echo $i; ?>.</td>
                          <td>
                             <input type="text" class="form-control" name="get_balance_json_res_key[<?php echo $i; ?>]">
                          </td>
                          <td>
                            <select class="form-control" onchange="showGetBalanceJsonResponseStatus(<?php echo $i; ?>,this.value)" name="get_balance_json_res_type[<?php echo $i; ?>]">
                              <option value="0">Select</option>
                              <?php if($resValueList){ ?>
                                <?php foreach($resValueList as $list){ ?>
                                  <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                                <?php } ?>
                              <?php } ?>
                            </select>
                            <table class="table table-striped table-bordered" id="get_balance_json_res_status_<?php echo $i; ?>" style="display: none;">
                              <tr>
                                <td>Success <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Failed <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Pending <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                              </tr>
                              <tr>
                                <td>
                                  <input type="text" class="form-control" name="get_balance_json_res_status_val[<?php echo $i; ?>][0]">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="get_balance_json_res_status_val[<?php echo $i; ?>][1]">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="get_balance_json_res_status_val[<?php echo $i; ?>][2]">
                                </td>
                              </tr>
                            </table>
                          </td>
                         
                          
                        </tr>
                      <?php } ?>
                    </table>
                </div>
              </div>

              <!-- END GET BALANCE API -->

              <!-- START CHECK STATUS API -->
              <div class="row">
                <div class="col-sm-10">
                  <div class="form-group">
                  <label><b>Check Status Base URL</b></label>
                  <input type="text" class="form-control" name="check_status_base_url" id="check_status_base_url" placeholder="Check Balance Base URL">
                  <?php echo form_error('check_status_base_url', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                  <label><b>Request Type</b></label>
                  <select class="form-control" name="check_status_request_type">
                  <option value="">Select Type</option>
                  <?php if($requestTypeList){ ?>
                    <?php foreach($requestTypeList as $list){ ?>
                      <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                    <?php } ?>
                  <?php } ?>
                  </select>
                  <?php echo form_error('check_status_request_type', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
              </div> 
              <div class="row">
                <div class="col-sm-12">
                  <h5>Check Status Request Parameters</h5>
                  <hr />
                  <div class="row">
                    <div class="col-sm-6">
                      <h5>GET Method</h5>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" name="check_status_get_is_access_key" value="1" id="check_status_get_is_access_key">
                          <label for="check_status_get_is_access_key">Access Key</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" name="check_status_get_access_key">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" name="check_status_get_is_username" value="1" id="check_status_get_is_username">
                          <label for="check_status_get_is_username">Username</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" name="check_status_get_username">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" name="check_status_get_is_password" value="1" id="check_status_get_is_password">
                          <label for="check_status_get_is_password">Password/PIN</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" name="check_status_get_password">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-12">
                          <table class="table table-bordered table-striped">
                            <tr>
                              <th>#</th>
                              <th>Key</th>
                              <th>Value</th>
                              <th>Key Value</th>
                            </tr>
                            <?php $x = 0; for($i=1; $i<=10; $i++){ ?>
                              <tr>
                                <td><?php echo $i; ?>.</td>
                                <td>
                                  <input type="text" class="form-control" name="check_status_get_para_key[<?php echo $i; ?>]">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="check_status_get_para_val[<?php echo $i; ?>]">
                                  <p style="font-size: 11px;">Put # for dynamic data</p>
                                </td>
                                <td>
                                  <select class="form-control" name="check_status_get_para_key_val[<?php echo $i; ?>]">
                                    <option value="0">Select</option>
                                    <?php if($paraValueList){ ?>
                                      <?php foreach($paraValueList as $list){ ?>
                                        <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                                      <?php } ?>
                                    <?php } ?>
                                  </select>
                                </td>
                              </tr>
                            <?php $x++; } ?>
                          </table>
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <h5>POST Method</h5>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" name="check_status_post_is_access_key" value="1" id="check_status_post_is_access_key">
                          <label for="check_status_post_is_access_key">Access Key</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" name="check_status_post_access_key">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" name="check_status_post_is_username" value="1" id="check_status_post_is_username">
                          <label for="check_status_post_is_username">Username</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" name="check_status_post_username">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" name="check_status_post_is_password" value="1" id="check_status_post_is_password">
                          <label for="check_status_post_is_password">Password/PIN</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" name="check_status_post_password">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-12">
                          <table class="table table-bordered table-striped">
                            <tr>
                              <th>#</th>
                              <th>Key</th>
                              <th>Value</th>
                              <th>Key Value</th>
                            </tr>
                            <?php $x = 0; for($i=1; $i<=10; $i++){ ?>
                              <tr>
                                <td><?php echo $i; ?>.</td>
                                <td>
                                  <input type="text" class="form-control" name="check_status_post_para_key[<?php echo $i; ?>]">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="check_status_post_para_val[<?php echo $i; ?>]">
                                  <p style="font-size: 11px;">Put # for dynamic data</p>
                                </td>
                                <td>
                                  <select class="form-control" name="check_status_post_para_key_val[<?php echo $i; ?>]">
                                    <option value="0">Select</option>
                                    <?php if($paraValueList){ ?>
                                      <?php foreach($paraValueList as $list){ ?>
                                        <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                                      <?php } ?>
                                    <?php } ?>
                                  </select>
                                </td>
                              </tr>
                            <?php $x++; } ?>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <h5>Response Type</h5>
                  <hr />
                </div>
                <div class="col-sm-3">
                   <div class="form-group">
                    <label><b>Type</b></label>
                    <select class="form-control" name="check_status_response_type" id="check_status_response_type">
                      <option value="">Select</option>
                      <?php if($responseTypeList){ ?>
                        <?php foreach($responseTypeList as $list){ ?>
                          <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>  
                    </div>
                </div>
                <div class="col-sm-3" id="check_status_seperator_block" style="display: none;">
                   <div class="form-group">
                    <label><b>Seperator</b></label>
                    <input type="text" class="form-control" name="check_status_response_seperator">
                    </div>
                </div>
              </div>
              <div class="row" id="check_status_str_res_block" style="display: none;">
                <div class="col-sm-6">
                  <table class="table table-bordered table-striped">
                      <tr>
                        <th>#</th>
                        <th>Value</th>
                      </tr>
                      <?php $x = 0; for($i=1; $i<=8; $i++){ ?>
                        <tr>
                          <td><?php echo $i; ?>.</td>
                          <td>
                            <select class="form-control" onchange="showCheckStatusStrResponseStatus(<?php echo $i; ?>,this.value)" name="check_status_str_res_type[<?php echo $i; ?>]">
                              <option value="0">Select</option>
                              <?php if($resValueList){ ?>
                                <?php foreach($resValueList as $list){ ?>
                                  <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                                <?php } ?>
                              <?php } ?>
                            </select>
                            <table class="table table-striped table-bordered" id="check_status_str_res_status_<?php echo $i; ?>" style="display: none;">
                              <tr>
                                <td>Success <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Failed <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Pending <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                              </tr>
                              <tr>
                                <td>
                                  <input type="text" class="form-control" name="check_status_str_res_status_val[<?php echo $i; ?>][0]">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="check_status_str_res_status_val[<?php echo $i; ?>][1]">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="check_status_str_res_status_val[<?php echo $i; ?>][2]">
                                </td>
                              </tr>
                            </table>
                          </td>
                         
                          
                        </tr>
                      <?php $x++; } ?>
                    </table>
                </div>
              </div>

              <div class="row" id="check_status_xml_res_block" style="display: none;">
                <div class="col-sm-6">
                  <table class="table table-bordered table-striped">
                      <tr>
                        <th>#</th>
                        <th>Key</th>
                        <th>Value</th>
                      </tr>
                      <?php $x = 0; for($i=1; $i<=8; $i++){ ?>
                        <tr>
                          <td><?php echo $i; ?>.</td>
                          <td>
                             <input type="text" class="form-control" name="check_status_xml_res_key[<?php echo $i; ?>]">
                          </td>
                          <td>
                            <select class="form-control" onchange="showCheckStatusXMLResponseStatus(<?php echo $i; ?>,this.value)" name="check_status_xml_res_type[<?php echo $i; ?>]">
                              <option value="0">Select</option>
                              <?php if($resValueList){ ?>
                                <?php foreach($resValueList as $list){ ?>
                                  <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                                <?php } ?>
                              <?php } ?>
                            </select>
                            <table class="table table-striped table-bordered" id="check_status_xml_res_status_<?php echo $i; ?>" style="display: none;">
                              <tr>
                                <td>Success <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Failed <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Pending <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                              </tr>
                              <tr>
                                <td>
                                  <input type="text" class="form-control" name="check_status_xml_res_status_val[<?php echo $i; ?>][0]">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="check_status_xml_res_status_val[<?php echo $i; ?>][1]">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="check_status_xml_res_status_val[<?php echo $i; ?>][2]">
                                </td>
                              </tr>
                            </table>
                          </td>
                         
                          
                        </tr>
                      <?php $x++; } ?>
                    </table>
                </div>
              </div>

              <div class="row" id="check_status_json_res_block" style="display: none;">
                <div class="col-sm-6">
                  <table class="table table-bordered table-striped">
                      <tr>
                        <th>#</th>
                        <th>Key</th>
                        <th>Value</th>
                      </tr>
                      <?php for($i=1; $i<=8; $i++){ ?>
                        <tr>
                          <td><?php echo $i; ?>.</td>
                          <td>
                             <input type="text" class="form-control" name="check_status_json_res_key[<?php echo $i; ?>]">
                          </td>
                          <td>
                            <select class="form-control" onchange="showCheckStatusJsonResponseStatus(<?php echo $i; ?>,this.value)" name="check_status_json_res_type[<?php echo $i; ?>]">
                              <option value="0">Select</option>
                              <?php if($resValueList){ ?>
                                <?php foreach($resValueList as $list){ ?>
                                  <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                                <?php } ?>
                              <?php } ?>
                            </select>
                            <table class="table table-striped table-bordered" id="check_status_json_res_status_<?php echo $i; ?>" style="display: none;">
                              <tr>
                                <td>Success <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Failed <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Pending <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                              </tr>
                              <tr>
                                <td>
                                  <input type="text" class="form-control" name="check_status_json_res_status_val[<?php echo $i; ?>][0]">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="check_status_json_res_status_val[<?php echo $i; ?>][1]">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="check_status_json_res_status_val[<?php echo $i; ?>][2]">
                                </td>
                              </tr>
                            </table>
                          </td>
                         
                          
                        </tr>
                      <?php } ?>
                    </table>
                </div>
              </div>

              <!-- END CHECK STATUS API -->

              <!-- START CALLBACK API -->

              <div class="row">
                <div class="col-sm-10">
                  <div class="form-group">
                  <label><b>Callback Base URL</b></label>
                  <input type="text" readonly="readonly" class="form-control" name="callback_base_url" value="<?php echo base_url('cron/rechargeCallback/'.$callbackCode.'/?'); ?>" id="callback_base_url" placeholder="Callback Base URL">
                  <input type="hidden" name="callbackCode" value="{callbackCode}">
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                  <label><b>Response Type*</b></label>
                  <select class="form-control" name="callback_response_type">
                  <option value="">Select Type</option>
                  <option value="1">GET</option>
                  <option value="2">POST</option>
                  </select>
                  <?php echo form_error('callback_request_type', '<div class="error">', '</div>'); ?>  
                  </div>
                </div>
              </div> 
              <div class="row">
                <div class="col-sm-12">
                  <h5>Callback Parameters</h5>
                  <hr />
                </div>
                <div class="col-sm-6">
                  <table class="table table-bordered table-striped">
                      <tr>
                        <th>#</th>
                        <th>Key</th>
                        <th>Value</th>
                      </tr>
                      <?php $x = 0; for($i=1; $i<=8; $i++){ ?>
                        <tr>
                          <td><?php echo $i; ?>.</td>
                          <td>
                             <input type="text" class="form-control" name="call_back_res_key[<?php echo $i; ?>]">
                          </td>
                          <td>
                            <select class="form-control" onchange="showCallbackResponseStatus(<?php echo $i; ?>,this.value)" name="call_back_res_type[<?php echo $i; ?>]">
                              <option value="0">Select</option>
                              <?php if($resValueList){ ?>
                                <?php foreach($resValueList as $list){ ?>
                                  <option value="<?php echo $list['id']; ?>"><?php echo $list['title']; ?></option>
                                <?php } ?>
                              <?php } ?>
                            </select>
                            <table class="table table-striped table-bordered" id="call_back_res_status_<?php echo $i; ?>" style="display: none;">
                              <tr>
                                <td>Success <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Failed <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Pending <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                              </tr>
                              <tr>
                                <td>
                                  <input type="text" class="form-control" name="call_back_res_status_val[<?php echo $i; ?>][0]">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="call_back_res_status_val[<?php echo $i; ?>][1]">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="call_back_res_status_val[<?php echo $i; ?>][2]">
                                </td>
                              </tr>
                            </table>
                          </td>
                         
                          
                        </tr>
                      <?php $x++; } ?>
                    </table>
                </div>
              </div>

              <!-- END CALLBACK API -->




              
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




