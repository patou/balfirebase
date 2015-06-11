var app = angular.module("balApp", ["firebase", 'a8m.fuzzy']);
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


app.controller("InvitesCtrl", ["$scope", "$http", "ref", "invites",
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
	
	$scope.updateInvites = function() {
		$http.get('http://www.baldesparisiennes.com/billets/export.php').success(function(result) {
			ref.set(result);
		})
		.error(function(error) {
			$scope.error(error);
		});
	};
  }
]);