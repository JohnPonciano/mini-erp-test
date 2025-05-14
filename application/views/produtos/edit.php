<div class="row mb-4">
    <div class="col-md-6">
        <h2>Editar Produto</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?= base_url('produtos') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Editar Produto: <?= $produto->nome ?></h5>
    </div>
    <div class="card-body">
        <?= validation_errors('<div class="alert alert-danger">', '</div>'); ?>
        
        <?= form_open('produtos/edit/' . $produto->id); ?>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nome" class="form-label">Nome do Produto</label>
                    <input type="text" class="form-control" id="nome" name="nome" value="<?= set_value('nome', $produto->nome) ?>" required>
                </div>
                <div class="col-md-3">
                    <label for="preco" class="form-label">Preço (R$)</label>
                    <input type="number" class="form-control" id="preco" name="preco" step="0.01" min="0" value="<?= set_value('preco', $produto->preco) ?>" required>
                </div>
                <div class="col-md-3">
                    <label for="quantidade" class="form-label">Quantidade em Estoque</label>
                    <input type="number" class="form-control" id="quantidade" name="quantidade" min="0" value="<?= set_value('quantidade', $estoque ? $estoque->quantidade : 0) ?>" required>
                </div>
            </div>
            
            <h5 class="mt-4 mb-3">Variações do Produto</h5>
            
            <?php if(!empty($variacoes)): ?>
                <h6>Variações Existentes</h6>
                <div id="variacoes-existentes">
                    <?php foreach($variacoes as $index => $variacao): ?>
                        <div class="row mb-3 variacao-item">
                            <input type="hidden" name="variacao_ids[]" value="<?= $variacao->id ?>">
                            <div class="col-md-6">
                                <label class="form-label">Nome da Variação</label>
                                <input type="text" class="form-control" name="variacao_nomes[]" value="<?= $variacao->nome ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Quantidade em Estoque</label>
                                <input type="number" class="form-control" name="variacao_qtds[]" min="0" value="<?= $variacao->quantidade ?? 0 ?>" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <a href="<?= base_url('produtos/delete_variacao/' . $variacao->id) ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta variação?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <h6 class="mt-3">Adicionar Novas Variações</h6>
            <div id="variacoes-container">
                <div class="row mb-3 variacao-item">
                    <div class="col-md-6">
                        <label class="form-label">Nome da Variação</label>
                        <input type="text" class="form-control" name="novas_variacoes_nomes[]" placeholder="Ex: Tamanho P, Cor Azul, etc">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Quantidade em Estoque</label>
                        <input type="number" class="form-control" name="novas_variacoes_qtds[]" min="0" value="0">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger remove-variacao" style="display: none;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="mt-2 mb-4">
                <button type="button" id="add-variacao" class="btn btn-sm btn-success">
                    <i class="fas fa-plus"></i> Adicionar Variação
                </button>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar Alterações
                </button>
                <a href="<?= base_url('produtos') ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        <?= form_close(); ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add new variation
        document.getElementById('add-variacao').addEventListener('click', function() {
            var container = document.getElementById('variacoes-container');
            var newItem = document.querySelector('#variacoes-container .variacao-item').cloneNode(true);
            
            // Clear input values
            newItem.querySelectorAll('input').forEach(function(input) {
                input.value = '';
                if (input.type === 'number') {
                    input.value = '0';
                }
            });
            
            // Show remove button
            newItem.querySelector('.remove-variacao').style.display = 'block';
            
            // Add event listener to remove button
            newItem.querySelector('.remove-variacao').addEventListener('click', function() {
                container.removeChild(newItem);
            });
            
            container.appendChild(newItem);
        });
    });
</script> 