<?php
session_start();
// Limpa todas as variáveis de sessão
session_unset();
// Destrói a sessão
session_destroy();

// Redireciona para a página de login no novo caminho
header("Location: ../login-farmsmart.html");
exit;
?>