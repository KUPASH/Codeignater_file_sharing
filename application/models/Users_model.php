<?php

class Users_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    public function getUserByLogin($login)
    {
        $sql = $this->db->select('*')->from('users')->where('login', $login)->get();
        $user = $sql->result();
        return $user;
    }
    public function insertNewUser($login, $saltedPassword)
    {
        $data = ['login' => $login, 'password' => $saltedPassword];
        $sql = $this->db->insert('users', $data);
    }
    public function getUserByLogin2($login)
    {
        $sql = $this->db->select('*')->from('users')->where('login',$login)->get();
        $user = $sql->row_array();
        return $user;
    }
}