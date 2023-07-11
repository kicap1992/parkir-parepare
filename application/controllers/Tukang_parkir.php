<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tukang_parkir extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('model');
    $this->load->model('m_tabel_ss');

    // if ($this->session->userdata('level') != "tukang_parkir") {
    //     // remove session
    //     $this->session->unset_userdata('level');
    //     redirect(base_url("tukang_parkir/login"));
    // }
  }


  function index()
  {
    if ($this->session->userdata('level') != "tukang_parkir") {
      // remove session
      $this->session->unset_userdata(array('level', 'nik', 'nama'));
      redirect(base_url("tukang_parkir/login"));
    }
    $main['header'] = "Area Parkir Parepare";
    // $main['content'] = "admin/content/index";
    $this->load->view('tukang_parkir/index', $main);
  }

  function kritik()
  {
    if ($this->session->userdata('level') != "tukang_parkir") {
      // remove session
      $this->session->unset_userdata(array('level', 'nik', 'nama'));
      redirect(base_url("tukang_parkir/login"));
    }

    if ($this->input->post('proses') == "table_area_parkir") {
      $list = $this->m_tabel_ss->get_datatables(array('created_at','nama'), array('created_at','nama',null), array('id_kritik' => 'desc'), "tb_kritik", null,['id_parkir' =>$this->session->userdata('id_parkir')], "*");
      $data = array();
      $no = $_POST['start'];
      foreach ($list as $field) {

        
        

        $no++;
        $row = array();
        $row[] = $field->created_at;
        $row[] = $field->nama;
        $row[] = "<center><button type='button' onclick='lihat_kritik(".$field->id_kritik.")' title='Hapus Area Parkir' class='btn btn-info btn-circle btn-sm waves-effect waves-light'><i class='ico zmdi zmdi-info'></i></button></center>";
        
        $data[] = $row;
      }

      $output = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->m_tabel_ss->count_all("tb_kritik", null, null, "*"),
        "recordsFiltered" => $this->m_tabel_ss->count_filtered(array('created_at','nama'), array('created_at','nama',null), array('id_kritik' => 'desc'), "tb_area_parkir", null,['id_parkir' =>$this->session->userdata('id_parkir')], "*"),
        "data" => $data,
      );
      //output dalam format JSON
      echo json_encode($output);
    }  else {
      $main['header'] = "Kritik Dan Komen";
      // $main['content'] = "admin/content/index";
      $this->load->view('tukang_parkir/content/kritik', $main);
    }
  }

  function password()
  {
    if ($this->session->userdata('level') != "tukang_parkir") {
      // remove session
      $this->session->unset_userdata(array('level', 'nik', 'nama'));
      redirect(base_url("tukang_parkir/login"));
    }

    $main['header'] = "Ganti Password";
    // $main['content'] = "admin/content/index";
    $this->load->view('tukang_parkir/content/password', $main);
  }

  function login()
  {
    $this->load->view('tukang_parkir/login');
  }

  function logout()
  {
    // remove session
    $this->session->unset_userdata(array('level', 'nik', 'nama'));
    redirect(base_url("tukang_parkir/login"));
  }
}
