# Dot Env Resolver Commands


## Install

Via Composer
```bash
$ composer require alireaza/dot-env-resolver-commands
```


## Usage

```php
use AliReaza\DotEnv\DotEnv;
use AliReaza\DotEnv\Resolver\Commands;

$env = new DotEnv('.env', [
    new Commands(),
]);
$env->toArray(); // Array of variables defined in .env
```


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.