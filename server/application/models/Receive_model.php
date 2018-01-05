<?php
class Receive_model extends CI_Model {

    public function __construct()
    {
        $this->load->model('packet_model');

        $this->load->database();
    }
    public function get_receive($condition=array(),$type='row')
    {
        if (empty($condition))
        {
            $query = $this->db->get('receive');
            // echo "<pre>";print_r($query->result_array());exit;
            return $query->result_array();
        }

        $query = $this->db->get_where('receive', $condition);
        if($type=='row'){
            return $query->row_array();
        }elseif($type='result'){
            return $query->result_array();
        }
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
    public function count_reward($open_id)
    {
        $sql = "SELECT SUM(reward) as receive_reward FROM receive WHERE open_id = ?";
        $query = $this->db->query($sql, array($open_id));
        $row = $query->row();
        return $row;
    }
}
