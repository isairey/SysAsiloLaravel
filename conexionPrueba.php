<?php
// Forzamos la visualización de errores para una depuración clara
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "--- Iniciando prueba de conexión a servidor local: centro_adulto_mayor ---\n";

// --- PARÁMETROS DE LA BASE DE DATOS LOCAL ---
// Usamos 127.0.0.1 para asegurar una conexión de red local (TCP/IP)
$host = '127.0.0.1';
$port = '5432';
$dbname = 'centro_adulto_mayor';
$user = 'AdultoUser';
$password = 'Adulto123#'; // La contraseña que establecimos para este usuario
$charset = 'utf8';

try {
    // --- CADENA DE CONEXIÓN (DSN) PARA PDO ---
    $dsn = "pgsql:host={$host};port={$port};dbname={$dbname};options='--client_encoding={$charset}'";

    // --- OPCIONES DE PDO ---
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    echo "Intentando conectar a {$host} como usuario '{$user}'...\n";

    // --- INTENTO DE CONEXIÓN ---
    $pdo = new PDO($dsn, $user, $password, $options);

    echo "✅ ¡ÉXITO! Conexión establecida con la base de datos '{$dbname}' en el servidor local.\n";

} catch (PDOException $e) {
    echo "❌ ERROR: No se pudo conectar a la base de datos local.\n";
    echo "--------------------------------------------------------\n";
    echo "MENSAJE DE ERROR: " . $e->getMessage() . "\n";
    echo "--------------------------------------------------------\n";
}

echo "--- Fin del script de prueba ---\n";
