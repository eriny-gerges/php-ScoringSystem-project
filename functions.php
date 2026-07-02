<?php
declare(strict_types=1);

require_once __DIR__ . '/connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function is_valid_name(string $name): bool
{
    $name = trim($name);
    return $name !== ''
        && mb_strlen($name) <= 100
        && preg_match('/^[A-Za-z ]+$/', $name) === 1;
}
function is_valid_points($points): bool
{
    return is_numeric($points)
        && (string) (int) $points === (string) $points
        && (int) $points >= 0;
}
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}
function csrf_verify(): void
{
    $sent = $_POST['csrf'] ?? '';
    if (!is_string($sent) || !hash_equals($_SESSION['csrf'] ?? '', $sent)) {
        http_response_code(400);
        exit('Invalid request. Please reload the page and try again.');
    }
}
function set_flash(string $message, string $type = 'info'): void
{
    $_SESSION['flash'][] = ['message' => $message, 'type' => $type];
}

function get_flashes(): array
{
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

function is_admin(): bool
{
    return !empty($_SESSION['admin_id']);
}

function require_admin(): void
{
    if (!is_admin()) {
        set_flash('Please log in as an administrator to continue.', 'warning');
        header('Location: login.php');
        exit;
    }
}
function get_events_by_type(mysqli $db, string $type): array
{
    $stmt = $db->prepare(
        'SELECT event_id, event_name, max_points
           FROM events
          WHERE event_type = ?
          ORDER BY event_name'
    );
    $stmt->bind_param('s', $type);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}
