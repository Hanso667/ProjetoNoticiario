<?php
include './src/scripts/Connection.php';
$connection = new Connection();
$conn = $connection->connectar();

// Busca anúncios válidos e ativos
$query = "
    SELECT imagem 
    FROM anuncios 
    WHERE ativo = 1 
      AND validade >= CURDATE()
";
$result = $conn->query($query);

$imagens = [];
while ($row = $result->fetch_assoc()) {
  $imagens[] = './src/img/ads/' . $row['imagem'];
}

// Embaralha e retorna como JSON
shuffle($imagens);
echo json_encode($imagens);
