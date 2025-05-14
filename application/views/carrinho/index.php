<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Carrinho de Compras</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?= base_url('produtos') ?>" class="btn btn-outline-primary">
                <i class="fas fa-shopping-basket"></i> Continuar Comprando
            </a>
        </div>
    </div>

    <?php if ($this->cart->total_items() > 0): ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <?= form_open('carrinho/update'); ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Produto</th>
                                            <th class="text-center">Quantidade</th>
                                            <th class="text-end">Preço</th>
                                            <th class="text-end">Subtotal</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($this->cart->contents() as $item): ?>
                                            <tr>
                                                <td>
                                                    <div class="product-info">
                                                        <h6 class="mb-1"><?= $item['name'] ?></h6>
                                                        <?php if (isset($item['options']['variacao_nome'])): ?>
                                                            <small class="text-muted">
                                                                Variação: <?= $item['options']['variacao_nome'] ?>
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td class="text-center" style="width: 150px;">
                                                    <input type="number" name="cart[<?= $item['rowid'] ?>][qty]" 
                                                           class="form-control form-control-sm mx-auto" 
                                                           value="<?= $item['qty'] ?>" 
                                                           min="1" 
                                                           style="width: 80px;">
                                                </td>
                                                <td class="text-end">
                                                    R$ <?= number_format($item['price'], 2, ',', '.') ?>
                                                </td>
                                                <td class="text-end">
                                                    R$ <?= number_format($item['subtotal'], 2, ',', '.') ?>
                                                </td>
                                                <td class="text-end">
                                                    <a href="<?= base_url('carrinho/remove/' . $item['rowid']) ?>" 
                                                       class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-between mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-sync"></i> Atualizar Carrinho
                                </button>
                                <a href="<?= base_url('carrinho/limpar') ?>" class="btn btn-outline-danger">
                                    <i class="fas fa-trash"></i> Limpar Carrinho
                                </a>
                            </div>
                        <?= form_close(); ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Resumo do Pedido -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Resumo do Pedido</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <strong>R$ <?= number_format($this->cart->total(), 2, ',', '.') ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Frete:</span>
                            <strong>R$ <?= number_format($frete, 2, ',', '.') ?></strong>
                        </div>
                        
                        <?php if ($cupom_aplicado): ?>
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Desconto (<?= $cupom->codigo ?>):</span>
                                <strong>- R$ <?= number_format($cupom_desconto, 2, ',', '.') ?></strong>
                            </div>
                        <?php endif; ?>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="mb-0">Total:</h5>
                            <h5 class="mb-0 text-primary">R$ <?= number_format($total, 2, ',', '.') ?></h5>
                        </div>

                        <?php if (!$cupom_aplicado): ?>
                            <?= form_open('carrinho/aplicar_cupom', ['class' => 'mb-3']); ?>
                                <div class="input-group">
                                    <input type="text" name="cupom_codigo" class="form-control" 
                                           placeholder="Código do cupom">
                                    <button class="btn btn-outline-primary" type="submit">Aplicar</button>
                                </div>
                            <?= form_close(); ?>
                        <?php else: ?>
                            <div class="d-grid mb-3">
                                <a href="<?= base_url('carrinho/remover_cupom') ?>" 
                                   class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-times"></i> Remover Cupom
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="d-grid">
                            <a href="<?= base_url('carrinho/checkout') ?>" class="btn btn-primary btn-lg">
                                <i class="fas fa-check"></i> Finalizar Compra
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Informações de Frete -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Informações de Frete</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-truck text-primary me-2"></i>
                                Frete Grátis em compras acima de R$ 200,00
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-dollar-sign text-primary me-2"></i>
                                R$ 15,00 para compras entre R$ 52,00 e R$ 166,59
                            </li>
                            <li>
                                <i class="fas fa-info-circle text-primary me-2"></i>
                                R$ 20,00 para demais valores
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h4>Seu carrinho está vazio</h4>
                <p class="text-muted">Que tal começar a comprar?</p>
                <a href="<?= base_url('produtos') ?>" class="btn btn-primary">
                    <i class="fas fa-shopping-basket"></i> Ver Produtos
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.product-info {
    max-width: 300px;
}
.table > :not(caption) > * > * {
    padding: 1rem;
}
.form-control-sm {
    height: 32px;
}
.btn-sm {
    height: 32px;
    width: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
</style> 