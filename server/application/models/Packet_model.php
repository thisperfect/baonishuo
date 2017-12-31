<?php
class Packet_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }
    public function get_packet($condition = array(),$type='row')
    {
        if (empty($condition))
        {
            $query = $this->db->get('packet');
            // echo "<pre>";print_r($query->result_array());exit;
            return $query->result_array();
        }

        $query = $this->db->get_where('packet',$condition);
        if($type=='row'){
            return $query->row_array();
        }elseif($type=='result'){
            return $query->result_array();
        }
    }
    public function set_packet($result =array())
    {
        $data = array(
            'open_id' => $this->input->post('open_id'),
            'command' => $this->input->post('command'),
            'reward' => $this->input->post('reward'),
            'commission' => $this->input->post('commission'),
            'grand' => $this->input->post('commission') + $this->input->post('reward'),
            'qty' => $this->input->post('qty'),
            'left_qty' => $this->input->post('qty'),
            'reward' => $this->input->post('reward'),
            'left_reward' => $this->input->post('reward'),
            'status' => 0,
            'user_info'=>isset($result['userinfo']) ? json_encode($result['userinfo']) : '',
        );

        $result = $this->db->insert('packet', $data);
        if($result){
            return $this->db->insert_id();
        }else{
            return '';
        }
    }
    public function create_receive($packet=array(),$userinfo='')
    {
        // echo "<pre>";print_r($_POST);exit;
        $packet_id = $this->input->post('packet_id');
        $condition = array('packet_id'=>$packet_id);
        $packet = $this->get_packet($condition);
        if(!$packet){
            return array(
                'code' => -1,
                'data' => array('message'=>'Packet not found')
            );
        }
        // echo "<pre>";print_r($packet);exit;
        $open_id = $this->input->post('open_id');
        $this->load->model('user_model');
        $user = $this->user_model->get_user($open_id);
        // echo "<pre>";var_dump($user);exit;
        if(!$user){
            return array(
                'code' => -1,
                'data' => array('message'=>'Customer not found')
            );
        }

        // echo "<pre>";print_r($userinfo);exit 
        $condition = array('packet_id'=>$packet_id,'open_id'=>$open_id);
        $this->load->model('receive_model');
        $receive = $this->receive_model->get_receive($condition);
        // echo "<pre>";print_r($receive);exit;
        if($receive){
            return array(
                'code' => -1,
                'data' => array('message'=>'You have received before'),
            );
        }

        $rand_reward = $this->rand_reward($packet);
        $data = array(
            'open_id' => $this->input->post('open_id'),
            'packet_id' => $this->input->post('packet_id'),
            'reward' => $rand_reward,
            'voice' => $this->input->post('voice'),
            'user_info'=>$user['user_info'],
        );
        $packet_data = array(
            'received_qty'=>$packet['received_qty'] + 1,
            'left_qty'=>$packet['left_qty'] - 1,
            'left_reward' => $packet['left_reward'] - $rand_reward,
        );
        $result = $this->db->insert('receive', $data);

        if($result){
            $data['user_info'] = json_decode($user['user_info'],true);
            $data['receive_id'] = $this->db->insert_id();
            $packet = $this->db->update('packet',$packet_data,array('packet_id'=>$packet['packet_id']));
            return array(
                'code'=>0,
                'data'=>array(
                    'receive'=>$data,
                ),
            );
        }else{
            return array(
                'code'=>-1,
                'data'=>array('message'=>'Operation failed'),
            );
        }
    }
    public function packet_record()
    {
        $open_id = $this->input->post('open_id');
        $condition = array('open_id'=>$open_id);
        $packets = $this->get_packet($condition);
        if(!$packets){
            $packets = array();
        }
        $this->load->model('receive_model');
        $receives = $this->receive_model->get_receive($condition);
        if(!$receives){
            $receives = array();
        }
        return array(
            'code'=>0,
            'data'=>array(
                'send'=>$packets,
                'receive'=>$receives,
            ),
        );

    }
    public function rand_reward($packet)
    {
        $left_reward = $packet['left_reward'];
        $left_qty = $packet['left_qty'];
        if($left_qty==1){
            return $left_reward;
        }
        $reward =  $left_reward / $left_qty;
        $reward = number_format ( $reward ,  2 ,  '.' ,  '' );
        return $reward;
    }
}
