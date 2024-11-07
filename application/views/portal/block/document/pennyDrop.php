<div class="card shadow mb-4">
              {system_message}               
              {system_info}
              <?php echo form_open_multipart('portal/document/callbackAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>Penny Drop API Document</b></h4>
                </div>

                

              </div>
            </div>
            
            <div class="card-body">
              
              <div class="row">
                <div class="col-sm-12">
                  <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="rechargeAPI-tab" data-toggle="tab" href="#rechargeAPI" role="tab" aria-controls="rechargeAPI" aria-selected="true">Penny Drop API</a>
                    </li>
                    
                  </ul>
                  <div class="tab-content apidoc" id="myTabContent">
                    <div class="tab-pane fade show active" id="rechargeAPI" role="tabpanel" aria-labelledby="rechargeAPI-tab">
                      
                      <div class="col-sm-12">
                        <br />
                        <h1>Request API URL</h1>
                        <p style="font-size: 18px; font-weight: bold;">{site_url}portal/api/pennyDrop</p>
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
                            <td>Memberid</td>
                            <td>Your account unique id.</td>
                          </tr>
                          <tr>
                            <td>2.</td>
                            <td>Txnpwd</td>
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
                          <!-- <tr>
                            <td>1.</td>
                            <td>mobile</td>
                            <td>Customer Mobile No.</td>
                          </tr> -->
                          <tr>
                            <td>1.</td>
                            <td>account_holder_name</td>
                            <td>Account Holder Name</td>
                          </tr>
                          <tr>
                            <td>2.</td>
                            <td>account_number</td>
                            <td>Customer Account No.</td>
                          </tr>
                          <tr>
                            <td>3.</td>
                            <td>ifsc</td>
                            <td>Customer Bank IFSC Code.</td>
                          </tr>
                         
                          <tr>
                            <td>4.</td>
                            <td>txnID</td>
                            <td>Unique Trasaction ID(Min Length 10 )</td>
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
  "status": "SUCCESS",  
  "txnid": 16131198309337,
  "account_holder_name : Test",
  "account_number":1221108115222TYNUH
  "opt_msg" : "Transaction Successful"
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
    &#60;account_holder_name&#62;Test&#60;/account_holder_name&#62;
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
                            <td>Your Unique Trasaction ID.</td>
                          </tr>
                          <tr>
                            <td>5.</td>
                            <td>optxid</td>
                            <td>Unique Trasaction ID from Operator Side.</td>
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

