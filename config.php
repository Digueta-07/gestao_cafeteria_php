<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$host = 'localhost';
$db   = 'gestao_cafeteria';
$user = 'root';
$pass = ''; 
try {
    $conexao = mysqli_connect($host, $user, $pass, $db);
    mysqli_set_charset($conexao, 'utf8mb4');
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit('Não foi possível conectar ao banco de dados. Confira as configurações em config.php.');
}
