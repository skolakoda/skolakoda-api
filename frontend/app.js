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

angular
  .module('adminApp', [])
  .controller('Korisnici', $http => new Korisnici($http))
