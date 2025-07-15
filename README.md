# Sobre
Este é um projeto de estudo construído com **Laravel** versão **12** e **Vue** versão **3**.

O objetivo é praticar containerização com Docker e a integração entre Laravel e Vue utilizando Laravel UI package .

O projeto também implementa Axios para requisições HTTP, JWT (JSON Web Token) para autenticação e gerenciamento de estado com Vuex.

# 🚀 Laravel API com Docker + JWT

Este é um projeto Laravel configurado com Docker e autenticação JWT.

É voltado para testes técnicos, podendo ser executado localmente com poucos comandos, sem necessidade de instalações manuais.

---
## 📦 Requisitos

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

---

## ⚙️ Instalação

Na raiz do projeto rode o comando para construir o ambiente:

```
docker-compose up --build -d
```

O ambiente Docker será inicializado e rodará automaticamente os seguintes processos:

```
composer install

.env copiado automaticamente

APP_KEY gerado

JWT_SECRET gerado

Migrations aplicadas
```

Na pasta **/src** rode o comando para instalar as dependência do front:
```bash
npm install
```

E rode o front com:
```bash
npm run dev
```

Para acessar o container do app
```bash
docker exec -it laravel-app bash
```
---
### 🌱 Seeders

O ambiente já sobe com as **migrations aplicadas** e tudo pronto para uso.

Caso deseje popular o banco com dados de exemplo (ex: usuários de teste), execute:

```bash
docker exec -it laravel-app php artisan db:seed
```

Ou para rodar seeders específicos:

```bash
docker exec -it laravel-app php artisan db:seed --class=TestUserSeeder
```
---
# Testes
Rode os testes da aplicação com o comando:

```bash
docker exec -it book_travel_app php artisan test
```
---
### 🛠 Tecnologias
Laravel 12

[JWT Auth (tymon/jwt-auth)](https://github.com/tymondesigns/jwt-auth)

PHP 8.2 (CLI)

MySQL 8

Docker & Docker Compose

[Laravel/ui](https://github.com/laravel/ui)

Vue 3

Vuex

Axios

🐳 Dúvidas sobre docker-compose up --build?
| Quando usar                                                       | Comando recomendado            |
| ----------------------------------------------------------------- | ------------------------------ |
| Primeira vez rodando o projeto                                    | `docker-compose up --build -d` |
| Alterou o `Dockerfile` ou `entrypoint.sh`                         | `docker-compose up --build -d` |
| Mudou dependências do `composer.json`                             | `docker-compose up --build -d` |
| Apenas mudou código Laravel (PHP, rotas, controllers, views etc.) | `docker-compose up -d`         |
| Quer reiniciar os containers                                      | `docker-compose restart`       |
| Quer desligar e remover containers                                | `docker-compose down`          |

ℹ️ Dica: use --build somente quando necessário. Isso acelera seu ciclo de desenvolvimento e evita builds desnecessários.
---

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).