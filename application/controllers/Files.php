<?php

class Files extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('files_model');
        session_start();
    }
    private function generateKey()
    {
        $imageKey = '';
        $keyLength = 8;
        for($i=0; $i<$keyLength; $i++) {
            $imageKey .= chr(mt_rand(33,126));
        }
        return $imageKey;
    }
    public function filesharing()
    {
        if(isset($_SESSION['id']) && isset($_SESSION['login'])) {
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

                        $imageKey = $this->generateKey();
                        $filename = $filename . '.' . $ext;
                        $sql = $this->files_model->insertNewFile($realName, $filename, $imageKey);
                    }
                }
            } else {
                echo 'Hello, ' . $_SESSION['login'] . '. Please, select file to upload';
            }

            $row = $this->files_model->getAllFilesUser();

            $this->load->view('header');
            $this->load->view('files/filesharing', ['files' => $row]);
            $this->load->view('footer');
        }
    }
    public function delete()
    {
        if(isset($_SESSION['id']) && isset($_SESSION['login'])) {
            $num_string = $this->input->get('del');
            $file = $this->files_model->getFileById($num_string);
            $fileway = './uploads/' . $file['hash_name'][0] . '/' . $file['hash_name'][1] . '/' . $file['hash_name'];

            $sql = $this->files_model->deleteFileById($num_string);

            unlink($fileway);
        }
        header('Location: /files/filesharing');
    }
    public function download()
    {
        if(isset($_SESSION['id']) && isset($_SESSION['login'])) {
            $name = $this->input->get('name');
            $file = $this->files_model->getFileByShortName($name);
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