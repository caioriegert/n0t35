<?php
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title><?php echo isset($note) ? 'Editar Nota' : 'Criar Nota'; ?> - N0T3S</title>
    <style>
        :root{
            --max-width:820px;
            --accent:#00ff66;
            --muted:#bfffcf;
            --panel:rgba(0,20,0,0.7);
            --input-bg:rgba(0,0,0,0.45);
        }

        html,body{height:100%; margin:0; font-family:'Courier New', monospace; background:#000; color:var(--muted); -webkit-font-smoothing:antialiased;}
        .page-wrap{max-width:var(--max-width); margin:36px auto; padding:16px; box-sizing:border-box;}
        .container{background:var(--panel); border:1px solid #00ff66; border-radius:10px; padding:20px; box-shadow:0 0 14px rgba(0,128,0,0.06);}
        .form-header{display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:14px;}
        .title{color:var(--accent); font-size:1.25rem; font-weight:700;}
        .sub-actions a{color:var(--accent); text-decoration:none; border:1px solid rgba(255,255,255,0.03); padding:6px 10px; border-radius:6px;}
        .form-main{display:flex; flex-direction:column; gap:14px;}

        .form-group{display:flex; flex-direction:column; gap:6px;}
        .form-label{color:#bfffcf; font-size:0.95rem;}
        .form-input{padding:10px 12px; border-radius:8px; border:1px solid rgba(255,255,255,0.05); background:var(--input-bg); color:#dfffe6; outline:none; font-size:1rem; box-sizing:border-box;}

        /* editor */
        .editor-wrap{border:1px solid rgba(255,255,255,0.04); border-radius:8px; background:rgba(0,0,0,0.28); overflow:hidden;}
        .editor-toolbar{display:flex; gap:8px; padding:8px; border-bottom:1px solid rgba(255,255,255,0.02); background:rgba(0,0,0,0.12);}
        .toolbar-btn{background:transparent; color:var(--muted); border:1px solid rgba(255,255,255,0.03); padding:6px 8px; border-radius:6px; cursor:pointer; font-weight:700;}
        .editor{min-height:260px; padding:12px; color:#dfffe6; outline:none; overflow:auto;}
        .editor img{max-width:100%; height:auto; display:block; margin:8px 0; border-radius:6px;}

        .actions{display:flex; gap:10px; align-items:center; margin-top:6px;}
        .btn{padding:10px 14px; border-radius:8px; font-weight:700; cursor:pointer; text-decoration:none; display:inline-block; border:1px solid rgba(255,255,255,0.04); font-family:'Courier New', monospace;}
        .btn-primary{background:#000; color:var(--accent); border:1px solid var(--accent);}
        .btn-secondary{background:transparent; color:var(--muted); border:1px solid rgba(255,255,255,0.04);}

        .note-meta{display:flex; gap:10px; align-items:center; flex-wrap:wrap; color:#9fbf9f; font-size:0.95rem;}

        .alert{padding:10px 12px; border-radius:8px;}
        .alert-error{background:rgba(255,68,68,0.06); color:#ffbfbf; border:1px solid rgba(255,68,68,0.08);}
        .alert-info{background:rgba(0,255,102,0.02); color:#bfffcf; border:1px solid rgba(0,255,102,0.04);}

        @media (max-width:720px){
            .page-wrap{margin:18px 12px;}
            .editor{min-height:200px;}
        }
    </style>
</head>
<body>
    <?php include 'partials-dashboard/header.php'; ?>

    <main class="page-wrap">
        <div class="container">
            <div class="form-header">
                <h1 class="title"><?php echo isset($note) ? 'Editar Nota' : 'Criar Nova Nota'; ?></h1>
                <div class="sub-actions">
                    <a class="btn btn-secondary" href="/dashboard">Voltar</a>
                </div>
            </div>

            <?php if(!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form id="note-form" method="POST" action="<?php echo isset($note) ? '/note/edit?id=' . (int)$note['id'] : '/note/create'; ?>">
                <div class="form-main">
                    <div class="form-group">
                        <label class="form-label" for="title">Título</label>
                        <input id="title" name="title" class="form-input" type="text" value="<?php echo isset($note) ? htmlspecialchars($note['title']) : ''; ?>" required />
                    </div>

                    <div class="form-group">
                        <label class="form-label">Conteúdo (arraste e solte imagens)</label>
                        <div class="editor-wrap" id="editor-wrap">
                            <div class="editor-toolbar" aria-hidden="true">
                                <button type="button" class="toolbar-btn" id="btn-bold"><strong>B</strong></button>
                                <button type="button" class="toolbar-btn" id="btn-italic"><em>I</em></button>
                                <button type="button" class="toolbar-btn" id="btn-link">Link</button>
                                <button type="button" class="toolbar-btn" id="btn-upload">Upload imagem</button>
                                <input id="file-upload" type="file" accept="image/*" style="display:none" />
                            </div>

                            <div id="content" class="editor" contenteditable="true" spellcheck="true"><?php echo isset($note) ? $note['content'] : ''; ?></div>
                        </div>
                        <input type="hidden" name="content" id="content-hidden" />
                    </div>

                    <div class="note-meta">
                        <div>Preview salvará o HTML inserido.</div>
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn btn-primary"><?php echo isset($note) ? 'Salvar Alterações' : 'Criar Nota'; ?></button>
                        <a class="btn btn-secondary" href="/dashboard">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <?php include 'partials-dashboard/footer.php'; ?>

    <script>
        (function(){
            const editableDiv = document.getElementById('content');
            const hiddenInput = document.getElementById('content-hidden');
            const form = document.getElementById('note-form');

            function syncContent(){
                hiddenInput.value = editableDiv.innerHTML.trim();
            }

            // Basic formatting toolbar
            document.getElementById('btn-bold').addEventListener('click', ()=> document.execCommand('bold'));
            document.getElementById('btn-italic').addEventListener('click', ()=> document.execCommand('italic'));
            document.getElementById('btn-link').addEventListener('click', ()=>{
                const url = prompt('URL:','https://');
                if(url) document.execCommand('createLink', false, url);
            });

            // File upload button
            const fileInput = document.getElementById('file-upload');
            document.getElementById('btn-upload').addEventListener('click', ()=> fileInput.click());
            fileInput.addEventListener('change', (e)=>{
                const file = e.target.files[0];
                if(file && file.type.startsWith('image/')) uploadImageFile(file);
                fileInput.value = '';
            });

            // Drag & drop
            editableDiv.addEventListener('dragover', (e)=>{ e.preventDefault(); e.dataTransfer.dropEffect = 'copy'; });
            editableDiv.addEventListener('drop', (e)=> {
                e.preventDefault();
                const files = Array.from(e.dataTransfer.files || []);
                files.forEach(f => { if(f.type.startsWith('image/')) uploadImageFile(f); });
            });

            function uploadImageFile(file){
                const fd = new FormData();
                fd.append('image', file);
                fetch('/upload-image', { method:'POST', body: fd })
                    .then(r => r.json())
                    .then(data => {
                        if(data && data.success && data.imagePath){
                            insertImageAtCursor(data.imagePath);
                            syncContent();
                        } else {
                            alert('Erro ao enviar imagem.');
                        }
                    })
                    .catch(err => { console.error('upload error', err); alert('Erro ao enviar imagem.'); });
            }

            function insertImageAtCursor(src){
                const img = document.createElement('img');
                img.src = src;
                img.alt = 'Imagem da nota';
                img.style.maxWidth = '100%';
                img.contentEditable = 'false';

                let sel = window.getSelection();
                if(!sel || sel.rangeCount === 0){
                    editableDiv.appendChild(img);
                    return;
                }
                const range = sel.getRangeAt(0);
                range.collapse(false);
                range.insertNode(img);
                // move cursor after inserted image
                range.setStartAfter(img);
                range.setEndAfter(img);
                sel.removeAllRanges();
                sel.addRange(range);
            }

            // sync on input and before submit
            editableDiv.addEventListener('input', syncContent);
            form.addEventListener('submit', function(e){
                syncContent();
                if(!hiddenInput.value || hiddenInput.value.replace(/(<([^>]+)>)/gi, '').trim() === ''){
                    e.preventDefault();
                    alert('O conteúdo não pode estar vazio.');
                }
            });

            // init sync
            syncContent();
        })();
    </script>
</body>
</html>