<?php
header('Content-Type: application/json');

// Read API key from a file
$apiKeyFilePath = 'apiKey.txt';
if (!file_exists($apiKeyFilePath)) {
    echo json_encode(['error' => 'API key file not found']);
    exit();
}
$apiKey = trim(file_get_contents($apiKeyFilePath));

// Fetch user input from the received POST data
$dataReceived = json_decode(file_get_contents('php://input'), true);
$userInput = $dataReceived['userInput'];

$data = [
    'model' => 'gpt-4',
    'messages' => [
        ['role' => 'system', 'content' => 'Fix my spelling and my grammar but don\'t change it too much. '],
        ['role' => 'user', 'content' => $userInput],
    ],
    'temperature' => 0,
    'max_tokens' => 256,
    'top_p' => 1,
    'frequency_penalty' => 0,
    'presence_penalty' => 0
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$headers = array();
$headers[] = 'Content-Type: application/json';
$headers[] = 'Authorization: Bearer ' . $apiKey;
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo json_encode(['error' => curl_error($ch)]);
    exit();
}
curl_close($ch);

$responseData = json_decode($result, true);

if (isset($responseData['choices'][0]['message']['content'])) {
    echo json_encode(['message' => $responseData['choices'][0]['message']['content']]);
} else {
    echo json_encode(['error' => 'Unexpected API response']);
}
?>
