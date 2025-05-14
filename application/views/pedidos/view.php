<div class="row mb-4">
    <div class="col-md-6">
        <h2>Detalhes do Pedido #<?= $pedido->id ?></h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?= base_url('pedidos') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Informações do Pedido</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informações do Cliente</h6>
                        <p><strong>Nome:</strong> <?= $pedido->cliente_nome ?></p>
                        <p><strong>Email:</strong> <?= $pedido->cliente_email ?></p>
                        <p><strong>Telefone:</strong> <?= $pedido->cliente_telefone ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Endereço de Entrega</h6>
                        <p><?= $pedido->endereco ?></p>
                        <p><?= $pedido->cidade ?>, <?= $pedido->estado ?></p>
                        <p>CEP: <?= $pedido->cep ?></p>
                    </div>
                </div>
                
                <h6 class="mt-4">Produtos</h6>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th class="text-center">Quantidade</th>
                                <th class="text-end">Preço Unitário</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($itens as $item): ?>
                                <tr>
                                    <td>
                                        <?= $item->produto_nome ?>
                                        <?php if ($item->variacao_nome): ?>
                                            - <?= $item->variacao_nome ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?= $item->quantidade ?></td>
                                    <td class="text-end">R$ <?= number_format($item->preco_unitario, 2, ',', '.') ?></td>
                                    <td class="text-end">R$ <?= number_format($item->subtotal, 2, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h6>Detalhes do Pagamento</h6>
                        <p>Data do Pedido: <?= date('d/m/Y H:i', strtotime($pedido->created_at)) ?></p>
                        <p>Última Atualização: <?= date('d/m/Y H:i', strtotime($pedido->updated_at)) ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Resumo do Pedido</h6>
                        <div class="d-flex justify-content-between">
                            <span>Subtotal:</span>
                            <span>R$ <?= number_format($pedido->subtotal, 2, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Frete:</span>
                            <span>R$ <?= number_format($pedido->frete, 2, ',', '.') ?></span>
                        </div>
                        <?php if ($pedido->desconto > 0): ?>
                            <div class="d-flex justify-content-between">
                                <span>Desconto:</span>
                                <span>- R$ <?= number_format($pedido->desconto, 2, ',', '.') ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between mt-2">
                            <strong>Total:</strong>
                            <strong>R$ <?= number_format($pedido->total, 2, ',', '.') ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Status do Pedido</h5>
            </div>
            <div class="card-body">
                <h4 class="text-center mb-4">
                    <span class="badge <?= $pedido->status === 'pendente' ? 'bg-warning' : 'bg-success' ?>">
                        <?= ucfirst($pedido->status) ?>
                    </span>
                </h4>
                
                <?= form_open('pedidos/update_status'); ?>
                    <input type="hidden" name="pedido_id" value="<?= $pedido->id ?>">
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Atualizar Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="">Selecione...</option>
                            <option value="pendente" <?= $pedido->status === 'pendente' ? 'selected' : '' ?>>Pendente</option>
                            <option value="processando" <?= $pedido->status === 'processando' ? 'selected' : '' ?>>Processando</option>
                            <option value="enviado" <?= $pedido->status === 'enviado' ? 'selected' : '' ?>>Enviado</option>
                            <option value="entregue" <?= $pedido->status === 'entregue' ? 'selected' : '' ?>>Entregue</option>
                            <option value="cancelado" <?= $pedido->status === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                        </select>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Atualizar Status
                        </button>
                    </div>
                <?= form_close(); ?>
                
                <hr>
                
                <h6 class="mt-3">Teste de Webhook</h6>
                <p class="small">Use o comando abaixo para testar o webhook:</p>
                <div class="bg-light p-2 mt-2 rounded small">
                    <code>curl -X POST <?= base_url('pedidos/webhook') ?> \<br>
                    -H "Content-Type: application/json" \<br>
                    -d '{"id": <?= $pedido->id ?>, "status": "entregue"}'</code>
                </div>
            </div>
        </div>
    </div>
</div> 