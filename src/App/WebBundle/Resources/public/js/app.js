'use strict';

var origin = document.location.origin;
var folder = document.location.pathname.split('/')[1];

var path = origin +  "/bundles/appweb/templates";

var myApp = angular.module('myApp', ['ngRoute']);

myApp.config(function($routeProvider){
    $routeProvider

            // route for the contact page
            .when('/index', {
                templateUrl : path + '/index.html',
                controller  : 'mainController'
            })
            
            .when('/searchResults/:queryParam', {
                templateUrl : path + '/searchResults.html',
                controller  : 'formController'
            })
            
            .otherwise({
                redirectTo: '/index'
            })
            ;
});

myApp.controller('mainController',  function($scope, $log, $http, $routeParams,$location){
    console.log("mainController");
    
    $scope.submit = function() {
        $location.path('/searchResults/' + $scope.query);
    }
    
});

myApp.controller('formController',  function($scope, $log, $http, $routeParams, $location){
    console.log("formController");
    $scope.page = 1;
    $scope.list = [];
    $scope.orderField = 'seeds';
    $scope.orderReverse = true;
    $scope.query = $routeParams.queryParam;
    console.log($routeParams);
    
    var link = '/api/lists.json?page='+ $scope.page +'&query=' + encodeURI($routeParams.queryParam);
    console.log(link);
    console.log('sending...');
    $http.get(link).success(function(data){
        console.log(data.collection);
        $scope.list.push.apply($scope.list, data.collection);
        console.log($scope.list);
    });

    $scope.getNextPage = function() {
        console.log($scope.page);
        $scope.page++;
        console.log($scope.page);
        var link = '/api/lists.json?page='+ $scope.page +'&query=' + encodeURI($routeParams.queryParam);
        console.log(link);
        console.log('sending...');
        $http.get(link).success(function(data){
            console.log(data.collection);
            $scope.list.push.apply($scope.list, data.collection);
            console.log($scope.list);
        });
    }
    
    $scope.newSearch = function() {
        console.log('new search');
        $scope.page = 1;
        $scope.list = [];
        var link = '/api/lists.json?page='+ $scope.page +'&query=' + encodeURI($routeParams.queryParam);
        $http.get(link).success(function(data){
            console.log(data.collection);
            $scope.list.push.apply($scope.list, data.collection);
            console.log($scope.list);
        });
    }
    
    $scope.submit = function() {
        console.log('submit');
        $location.path('/searchResults/' + $scope.query);
    }
    
});