<?php
$pageTitle = "Kayıt Ol - BiBilet";
$pageDescription = "BiBilet'e üye olun ve en uygun otobüs biletlerini alın";

// Form hataları ve eski veriler (partial içinde kullanılacak)
$formErrors = $_SESSION['form_errors'] ?? [];
$formData = $_SESSION['form_data'] ?? [];

// İçerik partial yolu
$content = VIEWS_PATH . '/auth/partials/register_content.php';

require_once VIEWS_PATH . '/layouts/app.php';

// Form verilerini kullandıktan sonra session'dan temizle
unset($_SESSION['form_errors']);
unset($_SESSION['form_data']);
