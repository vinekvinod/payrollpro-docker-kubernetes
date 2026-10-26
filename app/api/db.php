
<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = getenv('DB_HOST') ?: '';
$user = getenv('DB_USER') ?: '';
$password = getenv('DB_PASSWORD') ?: '';
$database = getenv('DB_NAME') ?: 'payroll_db';
$port = (int)(getenv('DB_PORT') ?: 4000);
$ca = getenv('DB_CA_CERT') ?: '/etc/ssl/certs/ca-certificates.crt';
try {
    if ($host === '' || $user === '' || $password === '') {
        throw new RuntimeException('Missing database configuration.');
    }

    if (!is_readable($ca)) {
        throw new RuntimeException('Database CA certificate is unavailable.');
    }

    $conn = mysqli_init();
    $conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);
    $conn->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);
    $conn->ssl_set(null, null, $ca, null, null);

    $conn->real_connect(
        $host,
        $user,
        $password,
        $database,
        $port,
        null,
        MYSQLI_CLIENT_SSL
    );

    $conn->set_charset('utf8mb4');
} catch (Throwable $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed.'
    ]);
    exit;
}
