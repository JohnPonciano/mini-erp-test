    </div> <!-- /container -->

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?= date('Y') ?> Mini ERP - Sistema de Gerenciamento</p>
                </div>
                <div class="col-md-6 text-end">
                    <p>Desenvolvido com CodeIgniter 3</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // CEP lookup function
        function buscarCep() {
            var cep = $('#cep').val().replace(/\D/g, '');
            
            if (cep.length !== 8) {
                return;
            }
            
            $('#endereco').val('Carregando...');
            $('#cidade').val('Carregando...');
            $('#estado').val('');
            
            $.ajax({
                url: '<?= base_url('carrinho/buscar_cep') ?>',
                type: 'POST',
                data: { cep: cep },
                dataType: 'json',
                success: function(data) {
                    if (!data.erro) {
                        $('#endereco').val(data.logradouro);
                        $('#cidade').val(data.localidade);
                        $('#estado').val(data.uf);
                    } else {
                        alert('CEP não encontrado.');
                        $('#endereco').val('');
                        $('#cidade').val('');
                        $('#estado').val('');
                    }
                },
                error: function() {
                    alert('Erro ao buscar CEP. Tente novamente.');
                    $('#endereco').val('');
                    $('#cidade').val('');
                    $('#estado').val('');
                }
            });
        }
        
        // Attach to cep field if it exists
        $(document).ready(function() {
            var cepField = document.getElementById('cep');
            if (cepField) {
                $('#cep').blur(buscarCep);
            }
        });
    </script>
</body>
</html> 