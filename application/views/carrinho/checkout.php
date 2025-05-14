<div class="row mb-4">
    <div class="col-md-6">
        <h2>Finalizar Pedido</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?= base_url('carrinho') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar ao Carrinho
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Dados de Entrega</h5>
            </div>
            <div class="card-body">
                <?= validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                
                <?= form_open('carrinho/checkout'); ?>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="cliente_nome" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="cliente_nome" name="cliente_nome" value="<?= set_value('cliente_nome') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="cliente_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="cliente_email" name="cliente_email" value="<?= set_value('cliente_email') ?>" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="cliente_telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="cliente_telefone" name="cliente_telefone" value="<?= set_value('cliente_telefone') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="cep" class="form-label">CEP</label>
                            <input type="text" class="form-control" id="cep" name="cep" value="<?= set_value('cep') ?>" required>
                            <div class="form-text">Digite o CEP para buscar o endereço automaticamente.</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-9">
                            <label for="endereco" class="form-label">Endereço</label>
                            <input type="text" class="form-control" id="endereco" name="endereco" value="<?= set_value('endereco') ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="numero" class="form-label">Número</label>
                            <input type="text" class="form-control" id="numero" name="numero" value="<?= set_value('numero') ?>" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="bairro" class="form-label">Bairro</label>
                            <input type="text" class="form-control" id="bairro" name="bairro" value="<?= set_value('bairro') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="complemento" class="form-label">Complemento</label>
                            <input type="text" class="form-control" id="complemento" name="complemento" value="<?= set_value('complemento') ?>">
                            <div class="form-text">Opcional (apartamento, sala, etc.)</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="cidade" class="form-label">Cidade</label>
                            <input type="text" class="form-control" id="cidade" name="cidade" value="<?= set_value('cidade') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="estado" class="form-label">Estado</label>
                            <input type="text" class="form-control" id="estado" name="estado" value="<?= set_value('estado') ?>" required>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-check"></i> Confirmar Pedido
                        </button>
                    </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Resumo do Pedido</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th class="text-center">Qtd</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->cart->contents() as $item): ?>
                                <tr>
                                    <td class="small"><?= $item['name']; ?></td>
                                    <td class="text-center"><?= $item['qty']; ?></td>
                                    <td class="text-end">R$ <?= number_format($item['subtotal'], 2, ',', '.'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <strong>R$ <?= number_format($this->cart->total(), 2, ',', '.'); ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Frete:</span>
                    <strong>R$ <?= number_format($frete, 2, ',', '.'); ?></strong>
                </div>
                
                <?php if ($cupom_aplicado): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Desconto (<?= $cupom->codigo ?>):</span>
                        <strong class="text-danger">- R$ <?= number_format($cupom_desconto, 2, ',', '.'); ?></strong>
                    </div>
                <?php endif; ?>
                
                <hr>
                
                <div class="d-flex justify-content-between mb-3">
                    <h5>Total:</h5>
                    <h5>R$ <?= number_format($total, 2, ',', '.'); ?></h5>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Função para mostrar feedback visual de loading
    function showLoading() {
        $('#cep').addClass('is-loading');
        $('#endereco, #bairro, #complemento, #cidade, #estado').prop('disabled', true);
        $('#endereco').val('Buscando...');
        $('#bairro').val('Buscando...');
        $('#cidade').val('Buscando...');
        $('#estado').val('Buscando...');
    }

    // Função para esconder feedback visual de loading
    function hideLoading() {
        $('#cep').removeClass('is-loading');
        $('#endereco, #bairro, #complemento, #cidade, #estado').prop('disabled', false);
    }

    // Função para limpar campos de endereço
    function clearAddressFields() {
        $('#endereco, #bairro, #complemento, #cidade, #estado').val('').prop('disabled', false);
    }

    // Máscara para o CEP (99999-999)
    $('#cep').on('input', function() {
        var cep = $(this).val().replace(/\D/g, '');
        if (cep.length > 8) cep = cep.substr(0, 8);
        $(this).val(cep.replace(/(\d{5})(\d{3})/, '$1-$2'));
        
        // Limpa os campos se o CEP for apagado
        if (cep.length < 8) {
            clearAddressFields();
        }
    });

    // Função para buscar endereço pelo CEP diretamente no ViaCEP
    function buscarCep() {
        // Remove tudo que não for número
        var cep = $('#cep').val().replace(/\D/g, '');
        
        // Verifica se o CEP tem 8 dígitos
        if (cep.length !== 8) {
            if (cep.length > 0) {
                alert('CEP inválido. Digite 8 números.');
            }
            clearAddressFields();
            return;
        }
        
        // Mostra que está carregando
        showLoading();
        
        // Monta a URL do ViaCEP
        var url = `https://viacep.com.br/ws/${cep}/json/`;
        console.log('Consultando ViaCEP:', url);
        
        // Faz a consulta no ViaCEP
        $.getJSON(url)
            .done(function(data) {
                console.log('Resposta do ViaCEP:', data);
                hideLoading();
                
                // Verifica se o ViaCEP retornou erro
                if (data.erro) {
                    console.log('ViaCEP retornou erro');
                    alert('CEP não encontrado');
                    clearAddressFields();
                    $('#cep').focus();
                    return;
                }
                
                // Preenche os campos com os dados do ViaCEP
                $('#endereco').val(data.logradouro || '');
                $('#bairro').val(data.bairro || '');
                $('#cidade').val(data.localidade || '');
                $('#estado').val(data.uf || '');
                $('#complemento').val(data.complemento || '');
                
                // Se algum campo obrigatório veio vazio
                if (!data.logradouro || !data.bairro || !data.localidade || !data.uf) {
                    console.log('Alguns campos vieram vazios do ViaCEP');
                    alert('CEP encontrado, mas alguns campos precisam ser preenchidos manualmente.');
                }
                
                // Foca no próximo campo a ser preenchido
                if (!data.logradouro) {
                    $('#endereco').focus();
                } else {
                    $('#numero').focus();
                }
            })
            .fail(function(jqxhr, textStatus, error) {
                console.error('Erro ao consultar ViaCEP:', textStatus, error);
                hideLoading();
                alert('Erro ao consultar o CEP. Tente novamente.');
                clearAddressFields();
                $('#cep').focus();
            });
    }

    // Dispara a busca quando o campo perde o foco
    $('#cep').blur(buscarCep);
    
    // Dispara a busca quando pressionar Enter no campo
    $('#cep').keypress(function(e) {
        if (e.which === 13) {
            e.preventDefault();
            buscarCep();
        }
    });
});
</script>

<style>
/* Estilo para feedback visual de loading */
.is-loading {
    background-image: url('data:image/svg+xml;charset=utf8,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"%3E%3Cpath d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z" fill="%23ccc"/%3E%3Cpath d="M20 12h2A10 10 0 0 0 12 2v2a8 8 0 0 1 8 8z" fill="%23333"%3E%3CanimateTransform attributeName="transform" type="rotate" from="0 12 12" to="360 12 12" dur="1s" repeatCount="indefinite"/%3E%3C/path%3E%3C/svg%3E');
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 20px;
}

/* Estilo para campos desabilitados durante a busca */
input:disabled {
    background-color: #f8f9fa;
    cursor: wait;
}
</style> 