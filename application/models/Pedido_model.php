<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pedido_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    
    public function get_all() {
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get('pedidos');
        return $query->result();
    }
    
    public function get_by_id($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('pedidos');
        return $query->row();
    }
    
    public function insert($data) {
        $this->db->insert('pedidos', $data);
        return $this->db->insert_id();
    }
    
    public function update($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('pedidos', $data);
    }
    
    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete('pedidos');
    }
    
    public function add_item($data) {
        return $this->db->insert('pedido_itens', $data);
    }
    
    public function get_itens($pedido_id) {
        $this->db->select('pedido_itens.*, produtos.nome as produto_nome, variacoes.nome as variacao_nome');
        $this->db->from('pedido_itens');
        $this->db->join('produtos', 'produtos.id = pedido_itens.produto_id');
        $this->db->join('variacoes', 'variacoes.id = pedido_itens.variacao_id', 'left');
        $this->db->where('pedido_id', $pedido_id);
        $query = $this->db->get();
        return $query->result();
    }
    
    public function calcular_frete($subtotal) {
        if ($subtotal >= 52 && $subtotal <= 166.59) {
            return 15.00;
        } else if ($subtotal > 200.00) {
            return 0.00;
        } else {
            return 20.00;
        }
    }
    
    public function update_status($id, $status) {
        $this->db->where('id', $id);
        return $this->db->update('pedidos', array('status' => $status));
    }
} 