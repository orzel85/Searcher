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

myApp.controller('mainController',  function($scope, $location){
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
    $scope.disabled = true;
    $scope.hideResultsTable = true;
    $scope.loadingImage = true;
    $scope.showSortArrow = 'order_' + $scope.orderField + '_' + (($scope.orderReverse) ? 'desc' : 'asc');
    $scope.sizeFilter = 0;
    $scope.seedsFilter = 0;
    $scope.peersFilter = 0;
    
    console.log($scope.showSortArrow);
    var link = '/api/lists.json?page='+ $scope.page +'&query=' + encodeURI($routeParams.queryParam);
    $http.get(link).success(function(data){
        $scope.list.push.apply($scope.list, data.collection);
        $scope.disabled = false;
        $scope.hideResultsTable = false;
        $scope.loadingImage = false;
    });

    $scope.getNextPage = function() {
        $scope.page++;
        var link = '/api/lists.json?page='+ $scope.page +'&query=' + encodeURI($routeParams.queryParam);
        $scope.disabled = true;
        $scope.loadingImage = true;
        $http.get(link).success(function(data){
            $scope.list.push.apply($scope.list, data.collection);
            $scope.disabled = false;
            $scope.loadingImage = false;
        });
    }
    
    $scope.newSearch = function() {
        $scope.page = 1;
        $scope.list = [];
        var link = '/api/lists.json?page='+ $scope.page +'&query=' + encodeURI($routeParams.queryParam);
        $http.get(link).success(function(data){
            $scope.list.push.apply($scope.list, data.collection);
        });
    }
    
    $scope.submit = function() {
        $location.path('/searchResults/' + $scope.query);
    }
    
    $scope.order = function(fieldName) {
        $scope.orderReverse = ($scope.orderField === fieldName) ? !$scope.orderReverse : true;
        $scope.orderField = fieldName;
        var orderType = ($scope.orderReverse) ? 'desc' : 'asc';
        $scope.showSortArrow = 'order_' + $scope.orderField + '_' + orderType;
        console.log($scope.showSortArrow);
    }
    
    $scope.filterSeeds = function(item){
        return item.seeds >= $scope.seedsFilter ;
    }
    
    $scope.filterPeers = function(item){
        return item.peers >= $scope.peersFilter ;
    }
    
    $scope.filterSize = function(item){
        return item.size >= $scope.sizeFilter ;
    }
    
});