<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Cadastro</title>
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

        ul {
            padding-left: 20px;
        }

        li {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Bem-vindo(a) à Estudie!</h1>
        <p>Olá, {{ $name }}!</p>
        <p>Seja muito bem-vindo(a) à Estudie, a plataforma com uma metodologia única para vencer a curva do
            esquecimento! Estamos muito felizes por ter você com a gente e prontos para ajudar em cada passo da sua
            jornada rumo à aprovação!</p>

        <p>Aqui na Estudie, você encontrará:</p>
        <ul>
            <li>📅 <strong>Agenda de revisões:</strong> Registre o que você estudou no cursinho e nosso algoritmo irá
                organizar revisões de acordo com o seu desempenho em questões, flashcards e simulados.</li>
            <li>🎯 <strong>Milhares de questões do ENEM e vestibulares anteriores:</strong> Pratique com questões de
                diversas bancas organizadoras e áreas, filtrando exatamente o que você precisa.</li>
            <li>📚 <strong>Flashcards:</strong> Tenha acesso ao maior banco de flashcards disponível no mercado, além de
                poder criar seus próprios cards.</li>
            <li>👩‍🏫 <strong>Inteligência Emocional:</strong> Estudar é importante, mas cuidar da saúde mental durante
                a preparação para o Enem é fundamental. Acesse as aulas de inteligência emocional e participe das
                mentorias diretamente com a Ari, psicopedagoga e especialista no assunto.</li>
            <li>💬 <strong>Comunidade de estudantes:</strong> Comente questões, tire dúvidas e aprenda com outros
                estudantes como você. A troca de experiências é uma excelente forma de consolidar o aprendizado!</li>
        </ul>

        <p>Para começar, faça login na sua conta e explore todas as funcionalidades que preparamos para você!</p>

        <a href="{{$estudieLoginUrl}}" class="button">👉 Acessar minha conta</a>

        <p>Se precisar de qualquer ajuda, não hesite em nos contatar. Estamos prontos para fazer parte do seu caminho
            até a aprovação!</p>

        <p>Um grande abraço e bons estudos,<br>Equipe Estudie.</p>

        <div class="footer">
            <p>&copy; 2024 Estudie. Todos os direitos reservados.</p>
            <p><a href="{{$estudieEmail}}">{{$estudieEmail}}</a></p>
        </div>
    </div>
</body>

</html>