<?php

class Auth extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function index()
    {
        $this->load->view('header');
        $this->load->view('auth/firstpage');
        $this->load->view('footer');
    }

    public function register()
    {
        if ( !empty($login = $this->input->post('login')) && !empty($password = $this->input->post('pass')) ) {
            $sql = $this->db->select('*')->from('users')->where('login',$login)->get();
            $user = $sql->result();
            if (empty($user)) {
                $salt = $this -> config -> item ('salt');
                $saltedPassword = md5($password.$salt);
                $data = ['login' => $login, 'password' => $saltedPassword];
                $sql = $this->db->insert('users', $data);
            } else {
                exit ('This login is already taken!');
            }
        } else {
            exit ('Fields cannot be empty');
        }

        if (!empty($login = $this->input->post('login')) && !empty($password = $this->input->post('pass')) ) {
            $sql = $this->db->select('*')->from('users')->where('login',$login)->get();
            $user = $sql->row_array();
            if (!empty($user)) {
                $salt = $this -> config -> item ('salt');
                $saltedPassword = md5($password . $salt);
                if ($user['password'] == $saltedPassword) {
                    session_start();
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['login'] = $user['login'];
                    header('location: /files/filesharing');
                }
            }
        }
    }
    public function login()
    {
        if (!empty($login = $this->input->post('login')) && !empty($password = $this->input->post('pass')) ) {
            $sql = $this->db->select('*')->from('users')->where('login',$login)->get();
            $user = $sql->row_array();
            if(!empty($user)) {
                $salt = $this -> config -> item ('salt');
                $saltedPassword = md5($password.$salt);
                if($user['password'] == $saltedPassword) {
                    session_start();
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['login'] = $user['login'];
                    header('location: /files/filesharing');
                } else {
                    echo 'Wrong password!';
                }
            } else {
                echo 'Invalid login, please, sign up';
            }
        } else {
            echo 'Login or password can not be empty!';
        }
    }

}