<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auditoria SOC - Logs de Acesso</title>
    <link rel="stylesheet" href="/admin.css">
    <style>
        .date-info { font-size: 13px; color: #6c757d; display: block; line-height: 1.2; }
        .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; text-transform: uppercase; display: inline-block; }
        .status-sucesso { background: #e1f7e1; color: #198754; }
        .status-falha { background: #fdeaea; color: #dc3545; }
        .status-logout { background: #e2e3e5; color: #383d41; }
        .log-code { background: #f8f9fa; padding: 2px 6px; border: 1px solid #ddd; border-radius: 4px; font-family: monospace; font-size: 13px; }
    </style>
</head>
<body>

<header class="admin-header">
    <h1><span style="color: #dc3545;">🛡️ SOC</span> | Logs de Acesso</h1>
    <nav>
        <a href="/admin/viacoes" class="btn-nav">← Voltar</a>
    </nav>
</header>

<main class="admin-main">
    <div class="admin-card" style="margin-bottom: 20px; border-left: 4px solid #343a40;">
        <h3 style="margin: 0; font-size: 16px; color: #333;">Monitorização do Perímetro de Autenticação</h3>
    </div>

    <div class="admin-card" style="padding: 0;">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                <tr>
                    <th width="180">Data / Hora</th>
                    <th>E-mail Tentado</th>
                    <th>IP de Origem</th>
                    <th width="120">Status</th>
                    <th>Detalhes Técnicos</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($logs)): ?>
                    <tr><td colspan="5" style="text-align: center; padding: 20px;">Nenhum evento registado.</td></tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td>
                                <span class="date-info">
                                    <strong><?= date('d/m/Y', strtotime($log['data_hora'])) ?></strong> às <?= date('H:i:s', strtotime($log['data_hora'])) ?>
                                </span>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($log['email_tentativa'], ENT_QUOTES, 'UTF-8') ?></strong>
                            </td>
                            <td>
                                <span class="log-code"><?= htmlspecialchars($log['ip_origem'], ENT_QUOTES, 'UTF-8') ?></span>
                            </td>
                            <td>
                                <span class="status-badge status-<?= htmlspecialchars($log['status'], ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($log['status'], ENT_QUOTES, 'UTF-8') ?>
                                </span>
                            </td>
                            <td style="font-size: 13px; color: #444;">
                                <?= htmlspecialchars($log['detalhes'], ENT_QUOTES, 'UTF-8') ?>
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