# Skolakoda API

Otvara [skolakoda-api.herokuapp.com](https://skolakoda-api.herokuapp.com/):
```
heroku open
```

Instalira zavisnosti na lokalu:
```
composer install
```

## Baza

Ulazi u remote bazu:

```
heroku pg:psql postgresql-deep-31707 --app skolakoda-api
```

Ulazi u bazu na lokalu:

```
heroku pg:psql
```

Opisuje tabelu:
```
\d+ korisnici
```

Za `SQL` komande mora `;` na kraju!

## TODO

- ukinuti sve biblioteke
  - https://medium.com/@stevesohcot/converting-a-php-web-application-to-heroku-from-a-shard-host-ex-host-gator-7cb64e1ccd91
  - https://devcenter.heroku.com/articles/php-support
- dodati mysql
  - https://scotch.io/@phalconVee/deploying-a-php-and-mysql-web-app-with-heroku

