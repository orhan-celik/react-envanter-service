<?php
header('Access-Control-Allow-Origin: *');
defined('BASEPATH') OR exit('No direct script access allowed');

class Model extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
    }

    // GET ALL
    public function index(){
        sleep(2);
        $categories = $this->crud_model->get(array('status!='=>'2'),'inserted_date DESC','tge_modeller',$this->db);
        echo json_encode($categories);
        return;
    }

    // ADD NEW MODEL
    public function add(){

        $_POST = $data = (array)json_decode( file_get_contents('php://input') );

        // Validasyon Kuralları
        $form_kural = array(
            array(
                'field' => 'title',
                'label' => 'Başlık',
                'rules' => 'trim|required|max_length[10]|min_length[2]'
            ),
            array(
                'field' => 'brand',
                'label' => 'Marka',
                'rules' => 'trim|required|numeric'
            )
        );

        // Validasyon Mesajları
        $this->form_validation->set_message(
            array(
                "required"      => "{field} alanı boş bırakılmamalıdır.",
                "valid_email"   => "{field} formatı hatalı",
                "matches"       => "{field} ve {param} alanları birbiri ile uyuşmuyor",
                "is_unique"     => "{field} başka bir kullanıcı tarafından kullanılıyor.",
                "max_length"    => "{field} en fazla {param} karakter olmalıdır.",
                "min_length"    => "{field} en az {param} karakter olmalıdır."
            )
        );

        // Validasyon Uyarla
        $this->form_validation->set_rules($form_kural);

        // Validasyondan geçemediyse
        if(!$this->form_validation->run()){
            // İstek atılan yerde karşıla
            echo json_encode(array(
                "result"    => "error",
                "title"     => "İşlem Sonucu",
                "invalid"   => $this->form_validation->error_array()
            ));
            return;
        }

        $now        = date('Y-m-d- H:i:s');

        $dataInsert = $this->crud_model->insert(
            array(
                'title'         => $data['title'],
                'inserted_date' => $now
            ),
            'tge_modeller',$this->db
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

    // DELETE MODEL
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

        $category = $this->crud_model->get_single(array("id"=>$id),'','tge_modeller');
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
            'tge_modeller'
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

    // UPDATE MODEL
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
        $category = $this->crud_model->get_single(array("id"=>$id),'','tge_modeller');
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
            'tge_modeller'
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
