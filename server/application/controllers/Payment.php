<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \QCloud_WeApp_SDK\Auth\LoginService as LoginService;
use QCloud_WeApp_SDK\Constants as Constants;

class Payment extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->model('jsapi_model');
        // $this->load->helper('url_helper');
    }
    public function unifiedorder() {
        $result = $this->jsapi_model->get_unifiedorder();
        $this->json($result);
    }
}
