<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cupons extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('cupom_model');
    }
    
    public function index() {
        $data['cupons'] = $this->cupom_model->get_all();
        $data['title'] = 'Gerenciar Cupons';
        
        $this->load->view('templates/header', $data);
        $this->load->view('cupons/index', $data);
        $this->load->view('templates/footer');
    }
    
    public function create() {
        $data['title'] = 'Criar Novo Cupom';
        
        $this->form_validation->set_rules('codigo', 'Código', 'required|is_unique[cupons.codigo]');
        $this->form_validation->set_rules('desconto', 'Desconto', 'required|numeric');
        $this->form_validation->set_rules('tipo', 'Tipo', 'required');
        $this->form_validation->set_rules('data_validade', 'Data de Validade', 'required');
        
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('cupons/create', $data);
            $this->load->view('templates/footer');
        } else {
            $cupom_data = array(
                'codigo' => strtoupper($this->input->post('codigo')),
                'desconto' => $this->input->post('desconto'),
                'tipo' => $this->input->post('tipo'),
                'valor_minimo' => $this->input->post('valor_minimo') ? $this->input->post('valor_minimo') : NULL,
                'data_validade' => $this->input->post('data_validade'),
                'status' => true
            );
            
            $this->cupom_model->insert($cupom_data);
            $this->session->set_flashdata('success', 'Cupom criado com sucesso!');
            redirect('cupons');
        }
    }
    
    public function edit($id) {
        $data['cupom'] = $this->cupom_model->get_by_id($id);
        
        if (empty($data['cupom'])) {
            show_404();
        }
        
        $data['title'] = 'Editar Cupom: ' . $data['cupom']->codigo;
        
        $this->form_validation->set_rules('codigo', 'Código', 'required');
        $this->form_validation->set_rules('desconto', 'Desconto', 'required|numeric');
        $this->form_validation->set_rules('tipo', 'Tipo', 'required');
        $this->form_validation->set_rules('data_validade', 'Data de Validade', 'required');
        
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('cupons/edit', $data);
            $this->load->view('templates/footer');
        } else {
            $cupom_data = array(
                'codigo' => strtoupper($this->input->post('codigo')),
                'desconto' => $this->input->post('desconto'),
                'tipo' => $this->input->post('tipo'),
                'valor_minimo' => $this->input->post('valor_minimo') ? $this->input->post('valor_minimo') : NULL,
                'data_validade' => $this->input->post('data_validade'),
                'status' => $this->input->post('status') ? true : false
            );
            
            $this->cupom_model->update($id, $cupom_data);
            $this->session->set_flashdata('success', 'Cupom atualizado com sucesso!');
            redirect('cupons');
        }
    }
    
    public function delete($id) {
        $this->cupom_model->delete($id);
        $this->session->set_flashdata('success', 'Cupom excluído com sucesso!');
        redirect('cupons');
    }
} 