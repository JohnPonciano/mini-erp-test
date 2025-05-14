<div class="row mb-4">
    <div class="col-md-6">
        <h2>Produtos</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?= base_url('produtos/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Produto
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Lista de Produtos</h5>
    </div>
    <div class="card-body">
        <?php if(empty($produtos)): ?>
            <div class="alert alert-info">
                Nenhum produto cadastrado.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>Preço</th>
                            <th>Estoque</th>
                            <th>Variações</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($produtos as $produto): ?>
                            <tr>
                                <td><?= $produto->id ?></td>
                                <td><?= $produto->nome ?></td>
                                <td>R$ <?= number_format($produto->preco, 2, ',', '.') ?></td>
                                <td>
                                    <?php if(isset($produto->quantidade)): ?>
                                        <?= $produto->quantidade ?>
                                    <?php else: ?>
                                        0
                                    <?php endif; ?>
                                </td>
                                <td><?= $produto->total_variacoes ?? 0 ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= base_url('produtos/view/' . $produto->id) ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= base_url('produtos/edit/' . $produto->id) ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('produtos/delete/' . $produto->id) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este produto?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div> 