class Korisnici {
  constructor($http) {
    $http
      .get('https://skolakoda-api.herokuapp.com/korisnici')
      .then(
        response => this.korisnici = response.data,
        error => console.log(error)
      )
  }
}

class Kursevi {
  constructor($http) {
    $http
      .get('https://skolakoda-api.herokuapp.com/kursevi')
      .then(
        response => this.kursevi = response.data,
        error => console.log(error)
      )
  }
}

class Prijave {
  constructor($http) {
    $http
      .get('https://skolakoda-api.herokuapp.com/prijave')
      .then(
        response => this.prijave = response.data,
        error => console.log(error)
      )
  }
}

angular
  .module('adminApp', [])
  .controller('Korisnici', $http => new Korisnici($http))
  .controller('Kursevi', $http => new Kursevi($http))
  .controller('Prijave', $http => new Prijave($http))  
