<div class="row mb-4">
    <div class="col-md-6">
        <h2>Gerenciar Pedidos</h2>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Lista de Pedidos</h5>
    </div>
    <div class="card-body">
        <?php if(empty($pedidos)): ?>
            <div class="alert alert-info">
                Nenhum pedido encontrado.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Data</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pedidos as $pedido): ?>
                            <tr>
                                <td>#<?= $pedido->id ?></td>
                                <td><?= $pedido->cliente_nome ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($pedido->created_at)) ?></td>
                                <td class="text-end">R$ <?= number_format($pedido->total, 2, ',', '.') ?></td>
                                <td class="text-center">
                                    <span class="badge <?= $pedido->status === 'pendente' ? 'bg-warning' : 'bg-success' ?>">
                                        <?= ucfirst($pedido->status) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('pedidos/view/' . $pedido->id) ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Detalhes
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div> 