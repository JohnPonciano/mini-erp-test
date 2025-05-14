<div class="row mb-4">
    <div class="col-md-6">
        <h2>Editar Cupom</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?= base_url('cupons') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Editar Cupom: <?= $cupom->codigo ?></h5>
    </div>
    <div class="card-body">
        <?= validation_errors('<div class="alert alert-danger">', '</div>'); ?>
        
        <?= form_open('cupons/edit/' . $cupom->id); ?>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="codigo" class="form-label">Código do Cupom</label>
                    <input type="text" class="form-control" id="codigo" name="codigo" value="<?= set_value('codigo', $cupom->codigo) ?>" required>
                    <div class="form-text">Ex: WELCOME10, FRETEGRATIS, etc.</div>
                </div>
                <div class="col-md-6">
                    <label for="data_validade" class="form-label">Data de Validade</label>
                    <input type="date" class="form-control" id="data_validade" name="data_validade" value="<?= set_value('data_validade', $cupom->data_validade) ?>" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="tipo" class="form-label">Tipo de Desconto</label>
                    <select class="form-select" id="tipo" name="tipo" required>
                        <option value="percentual" <?= $cupom->tipo === 'percentual' ? 'selected' : '' ?>>Percentual (%)</option>
                        <option value="valor" <?= $cupom->tipo === 'valor' ? 'selected' : '' ?>>Valor Fixo (R$)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="desconto" class="form-label">Valor do Desconto</label>
                    <input type="number" class="form-control" id="desconto" name="desconto" step="0.01" min="0" value="<?= set_value('desconto', $cupom->desconto) ?>" required>
                    <div class="form-text">Se percentual, informe o valor sem o símbolo % (ex: 10 para 10%).</div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="valor_minimo" class="form-label">Valor Mínimo de Compra (opcional)</label>
                <input type="number" class="form-control" id="valor_minimo" name="valor_minimo" step="0.01" min="0" value="<?= set_value('valor_minimo', $cupom->valor_minimo) ?>">
                <div class="form-text">Valor mínimo de compra necessário para aplicar o cupom. Deixe em branco se não houver valor mínimo.</div>
            </div>
            
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="status" name="status" value="1" <?= $cupom->status ? 'checked' : '' ?>>
                    <label class="form-check-label" for="status">Cupom Ativo</label>
                </div>
                <div class="form-text">Desmarque para desativar o cupom temporariamente.</div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar Alterações
                </button>
                <a href="<?= base_url('cupons') ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        <?= form_close(); ?>
    </div>
</div> 