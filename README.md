# 🚌 Sistema de Gestão de Viações

> Aplicação web desenvolvida em **PHP 8.4** com arquitetura **MVC**, painel administrativo completo e fluxo CRUD para gerenciamento de viações de ônibus.

---

## 📋 Índice

- [Sobre o Projeto](#-sobre-o-projeto)
- [Funcionalidades](#-funcionalidades)
- [Tecnologias](#-tecnologias)
- [Arquitetura MVC](#-arquitetura-mvc)
- [Estrutura de Pastas](#-estrutura-de-pastas)
- [Como Executar](#-como-executar)
- [Rotas da Aplicação](#-rotas-da-aplicação)
- [Banco de Dados](#-banco-de-dados)
- [Cache](#-cache)
- [Autoload PSR-4](#-autoload-psr-4)

---

## 📌 Sobre o Projeto

Este sistema foi desenvolvido para gerenciar viações de ônibus de forma centralizada. Conta com uma **Home** estilo portal de passagens e um **painel administrativo** completo com busca, filtros, ordenação, upload de logo e histórico de alterações.

---

## ✨ Funcionalidades

### 🏠 Home
- Exibição dinâmica de viações ativas
- Layout estilo portal de passagens
- Cache em arquivo JSON para otimizar consultas

### 🛠️ Painel Administrativo (ADM)
- Listagem de viações com paginação
- Busca por nome
- Filtro por status (ativo/inativo)
- Ordenação por coluna
- Cadastro de novas viações
- Edição de viações existentes
- Exclusão de viações
- Upload de logo (`JPG`, `PNG`, `WEBP`)

### 📜 Histórico
- Registro de todas as operações: criação, edição e exclusão
- Listagem completa pelo painel ADM

---

## 🛠️ Tecnologias

| Tecnologia | Versão |
|---|---|
| PHP | `8.4` |
| MySQL | `8.0` |
| Apache | — |
| Docker | — |
| Docker Compose | — |
| Composer | PSR-4 Autoload |

---

## 🏗️ Arquitetura MVC

O projeto segue rigorosamente o padrão **Model-View-Controller**, separando as responsabilidades em camadas:

```
Requisição HTTP
      │
      ▼
 public/index.php        ← Front Controller
      │
      ▼
 Core/Router.php         ← Registro e despacho de rotas
      │                    (suporte a parâmetros e method spoofing via _method)
      ▼
 Controllers/*           ← Controle do fluxo HTTP
      │
      ▼
 Services/*              ← Regras de negócio
      │
      ▼
 Repositories/*          ← Acesso a dados via PDO
      │
      ▼
 Models/*                ← Objetos de domínio
      │
      ▼
 views/*                 ← Renderização das telas
```

---

## 📁 Estrutura de Pastas

```
MVC_site/
├── docker-compose.yml
├── Dockerfile
├── composer.json
├── .gitignore
└── src/
    ├── Controllers/         # Controladores HTTP
    ├── Core/
    │   └── Router.php       # Roteador com method spoofing
    ├── database/
    │   ├── init.sql         # Script de criação das tabelas
    │   └── db.php           # Conexão PDO e utilitários de cache
    ├── Models/              # Objetos de domínio
    ├── Repositories/        # Camada de acesso a dados
    ├── Services/            # Regras de negócio
    ├── Validators/          # Validações de entrada
    ├── views/               # Templates de renderização
    ├── routes/              # Definição das rotas
    └── cache/
        └── viacoes_ativas.json  # Cache da Home
```

---

## 🚀 Como Executar

### Pré-requisitos

- [Docker](https://www.docker.com/) instalado
- [Docker Compose](https://docs.docker.com/compose/) instalado

### Passo a passo

**1. Clone o repositório:**

```bash
git clone https://github.com/Mrcidele/MVC_site.git
cd MVC_site
```

**2. Suba os containers:**

```bash
docker compose up --build -d
```

**3. Acesse no navegador:**

| Página | URL |
|---|---|
| Home | `http://localhost/` |
| Home (alternativa) | `http://localhost:8081/` |
| ADM — Viações | `http://localhost/admin/viacoes` |
| ADM — Histórico | `http://localhost/admin/historico` |

**4. Para parar os containers:**

```bash
docker compose down
```

---

## 🗺️ Rotas da Aplicação

| Método | Rota | Ação | Controller |
|---|---|---|---|
| `GET` | `/` | Página inicial | `HomeController@index` |
| `GET` | `/admin/viacoes` | Listar viações | `ViacaoController@index` |
| `GET` | `/admin/viacoes/create` | Formulário de cadastro | `ViacaoController@create` |
| `POST` | `/admin/viacoes` | Criar viação | `ViacaoController@store` |
| `GET` | `/admin/viacoes/{id}/edit` | Formulário de edição | `ViacaoController@edit` |
| `PUT` | `/admin/viacoes/{id}` | Atualizar viação | `ViacaoController@update` |
| `DELETE` | `/admin/viacoes/{id}` | Excluir viação | `ViacaoController@destroy` |
| `GET` | `/admin/historico` | Listar histórico | `HistoricoController@index` |

> **Method Spoofing:** Os métodos `PUT` e `DELETE` são simulados via campo `_method` em formulários HTML, tratado pelo `Core/Router.php`.

---

## 🗄️ Banco de Dados

O script de inicialização está em `src/database/init.sql` e cria as seguintes tabelas:

### `viacoes`
Armazena os dados das viações cadastradas, incluindo nome, status e logo.

### `viacoes_historico`
Registra automaticamente todas as operações realizadas (criação, edição e exclusão) com timestamp e detalhes da alteração.

A conexão com o banco é feita via **PDO** e está centralizada em `src/database/db.php`.

---

## ⚡ Cache

Para otimizar o carregamento da Home, foi implementado um sistema de **cache em arquivo JSON**:

| Operação | Função |
|---|---|
| Leitura | `getCachedData('viacoes_ativas')` |
| Escrita | `setCachedData('viacoes_ativas', ...)` |
| Invalidação | Automática ao criar, editar ou excluir uma viação |
| TTL padrão | `300s` (5 minutos) |

O arquivo de cache é armazenado em `src/cache/viacoes_ativas.json`.

---

## 📦 Autoload PSR-4

Configurado no `composer.json`:

```json
"autoload": {
  "psr-4": {
    "App\\": "src/"
  },
  "files": [
    "src/database/db.php"
  ]
}
```

Caso altere a estrutura de namespaces, regenere o autoload com:

```bash
docker compose exec app composer dump-autoload -o
```

---

## 👤 Autor

**Mrcidele** — [@Mrcidele](https://github.com/Mrcidele)

---

> Projeto desenvolvido com foco em boas práticas de arquitetura PHP, separação de responsabilidades e containerização com Docker.