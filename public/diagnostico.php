<?php
// Diagnóstico simple para XAMPP
echo "<h1>Diagnóstico Laravel en XAMPP</h1>";

// Verificar autoload
if (file_exists('../vendor/autoload.php')) {
    require_once '../vendor/autoload.php';
    echo "<p>✅ Autoload OK</p>";
} else {
    echo "<p>❌ Autoload NO encontrado</p>";
    exit;
}

// Verificar .env
if (file_exists('../.env')) {
    $envContent = file_get_contents('../.env');
    if (strpos($envContent, 'APP_KEY=') !== false) {
        echo "<p>✅ APP_KEY existe en .env</p>";
    } else {
        echo "<p>❌ APP_KEY no encontrado en .env</p>";
    }
} else {
    echo "<p>❌ Archivo .env no encontrado</p>";
}

// Verificar cache de configuración
if (file_exists('../bootstrap/cache/config.php')) {
    echo "<p>✅ Cache de configuración existe</p>";
    $config = include '../bootstrap/cache/config.php';
    if (isset($config['app']['key']) && !empty($config['app']['key'])) {
        echo "<p>✅ APP_KEY en cache: " . substr($config['app']['key'], 0, 20) . "...</p>";
    } else {
        echo "<p>❌ APP_KEY no encontrado en cache</p>";
    }
} else {
    echo "<p>❌ Cache de configuración no existe</p>";
}

// Test simple de Laravel
try {
    $app = require_once '../bootstrap/app.php';
    echo "<p>✅ Laravel App creada</p>";

    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "<p>✅ Kernel HTTP creado</p>";

} catch (Exception $e) {
    echo "<p>❌ Error Laravel: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='/libro_planos/public/'>Ir a la aplicación</a></p>";
?>