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
    // Função para buscar endereço pelo CEP
    $('#cep').blur(function() {
        var cep = $(this).val().replace(/\D/g, '');
        
        if (cep.length !== 8) {
            return;
        }
        
        // Exibir indicador de carregamento
        $('#endereco').val('Buscando...');
        $('#cidade').val('Buscando...');
        $('#estado').val('Buscando...');
        
        $.ajax({
            url: '<?= base_url('carrinho/buscar_cep') ?>',
            type: 'POST',
            data: {cep: cep},
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    alert(response.error);
                    $('#endereco').val('');
                    $('#cidade').val('');
                    $('#estado').val('');
                } else {
                    // Preencher os campos com os dados retornados
                    $('#endereco').val(response.endereco);
                    $('#cidade').val(response.cidade);
                    $('#estado').val(response.estado);
                    
                    // Focar no campo de número após preencher o endereço
                    $('#numero').focus();
                }
            },
            error: function() {
                alert('Erro ao consultar o CEP. Tente novamente.');
                $('#endereco').val('');
                $('#cidade').val('');
                $('#estado').val('');
            }
        });
    });
});
</script> 