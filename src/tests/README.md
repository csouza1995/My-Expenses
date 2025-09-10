# Guia de Testes - Sistema de Gerenciamento de Despesas

## 📋 Visão Geral

Este projeto utiliza **Codeception** para testes automatizados com cobertura completa:
- **Unit Tests** (24 testes)
- **Functional Tests** (14 testes) 
- **Acceptance Tests** (8 testes)
- **API Tests** (23 testes)

## 🚀 Configuração Rápida

### Pré-requisitos
- PHP 7.4+
- Composer
- SQLite3

### Primeiro Setup
```bash
# 1. Clone o repositório
git clone <repository-url>
cd exam/src

# 2. Instale dependências
composer install

# 3. Os testes se configuram automaticamente na primeira execução!
vendor/bin/codecept run
```

## 🔄 Banco de Dados de Teste

### Configuração Automática
O sistema configura automaticamente o banco de dados de teste:
- **Localização**: `tests/_output/test.db`
- **Schema**: Criado automaticamente se não existir
- **Dados de teste**: Inseridos automaticamente

### Credenciais de Teste
```
Email: tester@example.com
Senha: ABCdef123!@#
```

### Reset Manual (se necessário)
```bash
# Reset completo do banco de teste
php tests/_data/reset_test_db.php

# Ou delete o arquivo e execute os testes
rm tests/_output/test.db
vendor/bin/codecept run
```

## 🧪 Executando Testes

### Todos os Testes
```bash
vendor/bin/codecept run
```

### Por Categoria
```bash
# Unit Tests
vendor/bin/codecept run unit

# Functional Tests  
vendor/bin/codecept run functional

# Acceptance Tests
vendor/bin/codecept run acceptance

# API Tests
vendor/bin/codecept run api
```

### Testes Específicos
```bash
# Teste específico
vendor/bin/codecept run api:ApiAuthCest:testLoginSuccess

# Por grupo/tag
vendor/bin/codecept run -g auth
```

### Com Verbose/Debug
```bash
# Mais detalhes
vendor/bin/codecept run --debug

# Parar no primeiro erro
vendor/bin/codecept run --fail-fast
```

## 📊 Estrutura dos Testes

### Unit Tests (`tests/unit/`)
- **LoginFormTest**: Validação de formulário de login
- **UserTest**: Model de usuário e autenticação
- **AlertTest**: Widget de alertas

### Functional Tests (`tests/functional/`)
- **LoginFormCest**: Fluxo de login completo
- **ExpenseFormCest**: Gestão de despesas via formulário

### Acceptance Tests (`tests/acceptance/`)
- **LoginCest**: Login end-to-end
- **ExpenseCest**: Listagem de despesas
- **HomeCest**: Navegação principal
- **SecurityCest**: Segurança da aplicação

### API Tests (`tests/api/`)
- **ApiAuthCest**: Autenticação JWT
- **ApiExpenseCest**: CRUD de despesas via API

## 🛠 Dados de Teste

### Usuário de Teste
```php
ID: 1
Nome: Test User
Email: tester@example.com
Senha: ABCdef123!@#
```

### Categorias de Teste
1. Food
2. Transport  
3. Entertainment
4. Health
5. Education

### Despesas de Exemplo
- Lunch at restaurant - $25.50 (Food)
- Bus ticket - $3.75 (Transport)
- Movie tickets - $18.00 (Entertainment)

## 🔧 Resolução de Problemas

### Banco de Dados Corrompido
```bash
# Reset completo
php tests/_data/reset_test_db.php
```

### Falhas de Autenticação
Verifique se o usuário de teste existe:
```bash
sqlite3 tests/_output/test.db "SELECT email FROM users WHERE email='tester@example.com';"
```

### Limpeza Total
```bash
# Remove todos os arquivos de teste
rm -rf tests/_output/*
vendor/bin/codecept run
```

## 📝 Adicionando Novos Testes

### Unit Test
```bash
vendor/bin/codecept generate:test unit NewModelTest
```

### Functional Test  
```bash
vendor/bin/codecept generate:cest functional NewFeatureCest
```

### API Test
```bash
vendor/bin/codecept generate:cest api NewApiCest
```

## 📈 Status Atual dos Testes

### ✅ Funcionando Perfeitamente (100%)
- **Acceptance Tests**: 8/8 ✅ - Navegação e login end-to-end
- **Unit Tests**: 23/24 ✅ (96%) - Apenas 1 teste de validação pendente

### 🟡 Em Progresso (Necessitam correções de schema)
- **API Tests**: 16/23 ✅ (70%) - Autenticação OK, problemas de campo `category`
- **Functional Tests**: 9/14 ✅ (64%) - Problemas com category null

### 🔧 Principais Problemas
1. **Schema Database**: Campo `category` vs `category_id` 
2. **Enum Validation**: category_id pode ser null
3. **API Response Format**: Pequenos ajustes no formato de resposta

### 📝 Para Atingir 100%
```bash
# Execute para ver os problemas específicos:
vendor/bin/codecept run --fail-fast

# Reset do banco se necessário:
php tests/_data/reset_test_db.php
```

## 🔍 Debug e Logs

### Arquivos de Debug
- `tests/_output/`: Capturas de tela e logs de falhas
- `runtime/logs/`: Logs da aplicação

### Debug de API
```bash
# Ver requisições/respostas da API
vendor/bin/codecept run api --debug
```

## 🚨 CI/CD

### Para Integração Contínua
```bash
# Script para CI
#!/bin/bash
composer install --no-dev --optimize-autoloader
php tests/_data/reset_test_db.php
vendor/bin/codecept run --xml
```

---

**Nota**: O sistema de testes é completamente automatizado. Após `git pull`, simplesmente execute `vendor/bin/codecept run` e tudo será configurado automaticamente!
