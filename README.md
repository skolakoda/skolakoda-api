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
(za `SQL` komande mora `;` na kraju)

Ulazi u bazu na serveru:
```
heroku pg:psql
```

Opisuje tabelu:
```
\d+ korisnici
```

## TODO
* dodati binarno polje za polaznike
* dodati korisnike koji su se prijavili sa mejla
* srediti prikaz prijava
