<div class="card shadow mb-4">
              {system_message}               
              {system_info}
              <?php echo form_open_multipart('portal/document/upiCallbackAuth', array('id' => 'admin_profile'),array('method'=>'post')); ?>
            <div class="card-header py-3">
              <div class="row">
                <div class="col-sm-8">
                <h4><b>UPI QR Only API Document</b></h4>
                </div>

                

              </div>
            </div>
            
            <div class="card-body">
              
              <div class="row">
                <div class="col-sm-12">
                  <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="bankListApi-tab" data-toggle="tab" href="#bankListApi" role="tab" aria-controls="bankListApi" aria-selected="true">Generate QR API</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" id="callBack-tab" data-toggle="tab" href="#callBack" role="tab" aria-controls="callBack" aria-selected="false">Call Back URL</a>
                    </li>
                    
                     <li class="nav-item">
                      <a class="nav-link" id="checkStatus-tab" data-toggle="tab" href="#checkStatus" role="tab" aria-controls="checkStatus" aria-selected="true">Status Check API</a>
                    </li>
                    
                    
                  </ul>
                  <div class="tab-content apidoc" id="myTabContent">
                    <div class="tab-pane fade show active" id="bankListApi" role="tabpanel" aria-labelledby="bankListApi-tab">
                      
                      <div class="col-sm-12">
                        <br />
                        <h1>Request API URL</h1>
                        <p style="font-size: 18px; font-weight: bold;">{site_url}portal/api/generateQrAuth?memberid={memberid}&txnpwd={txnpwd}&name={name}&amount={amount}&txnid={txnid}</p>
                        <hr />
                        <p><b>Request Method - GET</b></p>
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
                            <td>memberid</td>
                            <td>Your account unique id.</td>
                          </tr>
                          <tr>
                            <td>2.</td>
                            <td>txnpwd</td>
                            <td>Your account trasaction password.</td>
                          </tr>
                          <tr>
                            <td>3.</td>
                            <td>name</td>
                            <td>QR Map Name Which will show after scan qr code</td>
                          </tr>
                          <tr>
                            <td>4.</td>
                            <td>amount</td>
                            <td>Amount</td>
                          </tr>
                          <tr>
                            <td>4.</td>
                            <td>txnid</td>
                            <td>Unique Transaction ID, Minimum Length Should be 10</td>
                          </tr>
                          
                        </table>
                        <p style="color: red;">Note:- All Parameter key are case sensitive.</p>
                      </div>

                      <div class="col-sm-12">
                        <br />
                        <h1>JSON Response</h1>
                        <p style="font-size: 18px; white-space: pre;">{
  "status_code": 200,
  "status_msg": "QR Generated Successfully.",
  "status": "SUCCESS",
  "qr": "upi://pay?pa=<merchant VPA>&pn=<merchant name>&tr=<Refid>&am=<amount>&cu=INR&mc=<MCCcode>",
  "txnID": "12356445645"
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
                            <td>qr</td>
                            <td>QR Code String</td>
                          </tr>
                          <tr>
                            <td>3.</td>
                            <td>txnID</td>
                            <td>QR Reference ID</td>
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
                          <p>Ex: https://www.yourwebsite.com/index.php?</p>
                        </div>
                        <div class="form-group">
                          <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                        <hr />
                        <p><b>Request Method - POST Json Format</b></p>
                      </div>
                      <div class="col-sm-12">
                        <br />
                        <h1>Post Parameter</h1>
                        {"status":"SUCCESS","payerAmount":"100","payerName":"Test","txnID":"1234565465","BankRRN":"123564654564","payerVA":"0000000000@ybl","TxnInitDate":"20220608131419","TxnCompletionDate":"20220608131422"}
                        
                      </div>

                      

                      


                    </div>
                    
                    
                    <div class="tab-pane fade show" id="checkStatus" role="tabpanel" aria-labelledby="checkStatus-tab">
                      
                      <div class="col-sm-12">
                        <br />
                        <h1>Request API URL</h1>
                        <p style="font-size: 18px; font-weight: bold;">{site_url}portal/api/payinCheckStatus</p>
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

