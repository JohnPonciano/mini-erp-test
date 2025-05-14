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
        
        // Regras de validação básicas
        $this->form_validation->set_rules('nome', 'Nome', 'required');
        $this->form_validation->set_rules('preco', 'Preço', 'required|numeric');
        
        // Verifica se tem estoque base
        $tem_estoque_base = (bool)$this->input->post('quantidade');
        
        // Se não tem estoque base, então pelo menos uma variação é obrigatória
        if (!$tem_estoque_base) {
            $this->form_validation->set_rules('variacoes_nomes[0]', 'Variação Base', 'required');
        }
        
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('produtos/create', $data);
            $this->load->view('templates/footer');
        } else {
            // Cadastra o produto básico
            $produto_id = $this->produto_model->insert([
                'nome' => $this->input->post('nome'),
                'preco' => $this->input->post('preco')
            ]);
            
            // Se tem quantidade base, registra o estoque
            $quantidade_base = $this->input->post('quantidade');
            if ($quantidade_base > 0) {
                $this->estoque_model->update_quantidade_produto(
                    $produto_id, 
                    $quantidade_base
                );
            }
            
            // Cadastra as variações se houver
            $variacoes_nomes = $this->input->post('variacoes_nomes');
            $variacoes_qtds = $this->input->post('variacoes_qtds');
            
            if ($variacoes_nomes) {
                foreach ($variacoes_nomes as $key => $nome) {
                    if (!empty($nome)) {
                        // Cadastra a variação
                        $variacao_id = $this->produto_model->add_variacao([
                            'produto_id' => $produto_id,
                            'nome' => $nome
                        ]);
                        
                        // Registra o estoque da variação se houver
                        if (isset($variacoes_qtds[$key]) && $variacoes_qtds[$key] > 0) {
                            $this->estoque_model->update_quantidade_variacao(
                                $variacao_id,
                                $variacoes_qtds[$key]
                            );
                        }
                    }
                }
            }
            
            $this->session->set_flashdata('success', 'Produto cadastrado com sucesso!');
            redirect('produtos');
        }
    }
    
    public function edit($id) {
        // Buscando o produto que será editado
        $data['produto'] = $this->produto_model->get_by_id($id);
        
        if (empty($data['produto'])) {
            show_404();
        }
        
        // Pegando estoque e variações para exibir no formulário
        $data['estoque'] = $this->estoque_model->get_by_produto($id);
        $data['variacoes'] = $this->produto_model->get_variacoes($id);
        $data['title'] = 'Editar Produto: ' . $data['produto']->nome;
        
        // Regras de validação básicas
        $this->form_validation->set_rules('nome', 'Nome', 'required');
        $this->form_validation->set_rules('preco', 'Preço', 'required|numeric');
        
        // Verifica se tem estoque base
        $tem_estoque_base = (bool)$this->input->post('quantidade');
        
        // Verifica se já existem variações
        $tem_variacoes = false;
        if (!empty($data['variacoes'])) {
            $tem_variacoes = true;
        }
        
        // Se não tem estoque base e não tem variações existentes, 
        // verifica se está tentando adicionar novas variações
        if (!$tem_estoque_base && !$tem_variacoes) {
            $novas_variacoes = $this->input->post('novas_variacoes_nomes');
            if (empty($novas_variacoes) || empty($novas_variacoes[0])) {
                $this->form_validation->set_rules('quantidade', 'Quantidade Base', 'required|numeric',
                    array('required' => 'É necessário ter quantidade base ou pelo menos uma variação.')
                );
            }
        }
        
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('produtos/edit', $data);
            $this->load->view('templates/footer');
        } else {
            // Atualiza os dados básicos do produto
            $this->produto_model->update($id, [
                'nome' => $this->input->post('nome'),
                'preco' => $this->input->post('preco')
            ]);
            
            // Atualiza o estoque base se houver quantidade
            $quantidade_base = $this->input->post('quantidade');
            if ($quantidade_base > 0) {
                $this->estoque_model->update_quantidade_produto(
                    $id, 
                    $quantidade_base
                );
            }
            
            // Atualizando as variações existentes
            if ($this->input->post('variacao_ids')) {
                $variacao_ids = $this->input->post('variacao_ids');
                $variacao_nomes = $this->input->post('variacao_nomes');
                $variacao_qtds = $this->input->post('variacao_qtds');
                
                foreach ($variacao_ids as $key => $variacao_id) {
                    if (isset($variacao_nomes[$key])) {
                        $this->produto_model->update_variacao($variacao_id, [
                            'nome' => $variacao_nomes[$key]
                        ]);
                    }
                    
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
                        $variacao_id = $this->produto_model->add_variacao([
                            'produto_id' => $id,
                            'nome' => $nome
                        ]);
                        
                        if (isset($novas_qtds[$key]) && $novas_qtds[$key] > 0) {
                            $this->estoque_model->update_quantidade_variacao(
                                $variacao_id,
                                $novas_qtds[$key]
                            );
                        }
                    }
                }
            }
            
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
        $produto_id = $this->input->post('produto_id');
        $variacao_id = $this->input->post('variacao_id');
        $quantidade = $this->input->post('quantidade') ? $this->input->post('quantidade') : 1;
        
        $produto = $this->produto_model->get_by_id($produto_id);
        
        if (!$produto) {
            $this->session->set_flashdata('error', 'Produto não encontrado');
            redirect('produtos');
        }
        
        // Verifica se o produto tem variações
        $variacoes = $this->produto_model->get_variacoes($produto_id);
        
        // Se o produto tem variações, é obrigatório selecionar uma
        if (!empty($variacoes) && !$variacao_id) {
            $this->session->set_flashdata('error', 'Selecione uma variação do produto');
            redirect('produtos/view/' . $produto_id);
        }
        
        // Verificando o estoque
        if ($variacao_id) {
            // Produto com variação
            $variacao = $this->produto_model->get_variacao_by_id($variacao_id);
            $estoque = $this->estoque_model->get_by_variacao($variacao_id);
            
            if (!$variacao || !$estoque || $estoque->quantidade < $quantidade) {
                $this->session->set_flashdata('error', 'Estoque insuficiente para a variação selecionada');
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
            // Produto sem variação
            $estoque = $this->estoque_model->get_by_produto($produto_id);
            
            if (!$estoque || $estoque->quantidade < $quantidade) {
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
        
        // Adiciona ao carrinho
        $this->cart->insert($item);
        $this->session->set_flashdata('success', 'Produto adicionado ao carrinho');
        redirect('carrinho');
    }
} 