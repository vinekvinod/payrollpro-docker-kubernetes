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


require __DIR__ . '/db.php';

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

if ($name === '' || strlen($name) > 150 ||
    !is_numeric($basic) || !is_numeric($allowances) ||
    !is_numeric($deductions)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Enter a valid name and salary amounts.'
    ]);
    exit;
}

$basic = (float)$basic;
$allowances = (float)$allowances;
$deductions = (float)$deductions;

if (!is_finite($basic) || !is_finite($allowances) ||
    !is_finite($deductions) || $basic < 0 ||
    $allowances < 0 || $deductions < 0 ||
    $basic + $allowances > 1000000000 ||
    $deductions > 1000000000) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid salary amounts.'
    ]);
    exit;
}

$gross = round($basic + $allowances, 2);
$net = round($gross - $deductions, 2);

$stmt = $conn->prepare(
    'INSERT INTO employees
    (employee_name, basic_salary, allowances, deductions, gross_salary, net_salary)
    VALUES (?, ?, ?, ?, ?, ?)'
);

$stmt->bind_param(
    'sddddd',
    $name,
    $basic,
    $allowances,
    $deductions,
    $gross,
    $net
);

$stmt->execute();

echo json_encode([
    'success' => true,
    'message' => 'Payroll record saved successfully.',
    'id' => $stmt->insert_id,
    'employee_name' => $name,
    'gross_salary' => $gross,
    'net_salary' => $net
]);
