<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, PUT');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');

defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Api extends RestController
{
  function __construct()
  {
    parent::__construct();
    $this->load->model('model');;
    // $this->db->query("SET sql_mode = '' ");
    date_default_timezone_set("Asia/Kuala_Lumpur");
  }

  public function index_get()
  {
    $this->response(['message' => 'Halo Bosku', 'status' => true], 200);
    // redirect(base_url());

  }

  // -----------------------------------------------------------------------------------------------------------

  public function login_get()
  {
    $username = $this->get('username');
    $password = $this->get('password');

    if ($username != "admin" && $password != "admin") return
      $this->response(['message' => 'Username atau Password Salah', 'status' => false], 400);

    // set session
    $this->session->set_userdata('level', 'admin');    
    $this->response(['message' => 'Login Berhasil', 'status' => true], 200);
  }

  public function login_tukang_parkir_get()
  {
    $username = $this->get('username');
    $password = $this->get('password');

    $cek_login = $this->model->tampil_data_where('tb_tukang_parkir', array('nik' => $username, 'password' => $password))->result();

    if (count($cek_login) == 0) return
      $this->response(['message' => 'Username atau Password Salah', 'status' => false], 400);

    $cek_data = $this->model->tampil_data_where('tb_area_parkir', array('nik' => $cek_login[0]->nik))->result();

    $this->session->set_userdata('nik', $cek_login[0]->nik);
    $this->session->set_userdata('nama', $cek_login[0]->nama);
    $this->session->set_userdata('id_parkir', $cek_data[0]->id_parkir);

    // set session
    $this->session->set_userdata('level', 'tukang_parkir');    
    $this->response(['message' => 'Login Berhasil', 'status' => true], 200);
  }

  public function kabupaten_maps_get()
  {
    $check_data = $this->model->tampil_data_where('tb_kabupaten', array('no' => '1'))->result();

    $data = $check_data[0]->kordinat;

    $this->response(['message' => 'Berhasil', 'status' => true, 'data' => json_decode($data,true)], 200);
  }

  public function all_kecamatan_get(){
    $check_data = $this->model->tampil_data_keseluruhan('tb_kecamatan')->result();

    $this->response(['message' => 'Berhasil', 'status' => true, 'data' => $check_data], 200);
  }

  public function all_kelurahan_get(){
    $id_kecamatan = $this->get('id_kecamatan');
    $check_data = $this->model->tampil_data_where('tb_kelurahan' , ['kecamatan' => $id_kecamatan])->result();
    $check_maps = $this->model->tampil_data_where('tb_kecamatan' , ['no' => $id_kecamatan])->result();

    if(count($check_data) == 0) return $this->response(['message' => 'Data Tidak Ditemukan', 'status' => false], 400);

    $maps = $check_maps[0]->kordinat;

    $this->response(['message' => 'Berhasil', 'status' => true, 'data' => $check_data , 'maps' => json_decode($maps,true)], 200);
  }

  public function kelurahan_maps_get(){
    $id_kelurahan = $this->get('id_kelurahan');
    $check_data = $this->model->tampil_data_where('tb_kelurahan' , ['no' => $id_kelurahan])->result();

    if(count($check_data) == 0) return $this->response(['message' => 'Data Tidak Ditemukan', 'status' => false], 400);

    $maps = $check_data[0]->kordinat;

    $this->response(['message' => 'Berhasil', 'status' => true, 'data' => json_decode($maps,true)], 200);
  }

  public function tambah_parkir_post(){
    $id_kecamatan = $this->post('kecamatan_id');
    $id_kelurahan = $this->post('kelurahan_id');
    $kordinat = $this->post('kordinat');
    $center = $this->post('center');
    $alamat = $this->post('alamat');
    $luas = $this->post('luas');
    $nik = $this->post('nik');
    $nama = $this->post('nama');
    $biaya_motor = $this->post('biaya_motor');
    $biaya_mobil = $this->post('biaya_mobil');

    if($id_kecamatan == null || $id_kelurahan == null || $kordinat == null || $center == null || $alamat == null || $luas == null) return $this->response(['message' => 'Data Tidak Boleh Kosong', 'status' => false], 400);
    

    $array = [
      // 'kecamatan' => $id_kecamatan,
      'id_kelurahan' => $id_kelurahan,
      'kordinat' => $kordinat,
      'center' => $center,
      'alamat' => $alamat,
      'luas' => $luas,
    ];

    $this->model->insert('tb_tukang_parkir', array('nik' => $nik, 'nama' => $nama, 'password' => 12345678));
    $this->model->insert('tb_area_parkir', $array);
    
    

    $this->response(['message' => 'Berhasil', 'status' => true, 'data' => $array], 200);
    
  }

  public function area_parkir_get(){
    $check_data = $this->model->tampil_data_keseluruhan('tb_area_parkir')->result();

    $this->response(['message' => 'Berhasil', 'status' => true, 'data' => $check_data], 200);
  }


  public function area_parkir_delete(){
    $id = $this->delete('id');

    if($id == null) return $this->response(['message' => 'Data Tidak Boleh Kosong', 'status' => false], 400);

    $this->model->delete('tb_area_parkir', ['id_parkir' => $id]);

    $this->response(['message' => 'Berhasil', 'status' => true,"id" => $id], 200);
  }

  public function data_tukang_parkir_get(){
    $check_data = $this->model->custom_query("SELECT * FROM tb_tukang_parkir a join tb_area_parkir b on a.nik = b.nik  where a.nik = '".$this->session->userdata('nik')."'")->result();
    if (count($check_data) == 0) return $this->response(['message' => 'Data Tidak Ditemukan', 'status' => false], 400);

    $check_kelurahan = $this->model->tampil_data_where('tb_kelurahan', ['no' => $check_data[0]->id_kelurahan])->result();
    $check_kecamatan = $this->model->tampil_data_where('tb_kecamatan', ['no' => $check_kelurahan[0]->kecamatan])->result();

    $kecamatan = $check_kecamatan[0]->kecamatan;
    $kelurahan = $check_kelurahan[0]->kelurahan;

    $this->response(['message' => 'Berhasil', 'status' => true, 'data' => $check_data[0], 'kecamatan' => $kecamatan, 'kelurahan' => $kelurahan], 200);

    // $this->response(['message' => 'Berhasil', 'status' => true, 'data' => "ini dia"], 200);
  }

  public function ganti_password_post(){
    $password_lama = $this->post('password_lama');
    $password_baru = $this->post('password_baru');

    $cek_data = $this->model->tampil_data_where('tb_tukang_parkir', ['nik' => $this->session->userdata('nik')])->result();

    if(count($cek_data) == 0) return $this->response(['message' => 'Data Tidak Ditemukan', 'status' => false], 400);

    if($cek_data[0]->password != $password_lama) return $this->response(['message' => 'Password Lama Salah', 'status' => false], 200);

    $this->model->update('tb_tukang_parkir', ['nik' => $this->session->userdata('nik')], ['password' => $password_baru]);

    $this->response(['message' => 'Berhasil', 'status' => true], 200);
  }

  public function kritik_post(){
    $id_parkir = $this->post('id_parkir');
    $nama = $this->post('nama');
    $kritik = $this->post('kritik');

    $cek_data = $this->model->tampil_data_where('tb_area_parkir', ['id_parkir' => $id_parkir])->result();

    if(count($cek_data) == 0) return $this->response(['message' => 'Data Tidak Ditemukan', 'status' => false], 400);

    $this->model->insert('tb_kritik', ['id_parkir' => $id_parkir, 'kritik' => $kritik, 'nama' => $nama]);

    $this->response(['message' => 'Kritik Berhasil Dikirim', 'status' => true], 200);
  }


  public function kritik_get(){
    $id_kritik = $this->get('id_kritik');

    $cek_data = $this->model->tampil_data_where('tb_kritik', ['id_kritik' => $id_kritik])->result();

    if(count($cek_data) == 0) return $this->response(['message' => 'Data Tidak Ditemukan', 'status' => false], 400);

    $this->response(['message' => 'Berhasil', 'status' => true, 'data' => $cek_data[0]], 200);
  }

}
