# Estudie API

Este projeto está em desenvolvimento ativo. Todas as novas funcionalidades e correções devem seguir o fluxo de trabalho definido com o uso de GitFlow.

- [Boas praticas para esse projeto](.github/doc/boas_praticas.md)
- [Template pull request](.github/PULL_REQUEST_TEMPLATE/pull_request_template.md)
- [Mapeamento de requisitos](https://docs.google.com/document/d/1bXtmK2Z9Lu-bOcrbM2LqgzXhkZjvlbXqpuPAO6g4TFU/edit?usp=sharing)

## Requisitos do Ambiente

Para rodar o projeto localmente, certifique-se de ter as seguintes ferramentas instaladas:

- PHP >= 8.2
- Composer >= 2.8.1
- MySQL >= 8.x ou outro banco de dados compatível
- Docker (opcional, para execução do ambiente de desenvolvimento)

## Instalação

1. Clone o repositório:

```bash  
git clone git@github.com:RCDNC-Estudie/Estudie_backend.git
cd Estudie_backend
```  

## Instale as dependências do projeto:

```bash  
composer install
```  

## Configure o arquivo .env:

- Copie o arquivo `.env.example` e renomeie para `.env`
- Defina as variáveis de ambiente como o banco de dados, serviços de cache e outras integrações.

### Gerar chave de aplicação

Após configurar o arquivo `.env`, é importante gerar uma chave de criptografia única para a aplicação. Essa chave é usada pelo Laravel para garantir a segurança dos dados e é essencial para a encriptação de informações sensíveis.

Para gerar a chave, execute o seguinte comando:

```bash
php artisan key:generate
```
Esse comando cria uma chave aleatória e a define automaticamente na variável `APP_KEY` do arquivo `.env`. Essa chave é crucial para proteger os dados criptografados da aplicação, como sessões e cookies.

> Nota: Sem uma APP_KEY configurada, o Laravel não será capaz de encriptar e decriptar informações com segurança.

Execute as migrações e seeders do banco de dados:

```bash  
php artisan migrate --seed
```

Suba o servidor localmente:

```bash  
php artisan serve
```
Agora a API estará disponível em http://localhost:8000.

## Testes

Este projeto usa o Pest para os testes. Para rodar os testes, use o seguinte comando:

```bash  
php artisan test
```  
Garanta que seus PRs contenham testes para novas funcionalidades ou para corrigir testes quebrados.


## Uso com Docker

Para facilitar o setup e a execução do projeto, você pode utilizar o Docker. O Dockerfile e o docker-compose.yml foram configurados para rodar a aplicação e seus serviços.

### Subindo o ambiente com Docker

1. Certifique-se de ter o Docker e o Docker Compose instalados.
2. Construa e suba os contêineres:

```bash  
  docker compose up -d --build  
```

A API estará disponível em http://localhost:8000 ou em outra porta configurada no docker-compose.yml.

## Rodando Testes no Docker

Para rodar os testes dentro do contêiner Docker:

```bash
  docker compose exec app php artisan test
```

## Comandos úteis com Docker

- Acessar o contêiner da aplicação:

```bash  
  docker compose exec app bash  
```  

- Parar os contêineres:

```bash  
  docker compose down  
```  

- Ver logs dos contêineres:

```bash  
 docker compose logs -f  
```  

## Uso

Após configurar o ambiente, a API pode ser acessada através de endpoints documentados na pasta `doc/api` ou no arquivo `doc/api.json`.

### Autenticação com Token JWT

Para acessar endpoints protegidos, a API utiliza autenticação com **Bearer Token**. Ao fazer login no endpoint `/api/login`, você receberá um token JWT que deve ser enviado em todas as requisições autenticadas no cabeçalho `Authorization`.

#### Exemplo de Autenticação com Bearer Token

Após obter o token JWT com sucesso, inclua-o no cabeçalho `Authorization` de suas requisições:

```http
GET /api/protected-endpoint HTTP/1.1
Host: localhost:8000
Authorization: Bearer seu_token_jwt_aqui
```

Exemplo em cURL:

```bash
curl -X GET http://localhost:8000/api/protected-endpoint \
-H "Authorization: Bearer seu_token_jwt_aqui"
```
Exemplo em JavaScript (fetch):

```javascript
fetch('http://localhost:8000/api/protected-endpoint', {
  method: 'GET',
  headers: {
    'Authorization': 'Bearer seu_token_jwt_aqui',
    'Content-Type': 'application/json'
  }
})
  .then(response => response.json())
  .then(data => console.log(data))
  .catch(error => console.error('Erro:', error));
```

### Tempo de Expiração do Token e Renovação

Por questões de segurança, os tokens JWT possuem um tempo de expiração definido. Quando o token expira, ele perde sua validade, e um novo token será necessário para continuar acessando os endpoints protegidos. O tempo de expiração padrão é de **60 minutos**, mas pode ser ajustado nas configurações da aplicação em `config/sanctum.php`.

Para evitar a perda de acesso quando o token expira, a API oferece uma rota de **refresh token**. Essa rota permite renovar o token atual sem a necessidade de fazer login novamente, proporcionando uma experiência de autenticação contínua.

#### Exemplo de Renovação do Token com Refresh Token

Para renovar o token, envie uma requisição para a rota `/api/refresh-token` com o token JWT expirado no cabeçalho `Authorization`:

```http
POST /api/refresh HTTP/1.1
Host: localhost:8000
Authorization: Bearer seu_token_jwt_expirado
```
Ao fazer isso, você receberá um novo token JWT no corpo da resposta, que poderá ser usado para continuar acessando a API.

> Nota: Lembre-se de usar o novo token retornado na resposta para futuras requisições, substituindo o token expirado.

## Fluxo de Desenvolvimento

Este projeto segue o modelo GitFlow para organização das branches e fluxo de desenvolvimento.

- `main`: Branch principal que contém o código em produção.
- `develop`: Branch de desenvolvimento com o código que será testado antes de ir para a produção.
- `feature` branches: Cada nova funcionalidade ou melhoria deve ser desenvolvida em uma branch de feature (feature/nome-da-feature).
- `hotfix` branches: Correções de bugs urgentes devem ser feitas em branches específicas (hotfix/nome-do-hotfix).

Passos para contribuir com uma nova feature ou correção

1. Crie uma nova branch a partir de develop:

```bash  
git checkout -b feature/nome-da-feature develop
```  
2. Desenvolva a funcionalidade ou correção de bug.

3. Ao concluir, faça o commit das suas alterações:

```bash  
git add .git commit -m "feat: Descrição da feature ou correção"
```  

4. Faça o push da branch:  
  
```bash  
git push origin feature/nome-da-feature
```  

5. Abra um Pull Request (PR) para a branch develop.  
  
## Regras para Aprovação de PR  
  
- Todo PR deve ser revisado e aprovado por dois desenvolvedores antes de ser mesclado à branch develop.  
- O código será revisado para verificar a qualidade, adesão às práticas do projeto e testes adequados.  
- Somente após a aprovação dos dois revisores, o PR será aceito e mesclado.  

  
## Banco de dados  

<img alt='tabelas e relacionamento entre ela do banco de dados desse projeto' src='.github/assets/diagrama-de-tabelas.png'/>
