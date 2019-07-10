var chaosServerApp = angular.module('chaosServer',[]);

chaosServerApp.controller('ModeController', ['$scope', '$http', '$timeout', function($scope, $http, $timeout) {
	
	var refreshTime = 1000; // 1 sec
	var timer;
	
	$scope.modesData = {};
	$scope.results = false;
	$scope.checkedMode = '';
	
	$scope.setMode = function(selectedMode) {
		
		$http({
			method: 'POST',
			url: '/application/set-mode',
			data: {mode : selectedMode}
		}).then(function(res) {
			if (res.data.success == false) {
				alert('Setting mode failed: ' + res.data.error);
			}
		});

	};
	
	$scope.getMode = function() {
		
		$http({
			method: 'GET',
			url: '/application/get-mode'
		}).then(function(res) {
			if (res.data) {
				$scope.checkedMode = res.data.mode;
			}
		});

	};
	
	$scope.getModes = function() {
		$http({
			method: 'GET',
			url: '/application/get-modes'
		}).then(function(res) {
			if (res.data) {
				$scope.modesData = res.data;
			} else {
				alert('Getting modes failed: ' + res.data.error);
			}
		});
		
	};
	
	$scope.getResults = function() {
		$http({
			method: 'GET',
			url: '/application/get-results'
		}).then (function(res) {
			if (res.data) {
				$scope.results = res.data;
			} else {
				$scope.results = false;
			}
		}).finally(function() {
			nextLoad();
		});
	};
	
	var cancelNextLoad = function() {
		$timeout.cancel(timer);
	};
	  
	var nextLoad = function() {
	    //Always make sure the last timeout is cleared before starting a new one
	    cancelNextLoad();
	    timer = $timeout($scope.getResults, refreshTime);
    };
	 
	$scope.$on('$destroy', function() {
	    cancelNextLoad();
	});
	
	$scope.getModes();
	// get selected mode
	$scope.getMode();
	// start polling
	$scope.getResults();
	
}]);