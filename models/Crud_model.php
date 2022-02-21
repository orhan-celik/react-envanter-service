<?php

class Crud_Model extends CI_Model{

    public function __construct(){
        parent::__construct();
    }

    public function get($where = array(),$orderby,$table,$db){
        return $db->order_by($orderby)->where($where)->get($table)->result();
    }

    public function get_single($where = array(),$orderby,$table){
        return $this->db->order_by($orderby)->where($where)->limit(1)->get($table)->row();
    }

    public function insert($data = array(),$table,$db){
        if(!$data) return false;

        $db->trans_start();
        $db->insert($table,$data);
        if($db->trans_status() === FALSE){
            return false;
        }else{
            $db->trans_commit();
            $db->trans_complete();
            return true;
        }
    }

    public function update($data = array(),$where =  array(),$table){
        if(!$data || !$where) return false;

        $this->db->trans_start();
        $this->db->set($data)->where($where)->update($table);
        if($this->db->trans_status() === FALSE){
            return false;
        }else{
            $this->db->trans_commit();
            $this->db->trans_complete();
            return true;
        }
    }

    public function total($where = array(),$table){
        return $this->db->where($where)->count_all_results($table);
    }

}

?>