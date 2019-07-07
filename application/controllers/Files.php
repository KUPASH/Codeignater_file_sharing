<?php

class Files extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        session_start();
    }
    public function filesharing()
    {
        function generateKey() {
            $imageKey = '';
            $keyLength = 8;
            for($i=0; $i<$keyLength; $i++) {
                $imageKey .= chr(mt_rand(33,126));
            }
            return $imageKey;
        }
        $allowed_ext = ['jpg', 'jpeg'];
        if (isset($_FILES['userFile']) && $_FILES['userFile']['error'] == 0) {
            $realName = $_FILES['userFile']['name'];
            $filename = md5(time() . rand(1, 9999) . $realName);

            $ext = explode('.', $_FILES['userFile']['name']);
            $ext = $ext[count($ext) - 1];

            if (!in_array($ext, $allowed_ext)) {
                echo '<div style="color: red">ERROR: Invalid file extension; valid jpg, jpeg</div>';
            } else {

                $subdirname1 = $filename[0];
                $subdirname2 = $filename[1];

                if (!file_exists('./uploads/' .
                    $subdirname1 . '/' .
                    $subdirname2)
                ) {
                    mkdir('./uploads/' .
                        $subdirname1 . '/' .
                        $subdirname2, 0777, true);
                }

                move_uploaded_file($_FILES['userFile']['tmp_name'],
                    './uploads/' .
                    $subdirname1 . '/' .
                    $subdirname2 . '/' .
                    $filename . '.' . $ext);

                if (file_exists('./uploads/' .
                    $subdirname1 . '/' .
                    $subdirname2 . '/' .
                    $filename . '.' . $ext)) {

                    $imageKey = generateKey();
                    $filename = $filename . '.' . $ext;
                    $data = ['real_name' => $realName,
                        'hash_name' => $filename,
                        'image_key' => $imageKey,
                        'user_id' => $_SESSION['id']];
                    $sql = $this->db->insert('files', $data);
                }
            }
        } else {
            echo 'Please Select file to Upload';
        }

        $sql = $this->db->select('*')->from('files')->where('user_id',$_SESSION['id'])->get();
        $row = $sql->result();

        $this->load->view('header');
        $this->load->view('files/filesharing', ['files' => $row]);
        $this->load->view('footer');
    }
    public function delete()
    {
        if(isset($_SESSION['id']) && isset($_SESSION['login'])) {
            $num_string = $this->input->get('del');
            $sql = $this->db->select('*')->
                            from('files')->
                            where('user_id',$_SESSION['id'])->
                            where('id',$num_string)->get();

            $file = $sql->row_array();
            $fileway = './uploads/' . $file['hash_name'][0] . '/' . $file['hash_name'][1] . '/' . $file['hash_name'];

            $sql = $this->db->delete('files',['user_id' => $_SESSION['id'], 'id' => $num_string]);

            $sql = $this->db->select('*')->
            from('files')->
            where('user_id',$_SESSION['id'])->
            where('id',$num_string)->get();

            $file = $sql->result();
            if (empty($file)) {
                unlink($fileway);
            }
        }
        header('Location: /files/filesharing');
    }
    public function download()
    {
        if(isset($_SESSION['id']) && isset($_SESSION['login'])) {
            $name = $this->input->get('name');
            $sql = $this->db->select('*')->
                                from('files')->
                                where('user_id',$_SESSION['id'])->
                                where('image_key',$name)->get();
            $file = $sql->row_array();
            if(!empty($file)) {
                $realName = $file['real_name'];
                $fileway = './uploads/' . $file['hash_name'][0] . '/' . $file['hash_name'][1] . '/' . $file['hash_name'];
                if (file_exists($fileway)) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: octet-stream');
                    header('Content-Disposition: attachment; filename="' . $realName . '"');
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($fileway));
                    readfile($fileway);
                }
            }
        }
        header('Location: /files/filesharing');
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        header('location: /auth');
    }
}