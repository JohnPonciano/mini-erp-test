<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pedidos extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('pedido_model');
        $this->load->model('estoque_model');
    }
    
    public function index() {
        $data['pedidos'] = $this->pedido_model->get_all();
        $data['title'] = 'Gerenciar Pedidos';
        
        $this->load->view('templates/header', $data);
        $this->load->view('pedidos/index', $data);
        $this->load->view('templates/footer');
    }
    
    public function view($id) {
        $data['pedido'] = $this->pedido_model->get_by_id($id);
        
        if (empty($data['pedido'])) {
            show_404();
        }
        
        $data['itens'] = $this->pedido_model->get_itens($id);
        $data['title'] = 'Pedido #' . $id;
        
        $this->load->view('templates/header', $data);
        $this->load->view('pedidos/view', $data);
        $this->load->view('templates/footer');
    }
    
    public function confirmacao($id) {
        $data['pedido'] = $this->pedido_model->get_by_id($id);
        
        if (empty($data['pedido'])) {
            show_404();
        }
        
        $data['itens'] = $this->pedido_model->get_itens($id);
        $data['title'] = 'Confirmação de Pedido #' . $id;
        
        $this->load->view('templates/header', $data);
        $this->load->view('pedidos/confirmacao', $data);
        $this->load->view('templates/footer');
    }
    
    public function update_status() {
        $pedido_id = $this->input->post('pedido_id');
        $status = $this->input->post('status');
        
        if (!$pedido_id || !$status) {
            $this->session->set_flashdata('error', 'Dados inválidos');
            redirect('pedidos');
        }
        
        $this->pedido_model->update_status($pedido_id, $status);
        $this->session->set_flashdata('success', 'Status do pedido atualizado com sucesso');
        redirect('pedidos/view/' . $pedido_id);
    }
    
    // Webhook para receber atualizações externas de status
    public function webhook() {
        // Verificar se a requisição é POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }
        
        // Receber os dados JSON
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        
        if (!$data || !isset($data->id) || !isset($data->status)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid data']);
            return;
        }
        
        $pedido_id = $data->id;
        $status = $data->status;
        
        // Verificar se o pedido existe
        $pedido = $this->pedido_model->get_by_id($pedido_id);
        if (!$pedido) {
            http_response_code(404);
            echo json_encode(['error' => 'Order not found']);
            return;
        }
        
        // Se o status for "cancelado", excluir o pedido
        if ($status === 'cancelado') {
            // Restaurar estoque
            $itens = $this->pedido_model->get_itens($pedido_id);
            foreach ($itens as $item) {
                $this->estoque_model->restaurar_estoque(
                    $item->produto_id,
                    $item->variacao_id,
                    $item->quantidade
                );
            }
            
            $this->pedido_model->delete($pedido_id);
            
            http_response_code(200);
            echo json_encode(['success' => 'Order cancelled and deleted']);
            return;
        }
        
        // Atualizar o status do pedido
        $this->pedido_model->update_status($pedido_id, $status);
        
        http_response_code(200);
        echo json_encode(['success' => 'Order status updated']);
    }
} 