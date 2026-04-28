<?php
use App\Core\View;
$flash = $flash ?? View::pullFlash();

function sortLink(string $col, string $label, string $currentOrder, string $currentDir, array $filtros): string {
    $newDir = ($currentOrder === $col && $currentDir === 'ASC') ? 'desc' : 'asc';
    $params = array_merge($filtros, ['order' => $col, 'dir' => $newDir]);
    $url = '?' . http_build_query($params);
    $icon = $currentOrder === $col ? ($currentDir === 'ASC' ? ' ▲' : ' ▼') : ' ⇅';
    return "<a href=\"$url\" class=\"sort-link\">$label<span style=\"font-size:10px; margin-left:5px;\">$icon</span></a>";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administração de Viações</title>
    <link rel="stylesheet" href="/admin.css">
</head>
<body>

<header class="admin-header">
    <h1>Painel ADM - Viações</h1>
    <nav>
        <a href="/" class="btn-nav">← Voltar ao Site</a>
        <a href="/admin/historico" class="btn-nav">Ver Histórico</a>
        <a href="/admin/viacoes/create" class="btn-primary">+ Nova Viação</a>
    </nav>
</header>

<main class="admin-main">
    <?php if ($flash): ?>
        <div class="alert-success"><?= htmlspecialchars($flash['message']) ?></div>
    <?php endif; ?>

    <div class="admin-card">
        <form method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <input type="text" name="nome" value="<?= htmlspecialchars($filtros['busca'] ?? '') ?>" placeholder="Buscar..." style="padding: 8px; border: 1px solid #ccc; border-radius: 4px; flex: 1; min-width: 150px;">
            <select name="status" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px; min-width: 120px;">
                <option value="">Status</option>
                <option value="ativo" <?= ($filtros['status'] ?? '') === 'ativo' ? 'selected' : '' ?>>Ativos</option>
                <option value="inativo" <?= ($filtros['status'] ?? '') === 'inativo' ? 'selected' : '' ?>>Inativos</option>
            </select>
            <button type="submit" class="btn-primary">Filtrar</button>
        </form>
    </div>

    <div class="admin-card" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                <tr>
                    <th width="60">Logo</th>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Cidade</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($viacoes as $v): ?>
                    <tr>
                        <td>
                            <?php if ($v->logo): ?>
                                <img src="/uploads/logos/<?= htmlspecialchars($v->logo) ?>" width="40" height="40" style="border-radius:50%; object-fit:cover; border: 1px solid #eee;">
                            <?php else: ?>
                                <div style="width:40px; height:40px; background:#e9ecef; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:10px; color:#adb5bd;">N/A</div>
                            <?php endif; ?>
                        </td>
                        <td><?= $v->id ?></td>
                        <td><strong><?= htmlspecialchars($v->nome) ?></strong></td>
                        <td><?= htmlspecialchars($v->cidade) ?></td>
                        <td><span class="badge <?= $v->status ?>"><?= ucfirst($v->status) ?></span></td>
                        <td>
                            <div style="display:flex; gap: 5px;">
                                <a href="/admin/viacoes/<?= $v->id ?>/edit" class="btn-edit">Editar</a>
                                <form method="POST" action="/admin/viacoes/<?= $v->id ?>" style="display:inline;" onsubmit="return confirm('Tem certeza que deseja excluir esta viação?')">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn-delete">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
</body>
</html>