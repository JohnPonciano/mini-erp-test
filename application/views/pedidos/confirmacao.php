<div class="row mb-4">
    <div class="col-md-12 text-center">
        <h2>Pedido Confirmado</h2>
        <p class="lead text-success">
            <i class="fas fa-check-circle fa-2x me-2"></i>
            Seu pedido foi realizado com sucesso!
        </p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Dados do Pedido #<?= $pedido->id ?></h5>
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
                        <p>Status do Pedido: <span class="badge <?= $pedido->status === 'pendente' ? 'bg-warning' : 'bg-success' ?>"><?= ucfirst($pedido->status) ?></span></p>
                        <p>Data do Pedido: <?= date('d/m/Y H:i', strtotime($pedido->created_at)) ?></p>
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
        
        <div class="text-center mb-4">
            <p>Um email de confirmação foi enviado para <?= $pedido->cliente_email ?></p>
            <div class="mt-3">
                <a href="<?= base_url() ?>" class="btn btn-primary">
                    <i class="fas fa-home"></i> Voltar para a Página Inicial
                </a>
                <a href="<?= base_url('produtos') ?>" class="btn btn-success">
                    <i class="fas fa-shopping-basket"></i> Continuar Comprando
                </a>
            </div>
        </div>
    </div>
</div> 