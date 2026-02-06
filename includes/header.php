<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Título por defecto si no se define
if (!isset($pageTitle)) {
    $pageTitle = 'Sistema Escolar';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Sistema Escolar</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@300;400;500&family=Karla:wght@400;600;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg: #faf8f5;
            --text: #1a1614;
            --accent: #d64545;
            --border: #2a2623;
            --hover: #ff5656;
            --secondary: #4a7c59;
            --tertiary: #f4a261;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Karla', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .main-header {
            background: white;
            border-bottom: 2px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: var(--text);
        }

        .logo-icon {
            font-family: 'DM Mono', monospace;
            font-size: 2rem;
            font-weight: 300;
        }

        .logo-text h1 {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            text-transform: uppercase;
            color: var(--border);
        }

        .logo-text p {
            font-family: 'DM Mono', monospace;
            font-size: 0.75rem;
            color: var(--accent);
            letter-spacing: 0.05em;
        }

        /* Navegación */
        .main-nav {
            background: var(--bg);
            border-bottom: 1px solid #e5e3df;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            gap: 0.5rem;
        }

        .nav-container a {
            font-family: 'Karla', sans-serif;
            font-weight: 600;
            display: inline-block;
            padding: 1rem 1.5rem;
            color: var(--text);
            text-decoration: none;
            transition: all 0.2s ease;
            border-bottom: 3px solid transparent;
            position: relative;
        }

        .nav-container a::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: var(--accent);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .nav-container a:hover {
            color: var(--accent);
            background: #fff8f8;
        }

        .nav-container a:hover::before {
            transform: scaleY(1);
        }

        .nav-container a.active {
            color: var(--accent);
            border-bottom-color: var(--accent);
        }

        /* Mobile menu */
        .menu-toggle {
            display: none;
            background: none;
            border: 2px solid var(--border);
            color: var(--text);
            font-family: 'DM Mono', monospace;
            font-size: 1.25rem;
            cursor: pointer;
            padding: 0.5rem 1rem;
            transition: all 0.2s ease;
        }

        .menu-toggle:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        /* Main content */
        main {
            flex: 1;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Breadcrumb */
        .breadcrumb {
            font-family: 'DM Mono', monospace;
            font-size: 0.875rem;
            margin-bottom: 2rem;
            color: var(--text);
            opacity: 0.7;
        }

        .breadcrumb a {
            color: var(--accent);
            text-decoration: none;
            transition: opacity 0.2s ease;
        }

        .breadcrumb a:hover {
            opacity: 0.7;
        }

        /* Alert messages */
        .alert {
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            border: 2px solid;
            font-weight: 600;
            position: relative;
            overflow: hidden;
        }

        .alert::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
        }

        .alert-success {
            background: #f8fff8;
            border-color: var(--secondary);
            color: var(--secondary);
        }

        .alert-success::before {
            background: var(--secondary);
        }

        .alert-error {
            background: #fff8f8;
            border-color: var(--accent);
            color: var(--accent);
        }

        .alert-error::before {
            background: var(--accent);
        }

        .alert-info {
            background: #fffbf5;
            border-color: var(--tertiary);
            color: #8b5a2b;
        }

        .alert-info::before {
            background: var(--tertiary);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-container {
                padding: 1rem;
            }

            .logo-text h1 {
                font-size: 1.25rem;
            }

            .logo-text p {
                display: none;
            }

            .menu-toggle {
                display: block;
            }

            .nav-container {
                display: none;
                flex-direction: column;
                padding: 0;
            }

            .nav-container.show {
                display: flex;
            }

            .nav-container a {
                border-left: 3px solid transparent;
                border-bottom: 1px solid #e5e3df;
            }

            .nav-container a::before {
                width: 3px;
            }

            .nav-container a.active {
                border-left-color: var(--accent);
                border-bottom-color: #e5e3df;
            }

            main {
                padding: 1rem;
            }
        }

        @media (max-width: 480px) {
            .logo-icon {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="header-container">
            <a href="/index.php" class="logo">
                <span class="logo-icon">◆</span>
                <div class="logo-text">
                    <h1>Sistema Escolar</h1>
                    <p>ADMINISTRACIÓN</p>
                </div>
            </a>
            <button class="menu-toggle" id="menuToggle">☰</button>
        </div>
    </header>

    <!-- Navegación -->
    <nav class="main-nav">
        <div class="nav-container" id="navContainer">
            <a href="/index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                Inicio
            </a>
            <a href="/ver_alumnos.php" class="<?= in_array(basename($_SERVER['PHP_SELF']), ['ver_alumnos.php', 'formulario_alumno.php']) ? 'active' : '' ?>">
                Alumnos
            </a>
            <a href="/ver_grupos.php" class="<?= in_array(basename($_SERVER['PHP_SELF']), ['ver_grupos.php', 'formulario_grupo.php']) ? 'active' : '' ?>">
                Grupos
            </a>
            <a href="/catalogos.php" class="<?= in_array(basename($_SERVER['PHP_SELF']), ['catalogos.php', 'catalogo_carreras.php', 'catalogo_turnos.php']) ? 'active' : '' ?>">
                Catálogos
            </a>
        </div>
    </nav>

    <!-- Main content -->
    <main>
        <?php if (isset($breadcrumb) && !empty($breadcrumb)): ?>
        <div class="breadcrumb">
            <a href="/index.php">inicio</a>
            <?php foreach ($breadcrumb as $item): ?>
                <?php if (isset($item['url'])): ?>
                    / <a href="<?= htmlspecialchars($item['url']) ?>"><?= htmlspecialchars($item['text']) ?></a>
                <?php else: ?>
                    / <span><?= htmlspecialchars($item['text']) ?></span>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?= $_SESSION['mensaje_tipo'] ?? 'info' ?>">
            <?= htmlspecialchars($_SESSION['mensaje']) ?>
        </div>
        <?php 
            unset($_SESSION['mensaje']);
            unset($_SESSION['mensaje_tipo']);
        endif; 
        ?>