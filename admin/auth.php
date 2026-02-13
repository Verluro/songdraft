<?php

declare(strict_types=1);

session_start();

const ADMIN_USER = 'admin';
const ADMIN_PASS = 'songdraft123';

function requireLogin(): void
{
    if (empty($_SESSION['admin_logged_in'])) {
        header('Location: login.php');
        exit;
    }
}
