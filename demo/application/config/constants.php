<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

define('API_LOGIN_ID', 'API132333');
define('API_PASSWORD', '131313');
define('API_PIN', '1313');
define('API_DEDUCT_AMOUNT', '');
define('IS_CAPPING', 0);


define('SUPERADMIN_SESSION_ID', 'payolsuperasdf54asdf84sdf5');
define('ADMIN_SESSION_ID', 'payoladmin878sd4f8e4f5e4f5');
define('MASTER_DIST_SESSION_ID', 'payolmasterdist878as4df54sdf8sd4f');
define('DISTRIBUTOR_SESSION_ID', 'payoldistributor35s6df59efd53f565');
define('RETAILER_SESSION_ID', 'payolretailer7wer54f8df484fdf8');
define('API_MEMBER_SESSION_ID', 'payolapisdf54sdf84sdf84ds5f');
define('USER_SESSION_ID', 'payoluser78sad4f5sd8f45s4df84');
define('SUPERADMIN_EMPLOYE_SESSION_ID', 'payolsuperemploye78fgsad4fgff5sd8f45s4df84');
define('ADMIN_EMPLOYE_SESSION_ID', 'payolemploye78fgsad4fgfdfsdff5sd8f45s4df84');
define('FILE_UPLOAD_SERVER_PATH', 'E:/xampp/htdocs/payol/media/ticket/');
define('PROFILE_PHOTO_SERVER_PATH', 'E:/xampp/htdocs/payol/media/profile/');
define('AEPS_KYC_PHOTO_SERVER_PATH', 'E:/xampp/htdocs/payol/media/aeps_kyc_doc/');


define('ADMIN_DISPLAY_ID', 'TPA');
define('MASTER_DIST_DISPLAY_ID', 'TPMD');
define('DISTRIBUTOR_DISPLAY_ID', 'TPD');
define('RETAILER_DISPLAY_ID', 'TP');
define('API_DISPLAY_ID', 'TPAPI');
define('USER_DISPLAY_ID', 'TPU');
define('WALLET_DISPLAY_ID', 'TPW');
define('PACKAGE_DISPLAY_ID', 'TPP');
define('EMPLOYE_DISPLAY_ID', 'TPE');
define('ACCOUNT_LOG_PATH', 'F:/xampp/htdocs/payol/application/sitelogs/');
define('SUPERADMIN_ACCOUNT_ID', '10000');
define('BBPS_API_ID', '10000');
define('SUPERADMIN_ACCESS_ACCOUNT', 2);
define('DMT_IMPORT_FILE_PATH', 'E:/xampp/htdocs/payol/');


// UPI GATEWAY
define('UPI_KEY', '');
define('UPI_SALT', '');


