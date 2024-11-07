{system_message}    
{system_info}
<div class="card shadow ">
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-6">
                <h4><b>Update API</b></h4>
                </div>
                
                <div class="col-sm-6  text-right">
                <button onclick="window.history.back()" class="btn btn-primary"><i class="fa fa-arrow-left"> Back</i></button>
                </div>                  
              </div>  
              
            </div>
            <div class="card-body">
            <?php echo form_open_multipart('employe/api/updateApiAuth', array('id' => 'employe_profile'),array('method'=>'post')); ?>
              <input type="hidden" value="<?php echo $site_url;?>" id="siteUrl">
              <input type="hidden" value="<?php echo $api_id;?>" name="api_id">
              <div class="row">
               <div class="col-sm-2">
                <div class="form-group">
              <label><b>Provider*</b></label>
              <input type="text" class="form-control" name="provider" id="provider" placeholder="Provider" value="<?php echo $apiData['provider']; ?>">
              <?php echo form_error('provider', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-3">
              <div class="form-group">
              <label><b>Access Key</b></label>
              <input type="text" class="form-control" name="access_key" id="access_key" placeholder="Access Key" value="<?php echo $apiData['access_key']; ?>">
              <?php echo form_error('access_key', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <div class="col-sm-2">
              <div class="form-group">
              <label><b>Username</b></label>
              <input type="text" class="form-control" name="username" id="username" placeholder="Username" value="<?php echo $apiData['username']; ?>">
              <?php echo form_error('username', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              
              <div class="col-sm-2">
                <div class="form-group">
              <label><b>Password/PIN</b></label>
              <input type="text" class="form-control" name="password" id="password" placeholder="Password/PIN" value="<?php echo $apiData['password']; ?>">
              <?php echo form_error('password', '<div class="error">', '</div>'); ?>  
              </div>
              </div>
              <?php if($isInstantPayApiAllow){ ?>
              <div class="col-sm-3">
                <div class="form-group">
                  <br /><br />
                  <input type="checkbox" <?php if(isset($apiData['is_instantpay_api']) && $apiData['is_instantpay_api'] == 1){ ?> checked="checked" <?php } ?> name="is_instant_pay_api" value="1" id="is_instant_pay_api">
                  <label for="is_instant_pay_api"><b>Is api belongs to Instantpay ?</b></label>
              </div>
              </div>
              <?php } ?>
              
              </div>

              <div class="row">
                <div class="col-sm-10">
                  <div class="form-group">
                  <label><b>Request Base URL*</b></label>
                  <input type="text" class="form-control" name="request_base_url" id="request_base_url" placeholder="Request Base URL" value="<?php echo $apiData['request_base_url']; ?>">
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
                      <option value="<?php echo $list['id']; ?>" <?php if($apiData['request_type'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['title']; ?></option>
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
                          <input type="checkbox" <?php if(isset($getHeaderData['is_access_key']) && $getHeaderData['is_access_key'] == 1){ ?> checked="checked" <?php } ?> name="header_is_access_key" value="1" id="header_is_access_key">
                          <label for="header_is_access_key">Access Key</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" value="<?php echo isset($getHeaderData['access_key']) ? $getHeaderData['access_key'] : ''; ?>" name="header_access_key">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" <?php if(isset($getHeaderData['is_username']) && $getHeaderData['is_username'] == 1){ ?> checked="checked" <?php } ?> name="header_is_username" value="1" id="header_is_username">
                          <label for="header_is_username">Username</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" value="<?php echo isset($getHeaderData['username_key']) ? $getHeaderData['username_key'] : ''; ?>" name="header_username">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" <?php if(isset($getHeaderData['is_password']) && $getHeaderData['is_password'] == 1){ ?> checked="checked" <?php } ?> name="header_is_password" value="1" id="header_is_password">
                          <label for="header_is_password">Password/PIN</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" value="<?php echo isset($getHeaderData['password_key']) ? $getHeaderData['password_key'] : ''; ?>" name="header_password">
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
                          <input type="checkbox" <?php if(isset($getMethodData['is_access_key']) && $getMethodData['is_access_key'] == 1){ ?> checked="checked" <?php } ?> name="get_is_access_key" value="1" id="get_is_access_key">
                          <label for="get_is_access_key">Access Key</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" value="<?php echo isset($getMethodData['access_key']) ? $getMethodData['access_key'] : ''; ?>" name="get_access_key">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" <?php if(isset($getMethodData['is_username']) && $getMethodData['is_username'] == 1){ ?> checked="checked" <?php } ?> name="get_is_username" value="1" id="get_is_username">
                          <label for="get_is_username">Username</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" value="<?php echo isset($getMethodData['username_key']) ? $getMethodData['username_key'] : ''; ?>" name="get_username">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" <?php if(isset($getMethodData['is_password']) && $getMethodData['is_password'] == 1){ ?> checked="checked" <?php } ?> name="get_is_password" value="1" id="get_is_password">
                          <label for="get_is_password">Password/PIN</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" value="<?php echo isset($getMethodData['password_key']) ? $getMethodData['password_key'] : ''; ?>" name="get_password">
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
                                  <input type="text" class="form-control" name="get_para_key[<?php echo $i; ?>]" value="<?php echo isset($getParaList[$x]['para_key']) ? $getParaList[$x]['para_key'] : ''; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="get_para_val[<?php echo $i; ?>]" value="<?php echo isset($getParaList[$x]['value']) ? $getParaList[$x]['value'] : ''; ?>">
                                  <p style="font-size: 11px; margin-bottom: 0px;">Put # for dynamic data</p>
                                  <p style="font-size: 11px; margin-bottom: 0px;">If key has multi value, please enter "|" seperated values see example</p>
                                  <p style="font-size: 11px; margin-bottom: 0px;">Prepaid|Special|DTH|Postpaid</p>
                                </td>
                                <td>
                                  <select class="form-control" name="get_para_key_val[<?php echo $i; ?>]">
                                    <option value="0">Select</option>
                                    <?php if($paraValueList){ ?>
                                      <?php foreach($paraValueList as $list){ ?>
                                        <option value="<?php echo $list['id']; ?>" <?php if(isset($getParaList[$x]['value_id']) && $getParaList[$x]['value_id'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['title']; ?></option>
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
                          <input type="checkbox" <?php if(isset($postMethodData['is_access_key']) && $postMethodData['is_access_key'] == 1){ ?> checked="checked" <?php } ?> name="post_is_access_key" value="1" id="post_is_access_key">
                          <label for="post_is_access_key">Access Key</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" value="<?php echo isset($postMethodData['access_key']) ? $postMethodData['access_key'] : ''; ?>" name="post_access_key">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" <?php if(isset($postMethodData['is_username']) && $postMethodData['is_username'] == 1){ ?> checked="checked" <?php } ?> name="post_is_username" value="1" id="post_is_username">
                          <label for="post_is_username">Username</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" value="<?php echo isset($postMethodData['username_key']) ? $postMethodData['username_key'] : ''; ?>" name="post_username">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" <?php if(isset($postMethodData['is_password']) && $postMethodData['is_password'] == 1){ ?> checked="checked" <?php } ?> name="post_is_password" value="1" id="post_is_password">
                          <label for="post_is_password">Password/PIN</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" value="<?php echo isset($postMethodData['password_key']) ? $postMethodData['password_key'] : ''; ?>" name="post_password">
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
                                  <input type="text" class="form-control" name="post_para_key[<?php echo $i; ?>]" value="<?php echo isset($postParaList[$x]['para_key']) ? $postParaList[$x]['para_key'] : ''; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="post_para_val[<?php echo $i; ?>]" value="<?php echo isset($postParaList[$x]['value']) ? $postParaList[$x]['value'] : ''; ?>">
                                  <p style="font-size: 11px; margin-bottom: 0px;">Put # for dynamic data</p>
                                  <p style="font-size: 11px; margin-bottom: 0px;">If key has multi value, please enter "|" seperated values see example</p>
                                  <p style="font-size: 11px; margin-bottom: 0px;">Prepaid|Special|DTH|Postpaid</p>
                                </td>
                                <td>
                                  <select class="form-control" name="post_para_key_val[<?php echo $i; ?>]">
                                    <option value="0">Select</option>
                                    <?php if($paraValueList){ ?>
                                      <?php foreach($paraValueList as $list){ ?>
                                        <option value="<?php echo $list['id']; ?>" <?php if(isset($postParaList[$x]['value_id']) && $postParaList[$x]['value_id'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['title']; ?></option>
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
                    <select class="form-control" name="response_type" id="response_type">
                      <option value="">Select</option>
                      <?php if($responseTypeList){ ?>
                        <?php foreach($responseTypeList as $list){ ?>
                          <option value="<?php echo $list['id']; ?>" <?php if($apiData['response_type'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['title']; ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>  
                    </div>
                </div>
                <div class="col-sm-3" id="seperator_block" <?php if($apiData['response_type'] != 1){ ?> style="display: none;" <?php } ?>>
                   <div class="form-group">
                    <label><b>Seperator</b></label>
                    <input type="text" class="form-control" value="<?php echo $apiData['response_seperator']; ?>" name="response_seperator">
                    </div>
                </div>
              </div>
              <div class="row" id="str_res_block" <?php if($apiData['response_type'] != 1){ ?> style="display: none;" <?php } ?>>
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
                            <select class="form-control" onchange="showStrResponseStatus(<?php echo $i; ?>,this.value)" name="str_res_type[<?php echo $i; ?>]">
                              <option value="0">Select</option>
                              <?php if($resValueList){ ?>
                                <?php foreach($resValueList as $list){ ?>
                                  <option value="<?php echo $list['id']; ?>" <?php if(isset($responseParaList[$x]['value_id']) && $responseParaList[$x]['value_id'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['title']; ?></option>
                                <?php } ?>
                              <?php } ?>
                            </select>
                            <table class="table table-striped table-bordered" id="str_res_status_<?php echo $i; ?>"<?php if(isset($responseParaList[$x]['value_id']) && $responseParaList[$x]['value_id'] == 2){ ?> style="display: block;" <?php } else { ?> style="display: none;" <?php } ?>>
                              <tr>
                                <td>Success <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Failed <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Pending <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                              </tr>
                              <tr>
                                <td>
                                  <input type="text" class="form-control" name="str_res_status_val[<?php echo $i; ?>][0]" value="<?php echo isset($responseParaList[$x]['success_val']) ? $responseParaList[$x]['success_val'] : ''; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="str_res_status_val[<?php echo $i; ?>][1]" value="<?php echo isset($responseParaList[$x]['failed_val']) ? $responseParaList[$x]['failed_val'] : ''; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="str_res_status_val[<?php echo $i; ?>][2]" value="<?php echo isset($responseParaList[$x]['pending_val']) ? $responseParaList[$x]['pending_val'] : ''; ?>">
                                </td>
                              </tr>
                            </table>
                          </td>
                         
                          
                        </tr>
                      <?php $x++; } ?>
                    </table>
                </div>
              </div>

              <div class="row" id="xml_res_block" <?php if($apiData['response_type'] != 2){ ?> style="display: none;" <?php } ?>>
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
                             <input type="text" class="form-control" name="xml_res_key[<?php echo $i; ?>]" value="<?php echo isset($responseParaList[$x]['para_key']) ? $responseParaList[$x]['para_key'] : ''; ?>">
                          </td>
                          <td>
                            <select class="form-control" onchange="showXMLResponseStatus(<?php echo $i; ?>,this.value)" name="xml_res_type[<?php echo $i; ?>]">
                              <option value="0">Select</option>
                              <?php if($resValueList){ ?>
                                <?php foreach($resValueList as $list){ ?>
                                  <option value="<?php echo $list['id']; ?>" <?php if(isset($responseParaList[$x]['value_id']) && $responseParaList[$x]['value_id'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['title']; ?></option>
                                <?php } ?>
                              <?php } ?>
                            </select>
                            <table class="table table-striped table-bordered" id="xml_res_status_<?php echo $i; ?>"<?php if(isset($responseParaList[$x]['value_id']) && $responseParaList[$x]['value_id'] == 2){ ?> style="display: block;" <?php } else { ?> style="display: none;" <?php } ?>>
                              <tr>
                                <td>Success <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Failed <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Pending <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                              </tr>
                              <tr>
                                <td>
                                  <input type="text" class="form-control" name="xml_res_status_val[<?php echo $i; ?>][0]" value="<?php echo isset($responseParaList[$x]['success_val']) ? $responseParaList[$x]['success_val'] : ''; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="xml_res_status_val[<?php echo $i; ?>][1]" value="<?php echo isset($responseParaList[$x]['failed_val']) ? $responseParaList[$x]['failed_val'] : ''; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="xml_res_status_val[<?php echo $i; ?>][2]" value="<?php echo isset($responseParaList[$x]['pending_val']) ? $responseParaList[$x]['pending_val'] : ''; ?>">
                                </td>
                              </tr>
                            </table>
                          </td>
                         
                          
                        </tr>
                      <?php $x++; } ?>
                    </table>
                </div>
              </div>

              <div class="row" id="json_res_block" <?php if($apiData['response_type'] != 3){ ?> style="display: none;" <?php } ?>>
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
                             <input type="text" class="form-control" name="json_res_key[<?php echo $i; ?>]" value="<?php echo isset($responseParaList[$x]['para_key']) ? $responseParaList[$x]['para_key'] : ''; ?>">
                          </td>
                          <td>
                            <select class="form-control" onchange="showJsonResponseStatus(<?php echo $i; ?>,this.value)" name="json_res_type[<?php echo $i; ?>]">
                              <option value="0">Select</option>
                              <?php if($resValueList){ ?>
                                <?php foreach($resValueList as $list){ ?>
                                  <option value="<?php echo $list['id']; ?>" <?php if(isset($responseParaList[$x]['value_id']) && $responseParaList[$x]['value_id'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['title']; ?></option>
                                <?php } ?>
                              <?php } ?>
                            </select>
                            <table class="table table-striped table-bordered" id="json_res_status_<?php echo $i; ?>"<?php if(isset($responseParaList[$x]['value_id']) && $responseParaList[$x]['value_id'] == 2){ ?> style="display: block;" <?php } else { ?> style="display: none;" <?php } ?>>
                              <tr>
                                <td>Success <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Failed <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Pending <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                              </tr>
                              <tr>
                                <td>
                                  <input type="text" class="form-control" name="json_res_status_val[<?php echo $i; ?>][0]" value="<?php echo isset($responseParaList[$x]['success_val']) ? $responseParaList[$x]['success_val'] : ''; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="json_res_status_val[<?php echo $i; ?>][1]" value="<?php echo isset($responseParaList[$x]['failed_val']) ? $responseParaList[$x]['failed_val'] : ''; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="json_res_status_val[<?php echo $i; ?>][2]" value="<?php echo isset($responseParaList[$x]['pending_val']) ? $responseParaList[$x]['pending_val'] : ''; ?>">
                                </td>
                              </tr>
                            </table>
                          </td>
                         
                          
                        </tr>
                      <?php $x++; } ?>
                    </table>
                </div>
              </div>

              <!-- START GET BALANCE API -->
              <div class="row">
                <div class="col-sm-10">
                  <div class="form-group">
                  <label><b>Get Balance Base URL</b></label>
                  <input type="text" class="form-control" name="get_balance_base_url" id="get_balance_base_url" placeholder="Get Balance Base URL" value="<?php echo $apiData['get_balance_base_url']; ?>">
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
                      <option value="<?php echo $list['id']; ?>" <?php if($apiData['get_balance_request_type'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['title']; ?></option>
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
                          <input type="checkbox" <?php if(isset($getBalanceGetHeaderData['is_access_key']) && $getBalanceGetHeaderData['is_access_key'] == 1){ ?> checked="checked" <?php } ?> name="get_balance_header_is_access_key" value="1" id="get_balance_header_is_access_key">
                          <label for="get_balance_header_is_access_key">Access Key</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" name="get_balance_header_access_key" value="<?php echo isset($getBalanceGetHeaderData['access_key']) ? $getBalanceGetHeaderData['access_key'] : ''; ?>">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" <?php if(isset($getBalanceGetHeaderData['is_username']) && $getBalanceGetHeaderData['is_username'] == 1){ ?> checked="checked" <?php } ?> name="get_balance_header_is_username" value="1" id="get_balance_header_is_username">
                          <label for="get_balance_header_is_username">Username</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" value="<?php echo isset($getBalanceGetHeaderData['username_key']) ? $getBalanceGetHeaderData['username_key'] : ''; ?>" name="get_balance_header_username">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" <?php if(isset($getBalanceGetHeaderData['is_password']) && $getBalanceGetHeaderData['is_password'] == 1){ ?> checked="checked" <?php } ?> name="get_balance_header_is_password" value="1" id="get_balance_header_is_password">
                          <label for="get_balance_header_is_password">Password/PIN</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" value="<?php echo isset($getBalanceGetHeaderData['password_key']) ? $getBalanceGetHeaderData['password_key'] : ''; ?>" name="get_balance_header_password">
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
                          <input type="checkbox" <?php if(isset($getBalanceGetMethodData['is_access_key']) && $getBalanceGetMethodData['is_access_key'] == 1){ ?> checked="checked" <?php } ?> name="get_balance_get_is_access_key" value="1" id="get_balance_get_is_access_key">
                          <label for="get_balance_get_is_access_key">Access Key</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" value="<?php echo isset($getBalanceGetMethodData['access_key']) ? $getBalanceGetMethodData['access_key'] : ''; ?>" name="get_balance_get_access_key">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" <?php if(isset($getBalanceGetMethodData['is_username']) && $getBalanceGetMethodData['is_username'] == 1){ ?> checked="checked" <?php } ?> name="get_balance_get_is_username" value="1" id="get_balance_get_is_username">
                          <label for="get_balance_get_is_username">Username</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" value="<?php echo isset($getBalanceGetMethodData['username_key']) ? $getBalanceGetMethodData['username_key'] : ''; ?>" name="get_balance_get_username">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" <?php if(isset($getBalanceGetMethodData['is_password']) && $getBalanceGetMethodData['is_password'] == 1){ ?> checked="checked" <?php } ?> name="get_balance_get_is_password" value="1" id="get_balance_get_is_password">
                          <label for="get_balance_get_is_password">Password/PIN</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" value="<?php echo isset($getBalanceGetMethodData['password_key']) ? $getBalanceGetMethodData['password_key'] : ''; ?>" name="get_balance_get_password">
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
                                  <input type="text" class="form-control" name="get_balance_get_para_key[<?php echo $i; ?>]" value="<?php echo isset($getBalanceGetParaList[$x]['para_key']) ? $getBalanceGetParaList[$x]['para_key'] : ''; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="get_balance_get_para_val[<?php echo $i; ?>]" value="<?php echo isset($getBalanceGetParaList[$x]['value']) ? $getBalanceGetParaList[$x]['value'] : ''; ?>">
                                  <p style="font-size: 11px;">Put # for dynamic data</p>
                                </td>
                                <td>
                                  <select class="form-control" name="get_balance_get_para_key_val[<?php echo $i; ?>]">
                                    <option value="0">Select</option>
                                    <?php if($paraValueList){ ?>
                                      <?php foreach($paraValueList as $list){ ?>
                                        <option value="<?php echo $list['id']; ?>" <?php if(isset($getBalanceGetParaList[$x]['value_id']) && $getBalanceGetParaList[$x]['value_id'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['title']; ?></option>
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
                          <input type="checkbox" <?php if(isset($getBalancePostMethodData['is_access_key']) && $getBalancePostMethodData['is_access_key'] == 1){ ?> checked="checked" <?php } ?> name="get_balance_post_is_access_key" value="1" id="get_balance_post_is_access_key">
                          <label for="get_balance_post_is_access_key">Access Key</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" value="<?php echo isset($getBalancePostMethodData['access_key']) ? $getBalancePostMethodData['access_key'] : ''; ?>" name="get_balance_post_access_key">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" <?php if(isset($getBalancePostMethodData['is_username']) && $getBalancePostMethodData['is_username'] == 1){ ?> checked="checked" <?php } ?> name="get_balance_post_is_username" value="1" id="get_balance_post_is_username">
                          <label for="get_balance_post_is_username">Username</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" value="<?php echo isset($getBalancePostMethodData['username_key']) ? $getBalancePostMethodData['username_key'] : ''; ?>" name="get_balance_post_username">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" <?php if(isset($getBalancePostMethodData['is_password']) && $getBalancePostMethodData['is_password'] == 1){ ?> checked="checked" <?php } ?> name="get_balance_post_is_password" value="1" id="get_balance_post_is_password">
                          <label for="get_balance_post_is_password">Password/PIN</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" value="<?php echo isset($getBalancePostMethodData['password_key']) ? $getBalancePostMethodData['password_key'] : ''; ?>" name="get_balance_post_password">
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
                                  <input type="text" class="form-control" name="get_balance_post_para_key[<?php echo $i; ?>]" value="<?php echo isset($getBalancePostParaList[$x]['para_key']) ? $getBalancePostParaList[$x]['para_key'] : ''; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="get_balance_post_para_val[<?php echo $i; ?>]" value="<?php echo isset($getBalancePostParaList[$x]['value']) ? $getBalancePostParaList[$x]['value'] : ''; ?>">
                                  <p style="font-size: 11px;">Put # for dynamic data</p>
                                </td>
                                <td>
                                  <select class="form-control" name="get_balance_post_para_key_val[<?php echo $i; ?>]">
                                    <option value="0">Select</option>
                                    <?php if($paraValueList){ ?>
                                      <?php foreach($paraValueList as $list){ ?>
                                        <option value="<?php echo $list['id']; ?>" <?php if(isset($getBalancePostParaList[$x]['value_id']) && $getBalancePostParaList[$x]['value_id'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['title']; ?></option>
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
                          <option value="<?php echo $list['id']; ?>" <?php if($apiData['get_balance_response_type'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['title']; ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>  
                    </div>
                </div>
                <div class="col-sm-3" id="get_balance_seperator_block" <?php if($apiData['get_balance_response_type'] != 1){ ?> style="display: none;" <?php } ?>>
                   <div class="form-group">
                    <label><b>Seperator</b></label>
                    <input type="text" class="form-control" value="<?php echo $apiData['get_balance_response_seperator']; ?>" name="get_balance_response_seperator">
                    </div>
                </div>
              </div>
              <div class="row" id="get_balance_str_res_block" <?php if($apiData['get_balance_response_type'] != 1){ ?> style="display: none;" <?php } ?>>
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
                                  <option value="<?php echo $list['id']; ?>" <?php if(isset($getBalanceResponseParaList[$x]['value_id']) && $getBalanceResponseParaList[$x]['value_id'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['title']; ?></option>
                                <?php } ?>
                              <?php } ?>
                            </select>
                            <table class="table table-striped table-bordered" id="get_balance_str_res_status_<?php echo $i; ?>"<?php if(isset($getBalanceResponseParaList[$x]['value_id']) && $getBalanceResponseParaList[$x]['value_id'] == 2){ ?> style="display: block;" <?php } else { ?> style="display: none;" <?php } ?>>
                              <tr>
                                <td>Success <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Failed <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Pending <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                              </tr>
                              <tr>
                                <td>
                                  <input type="text" class="form-control" name="get_balance_str_res_status_val[<?php echo $i; ?>][0]" value="<?php echo isset($getBalanceResponseParaList[$x]['success_val']) ? $getBalanceResponseParaList[$x]['success_val'] : ''; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="get_balance_str_res_status_val[<?php echo $i; ?>][1]" value="<?php echo isset($getBalanceResponseParaList[$x]['failed_val']) ? $getBalanceResponseParaList[$x]['failed_val'] : ''; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="get_balance_str_res_status_val[<?php echo $i; ?>][2]" value="<?php echo isset($getBalanceResponseParaList[$x]['pending_val']) ? $getBalanceResponseParaList[$x]['pending_val'] : ''; ?>">
                                </td>
                              </tr>
                            </table>
                          </td>
                         
                          
                        </tr>
                      <?php $x++; } ?>
                    </table>
                </div>
              </div>

              <div class="row" id="get_balance_xml_res_block" <?php if($apiData['get_balance_response_type'] != 2){ ?> style="display: none;" <?php } ?>>
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
                             <input type="text" class="form-control" name="get_balance_xml_res_key[<?php echo $i; ?>]" value="<?php echo isset($getBalanceResponseParaList[$x]['para_key']) ? $getBalanceResponseParaList[$x]['para_key'] : ''; ?>">
                          </td>
                          <td>
                            <select class="form-control" onchange="showGetBalanceXMLResponseStatus(<?php echo $i; ?>,this.value)" name="get_balance_xml_res_type[<?php echo $i; ?>]">
                              <option value="0">Select</option>
                              <?php if($resValueList){ ?>
                                <?php foreach($resValueList as $list){ ?>
                                  <option value="<?php echo $list['id']; ?>" <?php if(isset($getBalanceResponseParaList[$x]['value_id']) && $getBalanceResponseParaList[$x]['value_id'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['title']; ?></option>
                                <?php } ?>
                              <?php } ?>
                            </select>
                            <table class="table table-striped table-bordered" id="get_balance_xml_res_status_<?php echo $i; ?>"<?php if(isset($getBalanceResponseParaList[$x]['value_id']) && $getBalanceResponseParaList[$x]['value_id'] == 2){ ?> style="display: block;" <?php } else { ?> style="display: none;" <?php } ?>>
                              <tr>
                                <td>Success <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Failed <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Pending <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                              </tr>
                              <tr>
                                <td>
                                  <input type="text" class="form-control" name="get_balance_xml_res_status_val[<?php echo $i; ?>][0]" value="<?php echo isset($getBalanceResponseParaList[$x]['success_val']) ? $getBalanceResponseParaList[$x]['success_val'] : ''; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="get_balance_xml_res_status_val[<?php echo $i; ?>][1]" value="<?php echo isset($getBalanceResponseParaList[$x]['failed_val']) ? $getBalanceResponseParaList[$x]['failed_val'] : ''; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="get_balance_xml_res_status_val[<?php echo $i; ?>][2]" value="<?php echo isset($getBalanceResponseParaList[$x]['pending_val']) ? $getBalanceResponseParaList[$x]['pending_val'] : ''; ?>">
                                </td>
                              </tr>
                            </table>
                          </td>
                         
                          
                        </tr>
                      <?php $x++; } ?>
                    </table>
                </div>
              </div>

              <div class="row" id="get_balance_json_res_block" <?php if($apiData['get_balance_response_type'] != 3){ ?> style="display: none;" <?php } ?>>
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
                             <input type="text" class="form-control" name="get_balance_json_res_key[<?php echo $i; ?>]" value="<?php echo isset($getBalanceResponseParaList[$x]['para_key']) ? $getBalanceResponseParaList[$x]['para_key'] : ''; ?>">
                          </td>
                          <td>
                            <select class="form-control" onchange="showGetBalanceJsonResponseStatus(<?php echo $i; ?>,this.value)" name="get_balance_json_res_type[<?php echo $i; ?>]">
                              <option value="0">Select</option>
                              <?php if($resValueList){ ?>
                                <?php foreach($resValueList as $list){ ?>
                                  <option value="<?php echo $list['id']; ?>" <?php if(isset($getBalanceResponseParaList[$x]['value_id']) && $getBalanceResponseParaList[$x]['value_id'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['title']; ?></option>
                                <?php } ?>
                              <?php } ?>
                            </select>
                            <table class="table table-striped table-bordered" id="get_balance_json_res_status_<?php echo $i; ?>"<?php if(isset($getBalanceResponseParaList[$x]['value_id']) && $getBalanceResponseParaList[$x]['value_id'] == 2){ ?> style="display: block;" <?php } else { ?> style="display: none;" <?php } ?>>
                              <tr>
                                <td>Success <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Failed <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Pending <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                              </tr>
                              <tr>
                                <td>
                                  <input type="text" class="form-control" name="get_balance_json_res_status_val[<?php echo $i; ?>][0]" value="<?php echo isset($getBalanceResponseParaList[$x]['success_val']) ? $getBalanceResponseParaList[$x]['success_val'] : ''; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="get_balance_json_res_status_val[<?php echo $i; ?>][1]" value="<?php echo isset($getBalanceResponseParaList[$x]['failed_val']) ? $getBalanceResponseParaList[$x]['failed_val'] : ''; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="get_balance_json_res_status_val[<?php echo $i; ?>][2]" value="<?php echo isset($getBalanceResponseParaList[$x]['pending_val']) ? $getBalanceResponseParaList[$x]['pending_val'] : ''; ?>">
                                </td>
                              </tr>
                            </table>
                          </td>
                         
                          
                        </tr>
                      <?php $x++; } ?>
                    </table>
                </div>
              </div>

              <!-- END GET BALANCE API -->

              <!-- START CHECK STATUS API -->
              <div class="row">
                <div class="col-sm-10">
                  <div class="form-group">
                  <label><b>Check Status Base URL</b></label>
                  <input type="text" class="form-control" name="check_status_base_url" id="check_status_base_url" placeholder="Check Balance Base URL" value="<?php echo $apiData['check_status_base_url']; ?>">
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
                      <option value="<?php echo $list['id']; ?>" <?php if($apiData['check_status_request_type'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['title']; ?></option>
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
                          <input type="checkbox" <?php if(isset($checkStatusGetMethodData['is_access_key']) && $checkStatusGetMethodData['is_access_key'] == 1){ ?> checked="checked" <?php } ?> name="check_status_get_is_access_key" value="1" id="check_status_get_is_access_key">
                          <label for="check_status_get_is_access_key">Access Key</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" value="<?php echo isset($checkStatusGetMethodData['access_key']) ? $checkStatusGetMethodData['access_key'] : ''; ?>" name="check_status_get_access_key">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" <?php if(isset($checkStatusGetMethodData['is_username']) && $checkStatusGetMethodData['is_username'] == 1){ ?> checked="checked" <?php } ?> name="check_status_get_is_username" value="1" id="check_status_get_is_username">
                          <label for="check_status_get_is_username">Username</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" value="<?php echo isset($checkStatusGetMethodData['username_key']) ? $checkStatusGetMethodData['username_key'] : ''; ?>" name="check_status_get_username">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" <?php if(isset($checkStatusGetMethodData['is_password']) && $checkStatusGetMethodData['is_password'] == 1){ ?> checked="checked" <?php } ?> name="check_status_get_is_password" value="1" id="check_status_get_is_password">
                          <label for="check_status_get_is_password">Password/PIN</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" value="<?php echo isset($checkStatusGetMethodData['password_key']) ? $checkStatusGetMethodData['password_key'] : ''; ?>" name="check_status_get_password">
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
                                  <input type="text" class="form-control" name="check_status_get_para_key[<?php echo $i; ?>]" value="<?php echo isset($checkStatusGetParaList[$x]['para_key']) ? $checkStatusGetParaList[$x]['para_key'] : ''; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="check_status_get_para_val[<?php echo $i; ?>]" value="<?php echo isset($checkStatusGetParaList[$x]['value']) ? $checkStatusGetParaList[$x]['value'] : ''; ?>">
                                  <p style="font-size: 11px;">Put # for dynamic data</p>
                                </td>
                                <td>
                                  <select class="form-control" name="check_status_get_para_key_val[<?php echo $i; ?>]">
                                    <option value="0">Select</option>
                                    <?php if($paraValueList){ ?>
                                      <?php foreach($paraValueList as $list){ ?>
                                        <option value="<?php echo $list['id']; ?>" <?php if(isset($checkStatusGetParaList[$x]['value_id']) && $checkStatusGetParaList[$x]['value_id'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['title']; ?></option>
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
                          <input type="checkbox" <?php if(isset($checkStatusPostMethodData['is_access_key']) && $checkStatusPostMethodData['is_access_key'] == 1){ ?> checked="checked" <?php } ?> name="check_status_post_is_access_key" value="1" id="check_status_post_is_access_key">
                          <label for="check_status_post_is_access_key">Access Key</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" value="<?php echo isset($checkStatusPostMethodData['access_key']) ? $checkStatusPostMethodData['access_key'] : ''; ?>" name="check_status_post_access_key">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" <?php if(isset($checkStatusPostMethodData['is_username']) && $checkStatusPostMethodData['is_username'] == 1){ ?> checked="checked" <?php } ?> name="check_status_post_is_username" value="1" id="check_status_post_is_username">
                          <label for="check_status_post_is_username">Username</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" value="<?php echo isset($checkStatusPostMethodData['username_key']) ? $checkStatusPostMethodData['username_key'] : ''; ?>" name="check_status_post_username">
                        </div>
                        <div class="col-sm-12">
                          
                          <hr />
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-sm-3">
                          <input type="checkbox" <?php if(isset($checkStatusPostMethodData['is_password']) && $checkStatusPostMethodData['is_password'] == 1){ ?> checked="checked" <?php } ?> name="check_status_post_is_password" value="1" id="check_status_post_is_password">
                          <label for="check_status_post_is_password">Password/PIN</label>
                        </div>
                        <div class="col-sm-1">
                          <label>Key</label>
                          
                        </div>
                        <div class="col-sm-3">
                          
                          <input type="text" class="form-control" value="<?php echo isset($checkStatusPostMethodData['password_key']) ? $checkStatusPostMethodData['password_key'] : ''; ?>" name="check_status_post_password">
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
                                  <input type="text" class="form-control" name="check_status_post_para_key[<?php echo $i; ?>]" value="<?php echo isset($checkStatusPostParaList[$x]['para_key']) ? $checkStatusPostParaList[$x]['para_key'] : ''; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="check_status_post_para_val[<?php echo $i; ?>]" value="<?php echo isset($checkStatusPostParaList[$x]['value']) ? $checkStatusPostParaList[$x]['value'] : ''; ?>">
                                  <p style="font-size: 11px;">Put # for dynamic data</p>
                                </td>
                                <td>
                                  <select class="form-control" name="check_status_post_para_key_val[<?php echo $i; ?>]">
                                    <option value="0">Select</option>
                                    <?php if($paraValueList){ ?>
                                      <?php foreach($paraValueList as $list){ ?>
                                        <option value="<?php echo $list['id']; ?>" <?php if(isset($checkStatusPostParaList[$x]['value_id']) && $checkStatusPostParaList[$x]['value_id'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['title']; ?></option>
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
                          <option value="<?php echo $list['id']; ?>" <?php if($apiData['check_status_response_type'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['title']; ?></option>
                        <?php } ?>
                      <?php } ?>
                    </select>  
                    </div>
                </div>
                <div class="col-sm-3" id="check_status_seperator_block" <?php if($apiData['check_status_response_type'] != 1){ ?> style="display: none;" <?php } ?>>
                   <div class="form-group">
                    <label><b>Seperator</b></label>
                    <input type="text" class="form-control" value="<?php echo $apiData['check_status_response_seperator']; ?>" name="check_status_response_seperator">
                    </div>
                </div>
              </div>
              <div class="row" id="check_status_str_res_block" <?php if($apiData['check_status_response_type'] != 1){ ?> style="display: none;" <?php } ?>>
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
                                  <option value="<?php echo $list['id']; ?>" <?php if(isset($checkStatusResponseParaList[$x]['value_id']) && $checkStatusResponseParaList[$x]['value_id'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['title']; ?></option>
                                <?php } ?>
                              <?php } ?>
                            </select>
                            <table class="table table-striped table-bordered" id="check_status_str_res_status_<?php echo $i; ?>"<?php if(isset($checkStatusResponseParaList[$x]['value_id']) && $checkStatusResponseParaList[$x]['value_id'] == 2){ ?> style="display: block;" <?php } else { ?> style="display: none;" <?php } ?>>
                              <tr>
                                <td>Success <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Failed <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Pending <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                              </tr>
                              <tr>
                                <td>
                                  <input type="text" class="form-control" name="check_status_str_res_status_val[<?php echo $i; ?>][0]" value="<?php echo isset($checkStatusResponseParaList[$x]['success_val']) ? $checkStatusResponseParaList[$x]['success_val'] : ''; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="check_status_str_res_status_val[<?php echo $i; ?>][1]" value="<?php echo isset($checkStatusResponseParaList[$x]['failed_val']) ? $checkStatusResponseParaList[$x]['failed_val'] : ''; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="check_status_str_res_status_val[<?php echo $i; ?>][2]" value="<?php echo isset($checkStatusResponseParaList[$x]['pending_val']) ? $checkStatusResponseParaList[$x]['pending_val'] : ''; ?>">
                                </td>
                              </tr>
                            </table>
                          </td>
                         
                          
                        </tr>
                      <?php $x++; } ?>
                    </table>
                </div>
              </div>

              <div class="row" id="check_status_xml_res_block" <?php if($apiData['check_status_response_type'] != 2){ ?> style="display: none;" <?php } ?>>
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
                             <input type="text" class="form-control" name="check_status_xml_res_key[<?php echo $i; ?>]" value="<?php echo isset($checkStatusResponseParaList[$x]['para_key']) ? $checkStatusResponseParaList[$x]['para_key'] : ''; ?>">
                          </td>
                          <td>
                            <select class="form-control" onchange="showCheckStatusXMLResponseStatus(<?php echo $i; ?>,this.value)" name="check_status_xml_res_type[<?php echo $i; ?>]">
                              <option value="0">Select</option>
                              <?php if($resValueList){ ?>
                                <?php foreach($resValueList as $list){ ?>
                                  <option value="<?php echo $list['id']; ?>" <?php if(isset($checkStatusResponseParaList[$x]['value_id']) && $checkStatusResponseParaList[$x]['value_id'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['title']; ?></option>
                                <?php } ?>
                              <?php } ?>
                            </select>
                            <table class="table table-striped table-bordered" id="check_status_xml_res_status_<?php echo $i; ?>"<?php if(isset($checkStatusResponseParaList[$x]['value_id']) && $checkStatusResponseParaList[$x]['value_id'] == 2){ ?> style="display: block;" <?php } else { ?> style="display: none;" <?php } ?>>
                              <tr>
                                <td>Success <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Failed <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Pending <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                              </tr>
                              <tr>
                                <td>
                                  <input type="text" class="form-control" name="check_status_xml_res_status_val[<?php echo $i; ?>][0]" value="<?php echo isset($checkStatusResponseParaList[$x]['success_val']) ? $checkStatusResponseParaList[$x]['success_val'] : ''; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="check_status_xml_res_status_val[<?php echo $i; ?>][1]" value="<?php echo isset($checkStatusResponseParaList[$x]['failed_val']) ? $checkStatusResponseParaList[$x]['failed_val'] : ''; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="check_status_xml_res_status_val[<?php echo $i; ?>][2]" value="<?php echo isset($checkStatusResponseParaList[$x]['pending_val']) ? $checkStatusResponseParaList[$x]['pending_val'] : ''; ?>">
                                </td>
                              </tr>
                            </table>
                          </td>
                         
                          
                        </tr>
                      <?php $x++; } ?>
                    </table>
                </div>
              </div>

              <div class="row" id="check_status_json_res_block" <?php if($apiData['check_status_response_type'] != 3){ ?> style="display: none;" <?php } ?>>
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
                             <input type="text" class="form-control" name="check_status_json_res_key[<?php echo $i; ?>]" value="<?php echo isset($checkStatusResponseParaList[$x]['para_key']) ? $checkStatusResponseParaList[$x]['para_key'] : ''; ?>">
                          </td>
                          <td>
                            <select class="form-control" onchange="showCheckStatusJsonResponseStatus(<?php echo $i; ?>,this.value)" name="check_status_json_res_type[<?php echo $i; ?>]">
                              <option value="0">Select</option>
                              <?php if($resValueList){ ?>
                                <?php foreach($resValueList as $list){ ?>
                                  <option value="<?php echo $list['id']; ?>" <?php if(isset($checkStatusResponseParaList[$x]['value_id']) && $checkStatusResponseParaList[$x]['value_id'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['title']; ?></option>
                                <?php } ?>
                              <?php } ?>
                            </select>
                            <table class="table table-striped table-bordered" id="check_status_json_res_status_<?php echo $i; ?>"<?php if(isset($checkStatusResponseParaList[$x]['value_id']) && $checkStatusResponseParaList[$x]['value_id'] == 2){ ?> style="display: block;" <?php } else { ?> style="display: none;" <?php } ?>>
                              <tr>
                                <td>Success <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Failed <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Pending <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                              </tr>
                              <tr>
                                <td>
                                  <input type="text" class="form-control" name="check_status_json_res_status_val[<?php echo $i; ?>][0]" value="<?php echo isset($checkStatusResponseParaList[$x]['success_val']) ? $checkStatusResponseParaList[$x]['success_val'] : ''; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="check_status_json_res_status_val[<?php echo $i; ?>][1]" value="<?php echo isset($checkStatusResponseParaList[$x]['failed_val']) ? $checkStatusResponseParaList[$x]['failed_val'] : ''; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="check_status_json_res_status_val[<?php echo $i; ?>][2]" value="<?php echo isset($checkStatusResponseParaList[$x]['pending_val']) ? $checkStatusResponseParaList[$x]['pending_val'] : ''; ?>">
                                </td>
                              </tr>
                            </table>
                          </td>
                         
                          
                        </tr>
                      <?php $x++; } ?>
                    </table>
                </div>
              </div>

              <!-- END CHECK STATUS API -->

              <!-- START CALLBACK API -->

              <div class="row">
                <div class="col-sm-10">
                  <div class="form-group">
                  <label><b>Callback Base URL</b></label>
                  <input type="text" readonly="readonly" class="form-control" name="callback_base_url" id="callback_base_url" placeholder="Callback Base URL" value="<?php echo $apiData['callback_base_url']; ?>">
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-group">
                  <label><b>Response Type*</b></label>
                  <select class="form-control" name="callback_response_type">
                  <option value="">Select Type</option>
                  <option value="1"<?php if($apiData['callback_response_type'] == 1){ ?> selected="selected" <?php } ?>>GET</option>
                  <option value="2"<?php if($apiData['callback_response_type'] == 2){ ?> selected="selected" <?php } ?>>POST</option>
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
                             <input type="text" class="form-control" name="call_back_res_key[<?php echo $i; ?>]" value="<?php echo isset($callBackResponseParaList[$x]['para_key']) ? $callBackResponseParaList[$x]['para_key'] : ''; ?>">
                          </td>
                          <td>
                            <select class="form-control" onchange="showCallbackResponseStatus(<?php echo $i; ?>,this.value)" name="call_back_res_type[<?php echo $i; ?>]">
                              <option value="0">Select</option>
                              <?php if($resValueList){ ?>
                                <?php foreach($resValueList as $list){ ?>
                                  <option value="<?php echo $list['id']; ?>" <?php if(isset($callBackResponseParaList[$x]['value_id']) && $callBackResponseParaList[$x]['value_id'] == $list['id']){ ?> selected="selected" <?php } ?>><?php echo $list['title']; ?></option>
                                <?php } ?>
                              <?php } ?>
                            </select>
                            <table class="table table-striped table-bordered" id="call_back_res_status_<?php echo $i; ?>"<?php if(isset($callBackResponseParaList[$x]['value_id']) && $callBackResponseParaList[$x]['value_id'] == 2){ ?> style="display: block;" <?php } else { ?> style="display: none;" <?php } ?>>
                              <tr>
                                <td>Success <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Failed <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                                <td>Pending <br /><p style="font-size: 10px;">(For Multi Response Put Comma (,) Seperate Value)</p></td>
                              </tr>
                              <tr>
                                <td>
                                  <input type="text" class="form-control" name="call_back_res_status_val[<?php echo $i; ?>][0]" value="<?php echo isset($callBackResponseParaList[$x]['success_val']) ? $callBackResponseParaList[$x]['success_val'] : ''; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="call_back_res_status_val[<?php echo $i; ?>][1]" value="<?php echo isset($callBackResponseParaList[$x]['failed_val']) ? $callBackResponseParaList[$x]['failed_val'] : ''; ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="call_back_res_status_val[<?php echo $i; ?>][2]" value="<?php echo isset($callBackResponseParaList[$x]['pending_val']) ? $callBackResponseParaList[$x]['pending_val'] : ''; ?>">
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




