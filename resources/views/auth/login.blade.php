<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar Sesión</title>

  {{-- Tus CSS globales --}}
  <link rel="stylesheet" href="{{ asset('css/animations.css') }}">
  <link rel="stylesheet" href="{{ asset('css/main.css') }}">
  <link rel="stylesheet" href="{{ asset('css/index.css') }}">

  <style>
    /* ==== Base: centrado y fondo gris claro ==== */
    body {
      font-family: sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
      background-color: #f0f0f0;
    }

    /* ==== Caja del login ==== */
    .login-container {
      background-color: #fff;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      text-align: center;
      width: 360px;
      animation: transitionIn-Y-over 0.5s;
    }
    .login-container h1 {
      margin-bottom: 20px;
      color: #333;
    }

    /* ==== Inputs ==== */
    .login-container input[type="text"],
    .login-container input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 4px;
      box-sizing: border-box;
    }

    /* ==== Botón ==== */
    .login-container button {
      width: 100%;
      padding: 10px;
      background-color: #007bff;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
    }
    .login-container button:hover {
      background-color: #0056b3;
    }

    /* ==== Mensajes ==== */
    .error-message {
      color: red;
      margin-bottom: 15px;
    }
    .success-message {
      color: green;
      margin-bottom: 15px;
    }

    /* ==== Link de registro ==== */
    .register-link {
      margin-top: 15px;
      font-size: 14px;
    }
    .register-link a {
      color: #007bff;
      text-decoration: none;
    }
    .register-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h1>Iniciar Sesión</h1>

    @if(session('success'))
      <p class="success-message">{{ session('success') }}</p>
    @endif

    @if($errors->any())
      <p class="error-message">{{ $errors->first() }}</p>
    @endif

    <form method="POST" action="{{ route('login') }}">
      @csrf

      <input
        type="text"
        name="ci"
        placeholder="Carnet de identidad"
        value="{{ old('ci') }}"
        required
        autofocus
      ><br>

      <input
        type="password"
        name="password"
        placeholder="Contraseña"
        required
      ><br>

      <button type="submit">Iniciar sesión</button>
    </form>

    {{-- Si solo el admin crea usuarios, muestra esto: --}}
    <span>Si no tienes cuenta, contacta al administrador.</span>

  </div>
</body>
</html>
