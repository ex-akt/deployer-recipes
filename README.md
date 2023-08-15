# Deployer recipes

The Deployer recipes used at [ex-akt IT MEDIA CONSULTING][1].

Intended to work with Contao version 4.13+ and Deployer 7. An opinionated extension of the [default Contao recipe][2] and [nutshell-framework/deployer-recipes][3]

## Install

```bash
composer require --dev 'ex-akt/deployer-recipes:^1.0'
```

## Usage

[Install Deployer][4] globally or per project.

Create a _deploy.php_ inside the project. Either use single recipes or the simplified [_project.php_][5] recipe.


### Deploy

- `dep deploy [host]`

### Custom _.env file per host

Create a _.env.prod_ file in your project, e.g., `.env.prod`, then configure it for the host:

```php
host('www.example.org')
    ->set('env_filename', '.env.prod')
```

----

[1]: https://www.ex-akt.de/
[2]: https://docs.contao.org/manual/en/guides/deployer/
[3]: https://github.com/nutshell-framework/deployer-recipes
[4]: https://deployer.org/docs/7.x/installation
[5]: /recipe/project.php