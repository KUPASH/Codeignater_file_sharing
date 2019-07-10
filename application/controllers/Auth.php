<?php

class Auth extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('users_model');
    }

    public function index()
    {
        $this->load->view('header');
        $this->load->view('/auth/firstpage');
        $this->load->view('footer');
    }

    public function register()
    {
        $login = $this->input->post('login');
        $password = $this->input->post('pass');
        if(!empty($login) && !empty($password)) {
            $user = $this->users_model->getUserByLogin($login);
            if (empty($user)) {
                $salt = $this->config->item('salt');
                $saltedPassword = md5($password . $salt);
                $sql = $this->users_model->insertNewUser($login, $saltedPassword);
            } else {
                $error['nameErr'] = 'Login is already taken';

                $this->load->view('header');
                $this->load->view('/auth/firstpage', $error);
                $this->load->view('footer');
            }
        } else {
            $error['emptyRegPassLogErr'] = 'Login or password can not be empty!';

            $this->load->view('header');
            $this->load->view('/auth/firstpage', $error);
            $this->load->view('footer');
        }

        $login = $this->input->post('login');
        $password = $this->input->post('pass');
        $user = $this->users_model->getUserByLogin2($login);
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
    public function login()
    {
        $login = $this->input->post('login');
        $password = $this->input->post('pass');
        if(!empty($login) && !empty($password)) {
            $user = $this->users_model->getUserByLogin2($login);
            if (!empty($user)) {
                $salt = $this->config->item('salt');
                $saltedPassword = md5($password . $salt);
                if ($user['password'] == $saltedPassword) {
                    session_start();
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['login'] = $user['login'];
                    header('location: /files/filesharing');
                } else {
                    $error['passErr'] = 'Wrong password!';

                    $this->load->view('header');
                    $this->load->view('/auth/firstpage', $error);
                    $this->load->view('footer');
                }
            } else {
                $error['loginErr'] = 'Invalid login, please, sign up';

                $this->load->view('header');
                $this->load->view('/auth/firstpage', $error);
                $this->load->view('footer');
            }
        } else {
            $error['emptyPassLogErr'] = 'Login or password can not be empty!';

            $this->load->view('header');
            $this->load->view('/auth/firstpage', $error);
            $this->load->view('footer');
        }
    }
}