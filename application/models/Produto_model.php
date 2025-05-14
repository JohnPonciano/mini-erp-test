<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produto_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    
    public function get_all() {
        // Pegando todos os produtos com suas variações - tudo num pacote só!
        $this->db->select('produtos.*, COUNT(variacoes.id) as total_variacoes');
        $this->db->from('produtos');
        $this->db->join('variacoes', 'variacoes.produto_id = produtos.id', 'left');
        $this->db->group_by('produtos.id');
        $this->db->order_by('produtos.nome', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_by_id($id) {
        // Encontrando produto pelo ID - simples assim!
        $this->db->where('id', $id);
        $query = $this->db->get('produtos');
        return $query->row();
    }
    
    public function insert($data) {
        // Produto novo chegando! Bora cadastrar.
        $this->db->insert('produtos', $data);
        return $this->db->insert_id();
    }
    
    public function update($id, $data) {
        // Atualizando o produto - mudou preço? nome? descrição?
        $this->db->where('id', $id);
        return $this->db->update('produtos', $data);
    }
    
    public function delete($id) {
        // Tchau produto! Foi bom enquanto durou.
        $this->db->where('id', $id);
        return $this->db->delete('produtos');
    }
    
    public function get_with_estoque() {
        // Busca os produtos já com o estoque - economia de consultas!
        $this->db->select('produtos.*, estoque.quantidade');
        $this->db->from('produtos');
        $this->db->join('estoque', 'estoque.produto_id = produtos.id AND estoque.variacao_id IS NULL', 'left');
        $this->db->order_by('produtos.nome', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_variacoes($produto_id) {
        // Buscando todas as variações do produto - cores, tamanhos, etc.
        $this->db->select('variacoes.*, estoque.quantidade');
        $this->db->from('variacoes');
        $this->db->join('estoque', 'estoque.variacao_id = variacoes.id', 'left');
        $this->db->where('variacoes.produto_id', $produto_id);
        $query = $this->db->get();
        return $query->result();
    }
    
    public function add_variacao($data) {
        // Nova variação? Vermelho, azul, P, M, G...?
        $this->db->insert('variacoes', $data);
        return $this->db->insert_id();
    }
    
    public function update_variacao($id, $data) {
        // Mudou o nome da variação? Era "Vermelho" e agora é "Vermelho Ferrari"?
        $this->db->where('id', $id);
        return $this->db->update('variacoes', $data);
    }
    
    public function delete_variacao($id) {
        // Essa variação não existe mais - ninguém comprava mesmo!
        $this->db->where('id', $id);
        return $this->db->delete('variacoes');
    }
    
    public function get_variacao_by_id($id) {
        // Buscando uma variação específica pelo ID
        $this->db->where('id', $id);
        $query = $this->db->get('variacoes');
        return $query->row();
    }
} 