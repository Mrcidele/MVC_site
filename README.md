# Sistema de Gestao de Viacoes

Projeto em PHP com arquitetura MVC, painel administrativo e fluxo completo de CRUD para viações de onibus, incluindo upload de logo, filtro/ordenacao, historico de alteracoes e cache simples para a Home.

## Visao geral

O sistema foi estruturado para separar responsabilidades por camada:

- `public/index.php`: front controller web.
- `Core/Router.php`: registro e despacho de rotas com suporte a parametros e method spoofing (`_method`).
- `Controllers/*`: controle do fluxo HTTP.
- `Services/*`: regras de negocio.
- `Repositories/*`: acesso a dados via PDO.
- `Models/*`: objetos de dominio.
- `views/*`: renderizacao das telas.

## Funcionalidades principais

- Home com layout estilo portal de passagens, exibindo viações ativas dinamicamente.
- Painel ADM de viações com:
  - listagem,
  - busca por nome,
  - filtro por status,
  - ordenacao por coluna,
  - cadastro,
  - edicao,
  - exclusao.
- Upload de logo da viação (`JPG`, `PNG`, `WEBP`).
- Historico de alteracoes (criacao, edicao e exclusao).
- Cache em arquivo JSON para consulta principal da Home (`src/cache/viacoes_ativas.json`).

## Tecnologias

- PHP `8.4` + Apache
- MySQL `8.0`
- Docker + Docker Compose
- Composer (autoload PSR-4)

## Estrutura de pastas

```text
php-task-app/
  docker-compose.yml
  Dockerfile
  composer.json
  src/
    Controllers/
    Core/
    database/
    Models/
    Repositories/
    Services/
    Validators/
    public/
    routes/
    views/
    cache/
```

## Como executar

1. Suba os containers:

```bash
cd /home/qp-1130257/Documentos/php-task-app
docker compose up --build -d
```

2. Acesse no navegador:

- Home: `http://localhost/` (tambem disponivel em `http://localhost:8081/`)
- ADM viações: `http://localhost/admin/viacoes`
- Historico: `http://localhost/admin/historico`

3. Para parar:

```bash
docker compose down
```

## Rotas web

- `GET /` -> Home (`HomeController@index`)
- `GET /admin/viacoes` -> Lista viações
- `GET /admin/viacoes/create` -> Formulario de cadastro
- `POST /admin/viacoes` -> Criar viação
- `GET /admin/viacoes/{id}/edit` -> Formulario de edicao
- `PUT /admin/viacoes/{id}` -> Atualizar viação
- `DELETE /admin/viacoes/{id}` -> Excluir viação
- `GET /admin/historico` -> Listar historico

## Banco de dados

Script: `src/database/init.sql`

Tabelas criadas:

- `viacoes`
- `viacoes_historico`

Conexao PDO e utilitarios de cache ficam em `src/database/db.php`.

## Cache

O cache atual foi implementado para acelerar a consulta padrao da Home (viações ativas ordenadas por nome):

- leitura: `getCachedData('viacoes_ativas')`
- escrita: `setCachedData('viacoes_ativas', ...)`
- invalidacao automatica ao criar/editar/excluir viação
- TTL padrao: `300s`


## Autoload (PSR-4)

Definido em `composer.json`:

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

Se alterar namespaces/estrutura:

```bash
docker compose exec app composer dump-autoload -o
```
