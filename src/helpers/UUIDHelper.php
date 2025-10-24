<?php

require_once __DIR__ . '/Security.php';

class UUIDHelper {
    
    public static function generate() {
        return Security::generateUUID();
    }
}
