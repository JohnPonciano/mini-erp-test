<div class="row mb-4">
    <div class="col-md-6">
        <h2><?= $produto->nome ?></h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?= base_url('produtos') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
        <a href="<?= base_url('produtos/edit/' . $produto->id) ?>" class="btn btn-primary">
            <i class="fas fa-edit"></i> Editar
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Detalhes do Produto</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Nome:</h6>
                        <p><?= $produto->nome ?></p>
                    </div>
                    <div class="col-md-3">
                        <h6>Preço:</h6>
                        <p>R$ <?= number_format($produto->preco, 2, ',', '.') ?></p>
                    </div>
                    <div class="col-md-3">
                        <h6>Estoque:</h6>
                        <p><?= $estoque ? $estoque->quantidade : 0 ?> unidades</p>
                    </div>
                </div>
                
                <?php if(!empty($variacoes)): ?>
                    <h5 class="mt-4 mb-3">Variações</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Estoque</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($variacoes as $variacao): ?>
                                    <tr>
                                        <td><?= $variacao->nome ?></td>
                                        <td><?= $variacao->quantidade ?? 0 ?> unidades</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Comprar Produto</h5>
            </div>
            <div class="card-body">
                <?= form_open('produtos/add_to_cart'); ?>
                    <input type="hidden" name="produto_id" value="<?= $produto->id ?>">
                    
                    <?php if(!empty($variacoes)): ?>
                        <div class="mb-3">
                            <label for="variacao_id" class="form-label">Selecione a Variação:</label>
                            <select class="form-select" id="variacao_id" name="variacao_id" required>
                                <option value="">Selecione...</option>
                                <?php foreach($variacoes as $variacao): ?>
                                    <?php if($variacao->quantidade > 0): ?>
                                        <option value="<?= $variacao->id ?>"><?= $variacao->nome ?> (<?= $variacao->quantidade ?> em estoque)</option>
                                    <?php else: ?>
                                        <option value="<?= $variacao->id ?>" disabled><?= $variacao->nome ?> (Fora de estoque)</option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="quantidade" class="form-label">Quantidade:</label>
                        <input type="number" class="form-control" id="quantidade" name="quantidade" min="1" value="1" required>
                    </div>
                    
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-shopping-cart"></i> Adicionar ao Carrinho
                        </button>
                    </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div> 