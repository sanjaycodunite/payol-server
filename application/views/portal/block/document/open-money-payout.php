<div class="card shadow mb-4">
              {system_message}               
              {system_info}
              <?php echo form_open_multipart('portal/document/openCallbackAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>Open Money Payout API Document</b></h4>
                </div>

              </div>
            </div>
            
            <div class="card-body">
              
              <div class="row">
                <div class="col-sm-12">
                  <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="beneficiary-tab" data-toggle="tab" href="#beneficiary" role="tab" aria-controls="beneficiary" aria-selected="true">Add Account Beneficiary Api</a>
                    </li>

                    <li class="nav-item">
                      <a class="nav-link" id="beneficiary-list-tab" data-toggle="tab" href="#beneficiary-list" role="tab" aria-controls="beneficiary-list" aria-selected="true"> Account Beneficiary List</a>
                    </li>

                     <li class="nav-item">
                      <a class="nav-link" id="vpa-beneficiary-tab" data-toggle="tab" href="#vpa-beneficiary" role="tab" aria-controls="vpa-beneficiary" aria-selected="true"> Add VPA Beneficiary</a>
                    </li>



                    <li class="nav-item">
                      <a class="nav-link" id="vpa-beneficiary-list-tab" data-toggle="tab" href="#vpa-beneficiary-list" role="tab" aria-controls="vpa-beneficiary-list" aria-selected="true"> VPA Beneficiary List</a>
                    </li>


                    <li class="nav-item">
                      <a class="nav-link" id="open-money-payout-tab" data-toggle="tab" href="#open-money-payout-list" role="tab" aria-controls="open-money-payout" aria-selected="true"> Payout Api</a>
                    </li>

                      
                    <li class="nav-item">
                      <a class="nav-link" id="callBack-tab" data-toggle="tab" href="#callBack" role="tab" aria-controls="callBack" aria-selected="false">Call Back URL</a>
                    </li>
                     <li class="nav-item">
                      <a class="nav-link" id="checkStatus-tab" data-toggle="tab" href="#checkStatus" role="tab" aria-controls="checkStatus" aria-selected="true">Status Check API</a>
                    </li>

                  </ul>
                  <div class="tab-content apidoc" id="myTabContent">
                    <div class="tab-pane fade show active" id="beneficiary" role="tabpanel" aria-labelledby="rechargeAPI-tab">
                      
                      <div class="col-sm-12">
                        <br />
                        <h1>Request API URL</h1>
                        <p style="font-size: 18px; font-weight: bold;">{site_url}portal/api/addBeneficiary</p>
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
                            <td>account_no</td>
                            <td>Customer Account No.</td>
                          </tr>
                          <tr>
                            <td>3.</td>
                            <td>ifsc</td>
                            <td>Customer Bank IFSC Code.</td>
                          </tr>
                          <tr>
                            <td>4.</td>
                            <td>email</td>
                            <td>Beneficiary's email</td>
                          </tr>
                          <tr>
                            <td>4.</td>
                            <td>mobile</td>
                            <td>Beneficiary's mobile</td>
                          </tr>
                          <tr>
                            <td>5.</td>
                            <td>txnID</td>
                            <td>Unique Trasaction ID(Min Length 10 )</td>
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
  "bene_id" : "vab_4585414585251252",
  "txnid": 16131198309337,
  "account_holder_name" : "Test Name",
  "account_number":"1221108115222",
  "ifsc":"PUNB0874500",
  "email" : "test@gmail.com",
  "mobile" : "8541521285",
  "opt_msg" : "Transaction Successful"
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
                            <td>bene_id</td>
                            <td>Beneficiary ID.</td>
                          </tr>

                          <tr>
                            <td>5.</td>
                            <td>txnid</td>
                            <td>Your Unique Trasaction ID.</td>
                          </tr>

                          <tr>
                            <td>6.</td>
                            <td>account_holder_name</td>
                            <td>Account Holder Name.</td>
                          </tr>
                        
                         <tr>
                            <td>7.</td>
                            <td>account_number</td>
                            <td>Account Number.</td>
                          </tr>


                           <tr>
                            <td>8.</td>
                            <td>ifsc</td>
                            <td>IFSC.</td>
                          </tr>
                        


                         <tr>
                            <td>9.</td>
                            <td>email</td>
                            <td> Beneficiary Email.</td>
                          </tr>
                        

                         <tr>
                            <td>10.</td>
                            <td>mobile</td>
                            <td>Beneficiary Mobile.</td>
                          </tr>
                        

                         <tr>
                            <td>11.</td>
                            <td>opt_msg</td>
                            <td>Opt Msg.</td>
                          </tr>
                        

                        

                        </table>
                        
                      </div>


                    </div>


                     <div class="tab-pane fade show" id="beneficiary-list" role="tabpanel" aria-labelledby="beneficiary-list-tab">
                      
                      <div class="col-sm-12">
                        <br />
                        <h1>Request API URL</h1>
                        <p style="font-size: 18px; font-weight: bold;">{site_url}portal/api/beneficiaryList</p>
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
                          
                            <td>1.</td>
                            <td>transaction_id</td>
                            <td>Unique Trasaction ID(Min Length 10 )</td>
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
  "bene_id" : "vab_4585414585251252",
  "txnid": 16131198309337,
  "account_holder_name" : "Test Name",
  "account_number":"1221108115222",
  "ifsc":"PUNB0874500",
  "email" : "test@gmail.com",
  "mobile" : "8541521285",
  "opt_msg" : "Transaction Successful"
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
                            <td>bene_id</td>
                            <td>Beneficiary ID.</td>
                          </tr>

                          <tr>
                            <td>5.</td>
                            <td>txnid</td>
                            <td>Your Unique Trasaction ID.</td>
                          </tr>

                          <tr>
                            <td>6.</td>
                            <td>account_holder_name</td>
                            <td>Account Holder Name.</td>
                          </tr>
                        
                         <tr>
                            <td>7.</td>
                            <td>account_number</td>
                            <td>Account Number.</td>
                          </tr>


                           <tr>
                            <td>8.</td>
                            <td>ifsc</td>
                            <td>IFSC.</td>
                          </tr>
                        


                         <tr>
                            <td>9.</td>
                            <td>email</td>
                            <td> Beneficiary Email.</td>
                          </tr>
                        

                         <tr>
                            <td>10.</td>
                            <td>mobile</td>
                            <td>Beneficiary Mobile.</td>
                          </tr>
                        

                         <tr>
                            <td>11.</td>
                            <td>opt_msg</td>
                            <td>Opt Msg.</td>
                          </tr>
                        

                        

                        </table>
                        
                      </div>


                    </div>



                    <div class="tab-pane fade show" id="vpa-beneficiary-list" role="tabpanel" aria-labelledby="vpa-beneficiary-list-tab">
                      
                      <div class="col-sm-12">
                        <br />
                        <h1>Request API URL</h1>
                        <p style="font-size: 18px; font-weight: bold;">{site_url}portal/api/vpaBeneficiaryList</p>
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
                          
                            <td>1.</td>
                            <td>transaction_id</td>
                            <td>Unique Trasaction ID(Min Length 10 )</td>
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
  "bene_id" : "vab_4585414585251252",
  "txnid": 16131198309337,
  "account_holder_name" : "Test Name",
  "account_number":"1221108115222",
  "ifsc":"PUNB0874500",
  "email" : "test@gmail.com",
  "mobile" : "8541521285",
  "opt_msg" : "Transaction Successful"
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
                            <td>bene_id</td>
                            <td>Beneficiary ID.</td>
                          </tr>

                          <tr>
                            <td>5.</td>
                            <td>txnid</td>
                            <td>Your Unique Trasaction ID.</td>
                          </tr>

                          <tr>
                            <td>6.</td>
                            <td>account_holder_name</td>
                            <td>Account Holder Name.</td>
                          </tr>
                        
                         <tr>
                            <td>7.</td>
                            <td>account_number</td>
                            <td>Account Number.</td>
                          </tr>

                         <tr>
                            <td>8.</td>
                            <td>email</td>
                            <td> Beneficiary Email.</td>
                          </tr>
                        

                         <tr>
                            <td>9.</td>
                            <td>mobile</td>
                            <td>Beneficiary Mobile.</td>
                          </tr>
                        

                         <tr>
                            <td>10.</td>
                            <td>opt_msg</td>
                            <td>Opt Msg.</td>
                          </tr>
                        </table>
                        
                      </div>


                    </div>




                       <div class="tab-pane fade show" id="vpa-beneficiary" role="tabpanel" aria-labelledby="vpa-beneficiary">
                      
                      <div class="col-sm-12">
                        <br />
                        <h1>Request API URL</h1>
                        <p style="font-size: 18px; font-weight: bold;">{site_url}portal/api/addVpaBeneficiary</p>
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
                            <td>account_no</td>
                            <td>Customer Vpa Address.</td>
                          </tr>
                         
                            <td>3.</td>
                            <td>email</td>
                            <td>Beneficiary's email</td>
                          </tr>
                          <tr>
                            <td>4.</td>
                            <td>mobile</td>
                            <td>Beneficiary's mobile</td>
                          </tr>
                          <tr>
                            <td>5.</td>
                            <td>txnID</td>
                            <td>Unique Trasaction ID(Min Length 10 )</td>
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
  "bene_id" : "vab_4585414585251252",
  "txnid": 16131198309337,
  "account_holder_name" : "Test Name",
  "account_number":"1221108115222",
  "ifsc":"PUNB0874500",
  "email" : "test@gmail.com",
  "mobile" : "8541521285",
  "opt_msg" : "Transaction Successful"
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
                            <td>bene_id</td>
                            <td>Beneficiary ID.</td>
                          </tr>

                          <tr>
                            <td>5.</td>
                            <td>txnid</td>
                            <td>Your Unique Trasaction ID.</td>
                          </tr>

                          <tr>
                            <td>6.</td>
                            <td>account_holder_name</td>
                            <td>Account Holder Name.</td>
                          </tr>
                        
                         <tr>
                            <td>7.</td>
                            <td>account_number</td>
                            <td>Account Number.</td>
                          </tr>


                         <tr>
                            <td>8.</td>
                            <td>email</td>
                            <td> Beneficiary Email.</td>
                          </tr>
                        

                         <tr>
                            <td>9.</td>
                            <td>mobile</td>
                            <td>Beneficiary Mobile.</td>
                          </tr>
                        

                         <tr>
                            <td>10.</td>
                            <td>opt_msg</td>
                            <td>Opt Msg.</td>
                          </tr>
                        

                        

                        </table>
                        
                      </div>


                    </div>


                    <div class="tab-pane fade show" id="open-money-payout-list" role="tabpanel" aria-labelledby="open-money-payout-list">
                      
                      <div class="col-sm-12">
                        <br />
                        <h1>Request API URL</h1>
                        <p style="font-size: 18px; font-weight: bold;">{site_url}portal/api/openPayout</p>
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
                            <td>txnID</td>
                            <td>Unique Trasaction ID(Min Length 10 )</td>

                          </tr>

                          <tr>
                            <td>2.</td>
                            <td>bene_id</td>
                            <td>Beneficiary ID(Get From Beneficiary List Api)</td>

                          </tr>

                           <tr>
                            <td>3.</td>
                            <td>amount</td>
                            <td>Amount</td>

                          </tr>

                          <tr>
                            <td>3.</td>
                            <td>payment_mode</td>
                            <td>Payment Mode (imps,neft,rtgs)</td>
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
  "bene_id" : "vab_4585414585251252",
  "txnid": 16131198309337,
  "account_holder_name" : "Test Name",
  "account_number":"1221108115222",
  "ifsc":"PUNB0874500",
  "email" : "test@gmail.com",
  "mobile" : "8541521285",
  "opt_msg" : "Transaction Successful"
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
                            <td>bene_id</td>
                            <td>Beneficiary ID.</td>
                          </tr>

                          <tr>
                            <td>5.</td>
                            <td>txnid</td>
                            <td>Your Unique Trasaction ID.</td>
                          </tr>

                          <tr>
                            <td>6.</td>
                            <td>account_holder_name</td>
                            <td>Account Holder Name.</td>
                          </tr>
                        
                         <tr>
                            <td>7.</td>
                            <td>account_number</td>
                            <td>Account Number.</td>
                          </tr>


                           <tr>
                            <td>8.</td>
                            <td>ifsc</td>
                            <td>IFSC.</td>
                          </tr>
                        


                         <tr>
                            <td>9.</td>
                            <td>email</td>
                            <td> Beneficiary Email.</td>
                          </tr>
                        

                         <tr>
                            <td>10.</td>
                            <td>mobile</td>
                            <td>Beneficiary Mobile.</td>
                          </tr>
                        

                         <tr>
                            <td>11.</td>
                            <td>opt_msg</td>
                            <td>Opt Msg.</td>
                          </tr>
                        

                        

                        </table>
                        
                      </div>


                    </div>


                    


                    <div class="tab-pane fade" id="callBack" role="tabpanel" aria-labelledby="callBack-tab">
                      
                      <div class="col-sm-12">
                        <br />
                        <h1>Update Callback URL</h1>
                        <div class="form-group">
                          <input type="text" class="form-control" name="open_payout_call_back_url" placeholder="Enter Callback URL" value="{open_payout_call_back_url}">
                        </div>
                        <div class="form-group">
                          <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                        <hr />
                        <p><b>Request Method - GET Format</b></p>
                      </div>
                      <div class="col-sm-12">
                        <br />
                        <h1>GET Parameter</h1>
                        status=SUCCESS&txnid=1707798479&optxid=tr_7aqbokI4kJM1KHZD6dOSwVoka&amount=10&rrn=404409703139
                        
                      </div>

                      

                      


                    </div>

                    <div class="tab-pane fade show" id="checkStatus" role="tabpanel" aria-labelledby="checkStatus-tab">
                      
                      <div class="col-sm-12">
                        <br />
                        <h1>Request API URL</h1>
                        <p style="font-size: 18px; font-weight: bold;">{site_url}portal/api/openPayoutCheckStatus</p>
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
                            <td>txnID</td>
                            <td>Your Unique Transcation ID</td>
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
  "amount" : 100,
  "txnid": 16131198309337,
  "rrn" : 302111739646,
  "opt_msg" : "Transaction Fetch Successfully"
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
                            <td>txnid</td>
                            <td>Your Unique Trasaction ID.</td>
                          </tr>
                          <tr>
                            <td>5.</td>
                            <td>rrn</td>
                            <td>rrn no from bank side.</td>
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

