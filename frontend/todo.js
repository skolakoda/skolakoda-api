function TodoListController($http) {

  $http({
    method: 'GET',
    url: 'https://skolakoda-api.herokuapp.com/korisnici'
  }).then(function (response) {
    console.log(response)
  }, function (response) {
    // called asynchronously if an error occurs
  });

  this.todos = [
    {
      text: 'learn AngularJS',
      done: true
    },
    {
      text: 'build an AngularJS app',
      done: false
    }]

}

angular
  .module('todoApp', [])
  .controller('TodoListController', TodoListController)
