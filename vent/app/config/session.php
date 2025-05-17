<?php
// Configuration des sessions
$sessionPath = sys_get_temp_dir();
if (is_writable($sessionPath)) {
    session_save_path($sessionPath);
}

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
} 