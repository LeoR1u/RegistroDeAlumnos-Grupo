</main>
    
    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3>Sistema Escolar</h3>
                <p>Plataforma de gestión académica para instituciones educativas.</p>
            </div>
            
            <div class="footer-section">
                <h4>Enlaces rápidos</h4>
                <ul>
                    <li><a href="/ver_alumnos.php">Alumnos</a></li>
                    <li><a href="/ver_grupos.php">Grupos</a></li>
                    <li><a href="/reportes.php">Reportes</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Soporte</h4>
                <ul>
                    <li><a href="/ayuda.php">Centro de ayuda</a></li>
                    <li><a href="/contacto.php">Contacto</a></li>
                    <li><a href="/documentacion.php">Documentación</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Estadísticas</h4>
                <div id="statsFooter">
                    <p>Cargando...</p>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> Sistema Escolar. Todos los derechos reservados.</p>
            <p>Desarrollado con ❤️ para la educación</p>
        </div>
    </footer>
    
    <style>
        /* Footer styles */
        .main-footer {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            margin-top: 60px;
        }
        
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .footer-section h3 {
            font-size: 20px;
            margin-bottom: 15px;
        }
        
        .footer-section h4 {
            font-size: 16px;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        
        .footer-section p {
            opacity: 0.8;
            line-height: 1.6;
        }
        
        .footer-section ul {
            list-style: none;
        }
        
        .footer-section ul li {
            margin-bottom: 8px;
        }
        
        .footer-section ul li a {
            color: white;
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }
        
        .footer-section ul li a:hover {
            opacity: 1;
            text-decoration: underline;
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }
        
        .footer-bottom p {
            margin: 5px 0;
            opacity: 0.8;
        }
        
        /* Alert styles */
        .alert {
            max-width: 1200px;
            margin: 20px auto;
            padding: 15px 20px;
            border-radius: 4px;
            font-weight: 500;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .footer-container {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }
    </style>
    
    <script>
        // Mobile menu toggle
        document.getElementById('menuToggle')?.addEventListener('click', function() {
            document.getElementById('navContainer').classList.toggle('show');
        });
        
        // Cargar estadísticas en el footer
        async function cargarEstadisticas() {
            try {
                const [resAlumnos, resGrupos] = await Promise.all([
                    fetch('/api/alumnos.php'),
                    fetch('/api/grupos.php')
                ]);
                
                const dataAlumnos = await resAlumnos.json();
                const dataGrupos = await resGrupos.json();
                
                const totalAlumnos = dataAlumnos.success ? dataAlumnos.data.length : 0;
                const totalGrupos = dataGrupos.success ? dataGrupos.data.length : 0;
                
                document.getElementById('statsFooter').innerHTML = `
                    <p>👨‍🎓 ${totalAlumnos} Alumnos</p>
                    <p>📚 ${totalGrupos} Grupos</p>
                `;
            } catch (error) {
                document.getElementById('statsFooter').innerHTML = `
                    <p>Error al cargar estadísticas</p>
                `;
            }
        }
        
        // Cargar estadísticas al cargar la página
        cargarEstadisticas();
        
        // Auto-cerrar alertas después de 5 segundos
        document.querySelectorAll('.alert').forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }, 5000);
        });
    </script>
</body>
</html>