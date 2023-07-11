<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('model');
    $this->load->model('m_tabel_ss');

    
  }


  function index()
  {
    $main['header'] = "Area Parkir Parepare";
    // $main['content'] = "admin/content/index";
    $this->load->view('user/index', $main);
  }

  function parkir_index()
  {
    if ($this->input->post('proses') == "table_area_parkir") {
      $list = $this->m_tabel_ss->get_datatables(array('luas','alamat'), array(null, 'alamat',null, null, 'luas', null), array('id_parkir' => 'desc'), "tb_area_parkir", null,null, "*");
      $data = array();
      $no = $_POST['start'];
      foreach ($list as $field) {

        

        $kecamatan = '';
        $kelurahan = '';
        $cek_kelurahan = $this->model->tampil_data_where('tb_kelurahan', array('no' => $field->id_kelurahan))->result();
        $kelurahan = $cek_kelurahan[0]->kelurahan;
        $cek_kecamatan = $this->model->tampil_data_where('tb_kecamatan', array('no' => $cek_kelurahan[0]->kecamatan))->result();
        $kecamatan = $cek_kecamatan[0]->kecamatan;

        

        $no++;
        $row = array();
        $row[] = $no;
        $row[] = $field->alamat;
        $row[] = $kecamatan;
        $row[] = $kelurahan;
        $row[] = $field->luas . " m<sup>2</sup>";
        $row[] = "Rp." .  number_format($field->biaya_motor, 0, ',', '.');
        $row[] = "Rp." .  number_format($field->biaya_mobil, 0, ',', '.');
        $data[] = $row;
      }

      $output = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->m_tabel_ss->count_all("tb_area_parkir", null, null, "*"),
        "recordsFiltered" => $this->m_tabel_ss->count_filtered(array('luas','alamat'), array(null, 'alamat',null, null, 'luas', null), array('id_parkir' => 'desc'), "tb_area_parkir", null,null, "*"),
        "data" => $data,
      );
      //output dalam format JSON
      echo json_encode($output);
    } else {
      $main['header'] = "Pengaturan Parkir";
      // $main['content'] = "admin/content/pengaturan_parkir";
      $this->load->view('admin/content/parkir', $main);
    }
  }
  
}
