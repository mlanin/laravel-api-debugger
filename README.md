# Laravel-API-Debugger
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
        "total_queries": 2,
        "queries": [
            {
                "query": "select * from `users` where `email` = 'john.doe@acme.com' limit 1;",
                "time": 0.38
            },
            {
                "query": "select * from `posts` where `author` = '1';",
                "time": 1.34
            },
        ],
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

[PHP](https://php.net) 5.4+ or [HHVM](http://hhvm.com) 3.3+, [Composer](https://getcomposer.org) and [Laravel](http://laravel.com) 5.0+ are required.

To get the latest version of Laravel Laravel-API-Debugger, simply add the following line to the require block of your `composer.json` file.

For Laravel 5.2
```
"lanin/laravel-api-debugger": "^0.2.0"
```

For Laravel 5.1
```
"lanin/laravel-api-debugger": "^0.1.0"
```

You'll then need to run `composer install` or `composer update` to download it and have the autoloader updated.

Once Laravel-API-Debugger is installed, you need to register the service provider. Open up `config/app.php` and add the following to the providers key.

```php
Lanin\ApiDebugger\DebuggerServiceProvider::class,
```

Also you can register `DebuggerFacade` for easier  access to the Debugger methods.

```php
'Debugger' => Lanin\ApiDebugger\DebuggerFacade::class,
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

return response()->json(compact('data'));`
```

For more info about better practicies in json apis you can find here http://jsonapi.org/

## Debugging

Debugger's two main tasks are to collect your SQL queries and dump variables.

### Query log

Debugger can provide you with all SQL queries that were fired during the request.

It will listen to the `illuminate.query` event and add queries to the output.

**Note!** It will add queries log ONLY when you set `APP_DEBUG=true`. So you don't have to worry that someone will see your queries on production.

### Var dump

Debugger provides you with the easy way to dump any variable you want right in your JSON answer. This functionality sometimes very handy when you have to urgently debug your production environment.

```php
$foo = 'foo';
$bar = [1, 2, 'bar'];
\Debugger::dump($foo, $bar);
```

You can simultaneously dump as many vars as you want and they will appear in the answer.

**Note!** Of course it it not the best way do debug your production environment, but sometimes it is the only way. So be careful with this, because everyone will see your output, but at least debug will not break your clients.

## Contributing

Please feel free to fork this package and contribute by submitting a pull request to enhance the functionalities.
