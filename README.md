# Projeto Yii2 com Docker

Este projeto é uma aplicação Yii2 Basic configurada para execução em containers Docker, incluindo PHP 8.2, Nginx e MySQL 8.0.

## 🐳 Configuração Docker

### Pré-requisitos

- Docker (versão 20.10+)
- Docker Compose (versão 2.0+)
- Git

### Estrutura dos Containers

- **app**: Container PHP 8.2-FPM com Composer
- **nginx**: Servidor web Nginx (porta 8080)
- **db**: Banco de dados MySQL 8.0 (porta 3307)

## 🚀 Instalação e Execução

### 1. Clone o projeto

```bash
git clone <url-do-repositorio>
cd exam
```

### 2. Subir os containers

```bash
# Construir e iniciar todos os containers
docker-compose up -d --build

# Verificar se os containers estão rodando
docker-compose ps
```

### 3. Instalar dependências do Composer

```bash
# Entrar no container da aplicação
docker-compose exec app bash

# Dentro do container, instalar as dependências
composer install

# Sair do container
exit
```
ou
```bash
# Rodar instalação das dependências fora do container
docker-compose exec app composer install
```

### 4. Configurar o banco de dados

```bash
# Executar as migrações (se houver)
docker-compose exec app php yii migrate

# Ou configurar manualmente no arquivo config/db.php
```

### 5. Acessar a aplicação

- **Frontend**: http://localhost:8080
- **MySQL**: localhost:3307
  - Usuário: `main`
  - Senha: `password`
  - Database: `main`

## 🛠️ Comandos Úteis

### Gerenciamento dos Containers

```bash
# Iniciar os containers
docker-compose up -d

# Parar os containers
docker-compose down

# Parar e remover volumes (CUIDADO: apaga dados do banco)
docker-compose down -v

# Ver logs
docker-compose logs -f

# Ver logs de um serviço específico
docker-compose logs -f app
```

### Comandos Yii2

```bash
# Executar comandos Yii dentro do container
docker-compose exec app php yii

# Listar comandos disponíveis
docker-compose exec app php yii help

# Gerar cache
docker-compose exec app php yii cache/flush-all
```

### Acesso aos Containers

```bash
# Entrar no container da aplicação
docker-compose exec app bash

# Entrar no container do Nginx
docker-compose exec nginx bash

# Entrar no container do MySQL
docker-compose exec db mysql -u main -p
```

## 🔧 Configurações

### Banco de Dados

As configurações do banco estão em `src/config/db.php`:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=db;dbname=main',
    'username' => 'main',
    'password' => 'password',
    'charset' => 'utf8',
];
```

### Nginx

A configuração do Nginx está em `docker/nginx/default.conf` e aponta para o diretório `src/web`.

### PHP

O Dockerfile do PHP está em `docker/php/Dockerfile` com as extensões necessárias para o Yii2.

## 📁 Estrutura do Projeto

```
exam/
├── docker/                 # Configurações Docker
│   ├── nginx/              # Configuração Nginx
│   └── php/                # Dockerfile PHP
├── src/                    # Código fonte Yii2
│   ├── config/             # Configurações
│   ├── controllers/        # Controllers
│   ├── models/             # Models
│   ├── views/              # Views
│   ├── web/                # Arquivos públicos
│   └── composer.json       # Dependências PHP
└── docker-compose.yml      # Configuração dos containers
```

## 🐛 Solução de Problemas

### Container não sobe

```bash
# Verificar logs
docker-compose logs

# Reconstruir containers
docker-compose down
docker-compose up -d --build
```

### Problemas de permissão

```bash
# Ajustar permissões na pasta src
sudo chown -R $USER:$USER src/
chmod -R 755 src/runtime
chmod -R 755 src/web/assets
```

### Banco de dados não conecta

- Verificar se o container `db` está rodando
- Conferir as credenciais em `src/config/db.php`
- Aguardar alguns segundos para o MySQL inicializar completamente

## 🧪 Testes

```bash
# Executar testes dentro do container
docker-compose exec app vendor/bin/codecept run
```

## 📝 Desenvolvimento

Para desenvolvimento ativo:

```bash
# Manter logs visíveis
docker-compose up

# Em outro terminal, fazer alterações no código
# As mudanças são refletidas automaticamente via volume mount
```