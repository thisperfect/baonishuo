<?php
class User_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
    }
    public function get_user($open_id = '')
    {
        if (!$open_id)
        {
            return false;
        }

        $query = $this->db->get_where('cSessionInfo', array('open_id' => $open_id));
        return $query->row_array();
    }
}
