# Mini ERP

Um sistema simples para controle de Pedidos, Produtos, Cupons e Estoque feito com CodeIgniter 3.

## Requisitos

- PHP 7.3 ou superior
- MySQL 5.7 ou superior
- Apache com mod_rewrite habilitado (para as URLs amigáveis)

## Instalação

1. Clone ou baixe este repositório
2. Configure seu servidor web para apontar para a pasta raiz do projeto
3. Crie um banco de dados MySQL
4. Importe o arquivo `database_setup.sql` para criar as tabelas necessárias
   - O script já inclui dados de exemplo com produtos temáticos geek, variações e estoque
5. Configure as credenciais do banco de dados em `application/config/database.php`
6. Configure a URL base em `application/config/config.php` de acordo com seu ambiente
7. Acesse o sistema pelo navegador

## Funcionalidades

### Produtos
- Cadastro, edição e exclusão de produtos
- Adição de variações de produtos (tamanhos, cores, etc.)
- Controle de estoque para cada produto e variação

### Cupons
- Cadastro, edição e exclusão de cupons de desconto
- Suporte a cupons percentuais ou de valor fixo
- Definição de valor mínimo para aplicação do cupom
- Controle de validade dos cupons

### Carrinho de Compras
- Adição de produtos ao carrinho
- Atualização de quantidades
- Aplicação de cupons de desconto
- Cálculo automático de frete baseado no valor do pedido

### Pedidos
- Finalização de compra com informações de entrega
- Busca de endereço automática pelo CEP (via ViaCEP)
- Envio de email de confirmação
- Gerenciamento de status dos pedidos
- Webhook para receber atualizações externas de status

## Acesso ao Sistema

Todas as funcionalidades são acessíveis pelo menu principal. O sistema não possui autenticação nesta versão para simplificar o teste.

## API/Webhook

O sistema fornece um endpoint de webhook para receber atualizações de status de pedidos:

```
POST /pedidos/webhook
Content-Type: application/json

{
  "id": 123,
  "status": "entregue"
}
```

Se o status for "cancelado", o pedido será removido e o estoque restaurado.

## Sobre o Projeto

Este projeto foi desenvolvido como um teste prático utilizando:

- CodeIgniter 3
- MySQL
- Bootstrap 5
- jQuery
- Font Awesome
- API ViaCEP 

## Dados de Demonstração

O banco de dados inclui dados iniciais para teste:

- 10 produtos temáticos geek (camisetas, action figures, canecas, etc.)
- 17 variações para os produtos (tamanhos, personagens, etc.)
- Níveis de estoque para cada produto/variação
- 3 cupons de desconto de exemplo 