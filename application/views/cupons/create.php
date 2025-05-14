<div class="row mb-4">
    <div class="col-md-6">
        <h2>Novo Cupom</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="<?= base_url('cupons') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Cadastrar Novo Cupom</h5>
    </div>
    <div class="card-body">
        <?= validation_errors('<div class="alert alert-danger">', '</div>'); ?>
        
        <?= form_open('cupons/create'); ?>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="codigo" class="form-label">Código do Cupom</label>
                    <input type="text" class="form-control" id="codigo" name="codigo" value="<?= set_value('codigo') ?>" required>
                    <div class="form-text">Ex: WELCOME10, FRETEGRATIS, etc.</div>
                </div>
                <div class="col-md-6">
                    <label for="data_validade" class="form-label">Data de Validade</label>
                    <input type="date" class="form-control" id="data_validade" name="data_validade" value="<?= set_value('data_validade') ?>" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="tipo" class="form-label">Tipo de Desconto</label>
                    <select class="form-select" id="tipo" name="tipo" required>
                        <option value="percentual" <?= set_select('tipo', 'percentual', true) ?>>Percentual (%)</option>
                        <option value="valor" <?= set_select('tipo', 'valor') ?>>Valor Fixo (R$)</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="desconto" class="form-label">Valor do Desconto</label>
                    <input type="number" class="form-control" id="desconto" name="desconto" step="0.01" min="0" value="<?= set_value('desconto') ?>" required>
                    <div class="form-text">Se percentual, informe o valor sem o símbolo % (ex: 10 para 10%).</div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="valor_minimo" class="form-label">Valor Mínimo de Compra (opcional)</label>
                <input type="number" class="form-control" id="valor_minimo" name="valor_minimo" step="0.01" min="0" value="<?= set_value('valor_minimo') ?>">
                <div class="form-text">Valor mínimo de compra necessário para aplicar o cupom. Deixe em branco se não houver valor mínimo.</div>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar Cupom
                </button>
                <a href="<?= base_url('cupons') ?>" class="btn btn-secondary">Cancelar</a>
            </div>
        <?= form_close(); ?>
    </div>
</div> 