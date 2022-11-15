<?php
defined('BASEPATH') or exit('No direct script access allowed');


class UserModel extends CI_Model
{

    // function __construct() {
    //     parent::__construct();
    //     $this->load->model();
    // }


    public function createUser()
    {
        $data = array(
            'name' => $this->input->post('name'),
            'password' => $this->input->post('password'),
            'created_time' => time(),
        );
        $this->db->insert('users', $data);
        $insertId = $this->db->insert_id();
        return  $insertId;
    }

    public function createUserNotes()
    {
        $data = array(
            'title' => $this->input->post('title'),
            'note' => $this->input->post('note'),
            'created_time' => time(), 
            'userid' => $this->session->userdata('userid')
        );
         $this->db->insert('un_'.$this->session->userdata('userid'), $data);
    }

    public function updateNote($id)
    {
        $query = $this->db->get_where('un_'. $this->session->userdata('userid'), ["deleted" => "N", 'userid' => $this->session->userdata('userid'),'id' => $id ]);
        $userid = $query->result();
        if (!empty($userid[0]->id)) {
            $update_data = [];
            $update_data['title'] =  $this->input->post('title');
            $update_data['note'] =  $this->input->post('note');
            $update_data['created_time'] = time();
            $this->db->where('userid',$this->session->userdata('userid') );
            $this->db->update('un_'.$this->session->userdata('userid'), $update_data);
        }
    }

    public function updateUser($id){
        $query = $this->db->get_where('users', array("deleted" => "N", 'id' => $id));
        $userid = $query->result();
        if (!empty($userid[0]->id)) {
            $update_data = [];
            $update_data['name'] =  $this->input->post('name');
            $update_data['password'] =  $this->input->post('password');
            $update_data['created_time'] = time();
            $this->db->where('id', $id);
            $this->db->update('users', $update_data);
        }
    }

    public function usercheck(){
        $query = $this->db->get_where('users', array("deleted" => "N", 'id' => $this->session->userdata('userid')));
        return $query->result();
    }


    public function userNotes($userid){
        $query = $this->db->get_where('un_'.$userid, ["deleted" => "N",'userid' => $userid]);
        return $query->result();
    }


    public function deleteupdateNote($id)
    {
        $query = $this->db->get_where('un_'.$this->session->userdata('userid'), array("deleted" => "N", 'userid' => $this->session->userdata('userid'),'id'=>$id));
        $userid = $query->result();
        if(!empty($userid[0]->id)){
            $update_data = [];
            $update_data['deleted'] =  'Y';
            $update_data['deleted_time'] = time();
            $this->db->where('userid', $this->session->userdata('userid'));
            $this->db->update('un_'.$this->session->userdata('userid'), $update_data);
        }
    }

    public function deleteupdateusers($id){
        $query = $this->db->get_where('users', array("deleted" => "N", 'id' => $id));
        $userid = $query->result();
        if(!empty($userid[0]->id)){
            $update_data = [];
            $update_data['deleted'] =  'Y';
            $update_data['deleted_time'] = time();
            $this->db->where('id', $id);
            $this->db->update('users', $update_data);
        }

    }

    public function extractAllNotes(){
        $query = $this->db->get_where("un_".$this->session->userdata('userid'), ["deleted " => "N"]);
        return $query->result();
    }

    public function logInUser($name, $pass)
    {
        $query = $this->db->get_where("users", ["deleted " => "N", 'name' => $name, "password" => $pass]);
        return $query->result();
    }

    //forgingTable
    public function uforgeTabledata($id)
    {
        // switch over to Library DB
        $this->load->dbforge();
        $fields = array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
                'not_null' => TRUE,
            ),
            'title' => array(
                'type' => 'VARCHAR',
                'null' => TRUE,
                'constraint' => 200,
            ),
            'note' => array(
                'type' => 'VARCHAR',
                'null' => TRUE,
                'constraint' => 2000,
            ),
            'userid' => array(
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
            ),
            'created_time' => array(
                'type' => 'INT',
                'null' => TRUE,
                'constraint' => 11,
            ),
            'deleted_time' => array(
                'type' => 'INT',
                'null' => TRUE,
                'constraint' => 11,
            ),
            'deleted' => array(
                'type' => 'VARCHAR',
                'constraint' => 1,
                'not_null' => TRUE,
                'default' => 'N',
            ),
        );

        $this->dbforge->add_field($fields);
        // define primary key
        $this->dbforge->add_key('id', TRUE);
        // create table
        $this->dbforge->create_table('un_' . $id);
    }

    public function isLogin()
    {
        if ($this->session->userdata("userid") != null) {
            $query = $this->db->get_where('users', array("id" => $this->session->userdata("userid"), "deleted" => "N"));
            $user = $query->result();
            if (!empty($user[0]->id)) {
                return true;
            }
        } else {
            return false;
        }
    }
}
