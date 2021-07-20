### Introduction

This is a simple application to make unfds transfer using paystack. This application has eight endpoints.

- To Register
- To Login
- To Logout
- To submit account details (This is just to create a  dummy transaction source)
- Get bank code (An endpoint to get valid bank code required for transfer)
- Make Transfer
- Search transactions by amount
- Get all transactions for a logged in user

### How to access the app online

Live URL can be accessed [here](https://laravel-transfer-api.herokuapp.com)

Check out the documentation [here](https://documenter.getpostman.com/view/5960688/TzsWspGG)

### How to run the app locally
- Clone the repo.
- Run ```composer install``` (visit [the official website](https://getcomposer.org/) to get started).
- Generate your application key with ```php artisan key:generate```
- Run your migration with ```php artisan migrate``` (you should have your credentials set in your .env before doing this).
- ```php artisan serve``` should spin up the application on port 8000 (you should see something like this: (Starting Laravel development server: http://127.0.0.1:8000)


### You should be up and running by now!