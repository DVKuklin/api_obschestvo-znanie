1. git clone git@github.com:DVKuklin/api_obschestvo-znanie.git .

2. conmposer install

3. cp .env.example .env

4. Сделать дам базы с сервера

    импортировать ее себе
        sudo mysql -pPASSWORD
        use db_name
        source path_to_file_name.sql

5. в .env указать данные для доступа к бд

6. Скопировать с сервера файлы папок
    public/storage
    public/uploads

7. php artisan serve