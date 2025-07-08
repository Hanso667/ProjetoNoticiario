<?php
session_start();
if (!isset($_SESSION['Mode']) || $_SESSION['Mode'] === 'Dark') {
    $_SESSION['Mode'] = 'Light';
} else {
    $_SESSION['Mode'] = 'Dark';
}
echo json_encode(['success' => true, 'mode' => $_SESSION['Mode']]);
?>