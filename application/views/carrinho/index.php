<div class="row mb-4">
    <div class="col-md-6">
        <h2>Carrinho de Compras</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?= base_url('produtos') ?>" class="btn btn-primary">
            <i class="fas fa-shopping-basket"></i> Continuar Comprando
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Itens no Carrinho</h5>
            </div>
            <div class="card-body">
                <?php if ($this->cart->total_items() == 0): ?>
                    <div class="alert alert-info">
                        Seu carrinho está vazio. <a href="<?= base_url('produtos') ?>" class="alert-link">Clique aqui</a> para adicionar produtos.
                    </div>
                <?php else: ?>
                    <?= form_open('carrinho/update'); ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th class="text-center">Quantidade</th>
                                    <th class="text-end">Preço</th>
                                    <th class="text-end">Subtotal</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($this->cart->contents() as $item): ?>
                                    <tr>
                                        <td>
                                            <?= $item['name']; ?>
                                            <input type="hidden" name="cart[<?= $item['id']; ?>][id]" value="<?= $item['id']; ?>">
                                            <input type="hidden" name="cart[<?= $item['id']; ?>][rowid]" value="<?= $item['rowid']; ?>">
                                        </td>
                                        <td class="text-center">
                                            <input type="number" name="cart[<?= $item['id']; ?>][qty]" class="form-control form-control-sm d-inline-block" style="width: 70px;" value="<?= $item['qty']; ?>" min="1">
                                        </td>
                                        <td class="text-end">R$ <?= number_format($item['price'], 2, ',', '.'); ?></td>
                                        <td class="text-end">R$ <?= number_format($item['subtotal'], 2, ',', '.'); ?></td>
                                        <td class="text-center">
                                            <a href="<?= base_url('carrinho/remove/' . $item['rowid']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja remover este item?');">
                                                <i class="fas fa-trash"></i> Remover
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-sync-alt"></i> Atualizar Carrinho
                        </button>
                        <a href="<?= base_url('carrinho/limpar') ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja esvaziar o carrinho?');">
                            <i class="fas fa-trash"></i> Esvaziar Carrinho
                        </a>
                    </div>
                    <?= form_close(); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Resumo da Compra</h5>
            </div>
            <div class="card-body">
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
                    <div class="mb-2">
                        <a href="<?= base_url('carrinho/remover_cupom'); ?>" class="btn btn-sm btn-danger">
                            <i class="fas fa-times"></i> Remover Cupom
                        </a>
                    </div>
                <?php else: ?>
                    <div class="mb-3">
                        <strong>Cupom de Desconto:</strong>
                        <?= form_open('carrinho/aplicar_cupom'); ?>
                            <div class="input-group mt-2">
                                <input type="text" class="form-control" name="cupom_codigo" placeholder="Código do cupom">
                                <button type="submit" class="btn btn-secondary">Aplicar</button>
                            </div>
                        <?= form_close(); ?>
                    </div>
                <?php endif; ?>
                
                <hr>
                
                <div class="d-flex justify-content-between mb-3">
                    <h5>Total:</h5>
                    <h5>R$ <?= number_format($total, 2, ',', '.'); ?></h5>
                </div>
                
                <?php if ($this->cart->total_items() > 0): ?>
                    <a href="<?= base_url('carrinho/checkout'); ?>" class="btn btn-success btn-lg w-100">
                        <i class="fas fa-check"></i> Finalizar Compra
                    </a>
                <?php else: ?>
                    <button class="btn btn-success btn-lg w-100" disabled>
                        <i class="fas fa-check"></i> Finalizar Compra
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div> 