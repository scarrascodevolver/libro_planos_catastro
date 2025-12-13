<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Libro de Planos Biobío</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/css/adminlte.min.css') }}">

    <style>
        :root {
            --gov-azul-principal: #0f69b4;
            --gov-azul-institucional: #0b4582;
            --gov-celeste: #d3def2;
            --gov-rojo-principal: #eb3c46;
            --gov-gris-claro: #e9e9e9;
        }

        body {
            background: linear-gradient(135deg, var(--gov-azul-institucional) 0%, var(--gov-azul-principal) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Source Sans Pro', sans-serif;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }

        .login-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .login-header {
            background: white;
            padding: 40px 30px 30px;
            text-align: center;
            border-bottom: 3px solid var(--gov-azul-principal);
        }

        .login-logo {
            max-width: 120px;
            height: auto;
            margin-bottom: 20px;
        }

        .login-title {
            color: var(--gov-azul-institucional);
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0 0 10px 0;
        }

        .login-subtitle {
            color: #6c757d;
            font-size: 0.95rem;
            margin: 0;
        }

        .login-body {
            padding: 30px 40px 40px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            color: var(--gov-azul-institucional);
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
            font-size: 0.9rem;
        }

        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            padding: 12px 15px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--gov-azul-principal);
            box-shadow: 0 0 0 0.2rem rgba(15, 105, 180, 0.15);
            outline: none;
        }

        .input-group-text {
            background-color: var(--gov-celeste);
            border: 2px solid #e0e0e0;
            border-right: none;
            color: var(--gov-azul-institucional);
        }

        .input-group .form-control {
            border-left: none;
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--gov-azul-principal);
            background-color: var(--gov-azul-principal);
            color: white;
        }

        .input-group-append .btn-toggle-password {
            background-color: var(--gov-celeste);
            border: 2px solid #e0e0e0;
            border-left: none;
            color: var(--gov-azul-institucional);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .input-group-append .btn-toggle-password:hover {
            background-color: var(--gov-azul-principal);
            color: white;
        }

        .input-group:focus-within .btn-toggle-password {
            border-color: var(--gov-azul-principal);
        }

        .btn-login {
            background: var(--gov-azul-principal);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: var(--gov-azul-institucional);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(11, 69, 130, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .custom-control-label {
            color: #6c757d;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .custom-checkbox .custom-control-input:checked~.custom-control-label::before {
            background-color: var(--gov-azul-principal);
            border-color: var(--gov-azul-principal);
        }

        .alert {
            border-radius: 6px;
            border: none;
            font-size: 0.9rem;
        }

        .alert-danger {
            background-color: #fee;
            color: #c00;
        }

        .login-footer {
            padding: 20px;
            text-align: center;
            background: var(--gov-gris-claro);
            color: #6c757d;
            font-size: 0.85rem;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .login-body {
                padding: 20px 25px 30px;
            }

            .login-header {
                padding: 30px 20px 20px;
            }

            .login-title {
                font-size: 1.3rem;
            }
        }

        /* Loading animation */
        .btn-login.loading {
            position: relative;
            color: transparent;
        }

        .btn-login.loading::after {
            content: "";
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid white;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <!-- Header con Logo -->
            <div class="login-header">
                <img src="{{ asset('LOGO_SISTEMA.png') }}" alt="Logo Sistema" class="login-logo">
                <h1 class="login-title">Libro de Planos Topográficos</h1>
                <p class="login-subtitle">Región del Biobío</p>
            </div>

            <!-- Body con Formulario -->
            <div class="login-body">
                <form method="POST" action="{{ route('login') }}" id="login-form">
                    @csrf

                    <!-- Errores -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">
                            <i class="fas fa-envelope mr-1"></i>
                            Correo Electrónico
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                            </div>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" value="{{ old('email') }}"
                                placeholder="usuario@biobio.cl" required autofocus>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock mr-1"></i>
                            Contraseña
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-key"></i>
                                </span>
                            </div>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" placeholder="••••••••" required>
                            <div class="input-group-append">
                                <button class="btn btn-toggle-password" type="button" id="toggle-password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="remember" name="remember"
                                {{ old('remember') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="remember">
                                Mantener sesión iniciada
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Iniciar Sesión
                    </button>
                </form>
            </div>

            <!-- Footer -->

        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Loading animation al enviar
        $('#login-form').on('submit', function() {
            $('.btn-login').addClass('loading').prop('disabled', true);
        });

        // Auto-focus en email al cargar
        $(document).ready(function() {
            $('#email').focus();
        });

        // Toggle mostrar/ocultar contraseña
        $('#toggle-password').on('click', function() {
            const passwordInput = $('#password');
            const icon = $(this).find('i');

            if (passwordInput.attr('type') === 'password') {
                passwordInput.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordInput.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });
    </script>
</body>

</html>
