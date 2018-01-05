<?php
ini_set('date.timezone','Asia/Shanghai');
//error_reporting(E_ERROR);
require_once "lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";
require_once 'log.php';
class Jsapi_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }
    public function get_unifiedorder()
    {
        $open_id = $this->input->post('open_id');

        $input = new WxPayUnifiedOrder();
        $input->SetBody("test");
        $input->SetAttach("test");
        $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
        $input->SetTotal_fee("1");
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");
        $input->SetNotify_url("http://paysdk.weixin.qq.com/example/notify.php");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($open_id);
        $order = WxPayApi::unifiedOrder($input);
        if ($order["return_code"] == 'FAIL' && $order["return_msg"]) {
            return [
                'code' => -1,
                'data' => array('message'=>$order["return_msg"])
            ];
        } elseif (isset($order["err_code"]) && $order["err_code"]) {
            return [
                'code' => -1,
                'data' => array('message'=>$order["return_msg"])
            ];
        } else {
            $jsapiparams = $this->get_jsapiparams($order);
            return array(
                'code'=>0,
                'data'=>array('jsapiparams'=>json_decode($jsapiparams,true)),
            );
        }
    }
    public function get_jsapiparams($order)
    {
        $tools = new JsApiPay();
        $jsApiParameters = $tools->GetJsApiParameters($order);
        return $jsApiParameters;
    }
}


?>
