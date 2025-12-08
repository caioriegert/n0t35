# n0t3s
Programa de anotações inspirado na estética do filme "Matrix" e monitores de fósforo.
Este é um projeto acadêmico desenvolvido para a disciplina de Programação na faculdade, com o objetivo de exercitar habilidades em Design Patterns, desenvolvimento web e integração com banco de dados. O aplicativo permite que usuários se cadastrem, façam login e gerenciem anotações pessoais (notas), incluindo criação, edição, visualização, exclusão e upload de imagens. A interface segue uma temática retro-futurista, remetendo ao visual verde-fosforescente do Matrix.

## 📋 Visão Geral
n0t3s é uma aplicação web full-stack simples, mas funcional, construída com uma stack clássica e acessível. Ela implementa um padrão MVC (Model-View-Controller) básico para separar responsabilidades, facilitando a manutenção e expansão do código.
Funcionalidades Principais

- Autenticação de Usuários: Registro, login, logout e gerenciamento de conta (atualização de senha, exclusão de conta e notas).
- Gerenciamento de Notas: Criar novas notas com título e conteúdo rico (suporte a texto formatado), Listar todas as notas do usuário no dashboard, Visualizar, editar e excluir notas individuais.
- Upload de Imagens: Anexar imagens às notas (armazenadas na pasta public/uploads).
- Interface Responsiva: Design inspirado no Matrix, com tons de verde e efeitos visuais em CSS/JS.
- Segurança Básica: Hash de senhas com password_hash(), sessões PHP e validações de entrada.
- Banco de Dados: Armazenamento persistente de usuários e notas via MySQL.

## 🛠️ Stack Tecnológica

- Backend: PHP 8+ (com PDO para acesso ao banco).
- Frontend: HTML5, CSS3, JavaScript (Vanilla JS para interações).
- Banco de Dados: MySQL (tabelas users e notes).
- Estrutura: Padrão MVC caseiro, com roteamento simples via routes.php.
- Servidor Web: Apache (com suporte a .htaccess para rewrites).
- Outros: Licença MIT, sem dependências externas (tudo nativo).

## 🚀 Instalação e Configuração
### Pré-requisitos

- PHP 8.0 ou superior com extensão PDO habilitada.
- Servidor MySQL (ex: XAMPP, WAMP ou MAMP para desenvolvimento local).
- Servidor web Apache (para .htaccess funcionar corretamente).

### Passos para Rodar o Projeto

- Clone o Repositório:textgit clone https://github.com/caioriegert/n0t3s.git
- cd n0t3s
- Configure o Banco de Dados:
  Crie um banco de dados chamado n0t3s no MySQL (ex: via phpMyAdmin ou linha de comando):textCREATE DATABASE n0t3s CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  Execute o script test_connection.php no navegador (ex: http://localhost/n0t3s/test_connection.php) para testar a conexão e, se necessário, criar as tabelas básicas. O script usa credenciais padrão: host localhost, usuário root, senha vazia. Ajuste em config.php se preciso.
- Estrutura das Tabelas (inferida do código):
  users: id (INT AUTO_INCREMENT PRIMARY KEY), username (VARCHAR), name (VARCHAR), email (VARCHAR UNIQUE), password_hash (VARCHAR(255)).
  notes: id (INT AUTO_INCREMENT PRIMARY KEY), title (VARCHAR), content (TEXT), user_id (INT FOREIGN KEY), created_at (TIMESTAMP), updated_at (TIMESTAMP), image_path (VARCHAR, opcional para uploads).


### Configure o Ambiente PHP:
- Edite config.php se as credenciais do banco mudarem (atualmente hardcoded para dev: root sem senha).
- Ative exibição de erros em routes.php apenas para desenvolvimento (remova em produção).

### Configure o Servidor Web:
- Coloque a pasta do projeto no diretório web do Apache (ex: htdocs no XAMPP).
- Certifique-se de que mod_rewrite está ativado para o roteamento funcionar via .htaccess.
- Acesse via http://localhost/n0t3s/public/ (o public/ é o ponto de entrada público).

### Teste a Aplicação:
- Abra http://localhost/n0t3s/public/ no navegador.
- Registre um novo usuário em /register.
- Faça login e explore o dashboard.


### Possíveis Problemas e Soluções

- Erro de Conexão DB: Verifique credenciais em config.php e se o MySQL está rodando.
- 404 em Rotas: Confirme mod_rewrite e .htaccess (conteúdo: RewriteEngine On e regras básicas para apontar para index.php).
- Uploads Não Funcionam: Verifique permissões na pasta public/uploads (chmod 755 ou 777 em dev).
- Sessões Não Persistem: Ative cookies no navegador e verifique session_start() nas controllers.

## 📖 Como Usar

### Registro e Login:
- Acesse /register para criar uma conta (preencha nome, email, username e senha >6 chars).
- Use /login para autenticar.

### Dashboard:
- Após login, vá para /dashboard para ver lista de notas.
- Clique em "Nova Nota" para criar.

### Gerenciando Notas:
- Criar: Preencha título e conteúdo em /note/create. Adicione imagem via upload.
- Visualizar: /note/view?id=ID para ver detalhes.
- Editar: /note/edit?id=ID para modificar.
- Excluir: Confirme em /note/delete?id=ID.

### Conta do Usuário:
- Acesse /account para ver perfil e opções.
- Atualize senha em /account/password-update.
- Delete notas ou conta com cuidado (irreversível).
- Logout: Clique em "Sair" ou acesse /logout.

## 🖼️ Screenshots

- Página login
<img width="986" height="699" alt="Screenshot 2025-11-07 at 00 47 44" src="https://github.com/user-attachments/assets/1eb57222-9a4b-4fbc-ae18-e111b6b87da8" />

- Página dashboard
<img width="1680" height="928" alt="Screenshot 2025-11-07 at 00 51 14" src="https://github.com/user-attachments/assets/8f12e356-e07c-4f86-ae75-ce52d9ccacd5" />

- Página de criação da anotação
<img width="1019" height="807" alt="Screenshot 2025-11-07 at 00 50 28" src="https://github.com/user-attachments/assets/3238d556-dafe-4fc2-99fa-680d4f39fbd7" />

## 📁 Estrutura do Projeto
textn0t3s/
├── app/
│   ├── controllers/
│   │   ├── AuthController.php  # Lógica de autenticação e conta
│   │   └── NoteController.php  # CRUD de notas e uploads
│   ├── models/
│   │   ├── user.php           # Model para usuários
│   │   └── note.php           # Model para notas
│   └── views/
│       ├── account.php        # Página de perfil
│       ├── dashboard.php      # Lista de notas
│       ├── login.php          # Formulário de login
│       ├── note_form.php      # Form para criar/editar nota
│       ├── note_view.php      # Visualização de nota
│       ├── register.php       # Formulário de registro
│       └── partials-dashboard/ # Partials reutilizáveis (header, footer, etc.)
├── public/
│   ├── index.php              # Ponto de entrada (autoload de routes)
│   ├── .htaccess              # Regras de rewrite
│   ├── style.css              # Estilos Matrix-like (adicione seu CSS aqui!)
│   ├── test.php               # Teste rápido
│   └── uploads/               # Pasta para imagens anexadas
├── config.php                 # Configurações DB (ajuste aqui)
├── routes.php                 # Roteador central
├── test_connection.php        # Script para testar/criar DB
├── LICENSE                    # Licença MIT
└── README.md                  # Este arquivo!

