<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Senha Alterada com Sucesso</title>
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
    <h1>Senha Alterada com Sucesso</h1>
    <p>Olá, {{ $name }}!</p>
    <p>Estamos confirmando que sua senha foi alterada com sucesso.</p>

    <p>Se você não realizou essa alteração, entre em contato imediatamente com o nosso suporte para garantir a segurança
      da sua conta.</p>

    <p>Se precisar de qualquer ajuda, estamos à disposição para assisti-lo.</p>

    <p>Atenciosamente,<br>Equipe Estudie.</p>

    <div class="footer">
      <p>&copy; 2024 Estudie. Todos os direitos reservados.</p>
      <p><a href="{{$estudieEmail}}">{{$estudieEmail}}</a></p>
    </div>
  </div>
</body>

</html>