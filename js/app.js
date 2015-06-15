var app = angular.module("balApp", ["firebase", 'a8m.fuzzy']);
app.run(['$rootScope',function($rootScope) {
	$rootScope.error = function (error) {
		alert(error);
	}
}]);
app.factory("ref", [function() {
    // create a reference to the Firebase database where we will store our data
    var ref = new Firebase("https://vivid-torch-943.firebaseio.com");
    return ref;
  }
]);
app.factory("invites", ["ref", "$firebaseArray",
  function(ref, $firebaseArray) {
    // create a reference to the Firebase database where we will store our data
    return $firebaseArray(ref);
  }
]);
app.controller("DashboardCtrl", ["$scope", "invites",
  function($scope, invites) {
    $scope.invites = invites;
  }
]);

app.controller("InvitesCtrl", ["$scope", "$http", "$timeout", "ref", "invites",
  function($scope, $http, $timeout, ref, invites) {
    $scope.invites = invites;
	$scope.orderBy = 'prenom';
	$scope.validInvite = false;
	
	$scope.valider = function(invite) {
		$http.get('http://www.baldesparisiennes.com/billets/valid.php?inviteId='+invite.id).success(function(result) {
			$scope.validInvite = invite;
			$timeout(function() {
				$scope.validInvite = false;
			}, 3000);
		})
		.error(function(error) {
			$scope.error(error);
		});
		ref.child(''+invite.id).child('valider').set("true");
	};
  }
]);
app.controller("HeaderCtrl", ["$scope", "$http", "ref",
  function($scope, $http, ref, invites) {
	$scope.updateInvites = function() {
		$http.get('http://www.baldesparisiennes.com/billets/export.php'+(confirm("Réinitialiser les invités ? /!\\ Cette action est irreversible")?'?reset=true':'')).success(function(result) {
			ref.set(result);
		})
		.error(function(error) {
			$scope.error(error);
		});
	};
  }
]);
app.controller("ClientsCtrl", ["$scope", "$http", "ref", "invites",
  function($scope, $http, ref, invites) {
    $scope.invites = invites;
	
	$scope.valider = function(invite) {
		$http.get('http://www.baldesparisiennes.com/billets/valid.php?inviteId='+invite.id).success(function(result) {
			alert('invité validé');
		})
		.error(function(error) {
			$scope.error(error);
		});
		ref.child(''+invite.id).child('valider').set("true");
	};
  }
]);