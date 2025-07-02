main — стабильная версия


dev — интеграционная ветка


feature/* — фичи. Например: feature/login-page, feature/refactor-api


Перед началом работы:


Обновить dev:


git checkout dev


git pull origin dev


Создать новую ветку от dev:


git checkout -b feature/название-задачи


Работать в своей ветке


git add .


git commit -m "Описание коммита"


Запушить ветку:


git push origin feature/название-задачи


Создать Pull Request в dev (делается через интерфейс гитхаба)


Коммиты желательно должны соответствовать правилам Conventional Commits