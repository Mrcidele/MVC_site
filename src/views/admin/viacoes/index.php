<?php
use App\Core\View;
$flash = View::pullFlash();

/**
 * Gera um link de ordenação com ícone
 */
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
        <form method="GET" style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="nome" value="<?= htmlspecialchars($filtros['busca'] ?? '') ?>" placeholder="Buscar por nome..." style="padding: 8px; border: 1px solid #ccc; border-radius: 4px; flex: 1;">
            <select name="status" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                <option value="">Todos os Status</option>
                <option value="ativo" <?= ($filtros['status'] ?? '') === 'ativo' ? 'selected' : '' ?>>Ativos</option>
                <option value="inativo" <?= ($filtros['status'] ?? '') === 'inativo' ? 'selected' : '' ?>>Inativos</option>
            </select>
            <button type="submit" class="btn-primary" style="padding: 9px 20px;">Filtrar</button>
        </form>
    </div>

    <div class="admin-card" style="padding: 0; overflow: hidden;">
        <table class="admin-table">
            <thead>
            <tr>
                <th width="60">Logo</th>
                <th><?= sortLink('id', 'ID', $filtros['ordem'], $filtros['dir'], $filtros) ?></th>
                <th><?= sortLink('nome', 'Nome', $filtros['ordem'], $filtros['dir'], $filtros) ?></th>
                <th>Cidade</th>
                <th>Status</th>
                <th><?= sortLink('criado_em', 'Criação', $filtros['ordem'], $filtros['dir'], $filtros) ?></th>
                <th><?= sortLink('alterado_em', 'Última Edição', $filtros['ordem'], $filtros['dir'], $filtros) ?></th>
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

                    <td style="font-size: 0.85rem; color: #666;">
                        <?= $v->criadoEm ? date('d/m/Y H:i', strtotime($v->criadoEm)) : '---' ?>
                    </td>

                    <td style="font-size: 0.85rem; color: #666;">
                        <?php
                        // Só mostra a data de alteração se ela for diferente da criação
                        if ($v->alteradoEm && $v->alteradoEm !== $v->criadoEm) {
                            echo date('d/m/Y H:i', strtotime($v->alteradoEm));
                        } else {
                            echo '<span style="color:#ccc">Sem edições</span>';
                        }
                        ?>
                    </td>

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
</main>
</body>
</html>