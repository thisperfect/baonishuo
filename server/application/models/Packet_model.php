<?php
class Packet_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }
    /**
     * 获取红包
     */
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
    /**
     * 生成红包
     */
    public function set_packet($result =array())
    {
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
            'user_info'=>$user['user_info'],
        );

        try {
            $result = $this->db->insert('packet', $data);
            return array(
                'code' => 0,
                'data' =>array(
                    'packet_id'=>$this->db->insert_id(),
                ),
            );
        } catch (Exception $e) {
            return array(
                'code'=>-1,
                'data'=>array(
                    'message'=>'语音口令生成失败: ' . $e->__toString(),
                ),
            );
        }
    }
    /**
     * 红包详情
     */
    public function view_packet()
    {
        $packet_id = $this->input->post('packet_id');
        $condition = array('packet_id'=>$packet_id);
        $packet = $this->get_packet($condition);
        if(!$packet){
            return array(
                'code' => -1,
                'data' => array('message'=>'Packet not found')
            );
        }else{
            $packet['user_info'] = json_decode($packet['user_info'],true);
        }

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
        $has_received = $receive ? true : false;
        $received_reward = $receive ? $receive['reward'] : '';
        $condition = array('packet_id'=>$packet_id);
        $receives = $this->receive_model->get_receive($condition,'result');
        if($receives && count($receives)){
            foreach ($receives as &$receive){
                $receive['user_info'] = json_decode($receive['user_info'],true);
                $receive['sender_info'] = json_decode($receive['sender_info'],true);
            }
        }
        $condition = array();
        return array(
            'code' => 0,
            'data' =>array(
                'has_received'=>$has_received,
                'received_reward'=>$received_reward,
                'packet'=>$packet,
                'receives'=>$receives,
            ),
        );
    }
    /**
     * 领取红包
     */
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
        $this->load->model('upload_model');
        //upload voice
        $file = $this->upload_model->upload_file();
        if($file['code']==-1){
            return array(
                'code'=>-1,
                'data'=>array('message'=>$file['error']),
            );
        }
        $voice = $file['data']['imgUrl'];
        $rand_reward = $this->rand_reward($packet);
        $data = array(
            'open_id' => $this->input->post('open_id'),
            'packet_id' => $this->input->post('packet_id'),
            'reward' => $rand_reward,
            'voice' => $voice,
            'user_info'=>$user['user_info'],
            'sender_info'=>$packet['user_info'],
        );
        $packet_data = array(
            'received_qty'=>$packet['received_qty'] + 1,
            'left_qty'=>$packet['left_qty'] - 1,
            'left_reward' => $packet['left_reward'] - $rand_reward,
        );
        $result = $this->db->insert('receive', $data);

        if($result){
            $data['user_info'] = json_decode($user['user_info'],true);
            $data['sender_info'] = json_decode($packet['user_info'],true);
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
    /**
     * 我的记录
     */
    public function packet_record()
    {
        $open_id = $this->input->post('open_id');
        $condition = array('open_id'=>$open_id);
        $packets = $this->get_packet($condition,'result');
        if(!$packets){
            $packets = array();
        }else{
            foreach ($packets as &$packet){
                $packet['create_time'] = date ( 'm月d日 H:i' , strtotime($packet['create_time']) );
            }
        }
        $send_reward = $this->count_reward($open_id);
        $this->load->model('receive_model');
        $receives = $this->receive_model->get_receive($condition,'result');
        $receive_reward = $this->receive_model->count_reward($open_id);
        if(!$receives){
            $receives = array();
        }else{
            foreach ($receives as &$receive){
                $receive['user_info'] = json_decode($receive['user_info'],true);
                $receive['sender_info'] = json_decode($receive['sender_info'],true);
                $receive['create_time'] = date ( 'm月d日 H:i' , strtotime($receive['create_time']) );
            }
        }
        return array(
            'code'=>0,
            'data'=>array(
                'send_reward'=>$send_reward,
                'send_number'=>count($packets),
                'send'=>$packets,
                'receive'=>$receives,
                'receive_reward'=>$receive_reward,
                'receive_number'=>count($receives),
            ),
        );

    }
    public function rand_reward($packet)
    {
        if($packet['left_qty']==1){
            return $packet['left_reward'];
        }
        $reward = $packet['left_reward'] / $packet['left_qty'];
        $reward = number_format ( $reward ,  2 ,  '.' ,  '' );
        return $reward;
    }
    public function count_reward($open_id)
    {
        $sql = "SELECT SUM(reward) as send_reward FROM packet WHERE open_id = ? AND status = ?";
        $query = $this->db->query($sql, array($open_id, 0));
        $row = $query->row();
        return $row;
    }

}
