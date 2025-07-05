#!/bin/bash
set -e

export PGPASSWORD=postgres
until pg_isready -h db -p 5432 -U postgres > /dev/null 2>&1; do
  echo "Ждём БД..."
  sleep 1
done

if [ "$(psql -h db -U postgres -d postgres -tAc "SELECT COUNT(*) FROM products;")" = "0" ]; then
  echo "Запускаем импорт FakeStore..."
  php bin/import_FakeStore.php
else
  echo "Данные уже импортированы. Пропускаем."
fi

exec php-fpm
