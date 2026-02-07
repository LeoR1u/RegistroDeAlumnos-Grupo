<?php
$pageTitle = 'Catálogos';
$breadcrumb = [
    ['text' => 'Catálogos']
];
include 'includes/header.php';
?>

<style>
    .catalogos-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .page-title {
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 800;
        letter-spacing: -0.03em;
        margin-bottom: 0.5rem;
        color: var(--border);
        text-transform: uppercase;
    }

    .page-subtitle {
        font-family: 'DM Mono', monospace;
        font-size: 0.875rem;
        color: var(--accent);
        margin-bottom: 3rem;
        letter-spacing: 0.05em;
    }

    .catalogos-menu {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .catalogo-item {
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

    .catalogo-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 4px;
        transform: scaleY(0);
        transition: transform 0.3s ease;
    }

    .catalogo-item:nth-child(1)::before {
        background: var(--accent);
    }

    .catalogo-item:nth-child(1):hover {
        border-color: var(--accent);
        background: #fff8f8;
    }

    .catalogo-item:nth-child(2)::before {
        background: var(--secondary);
    }

    .catalogo-item:nth-child(2):hover {
        border-color: var(--secondary);
        background: #f8fff8;
    }

    .catalogo-item:nth-child(3)::before {
        background: var(--tertiary);
    }

    .catalogo-item:nth-child(3):hover {
        border-color: var(--tertiary);
        background: #fffbf5;
    }

    .catalogo-item:hover {
        transform: translateX(8px);
    }

    .catalogo-item:hover::before {
        transform: scaleY(1);
    }

    .catalogo-icon {
        font-family: 'DM Mono', monospace;
        font-size: 1.5rem;
        font-weight: 300;
    }

    .catalogo-label {
        flex: 1;
    }

    .catalogo-count {
        font-family: 'DM Mono', monospace;
        font-size: 0.875rem;
        opacity: 0.6;
    }

    .catalogo-arrow {
        font-family: 'DM Mono', monospace;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .catalogo-item:hover .catalogo-arrow {
        opacity: 1;
    }

    @media (max-width: 480px) {
        .page-title {
            font-size: 2rem;
        }
        
        .catalogo-item {
            padding: 1rem;
            font-size: 1rem;
        }
    }
</style>

<div class="catalogos-container">
    <h1 class="page-title">Catálogos</h1>
    <div class="page-subtitle">GESTIÓN DE DATOS MAESTROS</div>
    
    <div class="catalogos-menu">
        <a href="catalogo_carreras.php" class="catalogo-item">
            <span class="catalogo-icon">◆</span>
            <span class="catalogo-label">Carreras</span>
            <span class="catalogo-arrow">→</span>
        </a>
        
        <a href="catalogo_turnos.php" class="catalogo-item">
            <span class="catalogo-icon">◆</span>
            <span class="catalogo-label">Turnos</span>
            <span class="catalogo-arrow">→</span>
        </a>
        
        <a href="catalogo_grupos.php" class="catalogo-item">
            <span class="catalogo-icon">◆</span>
            <span class="catalogo-label">Grupos</span>
            <span class="catalogo-arrow">→</span>
        </a>
    
        <a href="catalogo_grados.php" class="catalogo-item">
            <span class="catalogo-icon">◆</span>
            <span class="catalogo-label">Grados</span>
            <span class="catalogo-arrow">→</span>
        </a>
    </div>
</div>
