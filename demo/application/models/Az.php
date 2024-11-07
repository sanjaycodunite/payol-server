<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * Model used for setup default message and resize image
 * 
 * This one used for defined some methods accross all site.
 * this one used for show system message, errors.
 * this one used for image resizing
 * @author trilok
 */

require_once BASEPATH . '/core/Model.php';

class AZ extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function redirect($url, $falshdata = "", $flashvalue = NULL) {

	

        if (is_array($falshdata)) {
            foreach ($falshdata as $key => $value) {
                $this->session->set_flashdata($key, $value);
            }
            redirect(base_url($url));
        } else {
            $this->session->set_flashdata($falshdata, $flashvalue);
            redirect(base_url($url));
        }
    }

    public function getSystemMessageError($flashData = NULL) {
        if (is_null($flashData)) {
            $msg = $this->session->flashdata('system_message_error');
        } else {
            $msg = $flashData;
        }
        $msg = (!empty($msg)) ? $msg : '';
        unset($_SESSION['system_message_error']);
        return $msg;
    }

    public function getSystemMessageInfo($flashData = NULL) {
        if (is_null($flashData)) {
            $msg = $this->session->flashdata('system_message_info');
        } else {
            $msg = $flashData;
        }
        $msg = (!empty($msg)) ? $msg : '';
        unset($_SESSION['system_message_info']);
        return $msg;
    }

    public function getSystemMessageWarning($flashData = NULL) {
        if (is_null($flashData)) {
            $msg = $this->session->flashdata('system_message_warning');
        } else {
            $msg = $flashData;
        }
        $msg = (!empty($msg)) ? '<div class="alert_warning"><p>' . $msg . '</p></div>' : '';
        unset($_SESSION['system_message_warning']);
        return $msg;
    }
	    public function resizeImage($sourceImg, $savePath, $width, $height, $marker=FALSE){
        $resize_config['image_library'] = 'gd2';
        $resize_config['source_image'] = $sourceImg;
        $resize_config['new_image'] = $savePath;
        $resize_config['create_thumb'] = TRUE;
        $resize_config['thumb_marker'] = $marker;
       $resize_config['maintain_ratio'] = False;
        $resize_config['width'] = $width;
        $resize_config['height'] = $height;
        $this->load->library('image_lib');
        $this->image_lib->clear();
        $this->image_lib->initialize($resize_config);
        $this->image_lib->resize();
        if($marker === TRUE){
            $ext = '.'.end(explode('.',$sourceImg));
            $thumbPath = substr($savePath, 2). str_replace($ext, '', end(explode('/',$sourceImg))).'_thumb'.$ext;
        }else{
            $thumbPath = substr($savePath, 2). end(explode('/',$sourceImg));
        }
        return $thumbPath;
    }
	public function resizeImageWithRatio($sourceImg, $savePath, $width, $height, $marker=FALSE){
		
        $resize_config['image_library'] = 'gd2';
        $resize_config['source_image'] = $sourceImg;
        $resize_config['new_image'] = $savePath;
        $resize_config['create_thumb'] = TRUE;
        $resize_config['thumb_marker'] = $marker;
        $resize_config['maintain_ratio'] = True;
        $resize_config['width'] = $width;
        $resize_config['height'] = $height;
        $this->load->library('image_lib');
        $this->image_lib->clear();
        $this->image_lib->initialize($resize_config);
        $this->image_lib->resize();
        if($marker === TRUE){
            $ext = '.'.end(explode('.',$sourceImg));
            $thumbPath = substr($savePath, 2). str_replace($ext, '', end(explode('/',$sourceImg))).'_thumb'.$ext;
        }else{
            $thumbPath = substr($savePath, 2). end(explode('/',$sourceImg));
        }
        return $thumbPath;
    }

}


/* end of file: az.php */
/* Location: ./application/models/az.php */