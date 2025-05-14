<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Produtos extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Carregando os modelos necessários para brincar com produtos
        $this->load->model('produto_model');
        $this->load->model('estoque_model');
    }
    
    public function index() {
        // Buscando todos os produtos com seus estoques para exibir na listagem
        $data['produtos'] = $this->produto_model->get_with_estoque();
        $data['title'] = 'Listagem de Produtos';
        
        $this->load->view('templates/header', $data);
        $this->load->view('produtos/index', $data);
        $this->load->view('templates/footer');
    }
    
    public function create() {
        $data['title'] = 'Cadastrar Novo Produto';
        
        // Regras de validação - não dá pra cadastrar produto sem nome e preço, né?
        $this->form_validation->set_rules('nome', 'Nome', 'required');
        $this->form_validation->set_rules('preco', 'Preço', 'required|numeric');
        $this->form_validation->set_rules('quantidade', 'Quantidade', 'required|numeric');
        
        if ($this->form_validation->run() === FALSE) {
            // Formulário com erro ou primeira vez que abre
            $this->load->view('templates/header', $data);
            $this->load->view('produtos/create', $data);
            $this->load->view('templates/footer');
        } else {
            // Primeiro cadastra o produto básico
            $produto_id = $this->produto_model->insert([
                'nome' => $this->input->post('nome'),
                'preco' => $this->input->post('preco')
            ]);
            
            // Depois registra o estoque inicial
            $this->estoque_model->update_quantidade_produto(
                $produto_id, 
                $this->input->post('quantidade')
            );
            
            // Se tiver variações, vamos cadastrar cada uma
            if ($this->input->post('variacoes_nomes')) {
                $variacoes_nomes = $this->input->post('variacoes_nomes');
                $variacoes_qtds = $this->input->post('variacoes_qtds');
                
                foreach ($variacoes_nomes as $key => $nome) {
                    if (!empty($nome)) {
                        // Cadastra a variação (cor, tamanho, etc)
                        $variacao_id = $this->produto_model->add_variacao([
                            'produto_id' => $produto_id,
                            'nome' => $nome
                        ]);
                        
                        // E registra o estoque dessa variação
                        if (isset($variacoes_qtds[$key])) {
                            $this->estoque_model->update_quantidade_variacao(
                                $variacao_id,
                                $variacoes_qtds[$key]
                            );
                        }
                    }
                }
            }
            
            // Tudo pronto, produto cadastrado!
            $this->session->set_flashdata('success', 'Produto cadastrado com sucesso!');
            redirect('produtos');
        }
    }
    
    public function edit($id) {
        // Buscando o produto que será editado
        $data['produto'] = $this->produto_model->get_by_id($id);
        
        if (empty($data['produto'])) {
            // Produto não existe? Página 404 nele!
            show_404();
        }
        
        // Pegando estoque e variações para exibir no formulário
        $data['estoque'] = $this->estoque_model->get_by_produto($id);
        $data['variacoes'] = $this->produto_model->get_variacoes($id);
        $data['title'] = 'Editar Produto: ' . $data['produto']->nome;
        
        // Mesmas regras de validação do cadastro
        $this->form_validation->set_rules('nome', 'Nome', 'required');
        $this->form_validation->set_rules('preco', 'Preço', 'required|numeric');
        $this->form_validation->set_rules('quantidade', 'Quantidade', 'required|numeric');
        
        if ($this->form_validation->run() === FALSE) {
            // Exibe o formulário de edição
            $this->load->view('templates/header', $data);
            $this->load->view('produtos/edit', $data);
            $this->load->view('templates/footer');
        } else {
            // Atualiza os dados básicos do produto
            $this->produto_model->update($id, [
                'nome' => $this->input->post('nome'),
                'preco' => $this->input->post('preco')
            ]);
            
            // Atualiza o estoque
            $this->estoque_model->update_quantidade_produto(
                $id, 
                $this->input->post('quantidade')
            );
            
            // Atualizando as variações existentes
            if ($this->input->post('variacao_ids')) {
                $variacao_ids = $this->input->post('variacao_ids');
                $variacao_nomes = $this->input->post('variacao_nomes');
                $variacao_qtds = $this->input->post('variacao_qtds');
                
                foreach ($variacao_ids as $key => $variacao_id) {
                    // Atualiza o nome da variação
                    if (isset($variacao_nomes[$key])) {
                        $this->produto_model->update_variacao($variacao_id, [
                            'nome' => $variacao_nomes[$key]
                        ]);
                    }
                    
                    // E atualiza o estoque dela
                    if (isset($variacao_qtds[$key])) {
                        $this->estoque_model->update_quantidade_variacao(
                            $variacao_id,
                            $variacao_qtds[$key]
                        );
                    }
                }
            }
            
            // Adicionando novas variações, se houver
            if ($this->input->post('novas_variacoes_nomes')) {
                $novas_nomes = $this->input->post('novas_variacoes_nomes');
                $novas_qtds = $this->input->post('novas_variacoes_qtds');
                
                foreach ($novas_nomes as $key => $nome) {
                    if (!empty($nome)) {
                        // Adiciona a nova variação
                        $variacao_id = $this->produto_model->add_variacao([
                            'produto_id' => $id,
                            'nome' => $nome
                        ]);
                        
                        // E registra seu estoque inicial
                        if (isset($novas_qtds[$key])) {
                            $this->estoque_model->update_quantidade_variacao(
                                $variacao_id,
                                $novas_qtds[$key]
                            );
                        }
                    }
                }
            }
            
            // Produto atualizado com sucesso!
            $this->session->set_flashdata('success', 'Produto atualizado com sucesso!');
            redirect('produtos');
        }
    }
    
    public function view($id) {
        // Buscando o produto para visualização
        $data['produto'] = $this->produto_model->get_by_id($id);
        
        if (empty($data['produto'])) {
            // Não encontrou? 404 nele!
            show_404();
        }
        
        // Pega o estoque e variações para exibir na página
        $data['estoque'] = $this->estoque_model->get_by_produto($id);
        $data['variacoes'] = $this->produto_model->get_variacoes($id);
        $data['title'] = $data['produto']->nome;
        
        // Carrega a página de detalhes do produto
        $this->load->view('templates/header', $data);
        $this->load->view('produtos/view', $data);
        $this->load->view('templates/footer');
    }
    
    public function delete($id) {
        // Apaga o produto e redireciona para a listagem
        // Aviso: isso deveria verificar se tem vendas antes de apagar!
        $this->produto_model->delete($id);
        $this->session->set_flashdata('success', 'Produto excluído com sucesso!');
        redirect('produtos');
    }
    
    public function add_to_cart() {
        // Pegando os dados do formulário de "adicionar ao carrinho"
        $produto_id = $this->input->post('produto_id');
        $variacao_id = $this->input->post('variacao_id');
        $quantidade = $this->input->post('quantidade') ? $this->input->post('quantidade') : 1;
        
        $produto = $this->produto_model->get_by_id($produto_id);
        
        if (!$produto) {
            // Produto não encontrado? Algo deu errado...
            $this->session->set_flashdata('error', 'Produto não encontrado');
            redirect('produtos');
        }
        
        // Verificando o estoque antes de adicionar
        if ($variacao_id) {
            // É uma variação específica
            $variacao = $this->produto_model->get_variacao_by_id($variacao_id);
            $estoque = $this->estoque_model->get_by_variacao($variacao_id);
            
            if (!$variacao || !$estoque || $estoque->quantidade < $quantidade) {
                // Sem estoque suficiente!
                $this->session->set_flashdata('error', 'Estoque insuficiente');
                redirect('produtos/view/' . $produto_id);
            }
            
            // Monta o item com a variação
            $item = array(
                'id'      => $produto_id . '_' . $variacao_id,
                'qty'     => $quantidade,
                'price'   => $produto->preco,
                'name'    => $produto->nome . ' - ' . $variacao->nome,
                'options' => array(
                    'produto_id' => $produto_id,
                    'variacao_id' => $variacao_id,
                    'variacao_nome' => $variacao->nome
                )
            );
        } else {
            // É o produto sem variação
            $estoque = $this->estoque_model->get_by_produto($produto_id);
            
            if (!$estoque || $estoque->quantidade < $quantidade) {
                // Sem estoque suficiente!
                $this->session->set_flashdata('error', 'Estoque insuficiente');
                redirect('produtos/view/' . $produto_id);
            }
            
            // Monta o item sem variação
            $item = array(
                'id'      => $produto_id,
                'qty'     => $quantidade,
                'price'   => $produto->preco,
                'name'    => $produto->nome,
                'options' => array(
                    'produto_id' => $produto_id,
                    'variacao_id' => null
                )
            );
        }
        
        // Coloca no carrinho e redireciona
        $this->cart->insert($item);
        $this->session->set_flashdata('success', 'Produto adicionado ao carrinho');
        redirect('carrinho');
    }
} 