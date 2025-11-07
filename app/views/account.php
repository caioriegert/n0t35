<?php include 'partials-dashboard/header.php'; ?>

<style>
    :root {
        --accent: #00ff66;
        --panel: rgba(0,20,0,0.7);
        --muted: #bfffcf;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Courier New', monospace; background: #000; color: var(--muted); }

    .page-wrap { max-width: 820px; margin: 40px auto; padding: 18px; }
    .container { padding: 20px; border: 1px solid var(--accent); border-radius: 8px; background: var(--panel); box-shadow: 0 0 15px rgba(0,128,0,0.08); }

    .account-header { display:flex; justify-content:space-between; align-items:center; margin-bottom: 14px; gap:12px; }
    .account-title { color: var(--accent); font-size: 1.4rem; }
    .account-grid { display:grid; grid-template-columns: 1fr 320px; gap: 18px; align-items:start; }

    .card { padding: 14px; border-radius: 8px; background: rgba(0,0,0,0.15); border: 1px solid rgba(255,255,255,0.02); }

    .meta-row { margin-bottom: 8px; color: #bfffcf; }
    .meta-label { color: var(--accent); font-weight:700; margin-right:8px; }

    .form-group { margin-bottom: 12px; }
    .form-label { display:block; margin-bottom:6px; color:#bfffcf; font-size:0.95rem; }
    .form-input { width:100%; padding:10px; border-radius:6px; border:1px solid rgba(255,255,255,0.06); background: rgba(0,0,0,0.45); color: #dfffe6; }

    .btn { display:inline-block; padding:8px 12px; border-radius:6px; text-decoration:none; font-weight:700; cursor:pointer; }
    .btn-edit { background:transparent; border:1px solid #00bfff; color:#00bfff; }
    .btn-danger { background:transparent; border:1px solid #ff4444; color:#ff4444; }

    .actions { display:flex; gap:8px; flex-wrap:wrap; margin-top:10px; }

    .alert { padding:10px; border-radius:6px; margin-bottom:12px; }
    .alert-error { background: rgba(255,68,68,0.06); color:#ffbfbf; border:1px solid rgba(255,68,68,0.08); }
    .alert-success { background: rgba(0,255,102,0.04); color:#bfffcf; border:1px solid rgba(0,255,102,0.06); }

    @media (max-width: 820px) {
        .account-grid { grid-template-columns: 1fr; }
        .page-wrap { padding: 12px; }
    }
</style>

<div class="page-wrap">
    <div class="container">
        <div class="account-header">
            <h1 class="account-title">Minha Conta</h1>
            <div class="account-actions">
                <a class="btn btn-edit" href="/dashboard">Voltar</a>
            </div>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="account-grid">
            <div class="card account-info">
                <h3 style="color:var(--accent); margin-bottom:8px;">Informações</h3>

                <div class="meta-row"><span class="meta-label">Nome de usuário:</span> <?php echo htmlspecialchars($user['username'] ?? $user['name'] ?? '—'); ?></div>
                <div class="meta-row"><span class="meta-label">ID:</span> <?php echo htmlspecialchars($user['id'] ?? '—'); ?></div>

                <div class="meta-row"><span class="meta-label">Quantidade de notas:</span> <?php echo (int)$notesCount; ?></div>

                <hr style="margin:12px 0; border:0; height:1px; background:rgba(255,255,255,0.02)">

                <form method="POST" action="/account/delete-notes" onsubmit="return confirm('Tem certeza que deseja apagar todas as suas notas?');">
                    <div class="actions">
                        <button type="submit" class="btn btn-danger">Apagar todas as notas</button>
                    </div>
                </form>

                <form method="POST" action="/account/delete" style="margin-top:10px;" onsubmit="return confirm('Tem certeza que deseja apagar sua conta? Esta ação é irreversível.');">
                    <div class="actions">
                        <button type="submit" class="btn btn-danger">Apagar minha conta</button>
                    </div>
                </form>
            </div>

            <div class="card account-security">
                <h3 style="color:var(--accent); margin-bottom:8px;">Segurança</h3>

                <form method="POST" action="/account/password-update">
                    <div class="form-group">
                        <label class="form-label" for="current_password">Senha atual</label>
                        <input id="current_password" name="current_password" type="password" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="new_password">Nova senha</label>
                        <input id="new_password" name="new_password" type="password" class="form-input" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="confirm_password">Confirmar nova senha</label>
                        <input id="confirm_password" name="confirm_password" type="password" class="form-input" required>
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn btn-edit">Atualizar senha</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'partials-dashboard/footer.php'; ?>