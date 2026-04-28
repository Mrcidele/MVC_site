<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Viação</title>
    <link rel="stylesheet" href="/admin.css">
</head>
<body>
<header class="admin-header">
    <h1>Editar Viação (#<?= $viacao->id ?>)</h1>
</header>
<main class="admin-main">
    <?php require __DIR__ . '/partials/form.php'; ?>
</main>
</body>
</html>