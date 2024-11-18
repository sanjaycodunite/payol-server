<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'home';
$route['serviceapi/authController/(:any)'] = 'service/web/$1';
$route['serviceapi/authControllerDemo/(:any)'] = 'service/webnew/$1';
$route['404_override'] = '';
$route['page/(:any)'] = 'page/index/$1';
$route['translate_uri_dashes'] = FALSE;
$route['bill-payment'] = 'services/billPayment';
$route['insurance'] = 'services/insurance';
$route['payment-gateway'] = 'services/paymentGateway';
$route['tour'] = 'services/tour';
$route['our-partner'] = 'page/ourPartner';
$route['director'] = 'services/director';

$route['admin/contactus-enquiry'] = 'admin/enquiry/contactFormEnquiryList';
$route['admin/admin/become-a-partner-enquiry'] = 'admin/enquiry/becomeAPatnerFormEnquiryList';