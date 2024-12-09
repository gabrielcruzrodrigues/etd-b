<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinição de Senha</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333333;
        }

        .container {
            width: 100%;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 40px auto;
        }

        h1 {
            color: #FF8202;
            font-size: 24px;
        }

        p {
            font-size: 16px;
            line-height: 1.6;
        }

        .button {
            display: inline-block;
            padding: 12px 20px;
            font-size: 16px;
            color: #ffffff;
            background-color: #FF8202;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #888888;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Redefinição de Senha</h1>
        <p>Olá, {{ $name }}!</p>
        <p>Recebemos uma solicitação para redefinir a senha da sua conta.</p>

        <p>Para redefinir sua senha, clique no botão abaixo:</p>

        <a href="{{ $url }}" class="button">Redefinir Senha</a>

        <p>Este link de redefinição de senha é válido por 60 minutos. Caso você não tenha solicitado a redefinição de
            senha, ignore este e-mail.</p>

        <p>Se precisar de qualquer ajuda, entre em contato com nosso suporte.</p>

        <p>Atenciosamente,<br>Equipe Estudie.</p>

        <div class="footer">
            <p>&copy; 2024 Estudie. Todos os direitos reservados.</p>
            <p><a href="{{$estudieEmail}}">{{$estudieEmail}}</a></p>
        </div>
    </div>
</body>

</html>