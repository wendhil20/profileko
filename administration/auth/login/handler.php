<?php
// handler.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include ROOT_PATH . '/network/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = 'Please enter your username and password.';
        header('Location: ' . BASE_URL . '/adminlog');
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM portadmin WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $account = $result->fetch_assoc();
    $stmt->close();

    if ($account && password_verify($password, $account['password'])) {
        $_SESSION['account_id'] = $account['id'];
        $_SESSION['username']   = $account['username'];
        $_SESSION['role']       = $account['role'];
        $_SESSION['position']   = $account['position'];
        $_SESSION['logged_in']  = true;

        $position = $account['position'] ?? '';
        $role = $account['role'];

        if ($role === 'ACCOUNTING AND FINANCE DEPARTMENT') {
            $route = match ($position) {
                'head'           => 'accounting',
                'staff'          => 'accountantstaff',
                'custodian'      => 'accountantcustodian',
                'custoassistant' => 'accountingcustodianassistant',
                default          => 'accounting',
            };
        } else {
            $roleRoutes = [
                'HUMAN RESOURCES DEPARTMENT' => match ($position) {
                    'head'  => 'hr1',
                    'staff' => '',
                    default => 'hr1',
                },
                'SALES AND MARKETING DEPARTMENT' => match ($position) {
                    'head'       => 'saleshead',
                    'salesstaff' => 'sales',
                    default      => 'sales',
                },
            ];
            $route = $roleRoutes[$role] ?? 'adminlog';
        }

        header('Location: ' . BASE_URL . '/' . $route);
        exit;

    } else {
        // Mali ang username/password — bumalik sa login form na may error
        $_SESSION['login_error'] = 'Invalid username or password.';
        header('Location: ' . BASE_URL . '/adminlog');
        exit;
    }
}

// Kung dumiretso dito (GET request, o walang POST data) — bumalik lang sa login
if (empty($_SESSION['logged_in'])) {
    header('Location: ' . BASE_URL . '/adminlog');
    exit;
}

// Kung logged in na pala — i-route sa tamang page
$position = $_SESSION['position'] ?? '';
$role = $_SESSION['role'] ?? '';

if ($role === 'ACCOUNTING AND FINANCE DEPARTMENT') {
    $route = match ($position) {
        'head'           => 'accounting',
        'staff'          => 'accountantstaff',
        'custodian'      => 'accountantcustodian',
        'custoassistant' => 'accountingcustodianassistant',
        default          => 'accounting',
    };
} else {
    $roleRoutes = [
        'HUMAN RESOURCES DEPARTMENT' => match ($position) {
            'head'  => 'hr1',
            'staff' => '',
            default => 'hr1',
        },
        'SALES AND MARKETING DEPARTMENT' => match ($position) {
            'head'       => 'saleshead',
            'salesstaff' => 'sales',
            default      => 'sales',
        },
    ];
    $route = $roleRoutes[$role] ?? 'adminlog';
}

header('Location: ' . BASE_URL . '/' . $route);
exit;