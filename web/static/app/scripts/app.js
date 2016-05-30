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
    $scope.submit = function() {
        $location.path('/searchResults/' + $scope.query);
    }
    
});

myApp.controller('formController',  function($scope, $log, $http, $routeParams, $location){
    $scope.page = 1;
    $scope.list = [];
    $scope.orderField = 'seeds';
    $scope.orderReverse = true;
    $scope.query = $routeParams.queryParam;
    $scope.messageEmptyQuery = false;
    $scope.providerCounter = 0;
    $scope.totalProviderCount = 0;
    $scope.searchingInProgress = true;
    $scope.searchingCompletedHide = true;
    if( ($scope.query === 'undefined') || ($scope.query === '')) {
        $scope.query = '';
        $scope.hideResultsTable = true;
        $scope.messageEmptyQuery = true;
    }else{
        $scope.disabled = true;
        $scope.messageEmptyQuery = false;
        $scope.hideResultsTable = true;
        $scope.loadingImage = true;
        $scope.showSortArrow = 'order_' + $scope.orderField + '_' + (($scope.orderReverse) ? 'desc' : 'asc');
        $scope.sizeFilter = 0;
        $scope.seedsFilter = 0;
        $scope.peersFilter = 0;

        var link = '/api/lists.json?page='+ $scope.page +'&query=' + encodeURI($routeParams.queryParam);
        $http.get(link).success(function(data){
            $scope.searchingInProgress = false;
            $scope.totalProviderCount = data.length;
            $scope.providerCounter = 0;
            for(var i = 0; i < data.length; i++) {
                $scope.searchSingleProvider(data[i]);
            }
        });
    }
    
    $scope.searchSingleProvider = function(link) {
        $http.get(link)
                .success(function(data){
                    $scope.list.push.apply($scope.list, data);
                    $scope.providerCounter++;
                    $scope.hideResultsTable = false;
                    $scope.loadingImage = false;
                    if($scope.providerCounter === $scope.totalProviderCount) {
                        $scope.disabled = false;
                        $scope.searchingCompletedHide = false;
                        $scope.searchingInProgress = true;
                    }
                    else{
                        $scope.searchingCompletedHide = true;
                        
                    }
                })
                .error(function(data){
                    $scope.providerCounter++;
                    if($scope.providerCounter === $scope.totalProviderCount) {
                        $scope.disabled = false;
                        $scope.searchingCompletedHide = false;
                        $scope.searchingInProgress = true;
                    }
                    else{
                        $scope.searchingCompletedHide = false;
                        
                    }
                })
        ;
    };
    
    $scope.getNextPage = function() {
        $scope.page++;
        var link = '/api/lists.json?page='+ $scope.page +'&query=' + encodeURI($routeParams.queryParam);
        $scope.disabled = true;
        $scope.loadingImage = true;
        $scope.searchingCompletedHide = true;
        $http.get(link).success(function(data){
            $scope.searchingInProgress = false;
            $scope.totalProviderCount = data.length;
            alert($scope.totalProviderCount);
            $scope.providerCounter = 0;
            for(var i = 0; i < data.length; i++) {
                $scope.searchSingleProvider(data[i]);
            }
        });
    }
    
    $scope.newSearch = function() {
        $scope.page = 1;
        $scope.list = [];
    
        var link = '/api/lists.json?page='+ $scope.page +'&query=' + encodeURI($routeParams.queryParam);
        this.loadingImage = true;
        $scope.messageEmptyQuery = false;
        $http.get(link).success(function(data){
            $scope.searchingInProgress = false;
            $scope.totalProviderCount = data.length;
            $scope.providerCounter = 0;
            for(var i = 0; i < data.length; i++) {
                $scope.searchSingleProvider(data[i]);
            }
        });
    };
    
    $scope.submit = function() {
        if( ($scope.query === 'undefined') || ($scope.query === '')) {
           $scope.messageEmptyQuery = true;
        }else{
            $location.path('/searchResults/' + $scope.query);
            this.newSearch();
        }
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