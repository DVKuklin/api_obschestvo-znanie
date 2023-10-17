1. git clone git@github.com:DVKuklin/api_obschestvo-znanie.git .

2. composer install

3. cp .env.example .env

4. Сделать дам базы с сервера, предварительно добавив пользователя админа

    импортировать ее себе
        sudo mysql -pPASSWORD
        use db_name
        source path_to_file_name.sql

5. в .env указать данные для доступа к бд

6. Скопировать с сервера файлы папок
    public/storage
    public/uploads

6. php artisan key:generate

7. php artisan serve