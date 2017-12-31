<?php
class Receive_model extends CI_Model {

    public function __construct()
    {
        $this->load->model('packet_model');

        $this->load->database();
    }
    public function get_receive($condition=array())
    {
        if (empty($condition))
        {
            $query = $this->db->get('receive');
            // echo "<pre>";print_r($query->result_array());exit;
            return $query->result_array();
        }

        $query = $this->db->get_where('receive', $condition);
        return $query->result_array();
    }
    public function set_receive($userinfo ='')
    {
        $data = array(
            'open_id' => $this->input->post('open_id'),
            'packet_id' => $this->input->post('packet_id'),
            'reward' => $this->input->post('reward'),
            'voice' => $this->input->post('voice'),
            'user_info'=>$userinfo,
        );

        $result = $this->db->insert('receive', $data);
        if($result){
            // return $this->db->insert_id();
            $data['user_info'] = json_decode($userinfo,true);
            return $data;
        }else{
            return '';
        }
    }
}
