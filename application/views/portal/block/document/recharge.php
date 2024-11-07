<div class="card shadow mb-4">
              {system_message}               
              {system_info}
              <?php echo form_open_multipart('portal/document/callbackAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>Recharge API Document</b></h4>
                </div>

                

              </div>
            </div>
            
            <div class="card-body">
              
              <div class="row">
                <div class="col-sm-12">
                  <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="rechargeAPI-tab" data-toggle="tab" href="#rechargeAPI" role="tab" aria-controls="rechargeAPI" aria-selected="true">Recharge API</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="operator-tab" data-toggle="tab" href="#operator" role="tab" aria-controls="operator" aria-selected="true">Operator</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="circle-tab" data-toggle="tab" href="#circle" role="tab" aria-controls="circle" aria-selected="true">Circle</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="balanceCheck-tab" data-toggle="tab" href="#balanceCheck" role="tab" aria-controls="balanceCheck" aria-selected="false">Balance Check API</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="statusCheck-tab" data-toggle="tab" href="#statusCheck" role="tab" aria-controls="statusCheck" aria-selected="false">Status Check API</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="callBack-tab" data-toggle="tab" href="#callBack" role="tab" aria-controls="callBack" aria-selected="false">Call Back URL</a>
                    </li>
                  </ul>
                  <div class="tab-content apidoc" id="myTabContent">
                    <div class="tab-pane fade show active" id="rechargeAPI" role="tabpanel" aria-labelledby="rechargeAPI-tab">
                      
                      <div class="col-sm-12">
                        <br />
                        <h1>Request API URL</h1>
                        <p style="font-size: 18px; font-weight: bold;">{site_url}portal/api/rechargeAuth</p>
                        <hr />
                        <p><b>Request Method - POST</b></p>
                      </div>
                      <div class="col-sm-12">
                        <br />
                        <h1>Header Parameter</h1>
                        <table class="table table-bordered table-striped">
                          <tr>
                            <th>#</th>
                            <th>Parameter Key</th>
                            <th>Description</th>
                          </tr>
                          <tr>
                            <td>1.</td>
                            <td>MemberID</td>
                            <td>Your account unique id.</td>
                          </tr>
                          <tr>
                            <td>2.</td>
                            <td>TXNPWD</td>
                            <td>Your account trasaction password.</td>
                          </tr>
                        </table>
                        <p style="color: red;">Note:- All Parameter key are case sensitive.</p>
                      </div>
                      <div class="col-sm-12">
                        <br />
                        <h1>Post Parameter</h1>
                        <table class="table table-bordered table-striped">
                          <tr>
                            <th>#</th>
                            <th>Parameter Key</th>
                            <th>Description</th>
                          </tr>
                          <tr>
                            <td>1.</td>
                            <td>mobile</td>
                            <td>Customer Mobile No.</td>
                          </tr>
                          <tr>
                            <td>2.</td>
                            <td>operator</td>
                            <td>Operator Code. Please look into Operator List.</td>
                          </tr>
                          <tr>
                            <td>3.</td>
                            <td>circle</td>
                            <td>Circle Code. Please look into Circle List.</td>
                          </tr>
                          <tr>
                            <td>4.</td>
                            <td>amount</td>
                            <td>Recharge Amount</td>
                          </tr>
                          <tr>
                            <td>5.</td>
                            <td>txnID</td>
                            <td>Unique Trasaction ID Numbers Only. Minimum Lenght Should Be 10 Digit.</td>
                          </tr>
                          <tr>
                            <td>6.</td>
                            <td>response_type</td>
                            <td>1 for JSON, 2 for XML. Default Response Will be in JSON Format</td>
                          </tr>
                        </table>
                        <p style="color: red;">Note:- All Parameter key are case sensitive.</p>
                      </div>

                      <div class="col-sm-12">
                        <br />
                        <h1>JSON Response</h1>
                        <p style="font-size: 18px; white-space: pre;">{
  "status_code": 200,
  "status_msg": "OK",
  "status": "PENDING",
  "txnid": "1234567890",
  "operator_txnid": "4455845865875"
}</p>
                       
                      </div>

                      <div class="col-sm-12">
                        <br />
                        <h1>XML Response</h1>
                        <p style="font-size: 18px; white-space: pre;">
&#60;?xml version="1.0"?&#62;
&#60;response&#62;
    &#60;status_code&#62;200&#60;/status_code&#62;
    &#60;status_msg&#62;OK&#60;/status_msg&#62;
    &#60;status&#62;FAILED&#60;/status&#62;
    &#60;txnid&#62;1234567890&#60;/txnid&#62;
    &#60;opt_msg&#62;IVA_OR_DUPLICATE&#60;/opt_msg&#62;
&#60;/response&#62;
</p>
                       
                      </div>

                      <div class="col-sm-12">
                        <br />
                        <h1>Response Parameters</h1>
                        <table class="table table-bordered table-striped">
                          <tr>
                            <th>#</th>
                            <th>Parameter Key</th>
                            <th>Description</th>
                          </tr>
                          <tr>
                            <td>1.</td>
                            <td>status_code</td>
                            <td>
                              400 = Variable Related Error <br />
                              401 = Variable Data Related Error <br />
                              200 = Success
                            </td>
                          </tr>
                          <tr>
                            <td>2.</td>
                            <td>status_msg</td>
                            <td>API Success or Error Message according status code.</td>
                          </tr>
                          <tr>
                            <td>3.</td>
                            <td>status</td>
                            <td>
                              PENDING <br />
                              FAILED <br />
                              SUCCESS 
                            </td>
                          </tr>
                          <tr>
                            <td>4.</td>
                            <td>txnid</td>
                            <td>Your unique Recharge ID.</td>
                          </tr>
                          <tr>
                            <td>5.</td>
                            <td>operator_txnid</td>
                            <td>Unique Recharge ID from Operator Side.</td>
                          </tr>
                          <tr>
                            <td>6.</td>
                            <td>opt_msg</td>
                            <td>Operator Message.</td>
                          </tr>
                        </table>
                        
                      </div>


                    </div>

                    <div class="tab-pane fade" id="operator" role="tabpanel" aria-labelledby="operator-tab">
                      
                      <div class="col-sm-12">
                        <br />
                        <h1>Operator List</h1>
                        <table class="table table-bordered table-striped">
                          <tr>
                            <th>#</th>
                            <th>Operator Name</th>
                            <th>Operator Code</th>
                            <th>Type</th>
                          </tr>
                          <?php if($operatorList){ ?>
                            <?php $i = 1; foreach($operatorList as $list){ ?>
                            <tr>
                              <td><?php echo $i; ?>.</td>
                              <td><?php echo $list['operator_name']; ?></td>
                              <td><?php echo $list['operator_code']; ?></td>
                              <td><?php echo $list['type']; ?></td>
                            </tr>
                            <?php $i++; } ?>
                          <?php } ?>
                          
                        </table>
                        
                      </div>


                    </div>


                    <div class="tab-pane fade" id="circle" role="tabpanel" aria-labelledby="circle-tab">
                      
                      <div class="col-sm-12">
                        <br />
                        <h1>Circle List</h1>
                        <table class="table table-bordered table-striped">
                          <tr>
                            <th>#</th>
                            <th>Circle Name</th>
                            <th>Circle Code</th>
                            
                          </tr>
                          <?php if($circle){ ?>
                            <?php $i = 1; foreach($circle as $list){ ?>
                            <tr>
                              <td><?php echo $i; ?>.</td>
                              <td><?php echo $list['circle_name']; ?></td>
                              <td><?php echo $list['circle_code']; ?></td>
                              
                            </tr>
                            <?php $i++; } ?>
                          <?php } ?>
                          
                        </table>
                        
                      </div>

                    </div>

                    <div class="tab-pane fade" id="balanceCheck" role="tabpanel" aria-labelledby="balanceCheck-tab">
                      
                      <div class="col-sm-12">
                        <br />
                        <h1>Request API URL</h1>
                        <p style="font-size: 18px; font-weight: bold;">{site_url}portal/api/balanceAuth</p>
                        <hr />
                        <p><b>Request Method - GET</b></p>
                      </div>
                      <div class="col-sm-12">
                        <br />
                        <h1>Header Parameter</h1>
                        <table class="table table-bordered table-striped">
                          <tr>
                            <th>#</th>
                            <th>Parameter Key</th>
                            <th>Description</th>
                          </tr>
                          <tr>
                            <td>1.</td>
                            <td>MemberID</td>
                            <td>Your account unique id.</td>
                          </tr>
                          <tr>
                            <td>2.</td>
                            <td>TXNPWD</td>
                            <td>Your account trasaction password.</td>
                          </tr>
                        </table>
                        <p style="color: red;">Note:- All Parameter key are case sensitive.</p>
                      </div>
                      <div class="col-sm-12">
                        <br />
                        <h1>GET Parameter</h1>
                        <table class="table table-bordered table-striped">
                          <tr>
                            <th>#</th>
                            <th>Parameter Key</th>
                            <th>Description</th>
                          </tr>
                          <tr>
                            <td>1.</td>
                            <td>response_type</td>
                            <td>1 for JSON, 2 for XML. Default Response Will be in JSON Format</td>
                          </tr>
                        </table>
                        <p style="color: red;">Note:- All Parameter key are case sensitive.</p>
                      </div>

                      <div class="col-sm-12">
                        <br />
                        <h1>JSON Response</h1>
                        <p style="font-size: 18px; white-space: pre;">{
  "status_code": 200,
  "status_msg": "OK",
  "balance": 0.00
}</p>
                       
                      </div>

                      <div class="col-sm-12">
                        <br />
                        <h1>XML Response</h1>
                        <p style="font-size: 18px; white-space: pre;">
&#60;?xml version="1.0"?&#62;
&#60;response&#62;
    &#60;status_code&#62;200&#60;/status_code&#62;
    &#60;status_msg&#62;OK&#60;/status_msg&#62;
    &#60;balance&#62;0.00&#60;/balance&#62;
&#60;/response&#62;
</p>
                       
                      </div>

                      <div class="col-sm-12">
                        <br />
                        <h1>Response Parameters</h1>
                        <table class="table table-bordered table-striped">
                          <tr>
                            <th>#</th>
                            <th>Parameter Key</th>
                            <th>Description</th>
                          </tr>
                          <tr>
                            <td>1.</td>
                            <td>status_code</td>
                            <td>
                              400 = Variable Related Error <br />
                              401 = Variable Data Related Error <br />
                              200 = Success
                            </td>
                          </tr>
                          <tr>
                            <td>2.</td>
                            <td>status_msg</td>
                            <td>API Success or Error Message according status code.</td>
                          </tr>
                          <tr>
                            <td>3.</td>
                            <td>balance</td>
                            <td>
                              Your account current balance.
                            </td>
                          </tr>
                          
                        </table>
                        
                      </div>

                    </div>
                    <div class="tab-pane fade" id="statusCheck" role="tabpanel" aria-labelledby="statusCheck-tab">
                      
                      <div class="col-sm-12">
                        <br />
                        <h1>Request API URL</h1>
                        <p style="font-size: 18px; font-weight: bold;">{site_url}portal/api/statusAuth</p>
                        <hr />
                        <p><b>Request Method - POST</b></p>
                      </div>
                      <div class="col-sm-12">
                        <br />
                        <h1>Header Parameter</h1>
                        <table class="table table-bordered table-striped">
                          <tr>
                            <th>#</th>
                            <th>Parameter Key</th>
                            <th>Description</th>
                          </tr>
                          <tr>
                            <td>1.</td>
                            <td>MemberID</td>
                            <td>Your account unique id.</td>
                          </tr>
                          <tr>
                            <td>2.</td>
                            <td>TXNPWD</td>
                            <td>Your account trasaction password.</td>
                          </tr>
                        </table>
                        <p style="color: red;">Note:- All Parameter key are case sensitive.</p>
                      </div>
                      <div class="col-sm-12">
                        <br />
                        <h1>Post Parameter</h1>
                        <table class="table table-bordered table-striped">
                          <tr>
                            <th>#</th>
                            <th>Parameter Key</th>
                            <th>Description</th>
                          </tr>
                          <tr>
                            <td>1.</td>
                            <td>txnID</td>
                            <td>Unique Trasaction ID Numbers Only. Minimum Lenght Should Be 10 Digit.</td>
                          </tr>
                          <tr>
                            <td>2.</td>
                            <td>response_type</td>
                            <td>1 for JSON, 2 for XML. Default Response Will be in JSON Format</td>
                          </tr>
                        </table>
                        <p style="color: red;">Note:- All Parameter key are case sensitive.</p>
                      </div>

                      <div class="col-sm-12">
                        <br />
                        <h1>JSON Response</h1>
                        <p style="font-size: 18px; white-space: pre;">{
  "status_code": 200,
  "status_msg": "OK",
  "status": "FAILED",
  "txnid": 41191612523938,
  "amount": 10,
  "operator_txnid": ""
}</p>
                       
                      </div>

                      <div class="col-sm-12">
                        <br />
                        <h1>XML Response</h1>
                        <p style="font-size: 18px; white-space: pre;">
&#60;?xml version="1.0"?&#62;
&#60;response&#62;
    &#60;status_code&#62;200&#60;/status_code&#62;
    &#60;status_msg&#62;OK&#60;/status_msg&#62;
    &#60;status&#62;FAILED&#60;/status&#62;
    &#60;txnid&#62;1234567890&#60;/txnid&#62;
    &#60;amount&#62;10&#60;/amount&#62;
    &#60;operator_txnid&#62;&#60;/operator_txnid&#62;
&#60;/response&#62;
</p>
                       
                      </div>

                      <div class="col-sm-12">
                        <br />
                        <h1>Response Parameters</h1>
                        <table class="table table-bordered table-striped">
                          <tr>
                            <th>#</th>
                            <th>Parameter Key</th>
                            <th>Description</th>
                          </tr>
                          <tr>
                            <td>1.</td>
                            <td>status_code</td>
                            <td>
                              400 = Variable Related Error <br />
                              401 = Variable Data Related Error <br />
                              200 = Success
                            </td>
                          </tr>
                          <tr>
                            <td>2.</td>
                            <td>status_msg</td>
                            <td>API Success or Error Message according status code.</td>
                          </tr>
                          <tr>
                            <td>3.</td>
                            <td>status</td>
                            <td>
                              PENDING <br />
                              FAILED <br />
                              SUCCESS 
                            </td>
                          </tr>
                          <tr>
                            <td>4.</td>
                            <td>txnid</td>
                            <td>Your unique Recharge ID.</td>
                          </tr>
                          <tr>
                            <td>5.</td>
                            <td>operator_txnid</td>
                            <td>Unique Recharge ID from Operator Side.</td>
                          </tr>
                          <tr>
                            <td>6.</td>
                            <td>amount</td>
                            <td>Recharge Amount</td>
                          </tr>
                        </table>
                        
                      </div>

                    </div>
                    <div class="tab-pane fade" id="callBack" role="tabpanel" aria-labelledby="callBack-tab">
                      
                      <div class="col-sm-12">
                        <br />
                        <h1>Update Callback URL</h1>
                        <div class="form-group">
                          <input type="text" class="form-control" name="call_back_url" placeholder="Enter Callback URL" value="{call_back_url}">
                        </div>
                        <div class="form-group">
                          <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                        <hr />
                        <p><b>Request Method - POST</b></p>
                      </div>
                      <div class="col-sm-12">
                        <br />
                        <h1>Post Parameter</h1>
                        <table class="table table-bordered table-striped">
                          <tr>
                            <th>#</th>
                            <th>Parameter Key</th>
                            <th>Description</th>
                          </tr>
                          <tr>
                            <td>1.</td>
                            <td>status</td>
                            <td>
                              PENDING <br />
                              FAILED <br />
                              SUCCESS 
                            </td>
                          </tr>
                          <tr>
                            <td>2.</td>
                            <td>txnid</td>
                            <td>Your unique Recharge ID.</td>
                          </tr>
                          <tr>
                            <td>3.</td>
                            <td>operator_txnid</td>
                            <td>Unique Recharge ID from Operator Side.</td>
                          </tr>
                          <tr>
                            <td>4.</td>
                            <td>amount</td>
                            <td>Recharge Amount</td>
                          </tr>
                        </table>
                        
                      </div>

                      

                      


                    </div>
                  </div>
                </div>
              </div>
              
            </div>
            <?php echo form_close(); ?>
          </div>
        </div>

