<div class="row mb-4">
    <div class="col-md-6">
        <h2>Gerenciar Cupons</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?= base_url('cupons/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Cupom
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Lista de Cupons</h5>
    </div>
    <div class="card-body">
        <?php if(empty($cupons)): ?>
            <div class="alert alert-info">
                Nenhum cupom cadastrado.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Desconto</th>
                            <th>Valor Mínimo</th>
                            <th>Validade</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($cupons as $cupom): ?>
                            <tr>
                                <td><?= $cupom->codigo ?></td>
                                <td>
                                    <?php if($cupom->tipo === 'percentual'): ?>
                                        <?= $cupom->desconto ?>%
                                    <?php else: ?>
                                        R$ <?= number_format($cupom->desconto, 2, ',', '.') ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($cupom->valor_minimo): ?>
                                        R$ <?= number_format($cupom->valor_minimo, 2, ',', '.') ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y', strtotime($cupom->data_validade)) ?></td>
                                <td class="text-center">
                                    <?php 
                                        $hoje = new DateTime();
                                        $validade = new DateTime($cupom->data_validade);
                                        $expirado = $validade < $hoje;
                                    ?>
                                    
                                    <?php if($expirado): ?>
                                        <span class="badge bg-danger">Expirado</span>
                                    <?php elseif($cupom->status): ?>
                                        <span class="badge bg-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="<?= base_url('cupons/edit/' . $cupom->id) ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('cupons/delete/' . $cupom->id) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este cupom?');">
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