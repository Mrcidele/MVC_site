<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Histórico de Alterações</title>
    <link rel="stylesheet" href="/admin.css">
</head>
<body>

<header class="admin-header">
    <h1>Histórico de Alterações</h1>
    <nav>
        <a href="/admin/viacoes" class="btn-nav">← Voltar às Viações</a>
    </nav>
</header>

<main class="admin-main">
    <div class="admin-card" style="padding: 0; overflow: hidden;">
        <table class="admin-table">
            <thead>
            <tr>
                <th width="80">ID Ref.</th>
                <th width="120">Ação</th>
                <th>Detalhes / O que mudou</th>
                <th width="180">Data e Hora</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($historico)): ?>
                <tr><td colspan="4" style="text-align: center; color: #666; padding: 30px;">Nenhum registo no histórico ainda.</td></tr>
            <?php endif; ?>

            <?php foreach ($historico as $log): ?>
                <tr>
                    <td><?= $log->viacaoId ? '#' . $log->viacaoId : '-' ?></td>
                    <td>
                        <?php
                        $color = $log->acao === 'Excluido' ? '#dc3545' : ($log->acao === 'Criado' ? '#198754' : '#fd7e14');
                        ?>
                        <span style="color: <?= $color ?>; font-weight: bold;"><?= htmlspecialchars($log->acao) ?></span>
                    </td>
                    <td>
                        <?php
                        $detalhesDecode = json_decode($log->detalhes, true);
                        if ($log->acao === 'Editado' && is_array($detalhesDecode)):
                            ?>
                            <table class="log-table">
                                <thead>
                                <tr><th>Campo</th><th>Antes (De)</th><th>Depois (Para)</th></tr>
                                </thead>
                                <tbody>
                                <?php foreach ($detalhesDecode as $mudanca): ?>
                                    <tr>
                                        <td style="font-weight: bold; background: #f8f9fa; width: 120px;"><?= htmlspecialchars($mudanca['campo']) ?></td>
                                        <td style="color: #dc3545; text-decoration: line-through; word-break: break-word;"><?= htmlspecialchars($mudanca['de']) ?></td>
                                        <td style="color: #198754; word-break: break-word;"><?= htmlspecialchars($mudanca['para']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <?= htmlspecialchars($log->detalhes) ?>
                        <?php endif; ?>
                    </td>
                    <td style="color: #6c757d; font-size: 14px;">
                        <?= date('d/m/Y H:i:s', strtotime($log->dataHora)) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>