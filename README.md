# 📝 Portal de noticias Estudantil

Este projeto é uma aplicação web simples de postagens que permite aos usuários se cadastrarem, autenticarem, criarem, editarem e excluírem posts, além de comentar. Desenvolvido com PHP puro, sem frameworks.

## 📁 Estrutura do Projeto
```
ProjetoNoticiario/
│
├── index.html
├── logout.php
├── autenticar.php
├── postar.php
├── comentar.php
├── editarUser.php
├── editarPost.php
├── deletarUser.php
├── deletarPost.php
├── src/
│ ├── css/
│ │ ├── reset.css
│ │ ├── dashboard.css
│ │ ├── index.css
│ │ ├── login.css
│ │ ├── noticia.css
│ │ └── usuarios.css
│ ├── img/
│ │ ├── Logo.png
│ │ ├── NoImage.jpg
│ │ ├── NoProfile.jpg
│ │ ├── Search.jpg
│ │ └── imagens dos usuarios...
│ ├── scripts/
│ ├── script.js
│ └── dashScript.js
├── pages/
│ ├── dashboard.php
│ ├── login.php
│ ├── signin.php
│ ├── noticia.php
│ └── usuarios.php
```
## 🚀 Funcionalidades

- ✅ Autenticação de usuários (login/logout)
- 📝 Criação, edição e exclusão de postagens
- 💬 Comentários em postagens
- 🔐 Controle de sessão
- 🧑 Gerenciamento de perfil de usuário (editar/excluir)

## ⚙️ Requisitos

- Servidor com suporte a PHP (Apache, Nginx + PHP-FPM)
- PHP 7.4 ou superior
- MySQL/MariaDB
- Navegador web moderno

## 📦 Instalação

1. Clone ou baixe este repositório:

   ```
   git clone https://github.com/Hanso667/ProjetoNoticiario
   ```

2. Coloque os arquivos em um servidor local (como XAMPP, WAMP ou um ambiente LAMP).


3. Acesse http://localhost/ProjetoNoticiario/index.php no navegador.

## 🔐 Segurança

### A autenticação utiliza sessões PHP para manter usuários logados.

### senhas são hashadas no banco de dados.

### as imagens tem formato de nome padronizado.

## 🤝 Contribuição
Contribuições são bem-vindas! Sinta-se livre para abrir issues ou enviar pull requests.
