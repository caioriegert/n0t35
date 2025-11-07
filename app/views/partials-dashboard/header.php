<?php
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>N0T3S</title>
  <style>
    /* Tema compartilhado: escuro + neon verde */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { background: #000; color: #0f0; font-family: 'Courier New', monospace; line-height: 1.4; }

    nav {
      background: rgba(0, 0, 0, 0.95);
      color: #00ff66;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 12px 24px;
      border-bottom: 1px solid rgba(0, 255, 102, 0.06);
    }

    .nav-brand {
      color: #00ff66;
      font-weight: 700;
      text-decoration: none;
      font-size: 1.05rem;
      letter-spacing: 0.4px;
    }

    .nav-links {
      display: flex;
      gap: 18px;
      align-items: center;
    }

    .nav-links a {
      color: #00ff66;
      text-decoration: none;
      padding: 6px 8px;
      border-radius: 6px;
      transition: background-color .12s, color .12s, transform .08s;
      font-size: 0.95rem;
    }

    .nav-links a:hover {
      background: rgba(0, 255, 102, 0.05);
      color: #b8ffcc;
      transform: translateY(-1px);
    }
  </style>
</head>
<body>
  <nav>
    <a class="nav-brand" href="/dashboard">N0T3S</a>
    <div class="nav-links">
      <a href="/dashboard">Notas</a>
      <a href="/account">Conta</a>
      <a href="/logout">Sair</a>
    </div>
  </nav>