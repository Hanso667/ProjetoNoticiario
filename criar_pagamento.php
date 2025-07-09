<?php
$access_token = 'SEU_ACCESS_TOKEN_MERCADO_PAGO';

$valor = isset($_GET['valor']) ? floatval($_GET['valor']) : 0.00;
$email_usuario = isset($_GET['email']) ? $_GET['email'] : 'comprador@exemplo.com';

$anuncio_id = $_GET['anuncio_id']; // Passe o ID via URL ou sessão

$data = [
    "items" => [[
        "title" => "Pagamento de anúncio",
        "quantity" => 1,
        "unit_price" => $valor,
        "currency_id" => "BRL"
    ]],
    "payer" => ["email" => $email_usuario],
    "back_urls" => [
        "success" => "https://localhost/sucesso.php",
        "failure" => "https://localhost/falha.php",
        "pending" => "https://localhost/pendente.php"
    ],
    "external_reference" => $anuncio_id, // associamos ao anúncio
    "auto_return" => "approved"
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.mercadopago.com/checkout/preferences");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $access_token"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

$responseData = json_decode($response, true);

if (isset($responseData['init_point'])) {
    header("Location: " . $responseData['init_point']);
    exit;
} else {
    echo "Erro ao criar pagamento.";
    print_r($responseData);
}
?>
