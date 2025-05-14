<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('produto_model');
    }
    
    public function index() {
        $data['produtos'] = $this->produto_model->get_with_estoque();
        $data['title'] = 'Mini ERP - Página Inicial';
        
        $this->load->view('templates/header', $data);
        $this->load->view('home/index', $data);
        $this->load->view('templates/footer');
    }
} 