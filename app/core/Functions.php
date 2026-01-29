<?php
function redirect($url) {
    header('Location: ' . BASEURL . '/' . $url);
    exit;
}

function requireAuth() {
    if (!isset($_SESSION['user'])) {
        redirect('auth/login');
    }
}

function requireRole($allowed_roles) {
    if (!isset($_SESSION['user'])) {
        redirect('auth/login');
    }
    if (!in_array($_SESSION['user']['role'], $allowed_roles)) {
        redirect('dashboard');
    }
}
