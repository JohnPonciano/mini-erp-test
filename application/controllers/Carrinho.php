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
        $this->load->library('email');
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
        $produto_id = $this->input->post('produto_id');
        $variacao_id = $this->input->post('variacao_id');
        $quantidade = $this->input->post('quantidade') ? $this->input->post('quantidade') : 1;
        
        $produto = $this->produto_model->get_by_id($produto_id);
        
        if (!$produto) {
            // Ué, cadê o produto? Sumiu!
            $this->session->set_flashdata('error', 'Produto não encontrado');
            redirect('produtos');
        }
        
        // Vamos ver se tem no estoque ou se já acabou
        if ($variacao_id) {
            $variacao = $this->produto_model->get_variacao_by_id($variacao_id);
            $estoque = $this->estoque_model->get_by_variacao($variacao_id);
            
            if (!$variacao || !$estoque || $estoque->quantidade < $quantidade) {
                // Sem estoque? Não dá pra vender o que não tem!
                $this->session->set_flashdata('error', 'Estoque insuficiente');
                redirect('produtos/view/' . $produto_id);
            }
            
            // Verificando se o item já existe no carrinho
            $cart_contents = $this->cart->contents();
            $item_id = $produto_id . '_' . $variacao_id;
            $update = false;
            $rowid = '';
            
            foreach ($cart_contents as $item) {
                if ($item['id'] == $item_id) {
                    // Item já existe, vamos atualizar a quantidade em vez de duplicar
                    $update = true;
                    $rowid = $item['rowid'];
                    $nova_quantidade = $item['qty'] + $quantidade;
                    
                    // Verificando se tem estoque suficiente para a quantidade atualizada
                    if ($estoque->quantidade < $nova_quantidade) {
                        $this->session->set_flashdata('error', 'Estoque insuficiente para ' . $item['name']);
                        redirect('produtos/view/' . $produto_id);
                    }
                    
                    break;
                }
            }
            
            if ($update) {
                // Atualiza item existente
                $this->cart->update(array(
                    'rowid' => $rowid,
                    'qty' => $nova_quantidade
                ));
            } else {
                // Adiciona novo item
                $item = array(
                    'id'      => $item_id,
                    'qty'     => $quantidade,
                    'price'   => $produto->preco,
                    'name'    => $produto->nome . ' - ' . $variacao->nome,
                    'options' => array(
                        'produto_id' => $produto_id,
                        'variacao_id' => $variacao_id,
                        'variacao_nome' => $variacao->nome
                    )
                );
                $this->cart->insert($item);
            }
        } else {
            $estoque = $this->estoque_model->get_by_produto($produto_id);
            
            if (!$estoque || $estoque->quantidade < $quantidade) {
                // Ops, estoque zerado!
                $this->session->set_flashdata('error', 'Estoque insuficiente');
                redirect('produtos/view/' . $produto_id);
            }
            
            // Verificando se o item já existe no carrinho
            $cart_contents = $this->cart->contents();
            $update = false;
            $rowid = '';
            
            foreach ($cart_contents as $item) {
                if ($item['id'] == $produto_id) {
                    // Item já existe, vamos atualizar a quantidade em vez de duplicar
                    $update = true;
                    $rowid = $item['rowid'];
                    $nova_quantidade = $item['qty'] + $quantidade;
                    
                    // Verificando se tem estoque suficiente para a quantidade atualizada
                    if ($estoque->quantidade < $nova_quantidade) {
                        $this->session->set_flashdata('error', 'Estoque insuficiente para ' . $item['name']);
                        redirect('produtos/view/' . $produto_id);
                    }
                    
                    break;
                }
            }
            
            if ($update) {
                // Atualiza item existente
                $this->cart->update(array(
                    'rowid' => $rowid,
                    'qty' => $nova_quantidade
                ));
            } else {
                // Adiciona novo item
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
                $this->cart->insert($item);
            }
        }
        
        // Bota no carrinho e vamos às compras!
        $this->session->set_flashdata('success', 'Produto adicionado ao carrinho');
        redirect('carrinho');
    }
    
    public function update() {
        $cart_info = $_POST['cart'];
        
        foreach ($cart_info as $id => $cart) {
            $rowid = $cart['rowid'];
            $qty = $cart['qty'];
            
            // Antes de atualizar, vamos ver se tem produto suficiente
            $item = $this->cart->get_item($rowid);
            if ($item) {
                $produto_id = $item['options']['produto_id'];
                $variacao_id = $item['options']['variacao_id'];
                
                $quantidade_suficiente = $this->verificar_estoque($produto_id, $variacao_id, $qty);
                
                if (!$quantidade_suficiente) {
                    // Cliente querendo mais do que temos... não vai rolar!
                    $this->session->set_flashdata('error', 'Estoque insuficiente para ' . $item['name']);
                    redirect('carrinho');
                }
            }
            
            $data = array(
                'rowid' => $rowid,
                'qty' => $qty
            );
            
            $this->cart->update($data);
        }
        
        $this->session->set_flashdata('success', 'Carrinho atualizado com sucesso');
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
            
            // Envia e-mail de confirmação para o cliente ficar tranquilo
            $this->enviar_email_confirmacao($pedido_id);
            
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
    
    private function enviar_email_confirmacao($pedido_id) {
        // Busca os dados do pedido para mostrar no email
        $pedido = $this->pedido_model->get_by_id($pedido_id);
        $itens = $this->pedido_model->get_items($pedido_id);
        
        // Prepara o conteúdo do e-mail
        $this->email->from('vendas@microerp.com.br', 'Micro ERP');
        $this->email->to($pedido->cliente_email);
        $this->email->subject('Confirmação de Pedido #' . $pedido_id);
        
        $mensagem = "Olá " . $pedido->cliente_nome . ",\n\n";
        $mensagem .= "Seu pedido #" . $pedido_id . " foi recebido com sucesso!\n\n";
        $mensagem .= "Resumo do pedido:\n";
        
        foreach ($itens as $item) {
            $mensagem .= "- " . $item->nome . " x " . $item->quantidade . ": R$ " . number_format($item->subtotal, 2, ',', '.') . "\n";
        }
        
        $mensagem .= "\nSubtotal: R$ " . number_format($pedido->subtotal, 2, ',', '.');
        $mensagem .= "\nFrete: R$ " . number_format($pedido->frete, 2, ',', '.');
        
        if ($pedido->desconto > 0) {
            $mensagem .= "\nDesconto: -R$ " . number_format($pedido->desconto, 2, ',', '.');
        }
        
        $mensagem .= "\nTotal: R$ " . number_format($pedido->total, 2, ',', '.');
        
        $mensagem .= "\n\nEndereço de entrega:";
        $mensagem .= "\n" . $pedido->endereco;
        $mensagem .= "\n" . $pedido->cidade . " - " . $pedido->estado;
        $mensagem .= "\nCEP: " . $pedido->cep;
        
        $mensagem .= "\n\nObrigado por comprar conosco!";
        
        $this->email->message($mensagem);
        
        // Tenta enviar o email, mas não para tudo se der erro
        try {
            $this->email->send();
        } catch (Exception $e) {
            log_message('error', 'Erro ao enviar email: ' . $e->getMessage());
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
} 