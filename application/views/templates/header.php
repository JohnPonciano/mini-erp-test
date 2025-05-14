<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Mini ERP' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            padding-top: 60px;
            padding-bottom: 40px;
        }
        .bg-primary {
            background-color: #4e73df !important;
        }
        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2653d4;
        }
        .card {
            margin-bottom: 24px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }
        .nav-link {
            color: rgba(255, 255, 255, 0.8);
        }
        .nav-link:hover {
            color: #fff;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .alert {
            margin-top: 20px;
        }
        .footer {
            padding: 25px 0;
            margin-top: 20px;
            background-color: #f8f9fc;
            border-top: 1px solid #e3e6f0;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url() ?>">Mini ERP</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url() ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('produtos') ?>">Produtos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('cupons') ?>">Cupons</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('pedidos') ?>">Pedidos</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('carrinho') ?>">
                            <i class="fas fa-shopping-cart"></i> Carrinho
                            <?php if ($this->cart->total_items() > 0): ?>
                                <span class="badge bg-danger"><?= $this->cart->total_items() ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= $this->session->flashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= $this->session->flashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <h1 class="mt-4 mb-4"><?= $title ?? 'Mini ERP' ?></h1> 