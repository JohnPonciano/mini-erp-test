<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Carrinho extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Carregando os modelos necessários pra fazer a mágica acontecer
        $this->load->model('produto_model');
        $this->load->model('estoque_model');
        $this->load->model('cupom_model');
        $this->load->model('pedido_model');
    }
    
    public function index() {
        $data['title'] = 'Carrinho de Compras';
        $data['frete'] = $this->calcular_frete();
        
        // Vamos ver se tem cupom aplicado, né?
        $cupom_codigo = $this->session->userdata('cupom_codigo');
        $data['cupom_aplicado'] = false;
        $data['cupom_desconto'] = 0;
        
        if ($cupom_codigo) {
            $result = $this->cupom_model->validate_cupom($cupom_codigo, $this->cart->total());
            if ($result['valid']) {
                // Oba! Cupom válido, vamos aplicar o desconto
                $data['cupom_aplicado'] = true;
                $data['cupom_desconto'] = $result['discount'];
                $data['cupom'] = $result['cupom'];
            } else {
                // Eita, cupom inválido. Tchau cupom!
                $this->session->unset_userdata('cupom_codigo');
                $this->session->set_flashdata('error', $result['message']);
            }
        }
        
        // Soma tudo pra ver quanto vai doer no bolso
        $data['total'] = $this->cart->total() + $data['frete'] - $data['cupom_desconto'];
        
        $this->load->view('templates/header', $data);
        $this->load->view('carrinho/index', $data);
        $this->load->view('templates/footer');
    }
    
    public function add() {
        $response = array(
            'success' => true,
            'message' => '',
            'redirect' => ''
        );

        $produto_id = $this->input->post('produto_id');
        $variacao_id = $this->input->post('variacao_id');
        $quantidade = (int)$this->input->post('quantidade');

        if ($quantidade < 1) {
            $quantidade = 1;
        }

        // Log para debug
        log_message('debug', 'Adicionando ao carrinho - Produto ID: ' . $produto_id . ', Variação ID: ' . $variacao_id . ', Qtd: ' . $quantidade);

        if (!$produto_id) {
            $response['success'] = false;
            $response['message'] = 'Produto não encontrado';
            $response['redirect'] = base_url('produtos');
            
            $this->output_response($response);
            return;
        }

        $produto = $this->produto_model->get_by_id($produto_id);
        if (!$produto) {
            $response['success'] = false;
            $response['message'] = 'Produto não encontrado';
            $response['redirect'] = base_url('produtos');
            
            $this->output_response($response);
            return;
        }

        // Verifica variações
        $variacoes = $this->produto_model->get_variacoes($produto_id);
        
        // Log para debug
        log_message('debug', 'Variações encontradas: ' . json_encode($variacoes));

        if (!empty($variacoes) && !$variacao_id) {
            $response['success'] = false;
            $response['message'] = 'Por favor, selecione uma variação do produto';
            $response['redirect'] = base_url('produtos/view/' . $produto_id);
            
            $this->output_response($response);
            return;
        }

        // Verifica estoque
        if ($variacao_id) {
            $variacao = $this->produto_model->get_variacao_by_id($variacao_id);
            $estoque = $this->estoque_model->get_by_variacao($variacao_id);
            
            // Log para debug
            log_message('debug', 'Dados da variação: ' . json_encode($variacao));
            log_message('debug', 'Estoque da variação: ' . json_encode($estoque));

            if (!$variacao || !$estoque || $estoque->quantidade < $quantidade) {
                $response['success'] = false;
                $response['message'] = 'Estoque insuficiente para a variação selecionada';
                $response['redirect'] = base_url('produtos/view/' . $produto_id);
                
                $this->output_response($response);
                return;
            }

            // Para produtos com variação, o ID do item no carrinho será a combinação de produto_id e variacao_id
            $item_id = 'p' . $produto_id . 'v' . $variacao_id;
            $item_name = $produto->nome . ' - ' . $variacao->nome;
        } else {
            $estoque = $this->estoque_model->get_by_produto($produto_id);
            
            if (!$estoque || $estoque->quantidade < $quantidade) {
                $response['success'] = false;
                $response['message'] = 'Estoque insuficiente';
                $response['redirect'] = base_url('produtos/view/' . $produto_id);
                
                $this->output_response($response);
                return;
            }

            $item_id = 'p' . $produto_id;
            $item_name = $produto->nome;
        }

        // Log para debug
        log_message('debug', 'Item ID gerado: ' . $item_id);

        // Verifica se já existe no carrinho
        $cart_contents = $this->cart->contents();
        foreach ($cart_contents as $item) {
            if ($item['id'] == $item_id) {
                $nova_quantidade = $item['qty'] + $quantidade;
                
                if (($variacao_id && $estoque->quantidade < $nova_quantidade) || 
                    (!$variacao_id && $estoque->quantidade < $nova_quantidade)) {
                    $response['success'] = false;
                    $response['message'] = 'Estoque insuficiente para ' . $item_name;
                    $response['redirect'] = base_url('produtos/view/' . $produto_id);
                    
                    $this->output_response($response);
                    return;
                }

                // Log para debug
                log_message('debug', 'Atualizando item existente - RowID: ' . $item['rowid'] . ', Nova Qtd: ' . $nova_quantidade);

                $update_result = $this->cart->update(array(
                    'rowid' => $item['rowid'],
                    'qty' => $nova_quantidade
                ));

                // Log para debug
                log_message('debug', 'Resultado da atualização: ' . json_encode($update_result));

                $response['message'] = 'Quantidade atualizada no carrinho: ' . $item_name;
                $response['redirect'] = base_url('carrinho');
                
                $this->output_response($response);
                return;
            }
        }

        // Adiciona novo item
        $item = array(
            'id' => $item_id,
            'qty' => $quantidade,
            'price' => $produto->preco,
            'name' => $item_name,
            'options' => array(
                'produto_id' => $produto_id,
                'variacao_id' => $variacao_id,
                'variacao_nome' => isset($variacao) ? $variacao->nome : null
            )
        );

        // Log para debug
        log_message('debug', 'Adicionando novo item: ' . json_encode($item));

        $insert_result = $this->cart->insert($item);

        // Log para debug
        log_message('debug', 'Resultado da inserção: ' . json_encode($insert_result));

        if ($insert_result === false) {
            $response['success'] = false;
            $response['message'] = 'Erro ao adicionar item ao carrinho';
            $response['redirect'] = base_url('produtos/view/' . $produto_id);
            
            $this->output_response($response);
            return;
        }

        $response['success'] = true;
        $response['message'] = 'Adicionado ao carrinho: ' . $item_name;
        $response['redirect'] = base_url('carrinho');
        
        $this->output_response($response);
    }
    
    public function update() {
        // Prevent direct access
        if (!$this->input->is_ajax_request() && !$this->input->post()) {
            redirect('carrinho');
        }

        $response = array(
            'success' => true,
            'message' => 'Carrinho atualizado com sucesso',
            'errors' => array()
        );

        $cart_info = $this->input->post('cart');
        
        if (!$cart_info) {
            $response['success'] = false;
            $response['message'] = 'Dados inválidos';
            
            if ($this->input->is_ajax_request()) {
                echo json_encode($response);
                return;
            }
            
            $this->session->set_flashdata('error', $response['message']);
            redirect('carrinho');
        }

        foreach ($cart_info as $rowid => $info) {
            if (!isset($info['qty'])) {
                continue;
            }

            $qty = (int)$info['qty'];
            if ($qty < 1) {
                $qty = 1;
            }

            $cart_item = $this->cart->get_item($rowid);
            if (!$cart_item) {
                continue;
            }

            $produto_id = isset($cart_item['options']['produto_id']) ? $cart_item['options']['produto_id'] : null;
            $variacao_id = isset($cart_item['options']['variacao_id']) ? $cart_item['options']['variacao_id'] : null;

            if (!$produto_id) {
                continue;
            }

            // Verifica estoque
            if (!$this->verificar_estoque($produto_id, $variacao_id, $qty)) {
                $response['success'] = false;
                $response['errors'][] = 'Estoque insuficiente para ' . $cart_item['name'];
                continue;
            }

            $update_data = array(
                'rowid' => $rowid,
                'qty' => $qty
            );

            if (!$this->cart->update($update_data)) {
                $response['success'] = false;
                $response['errors'][] = 'Erro ao atualizar ' . $cart_item['name'];
            }
        }

        if (!$response['success']) {
            $response['message'] = 'Ocorreram alguns erros ao atualizar o carrinho';
        }

        if ($this->input->is_ajax_request()) {
            echo json_encode($response);
            return;
        }

        if ($response['success']) {
            $this->session->set_flashdata('success', $response['message']);
        } else {
            $this->session->set_flashdata('error', $response['message']);
            if (!empty($response['errors'])) {
                $this->session->set_flashdata('errors', $response['errors']);
            }
        }

        redirect('carrinho');
    }
    
    public function remove($rowid) {
        // Tchau item, foi bom te conhecer!
        $this->cart->remove($rowid);
        $this->session->set_flashdata('success', 'Item removido do carrinho');
        redirect('carrinho');
    }
    
    public function limpar() {
        // Reset total! Carrinho zerado.
        $this->cart->destroy();
        $this->session->unset_userdata('cupom_codigo');
        $this->session->set_flashdata('success', 'Carrinho esvaziado com sucesso');
        redirect('carrinho');
    }
    
    public function aplicar_cupom() {
        $codigo = $this->input->post('cupom_codigo');
        
        if (empty($codigo)) {
            // Cupom em branco não rola, né?
            $this->session->set_flashdata('error', 'Informe o código do cupom');
            redirect('carrinho');
        }
        
        $resultado = $this->cupom_model->validate_cupom($codigo, $this->cart->total());
        
        if ($resultado['valid']) {
            // Eba! Desconto na área!
            $this->session->set_userdata('cupom_codigo', $codigo);
            $this->session->set_flashdata('success', 'Cupom aplicado com sucesso');
        } else {
            $this->session->set_flashdata('error', $resultado['message']);
        }
        
        redirect('carrinho');
    }
    
    public function remover_cupom() {
        // Cliente desistiu do desconto... vai pagar mais!
        $this->session->unset_userdata('cupom_codigo');
        $this->session->set_flashdata('success', 'Cupom removido com sucesso');
        redirect('carrinho');
    }
    
    public function checkout() {
        if ($this->cart->total_items() == 0) {
            // Carrinho vazio? Vai comprar primeiro!
            $this->session->set_flashdata('error', 'Seu carrinho está vazio');
            redirect('produtos');
        }
        
        $data['title'] = 'Finalizar Pedido';
        $data['frete'] = $this->calcular_frete();
        
        // Hora de conferir o cupom de novo
        $cupom_codigo = $this->session->userdata('cupom_codigo');
        $data['cupom_aplicado'] = false;
        $data['cupom_desconto'] = 0;
        $data['cupom_id'] = null;
        
        if ($cupom_codigo) {
            $result = $this->cupom_model->validate_cupom($cupom_codigo, $this->cart->total());
            if ($result['valid']) {
                $data['cupom_aplicado'] = true;
                $data['cupom_desconto'] = $result['discount'];
                $data['cupom'] = $result['cupom'];
                $data['cupom_id'] = $result['cupom']->id;
            } else {
                // Cupom expirou ou deu ruim
                $this->session->unset_userdata('cupom_codigo');
            }
        }
        
        $data['total'] = $this->cart->total() + $data['frete'] - $data['cupom_desconto'];
        
        // Precisamos de todos esses dados, senão não tem como entregar!
        $this->form_validation->set_rules('cliente_nome', 'Nome', 'required');
        $this->form_validation->set_rules('cliente_email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('cliente_telefone', 'Telefone', 'required');
        $this->form_validation->set_rules('cep', 'CEP', 'required');
        $this->form_validation->set_rules('endereco', 'Endereço', 'required');
        $this->form_validation->set_rules('numero', 'Número', 'required');
        $this->form_validation->set_rules('cidade', 'Cidade', 'required');
        $this->form_validation->set_rules('estado', 'Estado', 'required');
        
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('carrinho/checkout', $data);
            $this->load->view('templates/footer');
        } else {
            // Tudo certo com os dados, vamos criar o pedido!
            $endereco_completo = $this->input->post('endereco') . ', ' . $this->input->post('numero');
            
            $order_data = array(
                'cliente_nome' => $this->input->post('cliente_nome'),
                'cliente_email' => $this->input->post('cliente_email'),
                'cliente_telefone' => $this->input->post('cliente_telefone'),
                'cep' => $this->input->post('cep'),
                'endereco' => $endereco_completo,
                'cidade' => $this->input->post('cidade'),
                'estado' => $this->input->post('estado'),
                'subtotal' => $this->cart->total(),
                'frete' => $data['frete'],
                'desconto' => $data['cupom_desconto'],
                'total' => $data['total'],
                'cupom_id' => $data['cupom_id'],
                'status' => 'pendente'
            );
            
            $pedido_id = $this->pedido_model->insert($order_data);
            
            // Adiciona os itens do pedido e atualiza o estoque
            foreach ($this->cart->contents() as $item) {
                $produto_id = $item['options']['produto_id'];
                $variacao_id = $item['options']['variacao_id'];
                
                $item_data = array(
                    'pedido_id' => $pedido_id,
                    'produto_id' => $produto_id,
                    'variacao_id' => $variacao_id,
                    'quantidade' => $item['qty'],
                    'preco_unitario' => $item['price'],
                    'subtotal' => $item['subtotal']
                );
                
                $this->pedido_model->add_item($item_data);
                
                // Diminui o estoque - um a menos no depósito!
                $this->estoque_model->reduzir_estoque(
                    $produto_id,
                    $variacao_id,
                    $item['qty']
                );
            }
            
            // Limpa o carrinho - já foi tudo pro pedido!
            $this->cart->destroy();
            $this->session->unset_userdata('cupom_codigo');
            
            // Redireciona para a página de sucesso
            $this->session->set_flashdata('success', 'Pedido realizado com sucesso!');
            redirect('pedidos/confirmacao/' . $pedido_id);
        }
    }
    
    private function calcular_frete() {
        // Nova lógica de frete baseada no subtotal
        $subtotal = $this->cart->total();

        // Frete grátis para compras acima de R$200
        if ($subtotal > 200) {
            // Acima de 200 reais? Frete grátis pra você!
            return 0;
        } 
        // Frete de R$15 para compras entre R$52 e R$166,59
        else if ($subtotal >= 52 && $subtotal <= 166.59) {
            // Entre 52 e 166,59? Frete mediano!
            return 15.00;
        } 
        // Frete de R$20 para os demais valores
        else {
            // Menos de 52 ou entre 166,60 e 200? Frete cheio!
            return 20.00;
        }
    }
    
    private function verificar_estoque($produto_id, $variacao_id, $quantidade) {
        if ($variacao_id) {
            $estoque = $this->estoque_model->get_by_variacao($variacao_id);
            return ($estoque && $estoque->quantidade >= $quantidade);
        } else {
            $estoque = $this->estoque_model->get_by_produto($produto_id);
            return ($estoque && $estoque->quantidade >= $quantidade);
        }
    }
    
    public function buscar_cep() {
        $cep = $this->input->post('cep');
        
        if (empty($cep)) {
            echo json_encode(['error' => 'CEP não informado']);
            return;
        }
        
        // Remove caracteres não numéricos
        $cep = preg_replace('/[^0-9]/', '', $cep);
        
        // Se depois de limpar não tem 8 dígitos, dá erro
        if (strlen($cep) != 8) {
            echo json_encode(['error' => 'CEP inválido']);
            return;
        }
        
        // Consulta na API dos Correios (ou ViaCEP)
        $url = "https://viacep.com.br/ws/{$cep}/json/";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        // Deu erro na consulta?
        if (!$response) {
            echo json_encode(['error' => 'Não foi possível consultar o CEP']);
            return;
        }
        
        $endereco = json_decode($response);
        
        // Endereço não encontrado ou deu erro?
        if (isset($endereco->erro)) {
            echo json_encode(['error' => 'CEP não encontrado']);
            return;
        }
        
        // Tudo certo, retorna o endereço formatado
        echo json_encode([
            'endereco' => $endereco->logradouro,
            'bairro' => $endereco->bairro,
            'cidade' => $endereco->localidade,
            'estado' => $endereco->uf
        ]);
    }

    private function output_response($response) {
        if ($this->input->is_ajax_request()) {
            echo json_encode($response);
            return;
        }

        if ($response['success']) {
            $this->session->set_flashdata('success', $response['message']);
        } else {
            $this->session->set_flashdata('error', $response['message']);
        }

        if (!empty($response['redirect'])) {
            redirect($response['redirect']);
        }
        
        redirect('carrinho');
    }
} 