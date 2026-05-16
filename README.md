# Iluma Todo

Iluma Todo e um projeto de codigo aberto para gerenciamento de tarefas organizadas por projeto.

A aplicacao permite criar e acompanhar tarefas, estruturar o trabalho em projetos e manter o fluxo mais claro no dia a dia.

Projeto Laravel executando em Docker com:
- PHP 8.5 (FPM)
- Nginx
- MySQL 8.4
- Mailpit

## Requisitos

- Docker
- Docker Compose

## Primeira execução

1. Suba os containers com build:

```bash
docker compose up -d --build
```

2. Instale dependências PHP (se necessário):

```bash
docker compose exec iluma composer install
```

3. Copie o arquivo de ambiente (se necessário):

```bash
cp .env.example .env
```

4. Gere a chave da aplicação:

```bash
docker compose exec iluma php artisan key:generate
```

5. Rode as migrations:

```bash
docker compose exec iluma php artisan migrate
```

## URLs e portas

- Aplicação: http://localhost:8088
- Mailpit (UI): http://localhost:8026
- Mailpit (SMTP): localhost:1026
- MySQL (host): localhost:3307

## Comandos comuns

### Ambiente

Subir ambiente:

```bash
docker compose up -d
```

Subir com rebuild:

```bash
docker compose up -d --build
```

Parar containers:

```bash
docker compose stop
```

Derrubar ambiente:

```bash
docker compose down
```

Derrubar ambiente e apagar volumes (cuidado: apaga dados do banco):

```bash
docker compose down -v
```

Ver status:

```bash
docker compose ps
```

Ver logs:

```bash
docker compose logs -f
```

### Laravel / PHP

Rodar Artisan:

```bash
docker compose exec iluma php artisan
```

Rodar migrations:

```bash
docker compose exec iluma php artisan migrate
```

Rollback de migration:

```bash
docker compose exec iluma php artisan migrate:rollback
```

Limpar cache:

```bash
docker compose exec iluma php artisan optimize:clear
```

Acessar shell do container PHP:

```bash
docker compose exec iluma sh
```

### Composer

Instalar dependências:

```bash
docker compose exec iluma composer install
```

Atualizar dependências:

```bash
docker compose exec iluma composer update
```

### Testes

Rodar testes:

```bash
docker compose exec iluma php artisan test --compact
```

## Banco de dados

Configuração usada no `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=tasks
DB_USERNAME=iluma
DB_PASSWORD=iluma123456
```

Observação: dentro do Docker, o host do banco é `mysql` (nome do serviço), não `localhost`.

## E-mails (Mailpit)

Configuração usada no `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
```

A caixa de entrada pode ser acessada em `http://localhost:8026`.

## Troubleshooting rápido

Erro de permissão em `storage` ou `bootstrap/cache`:

```bash
docker compose exec iluma sh -lc 'chmod -R ug+rw storage bootstrap/cache'
```

Aplicação não reflete mudanças:

- Verifique se os containers estão ativos: `docker compose ps`
- Rebuild: `docker compose up -d --build`
- Limpe cache do Laravel: `docker compose exec iluma php artisan optimize:clear`

Banco não conecta:

- Confirme `.env` com `DB_HOST=mysql`
- Reinicie serviços: `docker compose restart mysql iluma`
