<?php
$pageTitle = 'Inicio';
include 'includes/header.php';
?>

<style>
    .home-container {
        max-width: 600px;
        margin: 0 auto;
    }

    .home-title {
        font-size: clamp(2.5rem, 5vw, 4rem);
        font-weight: 800;
        letter-spacing: -0.03em;
        margin-bottom: 0.5rem;
        color: var(--border);
        text-transform: uppercase;
    }

    .home-subtitle {
        font-family: 'DM Mono', monospace;
        font-size: 0.875rem;
        color: var(--accent);
        margin-bottom: 3rem;
        letter-spacing: 0.05em;
    }

    .menu {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .menu-item {
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem 1.5rem;
        background: white;
        border: 2px solid var(--border);
        color: var(--text);
        font-size: 1.125rem;
        font-weight: 600;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .menu-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 4px;
        background: var(--accent);
        transform: scaleY(0);
        transition: transform 0.3s ease;
    }

    .menu-item:hover {
        transform: translateX(8px);
        border-color: var(--accent);
        background: #fff8f8;
    }

    .menu-item:hover::before {
        transform: scaleY(1);
    }

    .menu-item:nth-child(2)::before {
        background: var(--secondary);
    }

    .menu-item:nth-child(2):hover {
        border-color: var(--secondary);
        background: #f8fff8;
    }

    .menu-item:nth-child(3)::before {
        background: var(--tertiary);
    }

    .menu-item:nth-child(3):hover {
        border-color: var(--tertiary);
        background: #fffbf5;
    }

    .menu-item:nth-child(4)::before {
        background: var(--accent);
    }

    .menu-item:nth-child(4):hover {
        border-color: var(--accent);
        background: #fff8f8;
    }

    .icon {
        font-family: 'DM Mono', monospace;
        font-size: 1.5rem;
        font-weight: 300;
    }

    .label {
        flex: 1;
    }

    .arrow {
        font-family: 'DM Mono', monospace;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .menu-item:hover .arrow {
        opacity: 1;
    }

    @media (max-width: 480px) {
        .home-title {
            font-size: 2rem;
        }
        
        .menu-item {
            padding: 1rem;
            font-size: 1rem;
        }
    }
</style>

<div class="home-container">
    <h1 class="home-title">Administración</h1>
    <div class="home-subtitle">SISTEMA ESCOLAR</div>
    
    <nav class="menu">
        <a href="ver_alumnos.php" class="menu-item">
            <span class="icon">👁</span>
            <span class="label">Ver Alumnos</span>
            <span class="arrow">→</span>
        </a>
        
        <a href="formulario_alumno.php" class="menu-item">
            <span class="icon">+</span>
            <span class="label">Registrar Alumno</span>
            <span class="arrow">→</span>
        </a>
        
        <a href="ver_grupos.php" class="menu-item">
            <span class="icon">◆</span>
            <span class="label">Gestionar Grupos</span>
            <span class="arrow">→</span>
        </a>

        <a href="formulario_grupo.php" class="menu-item">
            <span class="icon">+</span>
            <span class="label">Crear Grupo</span>
            <span class="arrow">→</span>
        </a>
    </nav>
</div>
