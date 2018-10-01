<p align="middle">
<strong>Jugger API</strong>

[![Latest Stable Version](https://poser.pugx.org/jianastrero/jugger-api/v/stable)](https://packagist.org/packages/jianastrero/jugger-api)
[![Total Downloads](https://poser.pugx.org/jianastrero/jugger-api/downloads)](https://packagist.org/packages/jianastrero/jugger-api)
[![Latest Unstable Version](https://poser.pugx.org/jianastrero/jugger-api/v/unstable)](https://packagist.org/packages/jianastrero/jugger-api)
[![License](https://poser.pugx.org/jianastrero/jugger-api/license)](https://packagist.org/packages/jianastrero/jugger-api)
[![composer.lock](https://poser.pugx.org/jianastrero/jugger-api/composerlock)](https://packagist.org/packages/jianastrero/jugger-api)

</p>

![Jugger API](src/public/favicon.png)

###### screenshots

![Jugger API](login-screenshot.png)

![Jugger API](home-screenshot.png)

## Description
Jugger API makes creating API's the easiest way possible on laravel. It runs together with your app and can be found at http://yourdomain.com/jugger-api. It depends on Passport, dbal and VueJS. Laravel Passport for OAuth on API's, and VueJS for the web to create your API's. dbal is used for transformation / mutation. Jugger API follows [best practices for API development](https://blog.mwaysolutions.com/2014/06/05/10-best-practices-for-better-restful-api/).

## Dependencies
* laravel/passport
* doctrine/dbal

## Installation
#### 1. Require the package
`composer require jianastrero/jugger-api`
#### 2. Depending on your  laravel version, you may need to add this to config/app.php
```php
'providers' => [
...
JianAstrero\JuggerAPI\JuggerAPIServiceProvider::class
...
]
```
#### 3. Publish Jugger API resources
`php artisan vendor:publish --tag=jugger-api`
#### 4. Setup config with db credentials
```env
DB_HOST=localhost
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```
#### 5. Migrate your app, passport and jugger's tables *(This is both for JuggerAPI and passport)*
`php artisan migrate`
#### 6. Seed Jugger API with its own
`php artisan jugger:seed`

## Passport *(for OAuth)* *read more on: [Laravel Passport](https://laravel.com/docs/5.7/passport)*

#### 7. Install passport
`php artisan passport:install`
#### 8. use trait HasApiToken on User model
```php
use Notifiable, HasApiTokens;
```
#### 9. Add *Passport::routes* on  AuthServiceProvider
```php
public function boot()
{
    $this->registerPolicies();

    Passport::routes();
}
```
#### 10. Set the driver for api to passport on *config/auth.php*
```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ],
],
```

## VueJS
#### 11. Install npm packages
`npm install`
#### 12. Install npm vue session
`npm install vue-session`
#### 13. Let web pack recognize sources (*webpack.mix.js*)
```javascript
mix
    .js('resources/jianastrero/jugger-api/js/jugger-api.js', 'public/js')
    .sass('resources/jianastrero/jugger-api/sass/jugger-api.scss', 'public/css');
```
#### 14. Compile sources
`npm run dev`

## How to use
Run your web app(*php artisan serve*) then open your favorite web browser and navigate to http://127.0.0.1:8000/jugger-api
From here, you could use any user you have to login. Remember that as of now, it is required to use email and password to get authenticated and be logged in.

Once logged in, you could then create new routes, edit, or delete. Also, be careful to delete the record for JuggerAPI because it will refrain you from creating, modifying, or deleting new records. To fix this, run `php artisan jugger:seed` again.

## To make a model be recognized by JuggerAPI, your model should use the traits HasTable and CanMutate
```php
use Notifiable, HasApiTokens, HasTable, CanMutate;
```
When this is done, just refresh your page and this model will be available on add and edit modal selection.


## License
This software is released under the [MIT license](https://opensource.org/licenses/MIT).
