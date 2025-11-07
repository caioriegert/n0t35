<?php
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Anotações</title>
    <style>
      /* Aplica fonte Courier New */
      body { font-family: 'Courier New', monospace; background-color: #000; }

      /* Reaproveita o layout / tema da dashboard para telas de formulário */
      .page-wrap { max-width: 820px; margin: 60px auto; padding: 0 16px; display: flex; flex-direction: column; justify-content: space-around; align-items: center; min-height: fit-content; }
      .container { width: 500px; padding: 22px; border: 1px solid #00ff66; border-radius: 8px; background-color: rgba(0,20,0,0.7); box-shadow: 0 0 12px #00ff00ff; }

      .page-header-title { width: 200px; height: 100px; color: #00ff66; font-size: 1.2rem; margin-bottom: 12px; text-shadow: 0 0 4px rgba(0,255,102,0.06); text-align: center; font-weight: bold; justify-content: center; align-items: center; display: flex; }
      .page-title { color: #00ff66; margin-bottom: 14px; font-size: 1.4rem; text-shadow: 0 0 4px rgba(0,255,102,0.06); }

      .form-group { margin-bottom: 12px; }
      .form-label { display:block; color:#bfffcf; margin-bottom:6px; font-size:0.95rem; }
      .form-input {
        width:100%;
        padding:10px 12px;
        border-radius:6px;
        border:1px solid rgba(255,255,255,0.06);
        background: rgba(0,0,0,0.45);
        color: #dfffe6;
        outline: none;
        box-sizing: border-box;
      }
      .form-input::placeholder { color: rgba(191,255,207,0.45); }

      .btn-primary {
        display:block;
        width:100%;
        padding:10px 12px;
        border-radius:6px;
        background: #000;
        border: 1px solid #00ff66;
        color: #00ff66;
        font-weight:700;
        text-decoration:none;
        cursor:pointer;
      }
      .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 0 12px rgba(0,255,102,0.06); }

      .muted { color: #9fbf9f; font-size:0.9rem; text-align:center; margin-top:12px; }

      .alert {
        padding:10px 12px;
        border-radius:6px;
        margin-bottom:14px;
        font-size:0.95rem;
      }
      .alert-error { background: rgba(255,68,68,0.06); color: #ffbfbf; border:1px solid rgba(255,68,68,0.08); }
      .alert-success { background: rgba(0,255,102,0.04); color: #bfffcf; border:1px solid rgba(0,255,102,0.06); }

      a.form-link { color:#bfffcf; text-decoration:none; }
      a.form-link:hover { text-decoration:underline; color:#fff; }
    </style>
</head>
<body>
    <main class="page-wrap page-login">
      <p class="page-header-title">
        N0T35
      </p>
      <section class="container container-login">
        <h2 class="page-title">Entrar</h2>

        <?php if (!empty($error)): ?>
          <div class="alert alert-error" role="alert"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($_GET['registered'])): ?>
          <div class="alert alert-success" role="status">Cadastro realizado com sucesso! Faça login.</div>
        <?php endif; ?>

        <form class="login-form" method="POST" action="/login" novalidate>
          <div class="form-group">
            <label class="form-label" for="username">Usuário</label>
            <input id="username" class="form-input" name="username" type="text" placeholder="Seu usuário" required autofocus>
          </div>

          <div class="form-group">
            <label class="form-label" for="password">Senha</label>
            <input id="password" class="form-input" name="password" type="password" placeholder="Sua senha" required>
          </div>

          <button class="btn-primary btn-login" type="submit">Entrar</button>
        </form>

        <p class="muted login-help">Não tem conta? <a class="form-link" href="/register">Cadastre-se</a></p>
      </section>
    </main>
</body>
</html>