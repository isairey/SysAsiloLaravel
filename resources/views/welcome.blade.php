<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/animations.css') }}">  
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">  
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <title>Centro Hospitalario del Adulto Mayor</title>
    <style>
        table {
            animation: transitionIn-Y-bottom 0.5s;
        }
    </style>
</head>
<body>
    <div class="full-height">
        <center>
        <table border="0">
            <tr>
                <td width="80%">
                    <font class="edoc-logo">Centro Vida Plena</font>
                    <font class="edoc-logo-sub">| Atención Integral al Adulto Mayor</font>
                </td>
                <td width="10%">
                    <a href="{{ route('login') }}" class="non-style-link">
                        <p class="nav-item">INGRESAR</p>
                    </a>
                </td>
            </tr>

            <tr>
                <td colspan="3">
                    <p class="heading-text">Cuidando la salud de nuestros mayores</p>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <p class="sub-text2">
                        En el Centro Vida Plena brindamos atención médica especializada y afectuosa<br>
                        a personas de la tercera edad. Reserve una cita para fisioterapia, nutrición<br>
                        u otros servicios con nuestros profesionales capacitados.
                    </p>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <center>
                        <a href="{{ route('login') }}">
                            <input type="button" value="Reservar Cita" class="login-btn btn-primary btn" style="padding: 10px 25px;">
                        </a>
                    </center>
                </td>
            </tr>
        </table>
        <p class="sub-text2 footer-hashen">Un sistema diseñado para el bienestar del adulto mayor.</p>
        </center>
    </div>
</body>
</html>
