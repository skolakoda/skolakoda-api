class Admin {
  constructor($q, $http) {
    $q.all([
      $http.get('https://skolakoda-api.herokuapp.com/korisnici'),
      $http.get('https://skolakoda-api.herokuapp.com/kursevi'),
      $http.get('https://skolakoda-api.herokuapp.com/prijave')
    ])
      .then(responses => {
        this.korisnici = responses[0].data
        this.kursevi = responses[1].data
        this.prijave = responses[2].data
        this.asocirajPodatke()
      })
  }

  asocirajPodatke() {
    this.prijave.map(prijava => {
      this.kursevi.map(kurs => {
        if (prijava.kurs_id == kurs.id)
          return prijava.kurs_naziv = kurs.naziv
      })
      this.korisnici.map(korisnik => {
        if (prijava.korisnik_id == korisnik.id)
          return prijava.korisnik_ime = korisnik.ime
      })
    })
  }
}

angular
  .module('adminApp', [])
  .controller('Admin', ($q, $http) => new Admin($q, $http))
