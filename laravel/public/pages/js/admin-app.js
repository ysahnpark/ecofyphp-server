// When second argument (array) is provided then this becomes a definition,
// otherwise it is a loading
var pathBase = '/pages';
angular.module('adminApp', ['ngRoute', 'ngCookies', 'ngMaterial', 'ecofy-core', 'ecofy-account'])
.config(['$routeProvider', function($routeProvider) {

    $routeProvider.when('/', {
      //template: '<h5>This is the default route</h5>'
      controller: 'AccountController as accountCtrl',
      templateUrl: pathBase + '/partials/account_list.html'
    })
    .when('/account/:accountId', {
      controller: 'AccountController as accountCtrl',
      templateUrl: pathBase + '/partials/account_details.html'
    })
    .when('/account/:accountId/form', {
      controller: 'AccountController as accountCtrl',
      templateUrl: pathBase + '/partials/account_form.html'
    })
    .when('/account/:accountId/relations', {
      controller: 'RelationsController as relationsCtrl',
      templateUrl: pathBase + '/partials/account_relations.html'
    })
    .when('/import', {
      controller: 'ImportController as importCtrl',
      templateUrl: pathBase + '/partials/account_import.html'
    })
    .otherwise({redirectTo: '/'});
  }])

/**
 * For the Angular matrial design
 */
.config(function($mdThemingProvider) {
  $mdThemingProvider.theme('default')
    .primaryPalette('green')
    .accentPalette('orange');
})

/**
 * Frame controller than handles the account in session
 */
.controller('FrameController', ['$window', 'AuthService'
    , function($window, AuthService)
{
  var self = this;

  AuthService.fetchMyAccount()
  .then(function(account) {
    self.session = account;
  })
  .catch(function(error) {

  });

  this.showProfile = function() {
    AuthService.fetchMyAccount()
    .then(function(account) {
      $window.location.href = pathBase + '/admin.html#/account/' + account.uuid + '/form';
    })
    .catch(function(error) {
      alert(JSON.stringify(error, null, 2));
    });
  }

  this.signout = function() {
    AuthService.signout()
    .then(function(data) {
      $window.location.href = pathBase + '/main.html#/login';
    })
    .catch(function(error) {
      alert(JSON.stringify(error, null, 2));
    });
  }

}]);
