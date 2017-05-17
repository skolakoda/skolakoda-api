class Admin {
  constructor($http) {
    $http
      .get('https://skolakoda-api.herokuapp.com/korisnici')
      .then(response => this.korisnici = response.data)
    $http
      .get('https://skolakoda-api.herokuapp.com/kursevi')
      .then(response => this.kursevi = response.data)
    $http
      .get('https://skolakoda-api.herokuapp.com/prijave')
      .then(response => this.prijave = response.data)
  }
}

angular
  .module('adminApp', [])
  .controller('Admin', $http => new Admin($http))
