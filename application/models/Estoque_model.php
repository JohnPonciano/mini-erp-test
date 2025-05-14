<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Estoque_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    
    public function get_all() {
        // Pega todo o estoque com informações dos produtos - visão geral do armazém
        $this->db->select('estoque.*, produtos.nome as produto_nome, variacoes.nome as variacao_nome');
        $this->db->from('estoque');
        $this->db->join('produtos', 'produtos.id = estoque.produto_id', 'left');
        $this->db->join('variacoes', 'variacoes.id = estoque.variacao_id', 'left');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_by_produto($produto_id) {
        // Buscando estoque do produto principal - sem variações
        $this->db->where('produto_id', $produto_id);
        $this->db->where('variacao_id IS NULL', null, false);
        $query = $this->db->get('estoque');
        return $query->row();
    }
    
    public function get_by_variacao($variacao_id) {
        // Quanto temos em estoque desta variação específica?
        $this->db->where('variacao_id', $variacao_id);
        $query = $this->db->get('estoque');
        return $query->row();
    }
    
    public function update_quantidade_produto($produto_id, $quantidade) {
        // Vamos ver se o registro já existe ou se é a primeira vez
        $this->db->where('produto_id', $produto_id);
        $this->db->where('variacao_id IS NULL', null, false);
        $query = $this->db->get('estoque');
        
        if ($query->num_rows() > 0) {
            // Já existe, só atualiza a quantidade
            $this->db->where('produto_id', $produto_id);
            $this->db->where('variacao_id IS NULL', null, false);
            return $this->db->update('estoque', array('quantidade' => $quantidade));
        } else {
            // Primeira vez que registra estoque deste produto
            return $this->db->insert('estoque', array(
                'produto_id' => $produto_id,
                'variacao_id' => null,
                'quantidade' => $quantidade
            ));
        }
    }
    
    public function update_quantidade_variacao($variacao_id, $quantidade) {
        // Precisamos do produto_id pra vincular esta variação
        $this->db->select('produto_id');
        $this->db->where('id', $variacao_id);
        $query = $this->db->get('variacoes');
        $variacao = $query->row();
        
        if (!$variacao) {
            // Variação não existe? Não dá pra registrar estoque
            return false;
        }
        
        // Confere se já existe estoque registrado pra essa variação
        $this->db->where('variacao_id', $variacao_id);
        $query = $this->db->get('estoque');
        
        if ($query->num_rows() > 0) {
            // Já tinha registro, só atualiza
            $this->db->where('variacao_id', $variacao_id);
            return $this->db->update('estoque', array('quantidade' => $quantidade));
        } else {
            // Primeira vez registrando estoque dessa variação
            return $this->db->insert('estoque', array(
                'produto_id' => $variacao->produto_id,
                'variacao_id' => $variacao_id,
                'quantidade' => $quantidade
            ));
        }
    }
    
    public function reduzir_estoque($produto_id, $variacao_id, $quantidade) {
        if ($variacao_id) {
            // Estamos vendendo uma variação específica
            $estoque = $this->get_by_variacao($variacao_id);
            if ($estoque && $estoque->quantidade >= $quantidade) {
                // Tem estoque suficiente, pode vender!
                $nova_quantidade = $estoque->quantidade - $quantidade;
                $this->update_quantidade_variacao($variacao_id, $nova_quantidade);
                return true;
            }
        } else {
            // Estamos vendendo o produto sem variação
            $estoque = $this->get_by_produto($produto_id);
            if ($estoque && $estoque->quantidade >= $quantidade) {
                // Tem estoque suficiente, pode vender!
                $nova_quantidade = $estoque->quantidade - $quantidade;
                $this->update_quantidade_produto($produto_id, $nova_quantidade);
                return true;
            }
        }
        return false;
    }
    
    public function restaurar_estoque($produto_id, $variacao_id, $quantidade) {
        if ($variacao_id) {
            // Devolvendo ao estoque uma variação específica (cancelamentos, devoluções)
            $estoque = $this->get_by_variacao($variacao_id);
            if ($estoque) {
                // De volta pro estoque - reposição
                $nova_quantidade = $estoque->quantidade + $quantidade;
                $this->update_quantidade_variacao($variacao_id, $nova_quantidade);
                return true;
            }
        } else {
            // Devolvendo ao estoque um produto sem variação
            $estoque = $this->get_by_produto($produto_id);
            if ($estoque) {
                // De volta pro estoque - reposição
                $nova_quantidade = $estoque->quantidade + $quantidade;
                $this->update_quantidade_produto($produto_id, $nova_quantidade);
                return true;
            }
        }
        return false;
    }
} 