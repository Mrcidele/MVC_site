# 🚌 Sistema de Gestão de Viações

Aplicação web de nível empresarial desenvolvida em **PHP 8.4** com Clean Architecture, MVC, painel administrativo protegido e fluxo CRUD fortemente tipado com DTOs.

---

## 📋 Índice

- [Sobre o Projeto](#-sobre-o-projeto)
- [Funcionalidades e Segurança (SOC / Blue Team)](#-funcionalidades-e-segurança-soc--blue-team)
- [Arquitetura e Clean Code](#-arquitetura-e-clean-code)
- [Como Executar](#-como-executar)
- [Acesso Administrativo](#-acesso-administrativo)
- [Rotas da Aplicação](#-rotas-da-aplicação)
- [Testes Automatizados](#-testes-automatizados)
- [Cache Integrado](#-cache-integrado)

---

## 📌 Sobre o Projeto

Este sistema foi desenvolvido para centralizar a gestão de empresas de transporte (viações). Diferencia-se por seguir rigorosos princípios de arquitetura defensiva e operações de SOC (Security Operations Center), garantindo que o ambiente seja **100% replicável via Docker**, resistente às principais vulnerabilidades da web (OWASP) e que a integridade dos dados seja preservada através de um sistema de auditoria imutável.

---

## ✨ Funcionalidades e Segurança (SOC / Blue Team)

### 🛡️ Defesa Ativa, Autenticação e Sessões

- **Rate Limiting (Anti-Brute Force):** Bloqueio automático e temporário de sessão após múltiplas tentativas de login falhas, mitigando ataques de dicionário e força bruta.
- **Proteção CSRF:** Geração e validação rigorosa de tokens de sessão dinâmicos (`hash_equals`) em todas as requisições de alteração de estado (POST, PUT, DELETE).
- **Security Headers:** Blindagem no lado do cliente via `.htaccess` implementando Content-Security-Policy (CSP), X-Frame-Options (contra Clickjacking) e X-XSS-Protection.
- **Prevenção XSS (Cross-Site Scripting):** Defesa em profundidade utilizando sanitização rigorosa de entradas (`strip_tags` nos Validators) e escape estrito de saídas (`htmlspecialchars` nas Views).
- **Criptografia e Rehashing Dinâmico:** O sistema utiliza `password_verify` para validação, mas possui uma inteligência de atualização contínua (Blue Team). Caso a senha utilize um hash legado (como Bcrypt), o `AuthService` migra a credencial automaticamente e em tempo real para **Argon2id** (configurado com alto custo de memória e processamento multi-thread), blindando a base de dados contra ataques de força bruta modernos executados por GPUs/ASICs.

### 🛠️ Painel Administrativo (ADM)

- **Upload Blindado e Gestão de Disco:** O sistema não confia na extensão do ficheiro. Utiliza `mime_content_type` para validar os bytes mágicos (JPG, PNG, WEBP), renomeia com `uniqid()` e realiza a exclusão sincronizada de ficheiros físicos órfãos quando a viação correspondente é apagada do banco.
- **Filtragem Dinâmica:** Busca e ordenação parametrizada com prevenção contra SQL Injection via PDO Prepared Statements.
- **Contratos de Dados (DTOs):** Uso de Data Transfer Objects imutáveis para transportar dados entre o Controller e o Service, garantindo tipagem estrita e evitando manipulação indevida.

### 📜 Auditoria Imutável e Visual (Histórico)

- **Log de Ações:** Registo detalhado de criação, edição e exclusão.
- **Comparativo Visual Dinâmico:** O sistema converte automaticamente mudanças de ficheiros em miniaturas visuais lado a lado (De / Para), aplicando filtros de escala de cinza para evidenciar o descarte da logo antiga, elevando a auditoria de texto para uma interface gráfica intuitiva.
- **Integridade Vitalícia:** A arquitetura utiliza **Índices (INDEX)** em vez de Chaves Estrangeiras (FK) na tabela de histórico, permitindo que o registo de auditoria permaneça intacto mesmo após a exclusão definitiva do objeto associado.

---

## 🏗️ Arquitetura e Clean Code

O projeto segue a **Separação de Responsabilidades (SoC)**, garantindo um código manutenível e escalável:

```text
Requisição HTTP
      │
      ▼
 public/index.php        ← Front Controller (Ponto de entrada, Configuração CSRF/Sessão)
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
 Services/*              ← Regras de negócio, Lógica Anti-Brute Force e Tratamento de Ficheiros
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
git clone https://github.com/seu-usuario/MVC_site.git
cd MVC_site
```

**2. Suba os contentores:**

```bash
docker compose up --build -d
```

**3. Aceda no navegador:**

- Home: `http://localhost/` ou `http://localhost:8081/`
- Painel ADM: `http://localhost/login`

---

## 🔐 Acesso Administrativo

O script de automação (`init.sql`) cria o utilizador padrão automaticamente:

| Campo  | Valor           |
|--------|-----------------|
| E-mail | admin@admin.com |
| Senha  | admin123        |

---

## 🗺️ Rotas da Aplicação

| Método | Rota                  | Ação                                               | Controller                  |
|--------|-----------------------|----------------------------------------------------|-----------------------------|
| GET    | `/`                   | Página inicial (Fallback e Renderização de Destinos) | `HomeController@index`      |
| GET    | `/login`              | Tela de Login                                      | `LoginController@index`     |
| POST   | `/login`              | Processar Login (Com validação CSRF e Rate Limit)  | `LoginController@login`     |
| GET    | `/admin/viacoes`      | Listar viações                                     | `ViacaoController@index`    |
| POST   | `/admin/viacoes`      | Criar viação (Usa DTO e Validação)                 | `ViacaoController@store`    |
| PUT    | `/admin/viacoes/{id}` | Atualizar viação (Usa DTO)                         | `ViacaoController@update`   |
| DELETE | `/admin/viacoes/{id}` | Excluir viação e ficheiros físicos                 | `ViacaoController@destroy`  |
| GET    | `/admin/historico`    | Listar histórico visual de auditoria               | `HistoricoController@index` |

---

## 🧪 Testes Automatizados

O projeto possui uma suite de testes unitários configurada com **PHPUnit**, assegurando a estabilidade das regras de negócio, validações de entrada rigorosas (`ViacaoValidatorTest`) e a integridade dos modelos de dados (`ViacaoTest`).

Para correr os testes automatizados dentro do ambiente Docker, execute:

```bash
docker compose exec app ./vendor/bin/phpunit
```

---

## ⚡ Cache Integrado

Para otimizar o tempo de carregamento da Home, o sistema utiliza cache em ficheiro JSON para o catálogo (`viacoes_ativas.json`):

- **Leitura:** `getCachedData()` recupera dados estáticos se o TTL (300s) for válido.
- **Invalidação Sincronizada:** O cache é invalidado e regerado automaticamente pelo `ViacaoService` em qualquer operação de escrita (Criação, Edição ou Deleção), prevenindo a exibição de dados ou imagens inexistentes.