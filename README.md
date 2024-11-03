# Complaint-Ticket-Management
Clone the repo
and run your shell/terminal
update Composer

create a ".env" file and add your database and mail config data

Build the DB: create a Database named "what_you_want" and add db info to .env

Migrate all the Tables: php artisan migrate
Seed Initial Role Permission  Data:run
php artisan db:seed --class=RolePermissionSeeder
Seed Initial Admin User Data: run
php artisan db:seed --class=RolePermissionSeeder
run you application by command:
php artisan serve

