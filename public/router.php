<?php
// Bu dosya PHP'nin yerleşik sunucusu için bir router görevi görür
// Tüm istekleri index.php'ye yönlendirir

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Statik dosyaların kontrolü
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    // Dosya mevcut, doğrudan sunucunun işlemesine izin ver
    return false;
}

// Diğer tüm istekleri index.php'ye yönlendir
require_once __DIR__ . '/index.php';