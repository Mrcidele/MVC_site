<?php
/** @var object|null $viacao */
$isEdit = isset($viacao); ?>
<div class="admin-card" style="max-width: 600px; margin: 0 auto;">

    <?php if (!empty($errors)): ?>
        <div class="alert-error">
            <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>
        </div>
    <?php endif ?>

    <form method="POST" action="<?= $isEdit ? '/admin/viacoes/'.$viacao->id : '/admin/viacoes' ?>" enctype="multipart/form-data">

        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <?php if ($isEdit): ?>
            <input type="hidden" name="_method" value="PUT">
        <?php endif; ?>

        <div class="form-group">
            <label>Logo (Opcional)</label>
            <?php if ($isEdit && $viacao->logo): ?>
                <div style="margin-bottom: 10px;">
                    <img src="/uploads/logos/<?= htmlspecialchars($viacao->logo) ?>" width="80" style="border-radius: 8px; border: 1px solid #ccc;">
                </div>
            <?php endif; ?>
            <input type="file" name="logo" accept="image/*">
        </div>

        <div class="form-group">
            <label>Nome</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($old['nome'] ?? '') ?>" required placeholder="Ex: Viação Cometa">
        </div>

        <div class="form-group">
            <label>URL</label>
            <input type="url" name="url" value="<?= htmlspecialchars($old['url'] ?? '') ?>" required placeholder="Ex: https://viacaocometa.com.br">
        </div>

        <div class="form-group">
            <label>Cidade</label>
            <input type="text" name="cidade" value="<?= htmlspecialchars($old['cidade'] ?? '') ?>" required placeholder="Ex: São Paulo">
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status">
                <option value="ativo" <?= ($old['status'] ?? '') === 'ativo' ? 'selected' : '' ?>>Ativo</option>
                <option value="inativo" <?= ($old['status'] ?? '') === 'inativo' ? 'selected' : '' ?>>Inativo</option>
            </select>
        </div>

        <div class="form-actions" style="margin-top: 30px; display: flex; gap: 10px;">
            <button type="submit" class="btn-primary">Salvar Viação</button>
            <a href="/admin/viacoes" class="btn-nav" style="background: #e9ecef; color: #333; text-decoration: none; display: flex; align-items: center; justify-content: center;">Cancelar</a>
        </div>
    </form>
</div>