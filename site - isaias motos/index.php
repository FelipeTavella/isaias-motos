<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oficina Digital — Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="style.css" />

</head>

<body>

    <div class="container">

        <div class="painel-azul">
            <h2>Bem-vindo de volta!</h2>
            <p>Faça login e continue de onde parou.</p>
        </div>

        <form class="formulario" action="paginas/Inicio.php">
            <img src="Imagens/IsaiasmotosB.png" style="width: 57%; padding-bottom: 9%;" alt="logo">
            <h3>Entrar na conta</h3>
            <label class="field"><span><i class="fa fa-user"></i></span><input type="" placeholder="Email"></label>
            <label class="field"><span><i class="fa fa-lock"></i></span><input type="" placeholder="Senha"></label>
            <button type="submit" class="submit">Entrar</button>
            <p style="margin-top: 25px;">
                <a style="font-weight: 600; text-align: center; text-decoration: none; color: rgb(59, 50, 92);" href="#">
                    Solicitar conta!
                </a>
            </p>


        </form>

    </div>

</body>

</html>
