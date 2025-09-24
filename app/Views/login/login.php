<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delafiber - Sistema de Gestión | Acceso Seguro</title>
    
    <!-- ===== CSS FRAMEWORKS ===== -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- ===== ESTILOS PERSONALIZADOS ===== -->
    <style>
        :root {
            --delafiber-primary: #2563eb;
            --delafiber-secondary: #64748b;
            --delafiber-success: #059669;
            --delafiber-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --delafiber-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--delafiber-gradient);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Animación de fondo */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.3" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            z-index: -1;
        }

        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            box-shadow: var(--delafiber-shadow);
            overflow: hidden;
            max-width: 420px;
            width: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }

        .login-header {
            background: linear-gradient(135deg, var(--delafiber-primary), #3b82f6);
            color: white;
            padding: 2.5rem 2rem 2rem;
            text-align: center;
            position: relative;
        }

        .login-header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 15px solid transparent;
            border-right: 15px solid transparent;
            border-top: 10px solid var(--delafiber-primary);
        }

        .company-logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2.5rem;
            backdrop-filter: blur(10px);
        }

        .company-name {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            letter-spacing: -0.025em;
        }

        .company-subtitle {
            font-size: 0.95rem;
            opacity: 0.9;
            font-weight: 400;
        }

        .login-body {
            padding: 2.5rem 2rem;
        }

        .welcome-message {
            text-align: center;
            margin-bottom: 2rem;
        }

        .welcome-title {
            color: #1f2937;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .welcome-subtitle {
            color: var(--delafiber-secondary);
            font-size: 0.9rem;
        }

        .form-floating {
            margin-bottom: 1.5rem;
        }

        .form-floating > .form-control {
            height: 58px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .form-floating > .form-control:focus {
            border-color: var(--delafiber-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-floating > label {
            color: var(--delafiber-secondary);
            font-weight: 500;
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--delafiber-secondary);
            z-index: 5;
        }

        .btn-login {
            width: 100%;
            height: 54px;
            background: var(--delafiber-primary);
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .system-status {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
            padding: 1rem;
            background: rgba(5, 150, 105, 0.1);
            border: 1px solid rgba(5, 150, 105, 0.2);
            border-radius: 12px;
            font-size: 0.9rem;
            color: var(--delafiber-success);
        }

        .status-indicator {
            width: 8px;
            height: 8px;
            background: var(--delafiber-success);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .alert {
            border: none;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .alert-success {
            background: rgba(5, 150, 105, 0.1);
            color: var(--delafiber-success);
            border: 1px solid rgba(5, 150, 105, 0.2);
        }

        .footer-info {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
            color: var(--delafiber-secondary);
            font-size: 0.85rem;
        }

        .security-features {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
            font-size: 0.8rem;
            color: var(--delafiber-secondary);
        }

        .security-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* Animaciones de carga */
        .loading {
            position: relative;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 576px) {
            .login-container {
                padding: 1rem;
            }
            
            .login-body {
                padding: 2rem 1.5rem;
            }
            
            .company-name {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <!-- ===== HEADER CON LOGO Y BRANDING ===== -->
            <div class="login-header">
                <div class="company-logo">
                    <i class="bi bi-router-fill"></i>
                </div>
                <h1 class="company-name">Delafiber</h1>
                <p class="company-subtitle">Sistema de Gestión Integral</p>
            </div>

            <!-- ===== CUERPO DEL FORMULARIO ===== -->
            <div class="login-body">
                <!-- Mensaje de Bienvenida -->
                <div class="welcome-message">
                    <h2 class="welcome-title">
                        <?= $mensajeBienvenida ?? 'Bienvenido de vuelta' ?>
                    </h2>
                    <p class="welcome-subtitle">
                        Ingrese sus credenciales para acceder al sistema
                    </p>
                </div>

                <!-- Alertas de Estado -->
                <?php if(session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <?php if(session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <!-- Formulario de Login -->
                <form action="<?= site_url('login/auth') ?>" method="post" id="loginForm">
                    <?= csrf_field() ?>
                    
                    <!-- Campo Usuario -->
                    <div class="form-floating position-relative">
                        <input type="text" 
                               name="usuario" 
                               id="usuario"
                               class="form-control" 
                               placeholder="Nombre de usuario"
                               value="<?= old('usuario') ?>"
                               required
                               autocomplete="username"
                               maxlength="50">
                        <label for="usuario">Nombre de usuario</label>
                        <i class="bi bi-person input-icon"></i>
                    </div>

                    <!-- Campo Contraseña -->
                    <div class="form-floating position-relative">
                        <input type="password" 
                               name="clave" 
                               id="clave"
                               class="form-control" 
                               placeholder="Contraseña"
                               required
                               autocomplete="current-password">
                        <label for="clave">Contraseña</label>
                        <i class="bi bi-lock input-icon"></i>
                    </div>

                    <!-- Botón de Acceso -->
                    <button type="submit" class="btn btn-primary btn-login" id="btnLogin">
                        <span class="btn-text">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Acceder al Sistema
                        </span>
                    </button>
                </form>

                <!-- Estado del Sistema -->
                <?php if(!($mantenimiento ?? false)): ?>
                    <div class="system-status">
                        <div class="status-indicator"></div>
                        <span>Sistema operativo - Todos los servicios funcionando</span>
                    </div>
                <?php else: ?>
                    <div class="system-status" style="background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.2); color: #dc2626;">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        <span>Sistema en mantenimiento programado</span>
                    </div>
                <?php endif; ?>

                <!-- Información del Pie -->
                <div class="footer-info">
                    <div class="security-features">
                        <div class="security-item">
                            <i class="bi bi-shield-check"></i>
                            <span>Conexión Segura</span>
                        </div>
                        <div class="security-item">
                            <i class="bi bi-clock"></i>
                            <span>Sesión 8h</span>
                        </div>
                        <div class="security-item">
                            <i class="bi bi-eye-slash"></i>
                            <span>Datos Protegidos</span>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <strong>Delafiber</strong> - Servicios de Fibra Óptica<br>
                        <small>Version <?= $ultimaActualizacion['version'] ?? '2.1.0' ?> | © 2025</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== JAVASCRIPT ===== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ===== FUNCIONALIDAD MEJORADA DEL LOGIN =====
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const btnLogin = document.getElementById('btnLogin');
            const usuarioInput = document.getElementById('usuario');
            const claveInput = document.getElementById('clave');

            // Validación en tiempo real
            function validarCampo(input, minLength = 3) {
                const valor = input.value.trim();
                const valido = valor.length >= minLength;
                
                input.classList.toggle('is-valid', valido && valor.length > 0);
                input.classList.toggle('is-invalid', !valido && valor.length > 0);
                
                return valido;
            }

            // Event listeners para validación
            usuarioInput.addEventListener('input', () => validarCampo(usuarioInput, 3));
            claveInput.addEventListener('input', () => validarCampo(claveInput, 4));

            // Manejo del envío del formulario
            loginForm.addEventListener('submit', function(e) {
                const usuarioValido = validarCampo(usuarioInput, 3);
                const claveValida = validarCampo(claveInput, 4);

                if (!usuarioValido || !claveValida) {
                    e.preventDefault();
                    mostrarAlerta('Por favor, complete todos los campos correctamente', 'error');
                    return;
                }

                // Mostrar indicador de carga
                btnLogin.classList.add('loading');
                btnLogin.disabled = true;
                btnLogin.innerHTML = '<span class="btn-text">Verificando credenciales...</span>';
            });

            // Auto-focus en el primer campo vacío
            if (!usuarioInput.value) {
                usuarioInput.focus();
            } else if (!claveInput.value) {
                claveInput.focus();
            }

            // Función para mostrar alertas
            function mostrarAlerta(mensaje, tipo = 'error') {
                const alertaExistente = document.querySelector('.alert');
                if (alertaExistente) {
                    alertaExistente.remove();
                }

                const alerta = document.createElement('div');
                alerta.className = `alert alert-${tipo === 'error' ? 'danger' : 'success'}`;
                alerta.innerHTML = `
                    <i class="bi bi-${tipo === 'error' ? 'exclamation-triangle' : 'check-circle'}-fill me-2"></i>
                    ${mensaje}
                `;

                const welcomeMessage = document.querySelector('.welcome-message');
                welcomeMessage.parentNode.insertBefore(alerta, welcomeMessage.nextSibling);

                // Auto-remover después de 5 segundos
                setTimeout(() => {
                    if (alerta.parentNode) {
                        alerta.remove();
                    }
                }, 5000);
            }

            // Prevenir ataques de fuerza bruta (lado cliente básico)
            let intentosFallidos = parseInt(localStorage.getItem('intentos_login') || '0');
            if (intentosFallidos >= 3) {
                const ultimoIntento = parseInt(localStorage.getItem('ultimo_intento') || '0');
                const tiempoTranscurrido = Date.now() - ultimoIntento;
                const tiempoEspera = 5 * 60 * 1000; // 5 minutos

                if (tiempoTranscurrido < tiempoEspera) {
                    const minutosRestantes = Math.ceil((tiempoEspera - tiempoTranscurrido) / (60 * 1000));
                    mostrarAlerta(`Demasiados intentos fallidos. Espere ${minutosRestantes} minutos.`, 'error');
                    btnLogin.disabled = true;
                }
            }

            // Limpiar contador de intentos en login exitoso
            if (window.location.search.includes('success')) {
                localStorage.removeItem('intentos_login');
                localStorage.removeItem('ultimo_intento');
            }
        });

        // ===== VERIFICACIÓN DE ESTADO DE SESIÓN =====
        function verificarEstadoSistema() {
            fetch('<?= site_url('login/verificarSesion') ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.sesion_activa) {
                        window.location.href = '<?= site_url('dashboard') ?>';
                    }
                })
                .catch(error => {
                    console.log('No hay sesión activa');
                });
        }

        // Verificar cada 30 segundos si hay una sesión activa
        setInterval(verificarEstadoSistema, 30000);
    </script>
</body>
</html>
