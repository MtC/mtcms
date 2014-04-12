/**
* Created for AngularJS.
* User: MtC
* Date: 2014-03-29
* Time: 06:17 PM
*/

angular.module('mtcms',['ui.router'/*, 'textAngular'*/])

    .config(function ($stateProvider, $urlRouterProvider, $locationProvider) {
        $locationProvider.hashPrefix('!');
        $stateProvider
            .state('routing', {
                url: '/:route',
                templateUrl: function (stateParams) {
                    return 'api/partials/' + stateParams.route;
                }
            });
    })

    .factory('authInterceptor', function ($rootScope, $q, $window) {
        return {
            request: function (config) {
                config.headers = config.headers || {};
                if ($window.sessionStorage['ice-cream']) {
                    config.headers['X-User'] = $window.sessionStorage['ice-cream'];
                }
                return config;
            },
            response: function (response) {
                if (response.status === 401) {
                    // handle the case where the user is not authenticated
                }
                return response || $q.when(response);
            }
        };
    })

    .config(function ($httpProvider) {
        $httpProvider.interceptors.push('authInterceptor');
    })

    .controller('MainCtrl', ['$scope', '$location', function ($scope, $location) {
        console.log('check');
        $scope.testing = 'kiekeboe';

        $scope.menubar = [
        {
            name: 'feedback',
            link: 'feedback'
        }, {
            name: 'login',
            link: 'login'
        }, {
            name: 'test',
            link: 'test'
        }];

        $scope.sidebar = [{
            name: 'pizza',
            link: 'pizza'
        }, {
            name: 'koerier',
            link: 'koerier'
        }, {
            name: 'brommer',
            link: 'brommer'
        }];

        $scope.menuToggle = function () {
            $scope.menubar.menu = !$scope.menubar.menu;
        };

        $scope.goto = function (link) {
            $location.path(link);
        };
        
        $scope.contextmenu = {
            create: "pipo"
        };

    }])

    .controller('MenubarCtrl', ['$scope', function ($scope) {

    }])

    .controller('LoginCtrl', ['$scope', '$http', '$window', function ($scope, $http, $window) {
        $scope.submit = function () {
            console.log($window.sessionStorage);
            $http.post('/public/api/login', {'login_email': $scope.login_email, 'login_password': $scope.login_password})
                .then(
                    function (response) {
                        $window.sessionStorage['ice-cream'] = response.data['ice-cream'];
                    },
                    function () {
                        alert('oeps');
                    });
        };
    }])

    .controller('DragCtrl', ['$scope', function ($scope) {
        
        $scope.list = function (arr, id) {
            console.info(arr);
            for (i = 0; i < arr.length; i++) {
                if (arr[i].id === id) {
                    arr.splice(i, 1);
                    break;
                }
                arr[i].children = arr[i].children.length ? $scope.list(arr[i].children, id) : arr[i].children;
            }
            return arr;
        };
        
        $scope.listItems = [
            {id: 1, name: "tekst", children: []},
            {id: 2, name: "andere link", children: []},
            {id: 3, name: "pizzabakker", children: []},
            {id: 4, name: "vlaai", children: [
                {id: 6, name: "vliegen", children: []},
                {id: 7, name: "fietsen", children: []}]},
            {id: 5, name: "driehoek", children: []}];
        //$scope.listItems = ["tekst", "andere link", "pizzabakker", {"vlaai": ["vliegen", "fietsen"]}, "driehoek"];
        var rememberItems = JSON.parse(JSON.stringify($scope.listItems));
        
        $scope.remove = function (id) {
            $scope.listItems = $scope.list($scope.listItems, id);
            //console.log(index);
            //console.log(rememberItems);
            //$scope.listItems.splice(index, 1);
            //console.log(rememberItems);
            console.info($scope.listItems);
            
        };
        
        $scope.change = function (index) {
            
        };
        
        $scope.add = function (index) {
            
        };
        
        $scope.reset = function () {
            $scope.listItems = JSON.parse(JSON.stringify(rememberItems));
        };
        
        $scope.typeOf = function (value) {
            return typeof value;
        };
    }]);