<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Only POST requests are allowed.'
    ]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON data.'
    ]);
    exit;
}

$name = trim($data['employee_name'] ?? '');
$basic = $data['basic_salary'] ?? null;
$allowances = $data['allowances'] ?? null;
$deductions = $data['deductions'] ?? null;

if ($name === '' || !is_numeric($basic) ||
    !is_numeric($allowances) || !is_numeric($deductions)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Enter a name and valid salary amounts.'
    ]);
    exit;
}

$basic = (float)$basic;
$allowances = (float)$allowances;
$deductions = (float)$deductions;

if ($basic < 0 || $allowances < 0 || $deductions < 0 ||
    !is_finite($basic) || !is_finite($allowances) ||
    !is_finite($deductions) || $basic + $allowances > 1000000000 ||
    $deductions > 1000000000) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Salary amounts are outside the allowed range.'
    ]);
    exit;
}

$gross = $basic + $allowances;
$net = $gross - $deductions;

echo json_encode([
    'success' => true,
    'employee_name' => $name,
    'basic_salary' => round($basic, 2),
    'allowances' => round($allowances, 2),
    'deductions' => round($deductions, 2),
    'gross_salary' => round($gross, 2),
    'net_salary' => round($net, 2)
]);
