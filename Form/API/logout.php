<?php
session_start();       // inicia a sessão
session_unset();       // remove todas as variáveis da sessão
session_destroy();     // destrói a sessão

// Redireciona para o login
header("Location: ../index.html"); 
exit;
?>