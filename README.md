# Laravel-API-Debugger
[![Travis](https://img.shields.io/travis/rust-lang/rust.svg)](https://travis-ci.org/mlanin/laravel-api-debugger)

> Easily debug your JSON API.

When you are developing JSON API sometimes you need to debug it, but if you will use `dd()` or `var_dump()` you will break the output that will affect every client that is working with your API at the moment. Debugger is made to provide you with all your debug information and not corrupt the output.

```json
{
  "posts": [
    {
      "id": 1,
      "title": "Title 1",
      "body": "Body 1"
    },
    {
      "id": 2,
      "title": "Title 2",
      "body": "Body 2"
    }
  ],
  "meta": {
    "total": 2
  },
  "debug": {
    "database": {
      "total": 2,
      "items": [
        {
          "connection": "accounts",
          "query": "select * from `users` where `email` = 'john.doe@acme.com' limit 1;",
          "time": 0.38
        },
        {
          "connection": "posts",
          "query": "select * from `posts` where `author` = '1';",
          "time": 1.34
        }
      ]
    },
    "dump": [
      "foo",
      [
        1,
        2,
        "bar"
      ]
    ]
  }
}
```

## Installation

> This help is for Laravel 5.4 only. Readme for earlier versions can be found in the relevant branches of this repo.

[PHP](https://php.net) >=5.5.9+ or [HHVM](http://hhvm.com) 3.3+, [Composer](https://getcomposer.org) and [Laravel](http://laravel.com) 5.4+ are required.

To get the latest version of Laravel Laravel-API-Debugger, simply add the following line to the require block of your `composer.json` file.

For Laravel 5.4
```
"lanin/laravel-api-debugger": "^0.3"
```

You'll then need to run `composer install` or `composer update` to download it and have the autoloader updated.

Once Laravel-API-Debugger is installed, you need to register the service provider. Open up `config/app.php` and add the following to the providers key.

For Laravel 5.4
```php
Lanin\Laravel\ApiDebugger\ServiceProvider::class,
```

Also you can register a Facade for easier access to the Debugger methods.

For Laravel 5.4
```php
'Debugger' => Lanin\Laravel\ApiDebugger\Facade::class,
```

## Json response

Before extension will populate your answer it will try to distinguish if it is a json response. It will do it by validating if it is a JsonResponse instance. The best way to do it is to return `response()->json();` in your controller's method.

Also please be careful with what you return. As if your answer will not be wrapped in any kind of `data` attribute (`pages` in the example above), frontend could be damaged because of waiting the particular set of attributes but it will return extra `debug` one.

So the best way to return your responses is like this 
```php
$data = [
    'foo' => 'bar',
    'baz' => 1,
];

return response()->json([
    'data' => [
        'foo' => 'bar',
        'baz' => 1,
    ],
]);
```

For more info about better practices in JSON APIs you can find here http://jsonapi.org/

## Debugging

Debugger's two main tasks are to dump variables and collect your SQL queries. 

### Var dump

Debugger provides you with the easy way to dump any variable you want right in your JSON answer. This functionality sometimes very handy when you have to urgently debug your production environment.

```php
$foo = 'foo';
$bar = [1, 2, 'bar'];

// As a helper
lad($foo, $bar);

// or as a facade
\Debugger::dump($foo, $bar);
```

You can simultaneously dump as many vars as you want and they will appear in the answer.

**Note!** Of course it it not the best way do debug your production environment, but sometimes it is the only way. So be careful with this, because everyone will see your output, but at least debug will not break your clients.

### Query log

Debugger can provide you with all SQL queries that were fired during the request.

It will listen to the query events and add them to the the output.

**Note!** It will add queries log ONLY when you set `APP_DEBUG=true`. So you don't have to worry that someone will see your queries on production.

### Extending

You can easily add your own data collections to debug output like database queries are. 
Just look at how it was done in the package itself and repeat for anything you want (for example HTTP requests).

## Contributing

Please feel free to fork this package and contribute by submitting a pull request to enhance the functionalities.
