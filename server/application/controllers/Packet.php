<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \QCloud_WeApp_SDK\Auth\AuthAPI as AuthAPI;
use \QCloud_WeApp_SDK\Auth\LoginService as LoginService;
use QCloud_WeApp_SDK\Constants as Constants;

class Packet extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        // $this->load->model('user_model');

        $this->load->model('packet_model');
        $this->load->model('receive_model');
        $this->load->helper('url_helper');
    }
    /**
     * 生成红包
     */
    public function create() {
        // echo "<pre>";print_r($_GET);exit;
        // echo "<pre>";print_r($_POST);exit;
        // $skey = isset($_GET['skey']) ? $_GET['skey'] : '';
        // if($skey){
        //     $result = AuthAPI::checkLogin($skey);
        // }else{
        //     $result = LoginService::check();
        // }
        $fields = array('open_id','reward','qty','command','commission');
        $result = $this->validate($fields);
        if(!$result){
            $this->json([
                'code' => -1,
                'data' => array('message'=>'invalid param')
            ]);
            return;
        }
        // if ($result['loginState'] === Constants::S_AUTH) {
            $result = $this->packet_model->set_packet();
            $this->json($result);

        // } else {
        //     $this->json([
        //         'code' => -1,
        //         'data' => ['message'=>'请重新登陆']
        //     ]);
        // }
    }
    /**
     * 红包详情
     */
    public function view()
    {
        $packet_id = isset($_REQUEST['packet_id']) ? (int)$_REQUEST['packet_id'] : '';
        if($packet_id){
            $condition = array('packet_id'=>$packet_id);

            $packet = $this->packet_model->get_packet($condition);

            if($packet){
                $packet['user_info'] = json_decode($packet['user_info'],true);
                $receives = $this->receive_model->get_receive($condition,'result');
                if($receives && count($receives)){
                    foreach ($receives as &$receive){
                        $receive['user_info'] = json_decode($receive['user_info'],true);
                        $receive['sender_info'] = json_decode($receive['sender_info'],true);
                    }
                }
                $this->json([
                    'code' => 0,
                    'data' =>array(
                        'packet'=>$packet,
                        'receives'=>$receives,
                    ),
                ]);
            }else{
                $this->json([
                    'code' => -1,
                    'data' =>array(
                        'message'=>'No record',
                    ),
                ]);
            }
        }else{
            $this->json([
                'code' => -1,
                'data' => array('message'=>'invalid param packet_id')
            ]);
        }
    }

    /**
     * 领取红包
     */
    public function receive()
    {
        $fields = array('packet_id','open_id');
        $result = $this->validate($fields);
        if(!$result){
            $this->json([
                'code' => -1,
                'data' => array('message'=>'invalid param')
            ]);
            return;
        }
        $result = $this->packet_model->create_receive();
        $this->json($result);
    }
    /**
     * 我的记录
     */
    public function record()
    {
        $fields = array('open_id');
        $result = $this->validate($fields);
        if(!$result){
            $this->json([
                'code' => -1,
                'data' => array('message'=>'invalid param')
            ]);
            return;
        }
        $result = $this->packet_model->packet_record();
        $this->json($result);
    }
}
