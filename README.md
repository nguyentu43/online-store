# Eshop

[Try it](https://quiet-badlands-40969.herokuapp.com/)

How to run

- Rename .env.example to .env and add config to this
- Run artisan commands
```bash
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan jwt:secret
```
- Run mail queue
```bash
php artisan queue:work
```

- Admin account: eshop@eshop.test - 123456
![OV](https://github.com/nguyentu43/eshop/raw/master/screenshot.png)

