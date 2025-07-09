<?php
// webhook.php
include './src/scripts/Connection.php';
$access_token = 'SEU_ACCESS_TOKEN_MERCADO_PAGO';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true);

    if ($body['type'] === 'payment') {
        $payment_id = $body['data']['id'];

        // Obtem detalhes do pagamento
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.mercadopago.com/v1/payments/{$payment_id}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $access_token"
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        $pagamento = json_decode($response, true);

        if ($pagamento && $pagamento['status'] === 'approved') {
            $external_reference = $pagamento['external_reference']; // ID do anúncio

            $connection = new Connection();
            $conn = $connection->connectar();

            // Atualiza status do anúncio
            $stmt = $conn->prepare("UPDATE anuncios SET ativo = 1, aprovado = 0, pago = 1 WHERE id = ?");
            $stmt->bind_param("i", $external_reference);
            $stmt->execute();
            $stmt->close();

            // Salva na tabela de pagamentos
            $stmt2 = $conn->prepare("INSERT INTO pagamentos (anuncio_id, payment_id, status) VALUES (?, ?, ?)");
            $status = $pagamento['status'];
            $stmt2->bind_param("iis", $external_reference, $payment_id, $status);
            $stmt2->execute();
            $stmt2->close();

            http_response_code(200);
            echo "Pagamento registrado e anúncio ativado.";
            exit;
        }
    }
}

http_response_code(400);
echo "Requisição inválida";