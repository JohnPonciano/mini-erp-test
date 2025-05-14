<div class="py-5 text-center">
    <h1 class="display-5 fw-bold">Mini ERP</h1>
    <div class="col-lg-6 mx-auto">
        <p class="lead mb-4">Sistema de Controle de Pedidos, Produtos, Cupons e Estoque.</p>
        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
            <a href="<?= base_url('produtos') ?>" class="btn btn-primary btn-lg px-4 gap-3">
                <i class="fas fa-shopping-basket"></i> Ver Produtos
            </a>
            <a href="<?= base_url('carrinho') ?>" class="btn btn-outline-secondary btn-lg px-4">
                <i class="fas fa-shopping-cart"></i> Carrinho
            </a>
        </div>
    </div>
</div>

<hr class="my-5">

<h2 class="mb-4">Produtos Disponíveis</h2>

<?php if(empty($produtos)): ?>
    <div class="alert alert-info">
        Nenhum produto cadastrado ainda.
    </div>
<?php else: ?>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach($produtos as $produto): ?>
            <div class="col">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?= $produto->nome ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted">R$ <?= number_format($produto->preco, 2, ',', '.') ?></h6>
                        <p class="card-text">
                            <span class="badge <?= isset($produto->quantidade) && $produto->quantidade > 0 ? 'bg-success' : 'bg-danger' ?>">
                                <?= isset($produto->quantidade) && $produto->quantidade > 0 ? 'Em estoque' : 'Fora de estoque' ?>
                            </span>
                        </p>
                    </div>
                    <div class="card-footer">
                        <a href="<?= base_url('produtos/view/' . $produto->id) ?>" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Ver Detalhes
                        </a>
                        <?php if(isset($produto->quantidade) && $produto->quantidade > 0): ?>
                            <form action="<?= base_url('produtos/add_to_cart') ?>" method="post" class="d-inline">
                                <input type="hidden" name="produto_id" value="<?= $produto->id ?>">
                                <input type="hidden" name="quantidade" value="1">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-cart-plus"></i> Comprar
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?> 