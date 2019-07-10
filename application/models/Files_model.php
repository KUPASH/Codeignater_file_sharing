<?php

class Files_model extends CI_Model{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    public function insertNewFile($realName,$filename,$imageKey)
    {
        $data = ['real_name' => $realName,
            'hash_name' => $filename,
            'image_key' => $imageKey,
            'user_id' => $_SESSION['id']];
        $sql = $this->db->insert('files', $data);
    }
    public function getAllFilesUser()
    {
        $sql = $this->db->select('*')->from('files')->where('user_id',$_SESSION['id'])->get();
        $row = $sql->result();
        return $row;
    }
    public function getFileById($num_string)
    {
        $sql = $this->db->select('*')->
                            from('files')->
                            where('user_id',$_SESSION['id'])->
                            where('id',$num_string)->get();

        $file = $sql->row_array();
        return $file;
    }
    public function deleteFileById($num_string)
    {
        $sql = $this->db->delete('files',['user_id' => $_SESSION['id'], 'id' => $num_string]);
    }
    public function getFileByShortName($name)
    {
        $sql = $this->db->select('*')->
        from('files')->
        where('user_id',$_SESSION['id'])->
        where('image_key',$name)->get();
        $file = $sql->row_array();
        return $file;
    }
}