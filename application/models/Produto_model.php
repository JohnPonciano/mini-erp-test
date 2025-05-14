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
        // Primeiro pega todos os produtos
        $this->db->select('produtos.*');
        $this->db->from('produtos');
        $produtos = $this->db->get()->result();
        
        // Agora para cada produto, calcula o estoque total
        foreach ($produtos as $produto) {
            // Pega o estoque base (sem variação)
            $this->db->select('COALESCE(SUM(quantidade), 0) as quantidade');
            $this->db->from('estoque');
            $this->db->where('produto_id', $produto->id);
            $this->db->where('variacao_id IS NULL', null, false);
            $estoque_base = $this->db->get()->row();
            $quantidade_base = $estoque_base ? $estoque_base->quantidade : 0;
            
            // Pega a soma do estoque das variações
            $this->db->select('COALESCE(SUM(quantidade), 0) as quantidade');
            $this->db->from('estoque');
            $this->db->where('produto_id', $produto->id);
            $this->db->where('variacao_id IS NOT NULL');
            $estoque_variacoes = $this->db->get()->row();
            $quantidade_variacoes = $estoque_variacoes ? $estoque_variacoes->quantidade : 0;
            
            // O estoque total é a soma dos dois
            $produto->quantidade = $quantidade_base + $quantidade_variacoes;
            
            // Verifica se tem variações
            $this->db->where('produto_id', $produto->id);
            $produto->tem_variacoes = $this->db->count_all_results('variacoes') > 0;
        }
        
        return $produtos;
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
        $this->db->select('variacoes.*, estoque.quantidade');
        $this->db->from('variacoes');
        $this->db->join('estoque', 'estoque.variacao_id = variacoes.id', 'left');
        $this->db->where('variacoes.id', $id);
        $query = $this->db->get();
        return $query->row();
    }
} 