define('SMS_API_URL', '');
define('SMS_AUTH_KEY', '');
define('SMS_SENDER_ID', '');
define('SMS_OTP_SEND_API_URL', 'https://api.msg91.com/api/v5/otp?authkey={AUTHKEY}&template_id={TEMPID}&otp_length=6&mobile=+91{MOBILE}');
define('SMS_OTP_RESEND_API_URL', 'https://api.msg91.com/api/v5/otp/retry');
define('SMS_OTP_AUTH_API_URL', 'https://api.msg91.com/api/v5/otp/verify');
define('SMS_REGISTER_MSG_API_URL', 'https://control.msg91.com/api/v5/flow/
');

//recharge api
define('RECHARGE_API_URL', 'http://paymyrecharge.in/api/recharge.aspx?');
define('RECHARGE_MEMBERID', '');
define('RECHARGE_API_PIN', '');
define('RECHARGE_API_PWD', '');

//BBPS Live api
define('BBPS_SERVICE_BILL_FETCH_URL', 'https://paymyrecharge.in/api/V5/Servicesapi.asmx/ServiceBillFetchAuth');
define('BBPS_SERVICE_BILL_PAY_URL', 'https://paymyrecharge.in/api/V5/Servicesapi.asmx/ServiceBillPay');
define('BBPS_ELECTRICITY_BILL_FETCH_URL', 'https://paymyrecharge.in/api/V5/BBPS.asmx/electricityBillFetchAuth');
define('BBPS_ELECTRICITY_BILL_PAY_URL', 'https://paymyrecharge.in/api/V5/BBPS.asmx/electricityBillPay');
define('BBPS_FASTAG_BILL_FETCH_URL', 'https://paymyrecharge.in/api/V5/BBPS.asmx/FastegBillFetchAuth');
define('BBPS_FASTAG_BILL_PAY_URL', 'https://paymyrecharge.in/api/V5/bbps.asmx/FastegBillPay');

// Plan Finder API
define('PLAN_FINDER_API_URL', 'https://paymyrecharge.in/api/V5/apimaster.asmx/RechargePlanfinder');
define('DTH_PLAN_FINDER_API_URL', 'https://paymyrecharge.in/api/V5/apimaster.asmx/DTHPlanfinder');
define('PLAN_FINDER_API_TOKEN', 'ROYpLAP17DxRC3jkj9nK35X/XOdA2sJW');
define('ROFFER_API_URL', 'https://paymyrecharge.in/api/V5/apimaster.asmx/RechargeRoffer');
define('OPERATOR_FINDER_API_URL', 'https://paymyrecharge.in/api/V5/apimaster.asmx/Rechargeoperatorfinder');
define('DTH_BILLER_DETAIL_API_URL', 'https://paymyrecharge.in/api/V5/apimaster.asmx/DTHCustomerDetails');

// AEPS API
define('AEPS_BALANCE_API_URL', 'https://aeps.paymyrecharge.in/api/aeps/aepsgetbalance.aspx?');
define('AEPS_KYC_API_URL', 'https://paymyrecharge.in/api/aeps/aepskeyrequest.asmx/AespkycRequest');
define('AEPS_ONBOARD_API_URL', 'https://paymyrecharge.in/api/aeps/aepskeyrequest.asmx/AespkyconboardRequest');
define('AEPS_EKYC_SEND_OTP_API_URL', 'https://paymyrecharge.in/api/aeps/aepskeyrequest.asmx/AespEkycSendotp');
define('AEPS_EKYC_RESEND_OTP_API_URL', 'https://paymyrecharge.in/api/aeps/aepskeyrequest.asmx/AespEkycReSendotp');
define('AEPS_EKYC_VERIFY_API_URL', 'https://paymyrecharge.in/api/aeps/aepskeyrequest.asmx/AespEkycVerify');
define('AEPS_EKYC_BIOMATRIC_API_URL', 'https://paymyrecharge.in/api/aeps/AepsEkycVerify.aspx?');


//DMT API
define('DMT_FETCH_SENDER_DETAIL_API', 'https://api.billavenue.com/billpay/dmt/dmtServiceReq/xml');
define('DMT_TRANSACTION_API', 'https://api.billavenue.com/billpay/dmt/dmtTransactionReq/xml');


define('ELECTRICITY_RECHARGE_FETCH_API_URL', 'http://paymyrecharge.in/api/bbps/fatchbiller.aspx?');
define('ELECTRICITY_RECHARGE_FETCH_CUSTOMER_API_URL', 'http://paymyrecharge.in/api/bbps/FatchBillDetails.aspx?');
define('ELECTRICITY_RECHARGE_API_URL', 'http://paymyrecharge.in/api/bbps/Paybillnow.aspx?');

// CIB API
define('CIB_TOKEN_API_URL', 'https://api.cogentmind.tech/v1/auth/login');
define('CIB_TXN_API_URL', 'https://apibankingone.icicibank.com/api/Corporate/CIB/v1/Transaction');

//DMR API 
define('DMR_API_URL', 'https://paymyrecharge.in/api/DMR/payout.aspx?');
define('DMR_MEMBERID', '');
define('DMR_API_PIN', '');
define('PMR_SETTLEMENT_API', 'https://paymyrecharge.in/api/V5/Walletsystem.asmx/CoduniteaepsapiSettlement');


// UPI E-collection

define('UPI_REQUEST_API_URL', 'https://api.cogentmind.tech/v1/upicollection-sendrequest');
define('UPI_QR_API_URL', 'https://apibankingone.icicibank.com/api/MerchantAPI/UPI/v0/QR/6260611');
define('UPI_STATIC_QR_API_URL', 'https://api.cogentmind.tech/v1/upicollection-staticqr');
define('UPI_STATIC_QR_API_URL2', 'https://api.cogentmind.tech/v1/upicollection-staticqr2');
define('UPI_STATIC_QR_REPLACE_STR', 'https://cogentmind.tech/api/staticQRAPIWLCollection/');
define('UPI_TXN_STATUS_CHECK', 'https://api.cogentmind.tech/v1/upicollection-txnstatus');
define('UPI_QR_MAP_NAME', 'https://api.cogentmind.tech/v1/upicollection-dynamicvpa');

// UPI Cash E-collection

define('UPI_CASH_REQUEST_API_URL', 'https://api.cogentmind.tech/v1/upicash-sendrequest');
define('UPI_CASH_QR_API_URL', 'https://api.cogentmind.tech/v1/upicash-dynamicqr');
define('UPI_CASH_STATIC_QR_API_URL', 'https://api.cogentmind.tech/v1/upicash-staticqr');
define('UPI_CASH_STATIC_QR_API_URL2', 'https://api.cogentmind.tech/v1/upicash-staticqr2');
define('UPI_CASH_STATIC_QR_REPLACE_STR', 'https://cogentmind.tech/api/staticQRAPIWLCash/');
define('UPI_CASH_TXN_STATUS_CHECK', 'https://api.cogentmind.tech/v1/upicash-txnstatus');
define('UPI_CASH_QR_MAP_NAME', 'https://api.cogentmind.tech/v1/upicash-dynamicvpa');


// CURRENT ACCOUNT OPEN API
define('CURRENT_ACCOUNT_OPEN_API_URL', 'https://cadigital.icicibank.com/caSmartFormSrv/sendBcReq');

// AXIS SAVING ACCOUNT OPEN API
define('AXIS_ACCOUNT_KEY', 'UFMwMDc0OThhZjc1NTgxMzlmM2QwYzJhOTNhMjVmNTg4ZmU1MDQy');
define('AXIS_ACCOUNT_PARTNER_ID', 'PS00749');
define('AXIS_ACCOUNT_OPEN_API_URL', 'https://paysprint.in/service-api/api/v1/service/axisbank-utm/axisutm/generateurl');
define('AXIS_ACCOUNT_AUTH_KEY', 'MWM0MmI5YmYwZTM1N2JlZjhiZWZkYjQ4MjEwYzZmODM=');


// CASH DEPOSITE API
define('CASH_DEPOSITE_SEND_OTP', 'https://fingpayap.tapits.in/fpaepsservice/api/CashDeposit/merchant/php/generate/otp');
define('CASH_DEPOSITE_OTP_AUTH', 'https://fingpayap.tapits.in/fpaepsservice/api/CashDeposit/merchant/php/validate/otp');
define('CASH_DEPOSITE_TXN_API', 'https://fingpayap.tapits.in/fpaepsservice/api/CashDeposit/merchant/php/transaction');

//VIRTUAL ACCOUNT
define('VIRTUAL_ACCOUNT_CODE', 'CM0007COD');
define('VIRTUAL_ACCOUNT_IFSC', 'ICIC0000106');

//PANCARD API
define('PANCARD_KYC_URL', 'https://paymyrecharge.in/api/V5/Utipan.asmx/PsaRegistrationRequest');
define('PANCARD_KYC_STATUS_CHECK_URL', 'https://paymyrecharge.in/api/V5/Utipan.asmx/PsaStatusCheck');
define('PANCARD_PURCHASE_COUPON__URL', 'https://paymyrecharge.in/api/V5/Utipan.asmx/PsaCoupanPurchase');

//NSDL PANCARD API
define('NSDL_KYC_URL', 'http://api.gramsevak.com/Registration/apipsaregistration');
define('NSDL_INITIATE_URL', 'http://api.gramsevak.com/users/userinitiate');
define('NSDL_TOKEN', '8536F9E344074A67AEBE3A12708505AB');

//GOOGLE GEOCODE ACCOUNT
define('GOOGLE_GEOCODE_KEY', 'AIzaSyBqKeybSwcaOp9vdjAbhF1Pt-t3teXNHCE');


define('XLSX_LIB_ROOT_PATH', 'E:/xampp/htdocs/paysall/vendor/autoload.php');

//INSTANT PAY API
define('INSTANTPAY_EKYC_URL', 'https://api.instantpay.in/user/outlet/signup/initiate');
define('INSTANTPAY_EKYC_VERIFY_URL', 'https://api.instantpay.in/user/outlet/signup/validate');
define('INSTANTPAY_TXN_API', 'https://www.instantpay.in/ws/services/bbps/api');
define('INSTANTPAY_VIEW_PLAN_API', 'https://www.instantpay.in/ws/services/bbps/plans');
define('INSTANTPAY_BILLER_DETAIL_API', 'https://www.instantpay.in/ws/services/bbps/biller_details');
define('INSTANTPAY_ENCRYPTION_KEY', '98ee3569ee1cc83f98ee3569ee1cc83f');
define('INSTANTPAY_AUTH_CODE', '1');
define('INSTANTPAY_CLIENT_ID', 'YWY3OTAzYzNlM2ExZTJlOSPuwXwtaPvrer6D+uBtbb8=');
define('INSTANTPAY_CLIENT_SECRET', '341a5b78476690f3313e7ae9de8020b62e3f65215c6a206e25ad78fcea1921d0');
define('INSTANTPAY_TOKEN', '8eb2607eab4812cc1ff67b5f3dcf6e58');
define('INSTANTPAY_AEPS_BALANCE_ENQUIRY', 'https://api.instantpay.in/fi/aeps/balanceInquiry');
define('INSTANTPAY_AEPS_MINI_STATEMENT_API_URL','https://api.instantpay.in/fi/aeps/miniStatement');
define('INSTANTPAY_AEPS_WITHDRAWAL_API_URL','https://api.instantpay.in/fi/aeps/cashWithdrawal');
define('INSTANTPAY_AEPS_AADHARPAY_API_URL','https://api.instantpay.in/fi/aeps/aadhaarPay');
define('INSTANTPAY_PAYOUT_API_URL','https://api.instantpay.in/payments/payout');
define('BANK_VERIFICATION_URL','https://api.instantpay.in/identity/verifyBankAccount');
define('INSTANTPAY_2FA_URL','https://api.instantpay.in/fi/aeps/outletLoginStatus');
define('INSTANTPAY_2FA_LOGIN_URL','https://api.instantpay.in/fi/aeps/outletLogin');





// paysprint
#define('PAYSPRINT_AEPS_NEW_ONBOARD_API_URL', 'https://paysprint.in/service-api/api/v1/service/onboard/onboardnew/getonboardurl');

#define('PAYSPRINT_AEPS_NEW_MINI_STATEMENT_API_URL', 'https://paysprint.in/service-api/api/v1/service/aeps/ministatement/index');
#define('PAYSPRINT_AEPS_NEW_BALANCE_API_URL', 'https://paysprint.in/service-api/api/v1/service/aeps/balanceenquiry/index');
#define('PAYSPRINT_AEPS_NEW_WITHDRAWAL_API_URL', 'https://paysprint.in/service-api/api/v1/service/aeps/cashwithdraw/index');
#define('PAYSPRINT_AEPS_NEW_AADHARPAY_API_URL', 'https://paysprint.in/service-api/api/v1/service/aadharpay/aadharpay/index');

define('PAYSPRINT_AEPS_NEW_ONBOARD_API_URL', 'https://paysprint.in/service-api/api/v1/service/onboard/onboardnew/getonboardurl');

define('PAYSPRINT_AEPS_NEW_MINI_STATEMENT_API_URL', 'https://api.paysprint.in/api/v1/service/aeps/ministatement/index');
define('PAYSPRINT_AEPS_NEW_BALANCE_API_URL', 'https://api.paysprint.in/api/v1/service/aeps/balanceenquiry/index');
define('PAYSPRINT_AEPS_NEW_WITHDRAWAL_API_URL', 'https://api.paysprint.in/api/v1/service/aeps/cashwithdraw/index');
define('PAYSPRINT_AEPS_NEW_AADHARPAY_API_URL', 'https://api.paysprint.in/api/v1/service/aadharpay/aadharpay/index');


define('PAYSPRINT_AEPS_KEY','1c2ebd73b6fbdd87');
define('PAYSPRINT_AEPS_IV','5148931b33a884a1');
define('PAYSPRINT_PARTNER_ID','PS001617');
define('PAYSPRINT_SECRET_KEY','UFMwMDE2MTcxMGRkOGI2NmU4ZTQ3NTQ1MmMzYzQzZTUzOTUxYmUxYQ==');
define('PAYSPRINT_AUTHORIZED_KEY','M2QwMjdiMjdjYTJlYTBhMmE4MzA4OGVmYmI2YmNiOGU=');
define('PAYSPRINT_CMS_URL','https://paysprint.in/service-api/api/v1/service/finocms/fino/generate_url');
/*define('PAYSPRINT_NSDL_URL','https://paysprint.in/service-api/api/v1/service/pan/V2/generateurl');
define('PAYSPRINT_PAN_STATUS_CHECK_URL','https://paysprint.in/service-api/api/v1/service/pan/V2/pan_status');
define('PAYSPRINT_PAN_TRANSCATION_STATUS_CHECK_URL','https://paysprint.in/service-api/api/v1/service/pan/V2/txn_status');*/


define('PAYSPRINT_NSDL_URL','https://api.paysprint.in/api/v1/service/pan/V2/generateurl');
define('PAYSPRINT_PAN_STATUS_CHECK_URL','https://api.paysprint.in/api/v1/service/pan/V2/pan_status');
define('PAYSPRINT_PAN_TRANSCATION_STATUS_CHECK_URL','https://api.paysprint.in/api/v1/service/pan/V2/txn_status');






define('PAYSPRINT_ADD_BENEFICIARY_URL', 'https://api.paysprint.in/api/v1/service/payout/payout/add');
define('PAYSPRINT_BENEFICIARY_UPLOAD_DOCUMENT_URL', 'https://api.paysprint.in/api/v1/service/payout/payout/uploaddocument');

define('PAYSPRINT_FUND_TRANSFER_URL', 'https://api.paysprint.in/api/v1/service/payout/payout/dotransaction');
define('PAYSPRINT_STATUS_CHECK_URL', 'https://api.paysprint.in/api/v1/service/payout/payout/status');
define('PAYSPRINT_ACCOUNT_STATUS_CHECK_URL', 'https://api.paysprint.in/api/v1/service/payout/payout/accountstatus');
define('PAYSPRINT_ONBOARD_PIPE_STATUS_CHECK_API_URL', 'https://api.paysprint.in/api/v1/service/onboard/onboard/getonboardstatus');



// DMT UAT PAYSPRINT
/*define('PAYSPRINT_DMT_REMITTER_CHECK_API_URL', 'https://paysprint.in/service-api/api/v1/service/dmt/remitter/queryremitter');
define('PAYSPRINT_DMT_REGISTER_REMITTER_API_URL', 'https://paysprint.in/service-api/api/v1/service/dmt/remitter/registerremitter');
define('PAYSPRINT_DMT_REGISTER_BEN_API_URL', 'https://paysprint.in/service-api/api/v1/service/dmt/beneficiary/registerbeneficiary');

define('PAYSPRINT_DMT_FETCH_BEN_API_URL', 'https://paysprint.in/service-api/api/v1/service/dmt/beneficiary/registerbeneficiary/fetchbeneficiary');
define('PAYSPRINT_DMT_DELETE_BEN_API_URL', 'https://paysprint.in/service-api/api/v1/service/dmt/beneficiary/registerbeneficiary/deletebeneficiary');
define('PAYSPRINT_DMT_VERIFY_BEN_API_URL', 'https://paysprint.in/service-api/api/v1/service/dmt/beneficiary/registerbeneficiary/benenameverify');
define('PAYSPRINT_DMT_TXN_AUTH_API_URL', 'https://paysprint.in/service-api/api/v1/service/dmt/transact/transact');
define('PAYSPRINT_DMT_TXN_STATUS_CHECK_API_URL', 'https://paysprint.in/service-api/api/v1/service/dmt/transact/transact/querytransact');
define('PAYSPRINT_DMT_REFUND_OTP_API_URL', 'https://paysprint.in/service-api/api/v1/service/dmt/refund/refund/resendotp');
define('PAYSPRINT_DMT_REFUND_API_URL', 'https://paysprint.in/service-api/api/v1/service/dmt/refund/refund/');*/

define('PAYSPRINT_DMT_REMITTER_CHECK_API_URL', 'https://api.paysprint.in/api/v1/service/dmt/remitter/queryremitter');
define('PAYSPRINT_DMT_REGISTER_REMITTER_API_URL', 'https://api.paysprint.in/api/v1/service/dmt/remitter/registerremitter');
define('PAYSPRINT_DMT_REGISTER_BEN_API_URL', 'https://api.paysprint.in/api/v1/service/dmt/beneficiary/registerbeneficiary');

define('PAYSPRINT_DMT_FETCH_BEN_API_URL', 'https://api.paysprint.in/api/v1/service/dmt/beneficiary/registerbeneficiary/fetchbeneficiary');
define('PAYSPRINT_DMT_DELETE_BEN_API_URL', 'https://api.paysprint.in/api/v1/service/dmt/beneficiary/registerbeneficiary/deletebeneficiary');
define('PAYSPRINT_DMT_VERIFY_BEN_API_URL', 'https://api.paysprint.in/api/v1/service/dmt/beneficiary/registerbeneficiary/benenameverify');
define('PAYSPRINT_DMT_TXN_AUTH_API_URL', 'https://api.paysprint.in/api/v1/service/dmt/transact/transact');
define('PAYSPRINT_DMT_TXN_STATUS_CHECK_API_URL', 'https://api.paysprint.in/api/v1/service/dmt/transact/transact/querytransact');
define('PAYSPRINT_DMT_REFUND_OTP_API_URL', 'https://api.paysprint.in/api/v1/service/dmt/refund/refund/resendotp');
define('PAYSPRINT_DMT_REFUND_API_URL', 'https://api.paysprint.in/api/v1/service/dmt/refund/refund/');
define('PAYSPRINT_2FA_API_URL', 'https://paysprint.in/service-api/api/v1/service/aeps/kyc/Twofactorkyc/registration');
define('PAYSPRINT_2FA_API_LOGIN_URL', 'https://paysprint.in/service-api/api/v1/service/aeps/kyc/Twofactorkyc/authentication');



//morningpay nsdl api
define('MORNINGPAY_NSDL_API_URL', 'https://digipaydashboarduat.religaredigital.in/authenticate');
define('MORNINGPAY_NSDL_API_TOKEN', 'd7ef9658-471d-4b68-a81d-c347099980db');
define('MORNINGPAY_LOGO', 'https://www.morningpay.co.in/media/account/819113830.png');
define('MORNINGPAY_COPYRIGHT_MSG', 'Morningpay Digital Private Limited');
define('MORNINGPAY_FIRM_NAME', 'Morningpay');
define('MORNINGPAY_SERVICE_ID', '154');
define('MORNINGPAY_PAN_TRANSCATION_STATUS', 'https://digipaydashboarduat.religaredigital.in/PanTransactionStatus');
define('MORNINGPAY_PAN_STATUS', 'https://digipaydashboarduat.religaredigital.in/PancardStatus');
define('IS_SOCIETY',1);



// fingpay aeps 

define('FINGPAY_AEPS_ONBOARD_API_URL', 'https://fingpayap.tapits.in/fpaepsweb/api/onboarding/merchant/php/creation/v2');



#define('PAYSPRINT_AEPS_CALLBACK_URL', 'https://purveyindia.com/syscallback/aepsOnBoardCallback');


//UPI COLLECTION ACTIVE API 1 = Phonepe, 2 = Yesbank
define('UPI_COLLECTION_ACTIVE_API', 1);

//mobikwik api detail 
define('IS_MOBIKWIK_ACTIVE', '1');
#define('MOBIKWIK_API_URL','https://alpha3.mobikwik.com');
define('MOBIKWIK_USER_ID', 'info@payol.in');

define('MOBIKWIK_USER_PWD', 'Nayak@123');

define('MOBIKWIK_SECRET_KEY', '5DYJS4686C79M48QT68M6QLDFFT2TY25');

define('MOBIKWIK_BILL_FETCH_API', 'https://rapi.mobikwik.com/retailer/v2/retailerViewbill');

define('MOBIKWIK_BILL_PAY_API_URL','https://rapi.mobikwik.com/recharge.do?');

define('MOBIKWIK_STATUS_CHECK_API_URL', 'https://rapi.mobikwik.com/rechargeStatus.do?');


#define('PRODUCT_IMAGE_FILE_PATH', '/home/marwarcare/public_html/media/product_images/');
/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code
