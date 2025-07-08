<?php
session_start();

// Remove todas as variáveis da sessão
$_SESSION = [];

// Destrói a sessão
session_destroy();

// Redireciona para a página inicial
header("Location: index.php");
exit;