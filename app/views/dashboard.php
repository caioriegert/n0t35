<?php include 'partials-dashboard/header.php'; ?>

<style>
    /* Reset básico */
    * { margin: 0; padding: 0; box-sizing: border-box; }

    /* Layout */
    .page-wrap { max-width: 800px; margin: 50px auto; padding: 20px; }
    .container { padding: 20px; border: 1px solid #00ff66; border-radius: 8px; background-color: rgba(0,20,0,0.7); box-shadow: 0 0 15px #003300; }

    /* Cabeçalho */
    .page-header { margin-bottom: 20px; }
    .page-title { margin-bottom: 12px; color: #00ff66; text-shadow: 0 0 5px #00ff66; font-size: 1.8rem; }

    /* Ações */
    .actions-bar { margin-bottom: 20px; }
    .btn-create {
        display: inline-block;
        padding: 10px 20px;
        background-color: #000;
        border: 1px solid #00ff66;
        color: #00ff66;
        text-decoration: none;
        border-radius: 6px;
        font-weight: bold;
        transition: transform .12s, box-shadow .12s;
    }
    .btn-create:hover { transform: translateY(-2px); box-shadow: 0 0 12px rgba(0,255,102,0.08); }

    /* Lista de notas */
    .notes-list { list-style: none; padding: 0; margin: 0; }
    .note-item {
        border: 1px solid #00ff66;
        padding: 15px;
        margin-bottom: 15px;
        background-color: rgba(0,0,0,0.6);
        border-radius: 6px;
        box-shadow: 0 0 10px #002200;
        transition: transform 0.18s, box-shadow 0.18s;
    }
    .note-item:hover { transform: scale(1.01); box-shadow: 0 0 14px #00ff66; }

    /* Link que cobre o bloco */
    .note-link { display: block; color: inherit; text-decoration: none; padding: 4px 0; }
    .note-link:hover .note-title { text-decoration: underline; }

    .note-title { color: #00ff66; margin-bottom: 8px; font-size: 1.15rem; }
    .note-content {
        color: #00aa55;
        margin-bottom: 10px;
        /* Limitar visualmente a 3 linhas */
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        max-height: 4.5em;
        line-height: 1.5;
        white-space: normal;
    }

    .note-image { max-width: 100%; height: auto; border: 1px solid #00ff66; border-radius: 5px; margin-bottom: 10px; display: block; }
    .note-divider { margin: 14px 0; border: 0; height: 1px; background: linear-gradient(to right, rgba(255,255,255,0.03), rgba(255,255,255,0)); }

    .empty-message {
        color: #aaa;
        background: rgba(255,255,255,0.02);
        padding: 12px;
        border-radius: 6px;
        border: 1px dashed rgba(255,255,255,0.03);
    }
</style>

<div class="page-wrap">
    <div class="page-header">
        <h1 class="page-title">Minhas Notas</h1>
    </div>

    <div class="actions-bar">
        <a class="btn-create" href="/note/create">Criar Nova Nota</a>
    </div>

    <div class="container">
        <?php if (empty($notes)): ?>
            <p class="empty-message">Nenhuma nota encontrada. Crie sua primeira nota!</p>
        <?php else: ?>
            <ul class="notes-list">
                <?php foreach ($notes as $note): ?>
                    <li class="note-item">
                        <!-- Bloco clicável: abre a view da nota -->
                        <a class="note-link" href="/note/view?id=<?php echo urlencode($note['id']); ?>" title="Abrir nota">
                            <h3 class="note-title"><?php echo htmlspecialchars($note['title']); ?></h3>

                            <?php
                                // Gerar preview conciso no servidor (remove imgs, tags e limita)
                                $rawContent = isset($note['content']) ? $note['content'] : '';
                                $noImages = preg_replace('/<img[^>]*>/i', '', $rawContent);
                                $plain = trim(strip_tags($noImages));
                                $max = 200;
                                if (mb_strlen($plain, 'UTF-8') > $max) {
                                    $preview = mb_substr($plain, 0, $max, 'UTF-8') . '...';
                                } else {
                                    $preview = $plain;
                                }
                            ?>

                            <div class="note-content" title="<?php echo htmlspecialchars($plain); ?>">
                                <?php echo nl2br(htmlspecialchars($preview)); ?>
                            </div>

                            <?php if (!empty($note['image_path'])): ?>
                                <img class="note-image" src="<?php echo htmlspecialchars($note['image_path']); ?>" alt="Imagem da nota">
                            <?php endif; ?>
                        </a>

                        <hr class="note-divider">

                        <!-- Botões de editar/excluir removidos da dashboard -->
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<?php include 'partials-dashboard/footer.php'; ?>