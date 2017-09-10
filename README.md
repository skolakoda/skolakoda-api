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

Ulazi u bazu na serveru:
```
heroku pg:psql
```

Opisuje tabelu:
```
\d+ korisnici
```

Za `SQL` komande mora `;` na kraju!

## TODO

- backup baze!
- odvojiti biznis
- napraviti login
- probati simfoni
