# Deployer recipes

The Deployer recipes used at [ex-akt IT MEDIA CONSULTING][1].

Intended to work with Contao version 4.13+ and Deployer 7. An opinionated extension of the [default Contao recipe][2].

## Install

```bash
composer require --dev 'nutshell-framework/deployer-recipes:^1.0'
```

## Usage

[Install Deployer][3] globally or per project.

Create a _deploy.php_ inside the project. Either use single recipes or the simplified [_project.php_][4] recipe.


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
[3]: https://deployer.org/docs/7.x/installation
[4]: /recipe/project.php