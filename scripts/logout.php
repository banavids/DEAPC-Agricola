<?php
session_start();
// Limpa todas as variáveis de sessão
session_unset();
// Destrói a sessão
session_destroy();

// Redireciona para a página de login
header("Location: ../login/login-farmsmart.html");
exit;
?>