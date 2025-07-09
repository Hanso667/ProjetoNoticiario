<?php 
include './src/scripts/Connection.php';
$connection = new Connection();
$conn = $connection->connectar();

$query = "
    SELECT imagem, link
    FROM anuncios 
    WHERE ativo = 1 
      AND validade >= CURDATE()
      AND aprovado = 1
";

$result = $conn->query($query);
$anuncios = [];

while ($row = $result->fetch_assoc()) {
    $anuncios[] = [
        'imagem' => './src/img/ads/' . $row['imagem'],
        'link' => $row['link']
    ];
}

shuffle($anuncios);
echo json_encode($anuncios);
