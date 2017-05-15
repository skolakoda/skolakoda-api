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

Napomena: za `SQL` komande mora `;` na kraju.

## TODO
* dodati bul polje za prijavljene korisnike
* srediti prikazivanje prijava
* dodati korisnike koji su se prijavili
