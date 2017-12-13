TimeTracker
============================

TimeTracker this is application for displaying statistics of time spent by developers on the project hosted in [Jira](https://jira.atlassian.com/). TimeTracker powered by [Yii 2](http://www.yiiframework.com/).

This service uses the [Java Application](https://github.com/zapleo/timetracker-Java) for add information to the DataBase via API.


DIRECTORY STRUCTURE
-------------------

      assets/             contains assets definition
      commands/           contains console commands (controllers)
      components/         contains components (widgets, etc.)
      config/             contains application configurations
      controllers/        contains Web controller classes
      environment/        contains config for environments
      helpers/            contains helper classes
      mail/               contains view files for e-mails
      migrations/         contains migrations for DataBase
      models/             contains model classes
      modules/            contains modules (API)
      runtime/            contains files generated during runtime
      tests/              contains various tests for the basic application
      vendor/             contains dependent 3rd-party packages
      views/              contains view files for the Web application
      web/                contains the entry script and Web resources



REQUIREMENTS
------------

The minimum requirement by this project that your Web server supports PHP 5.4.0, MySQL, Git, Composer.


INSTALLATION
------------

- Clone repository using [Git](https://git-scm.com/). Install dependencies using [Composer](http://getcomposer.org/). Run the following command:
```
php composer.phar global update fxp/composer-asset-plugin —no-plugins
```
- Copy config files from `environment/` to `config/` directory, depending on your environment.
- Edit the file `config/db.php` with real data, for example:

```php
return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=yii2basic',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8',
];
```

- Edit the file `config/params.php` with real data, for example:

```php
return [
    'adminEmail' => 'admin@example.com',
    'base_url' => 'http://timetracker.com',
    'jira_url' => 'https://zapleo.atlassian.net',
];
```
- Run migrations:
```
php yii migrate
```
- Go to http://your_site.com