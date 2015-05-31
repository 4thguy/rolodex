'use strict'; 

var ngRolodex = angular.module('ngRolodex', [
	]);

var api = 'client/api/v1/'

ngRolodex.controller('ngRolodexLoginController', function ($scope, $http, $timeout) {
	$scope.loggedIn = false;

	var checkLogIn = {
		run: function() {
			$http.get(api+'user/loggedIn')
			.success(function(data, status, headers, config) {
				$scope.loggedIn = true;
				$timeout(checkLogIn.run, checkLogIn.data.interval);
			})
			.error(function(data, status, headers, config) {
				$scope.loggedIn = false;
				$timeout(checkLogIn.run, checkLogIn.data.interval);
			})
		},
		data: {
			interval: 5000
		}
	}; checkLogIn.run();

	$scope.$watch('loggedIn', function() {
		if ($scope.loggedIn) {
			$('#login-modal').modal('hide');
		} else {
			$('#login-modal').modal('show');
		}
	});

});