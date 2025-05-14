# Mini ERP

Um sistema simples para controle de Pedidos, Produtos, Cupons e Estoque feito com CodeIgniter 3.

## Requisitos

- PHP 7.3 ou superior
- MySQL 5.7 ou superior
- Apache com mod_rewrite habilitado (para as URLs amigáveis)

## Instalação

### Opção 1: Instalação sem Docker

1. Clone ou baixe este repositório
   ```
   git clone https://github.com/JohnPonciano/mini-erp-test.git
   cd micro-erp
   ```
2. Configure seu servidor web (Apache/Nginx) para apontar para a pasta raiz do projeto
3. Crie um banco de dados MySQL
   ```
   mysql -u root -p
   CREATE DATABASE micro_erp;
   exit;
   ```
4. Importe o arquivo `database_setup.sql` para criar as tabelas necessárias
   ```
   mysql -u root -p micro_erp < database_setup.sql
   ```
   - O script já inclui dados de exemplo com produtos temáticos geek, variações e estoque
5. Configure as credenciais do banco de dados em `application/config/database.php`:
   ```php
   $db['default'] = array(
       'hostname' => 'localhost',
       'username' => 'seu_usuario',
       'password' => 'sua_senha',
       'database' => 'micro_erp',
       // ... outras configurações
   );
   ```
6. Configure a URL base em `application/config/config.php` de acordo com seu ambiente:
   ```php
   $config['base_url'] = 'http://localhost/micro-erp/';
   ```
7. Certifique-se de que o PHP tem as extensões necessárias habilitadas:
   - mysqli
   - gd
   - curl
   - zip
8. Se estiver usando Apache, verifique se o mod_rewrite está ativado:
   ```
   sudo a2enmod rewrite
   sudo service apache2 restart
   ```
9. Acesse o sistema pelo navegador: `http://localhost/micro-erp`

### Opção 2: Instalação com Docker

1. Clone ou baixe este repositório
   ```
   git clone https://github.com/JohnPonciano/mini-erp-test.git
   cd micro-erp
   ```
2. Certifique-se de que você tem Docker e Docker Compose instalados no seu sistema
   - Para verificar, execute `docker --version` e `docker-compose --version`
   - Caso não tenha, instale seguindo as instruções em https://docs.docker.com/get-docker/
3. Configure a URL base em `application/config/config.php`:
   ```php
   $config['base_url'] = 'http://localhost:3000/';
   ```
4. Configure as credenciais do banco de dados em `application/config/database.php`:
   ```php
   $db['default'] = array(
       'hostname' => 'db', // Nome do serviço no docker-compose
       'username' => 'root',
       'password' => '', // MYSQL_ALLOW_EMPTY_PASSWORD está como 'yes' no docker-compose
       'database' => 'micro_erp',
       // ... outras configurações
   );
   ```
5. Inicie os containers Docker:
   ```
   docker-compose up -d
   ```
   - Isso vai criar e iniciar dois containers: um para o PHP/Apache e outro para o MySQL
   - O banco de dados será criado automaticamente usando o arquivo database_setup.sql
6. Aguarde alguns segundos para que o banco de dados seja inicializado corretamente
7. Acesse o sistema pelo navegador: `http://localhost:3000`

### Verificação da Instalação

Para ambos os métodos de instalação, você deve ver a página inicial do sistema com a listagem de produtos.

## Parando o Ambiente Docker

Para parar os containers sem removê-los:
```
docker-compose stop
```

Para parar e remover os containers (mantém os volumes de dados):
```
docker-compose down
```

Para parar e remover tudo, incluindo volumes de dados:
```
docker-compose down -v
```

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