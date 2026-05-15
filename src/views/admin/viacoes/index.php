<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Viações</title>
    <link rel="stylesheet" href="/admin.css">
    <style>
        .date-info { font-size: 11px; color: #6c757d; display: block; line-height: 1.2; }
        .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .status-ativo { background: #e1f7e1; color: #198754; }
        .status-inativo { background: #fdeaea; color: #dc3545; }
        .admin-table img { width: 50px; height: 50px; object-fit: contain; border-radius: 4px; border: 1px solid #eee; }
    </style>
</head>
<body>

<header class="admin-header">
    <h1>🚍 Gerenciar Viações</h1>
    <nav>
        <a href="/admin/viacoes/create" class="btn-nav" style="background: #198754;">+ Nova Viação</a>
        <a href="/admin/historico" class="btn-nav">Ver Histórico</a>

        <a href="/admin/logs-acesso" class="btn-nav" style="background: #343a40; color: #ffffff;">Logs de Acesso</a>

        <a href="/logout" class="btn-nav" style="background: #dc3545;">Sair</a>
    </nav>
</header>

<main class="admin-main">
    <div class="admin-card" style="margin-bottom: 20px;">
        <form method="GET" action="/admin/viacoes" class="filter-form" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">

            <div style="display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 200px;">
                <label style="font-size: 12px; font-weight: bold; color: #666;">🔎 Pesquisar Viação</label>
                <input type="text" name="nome" placeholder="Buscar por nome..." value="<?= htmlspecialchars((string)($filtros['busca'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>

            <div style="display: flex; flex-direction: column; gap: 5px;">
                <label style="font-size: 12px; font-weight: bold; color: #666;">✅ Status</label>
                <select name="status" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; min-width: 150px;">
                    <option value="">Todos</option>
                    <option value="ativo" <?= ($filtros['status'] ?? '') === 'ativo' ? 'selected' : '' ?>>Ativos</option>
                    <option value="inativo" <?= ($filtros['status'] ?? '') === 'inativo' ? 'selected' : '' ?>>Inativos</option>
                </select>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn-nav" style="background: #4e73df; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Filtrar</button>
                <a href="/admin/viacoes" class="btn-nav" style="background: #858796; color: white; padding: 10px 15px; border-radius: 4px; text-decoration: none; font-size: 14px; display: flex; align-items: center;">Limpar</a>
            </div>

        </form>
    </div>

    <div class="admin-card" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                <tr>
                    <th>Logo</th>
                    <th>Viação / Cidade</th>
                    <th>Status</th>
                    <th>Datas</th>
                    <th width="150">Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($viacoes)): ?>
                    <tr><td colspan="5" style="text-align: center; padding: 20px;">Nenhuma viação cadastrada.</td></tr>
                <?php else: ?>
                    <?php foreach ($viacoes as $v): ?>
                        <tr>
                            <td>
                                <img src="<?= $v->logo ? '/uploads/logos/' . htmlspecialchars($v->logo, ENT_QUOTES, 'UTF-8') : '/img/no-logo.png' ?>" alt="Logo">
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($v->nome, ENT_QUOTES, 'UTF-8') ?></strong><br>
                                <small style="color: #666;"><?= htmlspecialchars($v->cidade, ENT_QUOTES, 'UTF-8') ?></small>
                                <?php if($v->url): ?>
                                    <br><a href="<?= htmlspecialchars($v->url, ENT_QUOTES, 'UTF-8') ?>" target="_blank" style="font-size: 10px; color: #4e73df; text-decoration: none;">URL da Viação ↗</a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?= strtolower(htmlspecialchars($v->status, ENT_QUOTES, 'UTF-8')) ?>">
                                    <?= htmlspecialchars($v->status, ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td>
                                <span class="date-info">
                                    <strong>Criado:</strong> <?= $v->criado_em ? date('d/m/Y H:i', strtotime($v->criado_em)) : '-' ?>
                                </span>
                                <span class="date-info">
                                    <strong>Editado:</strong> <?= $v->alterado_em ? date('d/m/Y H:i', strtotime($v->alterado_em)) : '-' ?>
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 5px;">
                                    <a href="/admin/viacoes/<?= $v->id ?>/edit" class="btn-nav" style="padding: 5px 10px; font-size: 12px; background: #f6c23e;">Editar</a>

                                    <form action="/admin/viacoes/<?= $v->id ?>" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir?')">

                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn-nav" style="padding: 5px 10px; font-size: 12px; background: #e74a3b; border: none; cursor: pointer;">Excluir</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
</body>
</html>