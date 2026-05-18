<?php
/** @var object|null $viacao */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Viação</title>
    <link rel="stylesheet" href="/admin.css">
    <style>
        /* Um pouco de CSS simples apenas para a tabela do histórico */
        .historico-section { margin-top: 30px; padding: 15px; background: #f9f9f9; border-radius: 8px; }
        .historico-section summary { font-weight: bold; cursor: pointer; padding: 10px; background: #e0e0e0; border-radius: 4px; }
        .tabela-historico { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 14px; }
        .tabela-historico th, .tabela-historico td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .tabela-historico th { background-color: #f2f2f2; }
    </style>
</head>
<body>
<header class="admin-header">
    <h1>🚍 Editar Viação (#<?= $viacao->id ?>)</h1>
</header>
<main class="admin-main">
    <?php require __DIR__ . '/partials/form.php'; ?>

    <section class="historico-section" style="margin-top: 30px;">
        <details>
            <summary style="font-weight: bold; cursor: pointer; padding: 15px; background: #e9ecef; border-radius: 4px; font-size: 15px; color: #495057;">
                Ver Histórico de Alterações Desta Viação
            </summary>

            <div class="admin-card" style="margin-top: 15px; padding: 0;">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                        <tr>
                            <th width="150">Usuário</th>
                            <th width="120">Ação</th>
                            <th>Detalhes / O que mudou</th>
                            <th width="180">Data e Hora</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($historico)): ?>
                            <tr><td colspan="4" style="text-align:center; padding: 20px;">Nenhum registro encontrado para esta viação.</td></tr>
                        <?php endif; ?>

                        <?php foreach ($historico as $log): ?>
                            <tr>
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
        </details>
    </section>

</main>
</body>
</html>