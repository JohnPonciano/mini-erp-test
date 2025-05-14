<div class="container py-4">
    <!-- Cabeçalho do Produto -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-0"><?= $produto->nome ?></h2>
            <div class="text-muted mb-3">Código: #<?= str_pad($produto->id, 5, '0', STR_PAD_LEFT) ?></div>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?= base_url('produtos') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <a href="<?= base_url('produtos/edit/' . $produto->id) ?>" class="btn btn-outline-primary">
                <i class="fas fa-edit"></i> Editar
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Detalhes do Produto -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="price-tag mb-3">
                                <h3 class="text-primary mb-0">
                                    R$ <?= number_format($produto->preco, 2, ',', '.') ?>
                                </h3>
                                <small class="text-muted">à vista</small>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="stock-info">
                                <span class="badge bg-success mb-2">Em Estoque</span>
                                <div class="text-muted">
                                    <?= ($estoque ? $estoque->quantidade : 0) + array_sum(array_column($variacoes, 'quantidade')) ?> unidades disponíveis
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if(!empty($variacoes)): ?>
                        <div class="variations-section mt-4">
                            <h5 class="card-title mb-3">Opções Disponíveis</h5>
                            <?= form_open('produtos/add_to_cart', ['class' => 'variation-form']); ?>
                                <input type="hidden" name="produto_id" value="<?= $produto->id ?>">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="variacao_id" class="form-label">Selecione a Variação:</label>
                                        <select class="form-select form-select-lg" id="variacao_id" name="variacao_id" required>
                                            <option value="">Escolha uma opção</option>
                                            <?php foreach($variacoes as $variacao): ?>
                                                <?php if($variacao->quantidade > 0): ?>
                                                    <option value="<?= $variacao->id ?>" data-stock="<?= $variacao->quantidade ?>">
                                                        <?= $variacao->nome ?> (<?= $variacao->quantidade ?> disponíveis)
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-3 mb-3">
                                        <label for="quantidade" class="form-label">Quantidade:</label>
                                        <input type="number" class="form-control form-control-lg" id="quantidade" 
                                               name="quantidade" min="1" value="1" required>
                                    </div>
                                    
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-lg w-100">
                                            <i class="fas fa-shopping-cart"></i> Comprar
                                        </button>
                                    </div>
                                </div>
                            <?= form_close(); ?>

                            <div class="table-responsive mt-4">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Variação</th>
                                            <th class="text-end">Disponibilidade</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($variacoes as $variacao): ?>
                                            <tr>
                                                <td><?= $variacao->nome ?></td>
                                                <td class="text-end">
                                                    <?php if($variacao->quantidade > 0): ?>
                                                        <span class="text-success">
                                                            <?= $variacao->quantidade ?> unidades
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="text-danger">Indisponível</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php if($estoque && $estoque->quantidade > 0): ?>
                            <div class="mt-4">
                                <?= form_open('produtos/add_to_cart', ['class' => 'simple-form']); ?>
                                    <input type="hidden" name="produto_id" value="<?= $produto->id ?>">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="quantidade" class="form-label">Quantidade:</label>
                                            <input type="number" class="form-control form-control-lg" id="quantidade" 
                                                   name="quantidade" min="1" max="<?= $estoque->quantidade ?>" value="1" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                                <i class="fas fa-shopping-cart"></i> Comprar
                                            </button>
                                        </div>
                                    </div>
                                <?= form_close(); ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning mt-4">
                                <i class="fas fa-exclamation-triangle"></i> Produto temporariamente indisponível
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Informações Adicionais -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Informações de Envio</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-truck fa-2x text-primary me-3"></i>
                                <div>
                                    <h6 class="mb-1">Frete Grátis</h6>
                                    <small class="text-muted">Para compras acima de R$ 200</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-box-open fa-2x text-primary me-3"></i>
                                <div>
                                    <h6 class="mb-1">Envio Seguro</h6>
                                    <small class="text-muted">Embalagem especial</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-undo fa-2x text-primary me-3"></i>
                                <div>
                                    <h6 class="mb-1">Troca Garantida</h6>
                                    <small class="text-muted">Em até 7 dias</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Cupons Disponíveis -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Cupons Disponíveis</h5>
                </div>
                <div class="card-body">
                    <div class="coupon-item mb-3 p-3 border rounded">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-tag text-primary me-3"></i>
                            <div>
                                <h6 class="mb-1">GEEK10</h6>
                                <small class="text-muted">10% de desconto em compras acima de R$ 50</small>
                            </div>
                        </div>
                    </div>
                    <div class="coupon-item p-3 border rounded">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-tag text-primary me-3"></i>
                            <div>
                                <h6 class="mb-1">WELCOME20</h6>
                                <small class="text-muted">20% de desconto em compras acima de R$ 100</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Métodos de Pagamento -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Formas de Pagamento</h5>
                </div>
                <div class="card-body">
                    <div class="payment-methods">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-credit-card fa-2x text-primary me-3"></i>
                            <div>
                                <h6 class="mb-1">Cartão de Crédito</h6>
                                <small class="text-muted">Até 12x sem juros</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-barcode fa-2x text-primary me-3"></i>
                            <div>
                                <h6 class="mb-1">Boleto Bancário</h6>
                                <small class="text-muted">5% de desconto à vista</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-qrcode fa-2x text-primary me-3"></i>
                            <div>
                                <h6 class="mb-1">PIX</h6>
                                <small class="text-muted">10% de desconto à vista</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.price-tag {
    padding: 10px 0;
}
.stock-info {
    padding: 10px 0;
}
.variations-section {
    border-top: 1px solid #eee;
    padding-top: 20px;
}
.payment-methods i {
    width: 30px;
}
.coupon-item {
    transition: all 0.3s ease;
}
.coupon-item:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}
.form-control-lg, .form-select-lg {
    height: 48px;
}
.btn-lg {
    height: 48px;
}
.table > :not(caption) > * > * {
    padding: 1rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const variacaoSelect = document.getElementById('variacao_id');
    const quantidadeInput = document.getElementById('quantidade');
    
    if (variacaoSelect && quantidadeInput) {
        variacaoSelect.addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            const maxStock = option.getAttribute('data-stock');
            
            if (maxStock) {
                quantidadeInput.max = maxStock;
                if (parseInt(quantidadeInput.value) > parseInt(maxStock)) {
                    quantidadeInput.value = maxStock;
                }
            }
        });
    }
});
</script> 