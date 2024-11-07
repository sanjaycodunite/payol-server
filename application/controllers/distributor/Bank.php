<?php
class Bank extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->User->checkDistributorPermission();
        $this->lang->load('master/package', 'english');
    }

    public function verify()
    {
        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);

        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);

        $siteUrl = site_url();
        $data = [
            'site_url' => $siteUrl,
            'loggedUser' => $loggedUser,
            'meta_title' => lang('SITE_NAME'),
            'meta_keywords' => lang('SITE_NAME'),
            'meta_description' => lang('SITE_NAME'),
            'content_block' => 'bank/verify',
            'manager_description' => lang('SITE_NAME'),
            'system_message' => $this->Az->getSystemMessageError(),
            'system_info' => $this->Az->getSystemMessageInfo(),
            'system_warning' => $this->Az->getSystemMessageWarning(),
        ];

        $this->parser->parse('distributor/layout/column-1', $data);
    }

    // save member

     public function verifyAuth()
    {
        $response = [];

        $loggedUser = $this->User->getAdminLoggedUser(RETAILER_SESSION_ID);
        $loggedAccountID = $loggedUser['id'];

        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        $post = $this->input->post();

        // Load form validation library and rules
        $this->load->library('form_validation');
        $this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required|trim|xss_clean|regex_match[/^[a-zA-Z]+( [a-zA-Z]+)*$/]');
        $this->form_validation->set_rules('bankID', 'Bank', 'required|trim|xss_clean');
        $this->form_validation->set_rules('ben_account_number', 'Account Number', 'required|trim|xss_clean|numeric');
        $this->form_validation->set_rules('ifsc', 'IFSC', 'required|trim|xss_clean');
        $this->form_validation->set_rules('mobile_no', 'Mobile Number', 'required|trim|xss_clean|numeric|min_length[10]|max_length[10]');

        $this->form_validation->set_message('regex_match', 'The %s field must contain only alphabetic characters and single spaces between names.');

        if ($this->form_validation->run() === false) {
            $response = [
                'error' => true,
                'errors' => [
                    'account_holder_name' => form_error('account_holder_name'),
                    'bankID' => form_error('bankID'),
                    'ben_account_number' => form_error('ben_account_number'),
                    'ifsc' => form_error('ifsc'),
                    'mobile_no' => form_error('mobile_no'),
                ],
            ];
            echo json_encode($response);
            return;
        }

        // Wallet and package logic
        $chk_wallet_balance = $this->db->get_where('users', ['account_id' => $account_id, 'id' => $loggedUser['id']])->row_array();
        $wallet_balance = isset($chk_wallet_balance['wallet_balance']) ? $chk_wallet_balance['wallet_balance'] : 0;

        $get_verification_charge = $this->db->get_where('tbl_dmr_account_verify_charge', ['account_id' => $account_id, 'package_id' => $chk_wallet_balance['package_id']])->row_array();
        $verification_charge = isset($get_verification_charge['surcharge']) ? $get_verification_charge['surcharge'] : 0;

        $admin_id = $this->User->get_admin_id($account_id);
        $admin_wallet_balance = $this->User->getMemberWalletBalanceSP($admin_id);

        $account_package_id = $this->User->get_account_package_id($account_id);
        $admin_charge = $this->db->get_where('tbl_dmr_account_verify_charge', ['package_id' => $account_package_id])->row_array();
        $admin_verification_charge = isset($admin_charge['commision']) ? $admin_charge['commision'] : 0;

        if ($admin_wallet_balance < $admin_verification_charge) {
            $response = [
                'error' => true,
                'dataval' => 'Sorry! Insufficient balance in admin wallet.',
            ];
            echo json_encode($response);
            return;
        }

        if ($wallet_balance < $verification_charge) {
            $response = [
                'error' => true,
                'dataval' => 'Sorry! Insufficient balance in your wallet.',
            ];
            echo json_encode($response);
            return;
        }

        // Proceed with bank verification
        $transid = rand(111111, 999999) . time();
        $name = isset($post['account_holder_name']) ? $post['account_holder_name'] : '';
        $account_number = isset($post['ben_account_number']) ? $post['ben_account_number'] : '';
        $ifsc = isset($post['ifsc']) ? $post['ifsc'] : '';

        $bank_verification_url = BANK_VERIFICATION_URL;
        $request = [
            'payee' => [
                'accountNumber' => $account_number,
                'bankIfsc' => $ifsc,
            ],
            'externalRef' => $transid,
            'consent' => 'Y',
            'isCached' => 0,
            'latitude' => '22.9734229',
            'longitude' => '78.6568942',
        ];

        $header = ['X-Ipay-Auth-Code: 1', 'X-Ipay-Client-Id: ' . $accountData['instant_client_id'], 'X-Ipay-Client-Secret: ' . $accountData['instant_client_secret'], 'X-Ipay-Endpoint-Ip: 103.129.97.70', 'content-type: application/json'];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $bank_verification_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));

        $output = curl_exec($curl);
        curl_close($curl);

        $responseData = json_decode($output, true);
        $response_data = $responseData['data']['payee'];
        $statusMessages = [
            "TXN" => "Transaction Successful",
            "TUP" => "Transaction Under Process",
            "IRA" => "Invalid Refill Amount",
            "RBT" => "Refill Barred Temporarily",
            "IAN" => "Invalid Account Number",
            "IAB" => "Insufficient Wallet Balance",
            "DTX" => "Duplicate Transaction",
            "ISE" => "System Error",
            "IAT" => "Invalid Access Token",
            "SPD" => "Service Provider Downtime",
            "SPE" => "Service Provider Error",
            "ITI" => "Invalid Transaction ID",
            "DTB" => "Denomination Temporarily Barred",
            "TSU" => "Transaction Status Unavailable",
            "ISP" => "Invalid Service Provider",
            "RPI" => "Request Parameters are Invalid or Incomplete",
            "AAB" => "Account Blocked, Contact Helpdesk",
            "ANF" => "Account not found",
            "UED" => "Unknown Error Description, Contact Helpdesk",
            "IEC" => "Invalid or Unknown Error Code",
            "IRT" => "Invalid Response Type",
            "IPE" => "Internal Processing Error",
            "IAC" => "Invalid Dealer Credentials",
            "UAD" => "User Access Denied",
            "TRP" => "Transaction Refund Processed",
            "TDE" => "Transaction Dispute Error, Contact Helpdesk",
            "DLS" => "Dispute Logged Successfully",
            "DID" => "Duplicate Agent Transaction ID",
            "OUI" => "Outlet Unauthorized or Inactive",
            "ODI" => "Outlet Data Incorrect",
            "RNF" => "Remitter Not Found",
            "RAR" => "Remitter Already Registered",
            "UAR" => "User Already Registered",
            "IVC" => "Invalid Verification Code or OTP",
            "IUA" => "Invalid User Account - Outlet",
            "SNA" => "Service not available",
            "ERR" => "Provider Failure",
            "FAB" => "Failure at Bank end",
            "UFC" => "Fare has been changed",
            "OTP" => "OTP Successfully sent",
            "EOP" => "OTP Expired",
            "OLR" => "OTP limit reached",
            "ONV" => "OTP not valid",
            "RAB" => "Remitter Blocked",
            "VCI" => "Version Compatibility Issue",
            "OUE" => "Unknown Method",
            "KYC" => "KYC is mandatory to avail this service",
            "USM" => "Under Scheduled Maintenance",
            "CNL" => "Currently Not Live",
        ];

        if (isset($responseData['error']) && $responseData['error']) {
            $this->User->generateLog('Verify & Add Beneficiary API Error: ' . $responseData['error']['message']);
            $response = [
                'error' => true,
                'dataval' => $responseData['error']['message'],
            ];
            echo json_encode($response);
            return;
        } else {
            if (isset($responseData) && $responseData['statuscode'] == "TXN" && $responseData['status'] == "Transaction Successful") {
                $wallet_balance = $this->User->getMemberWalletBalanceSP($loggedUser['id']);
                $after_balance = $wallet_balance - $verification_charge;

                $history = [
                    'account_id' => $account_id,
                    'member_id' => $loggedUser['id'],
                    'api_url' => $bank_verification_url,
                    'post_data' => json_encode($post),
                    'api_response' => json_encode($responseData),
                    'txn_id' => $transid,
                    'before_balance' => $wallet_balance,
                    'amount' => $verification_charge,
                    'after_balance' => $after_balance,
                    'status' => 'Success',
                    'created' => date('Y-m-d H:i:s'),
                ];

                $this->db->insert('bank_verification', $history);

                $benDbTableName = "";
                if (isset($post['dbTableName'])) {
                    $benDbTableName = $post['dbTableName'];
                } else {
                    $benDbTableName = "settlement_user_benificary";
                }

                $bene_data = [
                    'account_id' => $account_id,
                    'type' => 2,
                    'user_id' => $loggedAccountID,
                    'account_holder_name' => $response_data['name'] ?? $name,
                    'bankID' => $post['bankID'] ?? "",
                    'account_no' => $response_data['account'],
                    'ifsc' => $response_data['ifsc'],
                    'encode_ban_id' => do_hash($response_data['account']),
                    'status' => 1,
                    'created' => date('Y-m-d H:i:s'),
                    'mobile' => $response_data['phone'] ?? $post['mobile_no'],
                    'txn_id' => $transid,
                    'ben_id' => $responseData['orderid'],
                    'is_active' => 1,
                    'dbTableName' => $benDbTableName,
                ];

                $wallet_data = [
                    'account_id' => $account_id,
                    'member_id' => $loggedUser['id'],
                    'before_balance' => $wallet_balance,
                    'amount' => $verification_charge,
                    'after_balance' => $after_balance,
                    'status' => 1,
                    'type' => 2,
                    'created' => date('Y-m-d H:i:s'),
                    'description' => 'Bank Account Verification #' . $transid . ' Amount Deducted.',
                ];

                $this->db->insert('member_wallet', $wallet_data);

                $str =
                    '<table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>Account No.</th>
                                <td>' .
                                        htmlspecialchars($response_data['account']) .
                                        '</td>
                            </tr>
                            <tr>
                                <th>Account Holder Name</th>
                                <td>' .
                                        htmlspecialchars($response_data['name']) .
                                        '</td>
                            </tr>
                             <tr>
                                <th>IFSC</th>
                                <td>' .
                                        htmlspecialchars($response_data['ifsc']) .
                                        '</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td><font color="green">A/C Verification Completed Successfully.</font></td>
                            </tr>
                        </tbody>
                    </table>';

                // Adding the form below the table
                $str .=
                    '<form name="verify_beneficary_addon_form" id="verify_beneficary_addon_form">
                        <input type="hidden" class="form-control" id="accountHolderName" name="account_holder_name" value="' .
                                        htmlspecialchars($bene_data['account_holder_name']) .
                                        '" >

                        <input type="hidden" class="form-control" id="ben_account_number" name="ben_account_number" value="' .
                                        htmlspecialchars($bene_data['account_no']) .
                                        '" >
                        <input type="hidden" class="form-control" id="bankID" name="bankID" value="' .
                                        htmlspecialchars($bene_data['bankID']) .
                                        '" >
                        <input type="hidden" class="form-control" id="benId" name="ben_id" value="' .
                                        htmlspecialchars($bene_data['ben_id']) .
                                        '" placeholder="Optional">
                        <input type="hidden" class="form-control" id="ifsc" name="ifsc" value="' .
                                        htmlspecialchars($bene_data['ifsc']) .
                                        '" >
                        <input type="hidden" class="form-control" id="mobile_no" name="mobile_no" value="' .
                                        htmlspecialchars($bene_data['mobile']) .
                                        '" >
                        <input type="hidden" class="form-control" id="txnId" name="txn_id" value="' .
                                        htmlspecialchars($bene_data['txn_id']) .
                                        '" >

                        <input type="hidden" class="form-control" id="type" name="type" value="' .
                                        htmlspecialchars($bene_data['type']) .
                                        '" >
                        <input type="hidden" name="isActive" id="isActive" value="' .
                                        htmlspecialchars($bene_data['is_active']) .
                                        '" >
                        <input type="hidden" name="status" id="status" value="' .
                                        htmlspecialchars($bene_data['status']) .
                                        '" >
                        <input type="hidden" name="dbTableName" id="dbTableName" value="' .
                                        htmlspecialchars($bene_data['dbTableName']) .
                                        '" >
                    </form>';

                $response = [
                    'error' => false,
                    'dataval' => $str,
                    'status' => $response_data['status'],
                ];
                echo json_encode($response);
                return;
            } else {
                $history = [
                    'account_id' => $account_id,
                    'member_id' => $loggedUser['id'],
                    'api_url' => $bank_verification_url,
                    'post_data' => json_encode($post),
                    'api_response' => json_encode($responseData),
                    'txn_id' => $transid,
                    'before_balance' => $wallet_balance,
                    'amount' => 0,
                    'after_balance' => $wallet_balance,
                    'status' => 'Failed',
                    'created' => date('Y-m-d H:i:s'),
                ];

                $this->db->insert('bank_verification', $history);

                $response = [
                    'error' => true,
                    'apiresponse' => 'yes',
                    'dataval' => [
                        "actcode" => $responseData['actcode'],
                        "data" => null,
                        "environment" => $responseData['environment'],
                        "ipay_uuid" => $responseData['ipay_uuid'],
                        "orderid" => $responseData['orderid'],
                        "status" => $responseData['status'],
                        "statuscode" => $responseData['statuscode'],
                        "statuscodemessage" => $statusMessages[$responseData['statuscode']],
                        "timestamp" => $responseData['timestamp'],
                        'bank_verify_url' => $bank_verification_url,
                    ],
                ];
                echo json_encode($response);
                return;
            }
        }
    }

    public function upiVerifyAuth()
    {
        $response = [];

        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);

        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        //check for foem validation
        $post = $this->input->post();

        $this->load->library('form_validation');

        $this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required|xss_clean');
        $this->form_validation->set_rules('account_number', 'Account No', 'required|xss_clean');
        //$this->form_validation->set_rules('ifsc', 'IFSC', 'required|xss_clean');

        if ($this->form_validation->run() == false) {
            $response = [
                'status' => 0,
                'msg' => validation_errors(),
            ];
        } else {
            $chk_wallet_balance = $this->db->get_where('users', ['account_id' => $account_id, 'id' => $loggedUser['id']])->row_array();

            $wallet_balance = isset($chk_wallet_balance['wallet_balance']) ? $chk_wallet_balance['wallet_balance'] : 0;

            $get_verification_charge = $this->db->get_where('tbl_dmr_account_verify_charge', ['account_id' => $account_id, 'package_id' => $chk_wallet_balance['package_id']])->row_array();

            $verification_charge = isset($get_verification_charge['surcharge']) ? $get_verification_charge['surcharge'] : 0;

            $admin_id = $this->User->get_admin_id($account_id);

            $admin_wallet_balance = $this->User->getMemberWalletBalanceSP($admin_id);

            //get admin verification charge

            $account_package_id = $this->User->get_account_package_id($account_id);

            $admin_charge = $this->db->get_where('tbl_dmr_account_verify_charge', ['package_id' => $account_package_id])->row_array();

            $admin_verification_charge = isset($admin_charge['commision']) ? $admin_charge['commision'] : 0;

            if ($admin_wallet_balance < $admin_verification_charge) {
                $response = [
                    'status' => 0,
                    'msg' => 'Sorry!! insufficient balance in your admin wallet.',
                ];
            } else {
                $wallet_balance = $this->User->getMemberWalletBalanceSP($loggedUser['id']);
                if ($wallet_balance < $verification_charge) {
                    $response = [
                        'status' => 0,
                        'msg' => 'Sorry!! you have insufficient balance in your wallet.',
                    ];
                } else {
                    $transid = rand(111111, 999999) . time();
                    $name = isset($post['account_holder_name']) ? $post['account_holder_name'] : '';
                    $account_number = isset($post['account_number']) ? $post['account_number'] : '';

                    $response = [];

                    $enckey = '7153f272dbdc71b459c6b49551988767';

                    $header = ['Content-type: application/json', 'X-IBM-Client-Secret: U0fW7iH6eH5lH1qS5wX0cQ2tC4hB8oH3lE8mV1pR7wM2dL1hF6', 'X-IBM-Client-ID: 0d38b91a-2b06-491c-8ef1-6da43d24dc89'];

                    $requestStr =
                        'YES0000011547194|YESD04F53227F184F6C88741779F8C|' .
                        $account_number .
                        '|T|com.msg.app|0.0 ,0.0 |Mumbai|172.16.50.65|MOB|' .
                        $transid .
                        '|Android7.0|351898082074677|89914902900059967808|4e9389eadeea5b7c|02:00:00:00:00:00|02:00:00:00:00:00|||||||||NA|NA';
                    $encryptData = $this->User->yesEncryptValue($requestStr, $enckey);

                    $data = '{"requestMsg":"' . $encryptData . '","pgMerchantId":"YES0000011547194"}';

                    $bank_verification_url = YES_BANK_CHECK_VPA_API_URL;

                    $cert_path = getcwd() . '/publiccrt.crt';

                    $key_path = getcwd() . '/privatekey.key';

                    $cert_password = '';

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL, $bank_verification_url);

                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    curl_setopt($ch, CURLOPT_POST, true);

                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

                    curl_setopt($ch, CURLOPT_SSLCERT, $cert_path);

                    curl_setopt($ch, CURLOPT_SSLKEY, $key_path);

                    curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $cert_password);

                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                    $response = curl_exec($ch);

                    $responseData = $this->User->yesDecryptValue($response, $enckey);

                    $response_data = explode('|', $responseData);

                    if (isset($response_data) && $response_data[4] == "Virtual Address exist for Transaction" && $response_data[3] == "VE") {
                        $wallet_balance = $this->User->getMemberWalletBalanceSP($loggedUser['id']);
                        $after_balance = $wallet_balance - $verification_charge;

                        $history = [
                            'account_id' => $account_id,
                            'member_id' => $loggedUser['id'],
                            'api_url' => $bank_verification_url,
                            'post_data' => json_encode($post),
                            'api_response' => json_encode($responseData),
                            'txn_id' => $transid,
                            'before_balance' => $wallet_balance,
                            'amount' => $verification_charge,
                            'after_balance' => $after_balance,
                            'status' => 'Success',
                            'created' => date('Y-m-d H:i:s'),
                        ];

                        $this->db->insert('bank_verification', $history);

                        //wallet deduct

                        $wallet_data = [
                            'account_id' => $account_id,
                            'member_id' => $loggedUser['id'],
                            'before_balance' => $wallet_balance,
                            'amount' => $verification_charge,
                            'after_balance' => $after_balance,
                            'status' => 1,
                            'type' => 2,
                            'created' => date('Y-m-d H:i:s'),
                            'description' => 'UPI Account Verification #' . $transid . ' Amount Deducted.',
                        ];

                        $this->db->insert('member_wallet', $wallet_data);

                        $str =
                            '<table class="table table-bordered table-striped">
				          <tbody>
				            <tr>
				              <th>Account No.</th>
				              <td>' .
                            $response_data[1] .
                            '</td>
				            </tr>

				            <tr>
				              <th>Account Holder Name</th>
				              <td>' .
                            $response_data[2] .
                            '</td>
				            </tr>

				            <tr>
				              <th>Status</th>
				              <td><font color="green">' .
                            $response_data[4] .
                            '</font></td>
				            </tr>

				          </tbody>
				        </table>';

                        $response = [
                            'status' => 1,
                            'msg' => $str,
                            'upi_account_holder_name' => $response_data[2],
                        ];
                    } else {
                        $history = [
                            'account_id' => $account_id,
                            'member_id' => $loggedUser['id'],
                            'api_url' => $bank_verification_url,
                            'post_data' => json_encode($post),
                            'api_response' => json_encode($responseData),
                            'txn_id' => $transid,
                            'before_balance' => $wallet_balance,
                            'amount' => 0,
                            'after_balance' => $wallet_balance,
                            'status' => 'Failed',
                            'created' => date('Y-m-d H:i:s'),
                        ];

                        $this->db->insert('bank_verification', $history);

                        $response = [
                            'status' => 0,
                            'msg' => $response_data[4],
                        ];
                    }
                }
            }
        }

        echo json_encode($response);
    }

    public function openVerifyAuth()
    {
        $response = [];

        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);

        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        //check for foem validation
        $post = $this->input->post();

        $this->load->library('form_validation');
        $this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required|trim|xss_clean');
        $this->form_validation->set_rules('bankID', 'Bank', 'required|xss_clean');
        $this->form_validation->set_rules('account_number', 'Account Number', 'required|xss_clean|numeric');
        $this->form_validation->set_rules('ifsc', 'IFSC', 'required|trim|xss_clean');
        $this->form_validation->set_rules('mobile_no', 'Mobile Number', 'required|xss_clean|numeric|min_length[10]|max_length[10]');

        if ($this->form_validation->run() == false) {
            $response = [
                'status' => 0,
                'msg' => validation_errors(),
            ];
        } else {
            $chk_wallet_balance = $this->db->get_where('users', ['account_id' => $account_id, 'id' => $loggedUser['id']])->row_array();

            $wallet_balance = isset($chk_wallet_balance['wallet_balance']) ? $chk_wallet_balance['wallet_balance'] : 0;

            $get_verification_charge = $this->db->get_where('tbl_dmr_account_verify_charge', ['account_id' => $account_id, 'package_id' => $chk_wallet_balance['package_id']])->row_array();

            $verification_charge = isset($get_verification_charge['surcharge']) ? $get_verification_charge['surcharge'] : 0;

            $admin_id = $this->User->get_admin_id($account_id);

            $admin_wallet_balance = $this->User->getMemberWalletBalanceSP($admin_id);

            //get admin verification charge

            $account_package_id = $this->User->get_account_package_id($account_id);

            $admin_charge = $this->db->get_where('tbl_dmr_account_verify_charge', ['package_id' => $account_package_id])->row_array();

            $admin_verification_charge = isset($admin_charge['commision']) ? $admin_charge['commision'] : 0;

            if ($admin_wallet_balance < $admin_verification_charge) {
                $response = [
                    'status' => 0,
                    'msg' => 'Sorry!! insufficient balance in your admin wallet.',
                ];
            } else {
                $wallet_balance = $this->User->getMemberWalletBalanceSP($loggedUser['id']);
                if ($wallet_balance < $verification_charge) {
                    $response = [
                        'status' => 0,
                        'msg' => 'Sorry!! you have insufficient balance in your wallet.',
                    ];
                } else {
                    $transid = rand(111111, 999999) . time();
                    $name = isset($post['account_holder_name']) ? $post['account_holder_name'] : '';
                    $account_number = isset($post['account_number']) ? $post['account_number'] : '';
                    $ifsc = isset($post['ifsc']) ? $post['ifsc'] : '';
                    $bank_verification_url = OPEN_MONEY_ACCOUNT_VERIFY_URL;

                    $response = [];
                    // save system log
                    $log_msg = '[' . date('d-m-Y H:i:s') . ' - RT(' . $loggedUser['user_code'] . ') - Open Money Account Verify API URL - ' . $bank_verification_url . ']' . PHP_EOL;
                    $this->User->generateLog($log_msg);

                    $request = [
                        'force_penny_drop' => false,
                        'force_penny_drop_amount' => 1,
                        'bank_account_number' => $account_number,
                        'bank_ifsc_code' => $ifsc,
                        'merchant_reference_id' => $transid,
                    ];

                    $header = ['Authorization: Bearer ak_live_bq0SO69ZdaATI2dabwpeuF7GPfWw09XAIsOP:sk_live_L0TiS0BSbJVeMR6oiEYJ16zc49bfQErxuMai', 'content-type: application/json'];

                    $curl = curl_init();
                    // URL
                    curl_setopt($curl, CURLOPT_URL, $bank_verification_url);

                    // Return Transfer
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                    // SSL Verify Peer
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

                    // SSL Verify Host
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

                    // Timeout
                    curl_setopt($curl, CURLOPT_TIMEOUT, 30);

                    // HTTP Version
                    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

                    // Request Method
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

                    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

                    // Request Body
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));

                    // Execute
                    $output = curl_exec($curl);

                    // Close
                    curl_close($curl);

                    $responseData = json_decode($output, true);

                    if (isset($responseData) && $responseData['status'] == "success") {
                        $wallet_balance = $this->User->getMemberWalletBalanceSP($loggedUser['id']);
                        $after_balance = $wallet_balance - $verification_charge;

                        $history = [
                            'account_id' => $account_id,
                            'member_id' => $loggedUser['id'],
                            'api_url' => $bank_verification_url,
                            'post_data' => json_encode($post),
                            'api_response' => json_encode($responseData),
                            'txn_id' => $transid,
                            'before_balance' => $wallet_balance,
                            'amount' => $verification_charge,
                            'after_balance' => $after_balance,
                            'status' => 'Success',
                            'created' => date('Y-m-d H:i:s'),
                        ];

                        $this->db->insert('bank_verification', $history);

                        //New Benefeciray Details save in database :
                        $bene_data = [
                           'account_id' => $account_id,
                           'type' => 3,
                           'email' => $responseData['email'],
                           'mobile' => $responseData['phone'] ?? $post['mobile_no'],
                           'txn_id' => $transaction_id,
                           'user_id' => $loggedAccountID,
                           'account_holder_name' => $responseData['name_of_account_holder'],
                           'account_no' => $responseData['bank_account_number'],
                           'ifsc' => $responseData['bank_ifsc_code'],
                           'ben_id' => $responseData['id'],
                           'is_active' => 1,
                           'created' => date('Y-m-d H:i:s'),

                       ];
                       $this->db->insert('settlement_user_benificary', $bene_data);

                        //wallet deduct

                        $wallet_data = [
                            'account_id' => $account_id,
                            'member_id' => $loggedUser['id'],
                            'before_balance' => $wallet_balance,
                            'amount' => $verification_charge,
                            'after_balance' => $after_balance,
                            'status' => 1,
                            'type' => 2,
                            'created' => date('Y-m-d H:i:s'),
                            'description' => 'Bank Account Verification #' . $transid . ' Amount Deducted.',
                        ];

                        $this->db->insert('member_wallet', $wallet_data);

                        $str =
                            '<table class="table table-bordered table-striped">
				          <tbody>
				            <tr>
				              <th>Account No.</th>
				              <td>' .
                            $responseData['bank_account_number'] .
                            '</td>
				            </tr>

				            <tr>
				              <th>Account Holder Name</th>
				              <td>' .
                            $responseData['name_as_per_bank'] .
                            '</td>
				            </tr>

				            <tr>
				              <th>Status</th>
				              <td><font color="green">' .
                            $responseData['status'] .
                            '</font></td>
				            </tr>

				          </tbody>
				        </table>';

                        $response = [
                            'status' => 1,
                            'msg' => $str,
                            'account_number' => $responseData['bank_account_number'],
                            'account_holder_name' => $responseData['name_as_per_bank'],
                            'ben_id' => $responseData['id'],
                        ];
                    } else {
                        $history = [
                            'account_id' => $account_id,
                            'member_id' => $loggedUser['id'],
                            'api_url' => $bank_verification_url,
                            'post_data' => json_encode($post),
                            'api_response' => json_encode($responseData),
                            'txn_id' => $transid,
                            'before_balance' => $wallet_balance,
                            'amount' => 0,
                            'after_balance' => $wallet_balance,
                            'status' => 'Failed',
                            'created' => date('Y-m-d H:i:s'),
                        ];

                        $this->db->insert('bank_verification', $history);

                        $response = [
                            'status' => 0,
                            'msg' => $responseData['error']['message'],
                        ];
                    }
                }
            }
        }

        echo json_encode($response);
    }

    //open upi verify api

    public function openUpiVerifyAuth()
    {
        $response = [];

        $loggedUser = $this->User->getAdminLoggedUser(DISTRIBUTOR_SESSION_ID);

        $account_id = $this->User->get_domain_account();
        $accountData = $this->User->get_account_data($account_id);
        //check for foem validation
        $post = $this->input->post();

        $this->load->library('form_validation');

        $this->form_validation->set_rules('account_holder_name', 'Account Holder Name', 'required|xss_clean');
        $this->form_validation->set_rules('account_number', 'Account No', 'required|xss_clean');

        if ($this->form_validation->run() == false) {
            $response = [
                'status' => 0,
                'msg' => validation_errors(),
            ];
        } else {
            $chk_wallet_balance = $this->db->get_where('users', ['account_id' => $account_id, 'id' => $loggedUser['id']])->row_array();

            $wallet_balance = isset($chk_wallet_balance['wallet_balance']) ? $chk_wallet_balance['wallet_balance'] : 0;

            $get_verification_charge = $this->db->get_where('tbl_dmr_account_verify_charge', ['account_id' => $account_id, 'package_id' => $chk_wallet_balance['package_id']])->row_array();

            $verification_charge = isset($get_verification_charge['surcharge']) ? $get_verification_charge['surcharge'] : 0;

            $admin_id = $this->User->get_admin_id($account_id);

            $admin_wallet_balance = $this->User->getMemberWalletBalanceSP($admin_id);

            //get admin verification charge

            $account_package_id = $this->User->get_account_package_id($account_id);

            $admin_charge = $this->db->get_where('tbl_dmr_account_verify_charge', ['package_id' => $account_package_id])->row_array();

            $admin_verification_charge = isset($admin_charge['commision']) ? $admin_charge['commision'] : 0;

            if ($admin_wallet_balance < $admin_verification_charge) {
                $response = [
                    'status' => 0,
                    'msg' => 'Sorry!! insufficient balance in your admin wallet.',
                ];
            } else {
                $wallet_balance = $this->User->getMemberWalletBalanceSP($loggedUser['id']);
                if ($wallet_balance < $verification_charge) {
                    $response = [
                        'status' => 0,
                        'msg' => 'Sorry!! you have insufficient balance in your wallet.',
                    ];
                } else {
                    $transid = rand(111111, 999999) . time();
                    $name = isset($post['account_holder_name']) ? $post['account_holder_name'] : '';
                    $account_number = isset($post['account_number']) ? $post['account_number'] : '';
                    $bank_verification_url = OPEN_MONEY_VPA_VERIFY_URL;

                    $response = [];
                    // save system log
                    $log_msg = '[' . date('d-m-Y H:i:s') . ' - RT(' . $loggedUser['user_code'] . ') - Open Money Account Verify API URL - ' . $bank_verification_url . ']' . PHP_EOL;
                    $this->User->generateLog($log_msg);

                    $request = [
                        'vpa' => $account_number,
                        'merchant_reference_id' => $transid,
                    ];

                    $header = ['Authorization: Bearer ak_live_bq0SO69ZdaATI2dabwpeuF7GPfWw09XAIsOP:sk_live_L0TiS0BSbJVeMR6oiEYJ16zc49bfQErxuMai', 'content-type: application/json'];

                    $curl = curl_init();
                    // URL
                    curl_setopt($curl, CURLOPT_URL, $bank_verification_url);

                    // Return Transfer
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                    // SSL Verify Peer
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);

                    // SSL Verify Host
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

                    // Timeout
                    curl_setopt($curl, CURLOPT_TIMEOUT, 30);

                    // HTTP Version
                    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

                    // Request Method
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

                    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

                    // Request Body
                    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($request));

                    // Execute
                    $output = curl_exec($curl);

                    // Close
                    curl_close($curl);

                    $responseData = json_decode($output, true);

                    if (isset($responseData) && $responseData['status'] == "success") {
                        $wallet_balance = $this->User->getMemberWalletBalanceSP($loggedUser['id']);
                        $after_balance = $wallet_balance - $verification_charge;

                        $history = [
                            'account_id' => $account_id,
                            'member_id' => $loggedUser['id'],
                            'api_url' => $bank_verification_url,
                            'post_data' => json_encode($post),
                            'api_response' => json_encode($responseData),
                            'txn_id' => $transid,
                            'before_balance' => $wallet_balance,
                            'amount' => $verification_charge,
                            'after_balance' => $after_balance,
                            'status' => 'Success',
                            'created' => date('Y-m-d H:i:s'),
                        ];

                        $this->db->insert('bank_verification', $history);

                        //wallet deduct

                        $wallet_data = [
                            'account_id' => $account_id,
                            'member_id' => $loggedUser['id'],
                            'before_balance' => $wallet_balance,
                            'amount' => $verification_charge,
                            'after_balance' => $after_balance,
                            'status' => 1,
                            'type' => 2,
                            'created' => date('Y-m-d H:i:s'),
                            'description' => 'Bank Account Verification #' . $transid . ' Amount Deducted.',
                        ];

                        $this->db->insert('member_wallet', $wallet_data);

                        $str =
                            '<table class="table table-bordered table-striped">
				          <tbody>
				            <tr>
				              <th>Account No.</th>
				              <td>' .
                            $responseData['vpa'] .
                            '</td>
				            </tr>

				            <tr>
				              <th>Account Holder Name</th>
				              <td>' .
                            $responseData['name_as_per_bank'] .
                            '</td>
				            </tr>

				            <tr>
				              <th>Status</th>
				              <td><font color="green">' .
                            $responseData['status'] .
                            '</font></td>
				            </tr>

				          </tbody>
				        </table>';

                        $response = [
                            'status' => 1,
                            'msg' => $str,
                            'account_number' => $responseData['vpa'],
                            'upi_account_holder_name' => $responseData['name_as_per_bank'],
                            'ben_id' => $responseData['id'],
                        ];
                    } else {
                        $history = [
                            'account_id' => $account_id,
                            'member_id' => $loggedUser['id'],
                            'api_url' => $bank_verification_url,
                            'post_data' => json_encode($post),
                            'api_response' => json_encode($responseData),
                            'txn_id' => $transid,
                            'before_balance' => $wallet_balance,
                            'amount' => 0,
                            'after_balance' => $wallet_balance,
                            'status' => 'Failed',
                            'created' => date('Y-m-d H:i:s'),
                        ];

                        $this->db->insert('bank_verification', $history);

                        $response = [
                            'status' => 0,
                            'msg' => $responseData['error']['message'],
                        ];
                    }
                }
            }
        }

        echo json_encode($response);
    }
}