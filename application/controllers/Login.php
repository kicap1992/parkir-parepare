<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();
    $this->load->model('model');
    $this->load->model('m_tabel_ss');
  }

  public function index()
  {
    $this->load->view('login/index');
  }
}
?>