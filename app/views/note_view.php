<?php include 'partials-dashboard/header.php'; ?>
<style>
.page-wrap { max-width: 800px; margin: 40px auto; padding: 20px; }
.container { padding: 20px; border: 1px solid #00ff66; border-radius: 8px; background-color: rgba(0,20,0,0.7); box-shadow: 0 0 15px #003300; }
.note-view-title { color: #00ff66; margin-bottom: 12px; font-size: 1.6rem; }
.note-view-meta { color: #00aa55; font-size: 0.9rem; margin-bottom: 12px; }
.note-view-content { color: #bfffcf; line-height: 1.6; background: rgba(0,0,0,0.2); padding: 12px; border-radius: 6px; border: 1px solid rgba(0,255,102,0.03); }
.note-view-image { max-width: 100%; height: auto; margin-top: 12px; border-radius: 6px; border: 1px solid #00ff66; }
.actions { margin-top: 16px; display:flex; gap:12px; }
.btn { display:inline-block; padding:8px 14px; border-radius:6px; text-decoration:none; font-weight:bold; }
.btn-edit { background:#000; border:1px solid #00ff66; color:#00ff66; }
.btn-back { background:transparent; border:1px solid rgba(255,255,255,0.04); color:#bfffcf; }
</style>

<div class="page-wrap">
    <div class="container">
        <h1 class="note-view-title"><?php echo htmlspecialchars($note['title']); ?></h1>
        <div class="note-view-meta">
            <!-- Exemplo: data/autor se disponível -->
            <?php if (!empty($note['created_at'])): ?>
                Criada em: <?php echo htmlspecialchars($note['created_at']); ?>
            <?php endif; ?>
        </div>

        <div class="note-view-content">
            <!-- Renderiza conteúdo: se seus conteúdos têm HTML/autorizado, ajuste aqui. -->
            <?php
                // Se quiser permitir algumas tags (p, br, strong, em, ul, li, ol, a, img):
                $allowed = '<p><br><strong><em><ul><li><ol><a><img><figure><figcaption>';
                echo $note['content'] ? strip_tags($note['content'], $allowed) : '';
            ?>
        </div>

        <?php if (!empty($note['image_path'])): ?>
            <img src="<?php echo htmlspecialchars($note['image_path']); ?>" class="note-view-image" alt="Imagem da nota">
        <?php endif; ?>

        <div class="actions">
            <a class="btn btn-edit" href="/note/edit?id=<?php echo $note['id']; ?>">Abrir no editor</a>
            <a class="btn btn-back" href="/dashboard">Voltar</a>
        </div>
    </div>
</div>

<?php include 'partials-dashboard/footer.php'; ?>