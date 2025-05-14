<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cupom_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    
    public function get_all() {
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get('cupons');
        return $query->result();
    }
    
    public function get_by_id($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('cupons');
        return $query->row();
    }
    
    public function get_by_codigo($codigo) {
        $this->db->where('codigo', $codigo);
        $this->db->where('status', true);
        $this->db->where('data_validade >=', date('Y-m-d'));
        $query = $this->db->get('cupons');
        return $query->row();
    }
    
    public function insert($data) {
        $this->db->insert('cupons', $data);
        return $this->db->insert_id();
    }
    
    public function update($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('cupons', $data);
    }
    
    public function delete($id) {
        $this->db->where('id', $id);
        return $this->db->delete('cupons');
    }
    
    public function validate_cupom($codigo, $subtotal) {
        $cupom = $this->get_by_codigo($codigo);
        
        if (!$cupom) {
            return array('valid' => false, 'message' => 'Cupom inválido ou expirado');
        }
        
        if ($cupom->valor_minimo && $subtotal < $cupom->valor_minimo) {
            return array(
                'valid' => false, 
                'message' => 'Valor mínimo para este cupom é R$ ' . number_format($cupom->valor_minimo, 2, ',', '.')
            );
        }
        
        // Calculate discount
        $discount = 0;
        if ($cupom->tipo == 'percentual') {
            $discount = $subtotal * ($cupom->desconto / 100);
        } else {
            $discount = $cupom->desconto;
        }
        
        return array(
            'valid' => true,
            'cupom' => $cupom,
            'discount' => $discount
        );
    }
} 