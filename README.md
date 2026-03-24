# promo_data

## Установка
Проект завернут в docker. Пошаговая установка. 

### Контейры, composer, миграции
``` 
    git clone https://...
    cd promo_data/
    make up
    make app
    composer install
    php artisan migrate:fresh --seed
```
По адресу http://localhost:8000/ откроется стартовая страница

### Фукцианал 
http://localhost:8000/process_control - Страница контроля выполнения процессов 

Так как миграции уже применены можно запустить воркеры и выполнить команду для формирования отчета
находясь в контейнере **`app`** выполните
```
php artisan queue:work database --queue=downloads,reports
```
Откройте новый терминал, зайти в контейнер **`app`** и выполните
```
php artisan app:report {category_id}
```

Для тестирования, в сидах, можно проставить больше значений. 