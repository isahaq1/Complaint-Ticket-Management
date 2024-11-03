# Complaint-Ticket-Management
```
Clone the repo
and run your shell/terminal
update Composer

create a ".env" file and add your database and mail config data

Build the DB: create a Database named "what_you_want" and add db info to .env

Migrate all the Tables: php artisan migrate
Seed Initial Role Permission  Data:run
php artisan db:seed --class=RolePermissionSeeder
Seed Initial Admin User Data: run
php artisan db:seed --class=UserSeeder
your default admin user emal:admin@example.com and password:12345678

if you want to add test category you can run
php artisan db:seed --class=CategorySeeder

run you application by command:
php artisan serve

