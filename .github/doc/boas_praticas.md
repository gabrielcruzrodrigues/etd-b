# Boas Práticas para o Projeto

## Estrutura de Nomeação

### 1. **Nomenclatura de Variáveis**
- As variáveis devem ser nomeadas de forma descritiva e significativa.
- Utilizar o padrão **camelCase** para nomear variáveis:
  - Correto: `$userEmail`, `$orderTotal`
  - Incorreto: `$User_email`, `$OrderTOTAL`
- Utilize nomes em inglês e preferencialmente curtos, porém descritivos. Exemplo: `$firstName` ao invés de `$fname`.

### 2. **Nomenclatura de Métodos**
- Métodos devem seguir o padrão **camelCase** e começar com um verbo que descreva sua ação:
  - Correto: `getUserName()`, `createOrder()`
  - Incorreto: `GetUserName()`, `Create_Order()`

### 3. **Nomenclatura de Classes**
- Utilize o padrão **PascalCase** para classes.
- Classes devem ser nomeadas com substantivos que indicam o que elas representam:
  - Correto: `UserController`, `OrderService`
  - Incorreto: `userController`, `order_service`

### 4. **Nomenclatura de Arquivos**
- Os arquivos de classes devem seguir o nome da classe que contêm, também utilizando o padrão **PascalCase**:
  - Correto: `UserController.php`, `OrderService.php`
  - Incorreto: `user_controller.php`, `orderservice.php`

### 5. **Nomenclatura de Pastas**
Como o projeto é separado por módulos, provavelmente não vai existir a necessidade de criar pastas com nomes muito grandes.

- Use o padrão **PascalCase** para nomear pastas.


## Arquivo `.env.example`

- Sempre que uma nova configuração for adicionada ao arquivo `.env`, é importante atualizá-la no arquivo `.env.example`, para que outros desenvolvedores saibam quais variáveis precisam ser configuradas ao iniciar o projeto.

- O arquivo `.env` contém variáveis de ambiente, como configurações de banco de dados, chave da aplicação, configuração de e-mail, entre outras.

Exemplo de adição de variáveis:
```
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
```

Atualize o arquivo `.env.example` com a mesma variável:
```
MAIL_HOST=host.de.exemplo
MAIL_PORT=1234
```

## Arquivo de configuração e uso de `.env`

- Os arquivos de configuração dentro da pasta `config` são usados para definir padrões que podem ser sobrescritos pelas variáveis do `.env`.
- Variáveis sensíveis e específicas do ambiente, como chaves de API e configurações de banco de dados, devem ser definidas usando a função `env()`, que obtém seus valores do arquivo `.env`.
- Através dos arquivos de configuração (`config/*.php`), você organiza as configurações da aplicação e facilita a manutenção.

### Por que usar `config()`?

Laravel armazena as configurações de toda a aplicação em cache, e as funções `config()` permitem acessar essas configurações de forma eficiente e segura:

- **Desempenho**: A função `config()` é mais rápida que `env()`, pois acessa as configurações já carregadas em cache, ao invés de buscar diretamente no arquivo `.env`. Isso reduz o tempo de resposta da aplicação.
- **Segurança**: Com `config()`, a aplicação não precisa acessar variáveis de ambiente diretamente no código, o que reduz o risco de vazamento de informações sensíveis.
- **Flexibilidade**: Permite definir configurações dinâmicas para diferentes ambientes (desenvolvimento, produção, homologação) sem modificar o código. Apenas as variáveis do `.env` precisam ser ajustadas.

**Exemplo de acesso a uma configuração com `config()`:**

```php
// Exemplo de configuração no arquivo config/mail.php
return [
    'host' => env('MAIL_HOST', 'smtp.mailtrap.io'),
    'port' => env('MAIL_PORT', 2525),
];

// Exemplo de uso em qualquer parte da aplicação
$mailHost = config('mail.host');
$mailPort = config('mail.port');
```

Boas práticas para o uso do .env e config()

- Evite usar env() diretamente: Use config() para acessar valores em qualquer lugar da aplicação. env() deve ser usado apenas dentro dos arquivos de configuração (config/*.php) para definir as variáveis.
- Não exponha informações sensíveis no código: Evite senhas, chaves de API e outros dados sensíveis diretamente no código. Sempre utilize variáveis no .env.
- Atualize o .env.example: Mantenha o arquivo .env.example atualizado com todas as variáveis necessárias para o funcionamento do projeto.
- Cache de configuração: Em ambientes de produção, utilize o comando php artisan config:cache para melhorar o desempenho. Esse comando cria um cache das configurações, o que reduz o uso de env() diretamente.
