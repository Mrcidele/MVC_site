# 🚌 Sistema de Gestão de Viações

> Aplicação web de nível empresarial desenvolvida em **PHP 8.4** com **Clean Architecture**, **MVC**, painel administrativo protegido e fluxo CRUD fortemente tipado com **DTOs**.

![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker&logoColor=white)

---

## 📋 Índice

- [Sobre o Projeto](#-sobre-o-projeto)
- [Funcionalidades e Segurança](#-funcionalidades-e-segurança)
- [Arquitetura e Clean Code](#-arquitetura-e-clean-code)
- [Como Executar](#-como-executar)
- [Acesso Administrativo](#-acesso-administrativo)
- [Rotas da Aplicação](#-rotas-da-aplicação)
- [Banco de Dados e Auditoria](#-banco-de-dados-e-auditoria)
- [Cache](#-cache)

---

## 📌 Sobre o Projeto

Este sistema foi desenvolvido para centralizar a gestão de empresas de transporte (viações). Diferencia-se por seguir princípios de **arquitetura defensiva**, garantindo que o ambiente seja 100% replicável via Docker e que a integridade dos dados seja preservada através de um sistema de auditoria imutável.

---

## ✨ Funcionalidades e Segurança

### 🛡️ Autenticação e Sessões
- **Criptografia:** Senhas armazenadas com hash **Bcrypt** e verificadas via `password_verify`.
- **Proteção de Rotas:** Bloqueio de acesso não autorizado através de verificação de sessão no construtor dos Controllers Administrativos.

### 🛠️ Painel Administrativo (ADM)
- **Filtragem Dinâmica:** Busca e ordenação parametrizada com prevenção contra *SQL Injection* via PDO Prepared Statements.
- **Upload Blindado:** O sistema não confia na extensão do ficheiro. Utiliza `mime_content_type` para validar os *bytes mágicos* (JPG, PNG, WEBP) e renomeia os ficheiros com `uniqid()` para evitar ataques de execução de scripts.
- **Contratos de Dados (DTOs):** Uso de **Data Transfer Objects** imutáveis para transportar dados entre o Controller e o Service, garantindo tipagem estrita e evitando manipulação indevida de dados.

### 📜 Auditoria Imutável (Histórico)
- **Log de Ações:** Registo detalhado de criação, edição e exclusão.
- **Antes e Depois:** Armazenamento de mudanças em formato JSON para comparação visual.
- **Integridade Vitalícia:** A arquitetura utiliza **Índices (`INDEX`)** em vez de Chaves Estrangeiras (`FK`) na tabela de histórico. Isto permite que o registo de auditoria permaneça intacto mesmo após a exclusão definitiva de uma viação.

---

## 🏗️ Arquitetura e Clean Code

O projeto segue a **Separação de Responsabilidades (SoC)**, garantindo um código manutenível e escalável:

```text
Requisição HTTP
      │
      ▼
 public/index.php        ← Front Controller (Ponto de entrada)
      │
      ▼
 Core/Router.php         ← Despacho de rotas e Method Spoofing
      │
      ▼
 Controllers/*           ← Camada fina: Gere o fluxo e empacota DTOs
      │
      ▼
 DTOs/*                  ← Objetos de transporte imutáveis e tipados
      │
      ▼
 Services/*              ← Regras de negócio, Auditoria e Segurança
      │
      ▼
 Repositories/*          ← Camada de persistência (Acesso ao Banco via PDO)
      │
      ▼
 Models/*                ← Objetos de domínio
```

---

## 🚀 Como Executar

### Pré-requisitos
- Docker e Docker Compose instalados.

### Passo a passo

**1. Clone o repositório:**
```bash
git clone https://github.com/Mrcidele/MVC_site.git
cd MVC_site
```

**2. Suba os contentores:**
```bash
docker compose up --build -d
```

**3. Aceda no navegador:**
- Home: http://localhost/ ou http://localhost:8081/
- Painel ADM: http://localhost/admin/viacoes

---

## 🔐 Acesso Administrativo

O script de automação (`init.sql`) cria o utilizador padrão automaticamente:

| Campo | Valor |
|-------|-------|
| E-mail | admin@admin.com |
| Senha | admin123 |

---

## 🗺️ Rotas da Aplicação

| Método | Rota | Ação | Controller |
|--------|------|------|------------|
| GET | `/` | Página inicial | `HomeController@index` |
| GET | `/login` | Tela de Login | `LoginController@index` |
| POST | `/login` | Processar Login | `LoginController@login` |
| GET | `/admin/viacoes` | Listar viações | `ViacaoController@index` |
| POST | `/admin/viacoes` | Criar viação (Usa DTO) | `ViacaoController@store` |
| PUT | `/admin/viacoes/{id}` | Atualizar viação (Usa DTO) | `ViacaoController@update` |
| DELETE | `/admin/viacoes/{id}` | Excluir viação | `ViacaoController@destroy` |
| GET | `/admin/historico` | Listar histórico | `HistoricoController@index` |

---

## 🧪 Testes Automatizados

O projeto possui uma suite de testes unitários configurada com PHPUnit, assegurando a estabilidade das regras de negócio, validações de entrada (`ViacaoValidatorTest`) e integridade dos modelos de dados (`ViacaoTest`).

Para correr os testes automatizados dentro do ambiente Docker, execute:

```bash
docker compose exec app ./vendor/bin/phpunit
```

---

## ⚡ Cache Integrado

Para otimizar a Home, o sistema utiliza cache em ficheiro JSON:

- **Leitura:** `getCachedData()` recupera dados se o TTL (300s) for válido.
- **Invalidação:** O cache é invalidado automaticamente pelo `ViacaoService` em qualquer operação de escrita.

---

*Desenvolvido por Caio Marcidele — Focado em Engenharia de Software.*