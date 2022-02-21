<?php
header('Access-Control-Allow-Origin: *');
defined('BASEPATH') OR exit('No direct script access allowed');

class Brand extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    // GET ALL
    public function index(){
        sleep(2);
        $categories = $this->crud_model->get(array('status!='=>'2'),'inserted_date DESC','tge_markalar',$this->db);
        echo json_encode($categories);
        return;
    }

    // ADD NEW CATEGORY
    public function add(){
        $data = (array)json_decode( file_get_contents('php://input') );

        $now        = date('Y-m-d- H:i:s');

        $dataInsert = $this->crud_model->insert(
            array(
                'title'         => $data['title'],
                'inserted_date' => $now
            ),
            'tge_markalar',$this->db
        );

        // İstek atılan yerde karşıla
        if(!$dataInsert){
            // İstek atılan yerde karşıla
            echo json_encode(array(
                "result"    => "error",
                "title"     => "İşlem Sonucu",
                "message"   => "Kayıt eklenirken bir hata oluştu"
            ));
            return;
        }

        $data['id']             = $this->db->insert_id();
        $data['inserted_date']  = $now;
        $data['result']         = 'success';
        echo json_encode($data);
        return;
    }

    // DELETE CATEGORY
    public function delete(){
        $data = (array)json_decode( file_get_contents('php://input') );

        $id = $data['id'];

        if(!$id || $id < 1){
            // İstek atılan yerde karşıla
            echo json_encode(array(
                "result" => "error",
                "title" => "Hata",
                "message" => "Makale No Eksik"
            ));

            return;
        }

        $category = $this->crud_model->get_single(array("id"=>$id),'','tge_markalar');
        if(!$category){
            // İstek atılan yerde karşıla
            echo json_encode(array(
                "result" => "error",
                "title" => "Hata",
                "message" => "Geçersiz Makale Numarası gönderdiniz, IP Adresiniz Bloklanacaktır."
            ));

            return;
        }

        $categoryDelete = $this->crud_model->update(
            array('status'=>2,'deleted_date'=>date('Y-m-d H:i:s')),
            array('id'=>$id),
            'tge_markalar'
        );

        if(!$categoryDelete){
            // İstek atılan yerde karşıla
            echo json_encode(array(
                "result" => "error",
                "title" => "Hata",
                "message" => "Makale silinirken bir hata oluştu."
            ));

            return;
        }

        // İstek atılan yerde karşıla
        echo json_encode(array(
            "result" => "success",
            "title" => "İşlem Sonucu",
            "message" => "Makale başarılı bir şekilde silindi, Yönlendiriliyorsunuz",
            "redirect"  => base_url("admin/post")
        ));

    }

    // UPDATE CATEGORY
    public function update(){

        $data = (array)json_decode( file_get_contents('php://input') );

        $id     = $data['id'];
        $title  = $data['title'];

        if(!$id || $id < 1){
            // İstek atılan yerde karşıla
            echo json_encode(array(
                "result" => "error",
                "title" => "Hata",
                "message" => "Makale No Eksik"
            ));

            return;
        }

        // Makele Id gerçekten DB de varmı kontrol et.
        $category = $this->crud_model->get_single(array("id"=>$id),'','tge_markalar');
        if(!$category){
            // İstek atılan yerde karşıla
            echo json_encode(array(
                "result" => "error",
                "title" => "Hata",
                "message" => "Geçersiz Makale Numarası gönderdiniz, IP Adresiniz Bloklanacaktır."
            ));

            return;
        }

        $categoryGuncelle = $this->crud_model->update(
            array(
                'title'             => $title,
                'updated_date'    => date('Y-m-d H:i:s'),
            ),
            array('id'=>$id),
            'tge_markalar'
        );

        // İstek atılan yerde karşıla
        if(!$categoryGuncelle){
            // İstek atılan yerde karşıla
            echo json_encode(array(
                "result" => "error",
                "title" => "İşlem Sonucu",
                "valid" => "Kayıt güncellenirken bir hata oluştu"
            ));
            return;
        }

        echo json_encode(array(
            "result" => "success",
            "title" => "İşlem Sonucu",
            "message" => "Makale başarılı bir şekilde güncellendi, Yönlendiriliyorsunuz"
        ));
        return;
    }

}
