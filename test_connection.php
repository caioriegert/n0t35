<?php
require 'config.php';

try {
    // Apenas tenta executar uma consulta simples
    $stmt = $pdo->query("SELECT NOW() AS data_atual;");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "✅ Conexão com o banco de dados bem-sucedida!<br>";
    echo "Data/hora do servidor MySQL: " . $row['data_atual'];
} catch (PDOException $e) {
    echo "❌ Erro ao conectar: " . $e->getMessage();
}
