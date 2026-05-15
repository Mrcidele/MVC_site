<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Alterações</title>
    <link rel="stylesheet" href="/admin.css">
</head>
<body>

<header class="admin-header">
    <h1>📜 Histórico de Alterações</h1>
    <nav>
        <a href="/admin/viacoes" class="btn-nav">← Voltar às Viações</a>
    </nav>
</header>

<main class="admin-main">

    <div class="admin-card" style="margin-bottom: 20px;">
        <form method="GET" action="/admin/historico" class="filter-form" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">

            <div style="display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 200px;">
                <label style="font-size: 12px; font-weight: bold; color: #666;">🔎 Pesquisar</label>
                <input type="text" name="busca" placeholder="Viação, usuário ou detalhe..." value="<?= htmlspecialchars((string)($filtros['busca'] ?? '')) ?>" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
            </div>

            <div style="display: flex; flex-direction: column; gap: 5px;">
                <label style="font-size: 12px; font-weight: bold; color: #666;">🎬 Ação</label>
                <select name="acao" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; min-width: 150px;">
                    <option value="">Todas</option>
                    <option value="Criado" <?= ($filtros['acao'] ?? '') === 'Criado' ? 'selected' : '' ?>>Criação</option>
                    <option value="Editado" <?= ($filtros['acao'] ?? '') === 'Editado' ? 'selected' : '' ?>>Edição</option>
                    <option value="Excluido" <?= ($filtros['acao'] ?? '') === 'Excluido' ? 'selected' : '' ?>>Exclusão</option>
                </select>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn-nav" style="background: #4e73df; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Filtrar</button>
                <a href="/admin/historico" class="btn-nav" style="background: #858796; color: white; padding: 10px 15px; border-radius: 4px; text-decoration: none; font-size: 14px; display: flex; align-items: center;">Limpar</a>
            </div>

        </form>
    </div>

    <div class="admin-card" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table" style="min-width: 1000px;">
                <thead>
                <tr>
                    <th width="80">ID Ref.</th>
                    <th width="150">Usuário</th>
                    <th width="120">Ação</th>
                    <th>Detalhes / O que mudou</th>
                    <th width="180">Data e Hora</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($historico)): ?>
                    <tr><td colspan="5" style="text-align:center; padding: 20px;">Nenhum registro encontrado.</td></tr>
                <?php endif; ?>

                <?php foreach ($historico as $log): ?>
                    <tr>
                        <td><?= !empty($log['viacao_id']) ? '#' . $log['viacao_id'] : '-' ?></td>

                        <td>
                            <strong><?= htmlspecialchars((string)($log['usuario_nome'] ?? 'Sistema')) ?></strong>
                            <br><small style="color: #6c757d;">ID: #<?= $log['usuario_id'] ?? '?' ?></small>
                        </td>

                        <td>
                            <?php
                            $acao = $log['acao'] ?? '';
                            $color = ($acao === 'exclusao' || $acao === 'Excluido') ? '#dc3545' :
                                    (($acao === 'criacao' || $acao === 'Criado' || $acao === 'cadastro') ? '#198754' : '#fd7e14');
                            ?>
                            <span style="color: <?= $color ?>; font-weight: bold; text-transform: capitalize;">
                                <?= htmlspecialchars((string)$acao) ?>
                            </span>
                        </td>

                        <td>
                            <?php
                            $detalhesRaw = $log['detalhes'] ?? '';
                            $detalhesDecode = json_decode((string)$detalhesRaw, true);

                            if (($acao === 'edicao' || $acao === 'Editado' || $acao === 'edicao') && is_array($detalhesDecode)):
                                ?>
                                <table class="log-table" style="width: 100%; font-size: 13px; border-collapse: collapse;">
                                    <thead>
                                    <tr style="border-bottom: 1px solid #eee;"><th align="left">Campo</th><th align="left">De</th><th align="left">Para</th></tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($detalhesDecode as $campo => $mudanca): ?>
                                        <tr>
                                            <td style="font-weight: bold; vertical-align: middle;"><?= htmlspecialchars((string)($mudanca['campo'] ?? $campo)) ?></td>

                                            <td style="color: #dc3545; text-decoration: line-through; vertical-align: middle;">
                                                <?php
                                                $valorDe = (string)($mudanca['de'] ?? '');
                                                if (strpos($valorDe, '[IMG]') === 0) {
                                                    $imgNome = str_replace('[IMG]', '', $valorDe);
                                                    if ($imgNome === 'Sem logo') {
                                                        echo "<em>Sem logo</em>";
                                                    } else {
                                                        echo '<img src="/uploads/logos/' . htmlspecialchars($imgNome) . '" style="height: 40px; border-radius: 4px; object-fit: contain; filter: grayscale(100%); opacity: 0.7;">';
                                                    }
                                                } else {
                                                    echo htmlspecialchars($valorDe);
                                                }
                                                ?>
                                            </td>

                                            <td style="color: #198754; vertical-align: middle;">
                                                <?php
                                                $valorPara = (string)($mudanca['para'] ?? '');
                                                if (strpos($valorPara, '[IMG]') === 0) {
                                                    $imgNome = str_replace('[IMG]', '', $valorPara);
                                                    if ($imgNome === 'Sem logo') {
                                                        echo "<em>Sem logo</em>";
                                                    } else {
                                                        echo '<img src="/uploads/logos/' . htmlspecialchars($imgNome) . '" style="height: 40px; border-radius: 4px; object-fit: contain; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">';
                                                    }
                                                } else {
                                                    echo htmlspecialchars($valorPara);
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <?= htmlspecialchars((string)$detalhesRaw) ?>
                            <?php endif; ?>
                        </td>

                        <td style="color: #6c757d; font-size: 14px;">
                            <?= date('d/m/Y H:i:s', strtotime((string)($log['data_hora'] ?? 'now'))) ?>
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