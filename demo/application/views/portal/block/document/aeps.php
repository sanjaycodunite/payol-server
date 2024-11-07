<div class="card shadow mb-4">
              {system_message}               
              {system_info}
              <?php echo form_open_multipart('portal/document/dmtCallbackAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>AEPS API Document</b></h4>
                </div>

                

              </div>
            </div>
            
            <div class="card-body">
              
              <div class="row">
                <div class="col-sm-12">
                  <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="onboardingapi-tab" data-toggle="tab" href="#onboardingapi" role="tab" aria-controls="onboardingapi" aria-selected="true">On Boarding API</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="onboardingsendotpapi-tab" data-toggle="tab" href="#onboardingsendotpapi" role="tab" aria-controls="onboardingsendotpapi" aria-selected="true">Send OTP API</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="onboardingresendotpapi-tab" data-toggle="tab" href="#onboardingresendotpapi" role="tab" aria-controls="onboardingresendotpapi" aria-selected="true">Resend OTP API</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="onboardingotpauthapi-tab" data-toggle="tab" href="#onboardingotpauthapi" role="tab" aria-controls="onboardingotpauthapi" aria-selected="true">OTP Auth API</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="onboardingbioauth-tab" data-toggle="tab" href="#onboardingbioauth" role="tab" aria-controls="onboardingbioauth" aria-selected="true">Biomatric Auth API</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="txnauthapi-tab" data-toggle="tab" href="#txnauthapi" role="tab" aria-controls="txnauthapi" aria-selected="true">Txn Auth API</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="cashdepositeauth-tab" data-toggle="tab" href="#cashdepositeauth" role="tab" aria-controls="cashdepositeauth" aria-selected="true">Cash Deposite</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="cashdepositeotpauth-tab" data-toggle="tab" href="#cashdepositeotpauth" role="tab" aria-controls="cashdepositeotpauth" aria-selected="true">Cash Deposite OTP</a>
                    </li>
                    
                  </ul>
                  <div class="tab-content apidoc" id="myTabContent">
                    <div class="tab-pane fade show active" id="onboardingapi" role="tabpanel" aria-labelledby="onboardingapi-tab">
                      
                      <div class="col-sm-12">
                        <br />
                        <h1>Request API URL</h1>
                        <p style="font-size: 18px; font-weight: bold;">{site_url}portal/api/aepsOnBoardAuth</p>
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
                          <tr>
                            <td>1.</td>
                            <td>first_name</td>
                            <td>Member First Name</td>
                          </tr>
                          <tr>
                            <td>2.</td>
                            <td>last_name</td>
                            <td>Member Last Name</td>
                          </tr>
                          <tr>
                            <td>3.</td>
                            <td>mobile</td>
                            <td>Member Mobile</td>
                          </tr>
                          <tr>
                            <td>4.</td>
                            <td>shop_name</td>
                            <td>Member Shop/Firm Name</td>
                          </tr>
                          <tr>
                            <td>5.</td>
                            <td>state_id</td>
                            <td>State ID</td>
                          </tr>
                          <tr>
                            <td>5.</td>
                            <td>city_id</td>
                            <td>City ID</td>
                          </tr>
                          <tr>
                            <td>6.</td>
                            <td>address</td>
                            <td>Member Address</td>
                          </tr>
                          <tr>
                            <td>7.</td>
                            <td>pin_code</td>
                            <td>Member PIN Code/Zip Code</td>
                          </tr>
                          <tr>
                            <td>8.</td>
                            <td>aadhar_no</td>
                            <td>Member Aadhar No</td>
                          </tr>
                          <tr>
                            <td>9.</td>
                            <td>pancard_no</td>
                            <td>Member Pancard No.</td>
                          </tr>
                          <tr>
                            <td>10.</td>
                            <td>aadhar_photo</td>
                            <td>Member Aadhar Photo base64encode Format</td>
                          </tr>
                          <tr>
                            <td>11.</td>
                            <td>pancard_photo</td>
                            <td>Member Pancard Photo base64encode Format</td>
                          </tr>
                          <tr>
                            <td>12.</td>
                            <td>member_id</td>
                            <td>Member Login ID</td>
                          </tr>
                          <tr>
                            <td>13.</td>
                            <td>email</td>
                            <td>Member Email</td>
                          </tr>
                          <tr>
                            <td>14.</td>
                            <td>txn_pin</td>
                            <td>Member Login Transaction PIN</td>
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
  "status": "SUCCESS"
}</p>
                       
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
                         
                        </table>
                        
                      </div>


                    </div>
                    <div class="tab-pane fade" id="onboardingsendotpapi" role="tabpanel" aria-labelledby="onboardingsendotpapi-tab">
                      
                      <div class="col-sm-12">
                        <br />
                        <h1>Request API URL</h1>
                        <p style="font-size: 18px; font-weight: bold;">{site_url}portal/api/aepsOnBoardSendOtp</p>
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
                          <tr>
                            <td>1.</td>
                            <td>mobile</td>
                            <td>Member Mobile</td>
                          </tr>
                          <tr>
                            <td>2.</td>
                            <td>aadhar_no</td>
                            <td>Member Aadhar No</td>
                          </tr>
                          <tr>
                            <td>3.</td>
                            <td>pancard_no</td>
                            <td>Member Pancard No.</td>
                          </tr>
                          <tr>
                            <td>4.</td>
                            <td>member_id</td>
                            <td>Member Login ID</td>
                          </tr>
                          <tr>
                            <td>5.</td>
                            <td>email</td>
                            <td>Member Email</td>
                          </tr>
                          <tr>
                            <td>6.</td>
                            <td>txn_pin</td>
                            <td>Member Login Transaction PIN</td>
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
  "primaryKeyId": "E45484654SDF84",
  "encodeFPTxnId": "4SDF54SDF84DF"
}</p>
                       
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
                            <td>primaryKeyId</td>
                            <td>
                              Primary Key ID
                            </td>
                          </tr>
                          <tr>
                            <td>5.</td>
                            <td>encodeFPTxnId</td>
                            <td>
                              Encode FPTxnId
                            </td>
                          </tr>
                         
                        </table>
                        
                      </div>


                    </div>
                    <div class="tab-pane fade" id="onboardingresendotpapi" role="tabpanel" aria-labelledby="onboardingresendotpapi-tab">
                      
                      <div class="col-sm-12">
                        <br />
                        <h1>Request API URL</h1>
                        <p style="font-size: 18px; font-weight: bold;">{site_url}portal/api/aepsOnBoardResendOtp</p>
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
                          <tr>
                            <td>1.</td>
                            <td>member_id</td>
                            <td>Member Login ID</td>
                          </tr>
                          <tr>
                            <td>2.</td>
                            <td>primaryKeyId</td>
                            <td>Primary Key Id</td>
                          </tr>
                          <tr>
                            <td>3.</td>
                            <td>encodeFPTxnId</td>
                            <td>Encode FPTxnId</td>
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
  "primaryKeyId": "E45484654SDF84",
  "encodeFPTxnId": "4SDF54SDF84DF"
}</p>
                       
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
                            <td>primaryKeyId</td>
                            <td>
                              Primary Key ID
                            </td>
                          </tr>
                          <tr>
                            <td>5.</td>
                            <td>encodeFPTxnId</td>
                            <td>
                              Encode FPTxnId
                            </td>
                          </tr>
                         
                        </table>
                        
                      </div>


                    </div>
                    <div class="tab-pane fade" id="onboardingotpauthapi" role="tabpanel" aria-labelledby="onboardingotpauthapi-tab">
                      
                      <div class="col-sm-12">
                        <br />
                        <h1>Request API URL</h1>
                        <p style="font-size: 18px; font-weight: bold;">{site_url}portal/api/aepsOnBoardOtpAuth</p>
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
                          <tr>
                            <td>1.</td>
                            <td>member_id</td>
                            <td>Member Login ID</td>
                          </tr>
                          <tr>
                            <td>2.</td>
                            <td>primaryKeyId</td>
                            <td>Primary Key Id</td>
                          </tr>
                          <tr>
                            <td>3.</td>
                            <td>encodeFPTxnId</td>
                            <td>Encode FPTxnId</td>
                          </tr>
                          <tr>
                            <td>4.</td>
                            <td>otp_code</td>
                            <td>OTP Code</td>
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
  "status": "SUCCESS"
}</p>
                       
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
                          
                        </table>
                        
                      </div>


                    </div>
                    <div class="tab-pane fade" id="onboardingbioauth" role="tabpanel" aria-labelledby="onboardingbioauth-tab">
                      
                      <div class="col-sm-12">
                        <br />
                        <h1>Request API URL</h1>
                        <p style="font-size: 18px; font-weight: bold;">{site_url}portal/api/aepsOnBoardBioAuth</p>
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
                          <tr>
                            <td>1.</td>
                            <td>member_id</td>
                            <td>Member Login ID</td>
                          </tr>
                          <tr>
                            <td>2.</td>
                            <td>primaryKeyId</td>
                            <td>Primary Key Id</td>
                          </tr>
                          <tr>
                            <td>3.</td>
                            <td>encodeFPTxnId</td>
                            <td>Encode FPTxnId</td>
                          </tr>
                          <tr>
                            <td>4.</td>
                            <td>BiometricData</td>
                            <td>Biomatric Data</td>
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
  "status": "SUCCESS"
}</p>
                       
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
                          
                        </table>
                        
                      </div>


                    </div>
                    <div class="tab-pane fade" id="txnauthapi" role="tabpanel" aria-labelledby="txnauthapi-tab">
                      
                      <div class="col-sm-12">
                        <br />
                        <h1>Request API URL</h1>
                        <p style="font-size: 18px; font-weight: bold;">{site_url}portal/api/aepsTxnAuth</p>
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
                          <tr>
                            <td>1.</td>
                            <td>member_id</td>
                            <td>Member Login ID</td>
                          </tr>
                          <tr>
                            <td>2.</td>
                            <td>txn_pin</td>
                            <td>Member Txn Pin</td>
                          </tr>
                          <tr>
                            <td>3.</td>
                            <td>serviceType</td>
                            <td>Service Type (balinfo,ministatement,balwithdraw,aadharpay)</td>
                          </tr>
                          <tr>
                            <td>4.</td>
                            <td>deviceIMEI</td>
                            <td>Device IMEI</td>
                          </tr>
                          <tr>
                            <td>5.</td>
                            <td>aadharNumber</td>
                            <td>Member Aadhar No</td>
                          </tr>
                          <tr>
                            <td>6.</td>
                            <td>mobile</td>
                            <td>Member Mobile No.</td>
                          </tr>
                          <tr>
                            <td>7.</td>
                            <td>biometricData</td>
                            <td>Biomatric Data</td>
                          </tr>
                          <tr>
                            <td>8.</td>
                            <td>amount</td>
                            <td>Amount</td>
                          </tr>
                          <tr>
                            <td>9.</td>
                            <td>iin</td>
                            <td>Bank IIN</td>
                          </tr>
                          <tr>
                            <td>10.</td>
                            <td>txnID</td>
                            <td>Transaction ID (Balance Info - BINQ, Mini Statement - MNST, Cash Withdrawal - CSWD, Aadhar Pay - APAY)</td>
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
  "balanceAmount":"0.00",
  "bankRRN":"123456789",
  "data":""
}</p>
                       
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
                          
                        </table>
                        
                      </div>


                    </div>

                    <div class="tab-pane fade" id="cashdepositeauth" role="tabpanel" aria-labelledby="cashdepositeauth-tab">
                      
                      <div class="col-sm-12">
                        <br />
                        <h1>Request API URL</h1>
                        <p style="font-size: 18px; font-weight: bold;">{site_url}portal/api/cashDepositeAuth</p>
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
                          <tr>
                            <td>1.</td>
                            <td>member_id</td>
                            <td>Member Login ID</td>
                          </tr>
                          <tr>
                            <td>2.</td>
                            <td>txn_pin</td>
                            <td>Member Txn Pin</td>
                          </tr>
                          <tr>
                            <td>3.</td>
                            <td>mobile</td>
                            <td>User Mobile No.</td>
                          </tr>
                          <tr>
                            <td>4.</td>
                            <td>account_no</td>
                            <td>User Account No.</td>
                          </tr>
                          <tr>
                            <td>5.</td>
                            <td>amount</td>
                            <td>Amount</td>
                          </tr>
                          <tr>
                            <td>6.</td>
                            <td>remark</td>
                            <td>Remark</td>
                          </tr>
                          <tr>
                            <td>7.</td>
                            <td>txnID</td>
                            <td>Unique Transaction ID</td>
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
  "txnID":"123456789"
}</p>
                       
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
                          
                        </table>
                        
                      </div>


                    </div>

                    <div class="tab-pane fade" id="cashdepositeotpauth" role="tabpanel" aria-labelledby="cashdepositeotpauth-tab">
                      
                      <div class="col-sm-12">
                        <br />
                        <h1>Request API URL</h1>
                        <p style="font-size: 18px; font-weight: bold;">{site_url}portal/api/cashDepositeOtpAuth</p>
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
                          <tr>
                            <td>1.</td>
                            <td>member_id</td>
                            <td>Member Login ID</td>
                          </tr>
                          <tr>
                            <td>2.</td>
                            <td>txn_pin</td>
                            <td>Member Txn Pin</td>
                          </tr>
                          <tr>
                            <td>3.</td>
                            <td>txnID</td>
                            <td>Unique Transaction ID</td>
                          </tr>
                          <tr>
                            <td>4.</td>
                            <td>otp_code</td>
                            <td>OTP</td>
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
  "txnid":"123456789",
  "bankRrn":"123456789"
}</p>
                       
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

