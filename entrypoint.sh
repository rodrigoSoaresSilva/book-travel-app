#!/bin/bash

echo "🔧 Preparando ambiente Laravel..."

cd /var/www

# Copia .env apenas se ainda não existir
if [ ! -f .env ]; then
    cp .env.example .env
    echo "✅ .env criado a partir de .env.example"
fi

# Gera chave da aplicação, se não estiver definida
if ! grep -q "^APP_KEY=" .env || [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
    echo "🔑 APP_KEY gerada"
fi

# Gera JWT_SECRET se ainda não estiver no .env
if ! grep -q "^JWT_SECRET=" .env; then
    php artisan jwt:secret --force
    echo "🔐 JWT_SECRET gerado"
fi

# 📦 Criar banco SQLite para testes (se ainda não existir)
if [ ! -f /var/www/database/book_travel_test.sqlite ]; then
  echo "🧪 Criando banco SQLite de teste em /var/www/database/book_travel_test.sqlite"
  touch /var/www/database/book_travel_test.sqlite
  chmod 777 /var/www/database/book_travel_test.sqlite
fi

# Roda migrations
echo "⏳ Verificando conexão com banco de dados..."

# Aguarda o MySQL estar disponível
until php artisan migrate --force > /dev/null 2>&1; do
    echo "   🔁 Ainda sem conexão... aguardando..."
    sleep 3
done

echo "✅ Banco conectado e migrations aplicadas"

echo "✅ Laravel pronto em http://localhost:8000"
echo "📌 Rode manualmente: php artisan db:seed (se desejar popular o banco)"

# Executa o comando original (artisan serve)
exec "$@"
