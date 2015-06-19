var app = angular.module("balApp", ["firebase", 'a8m.fuzzy']);
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
app.run(['$rootScope',function($rootScope) {
	$rootScope.error = function (error) {
		swal("Erreur!", error, "error");
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
app.factory("clientsFactory", ["$http", 
  function($http) {
	var clients = [];
    $http.get('http://www.baldesparisiennes.com/admin/clients.php').success(function(result) {
		angular.forEach(result, function(value, key) {
		  this.push(value);
		}, clients);
	})
	.error(function(error) {
			alert(error);
	});
	return {
		clients: function () {
			return clients;
		},
		count: function() {
			return clients.length;
		},
		getClient: function(id) {
			return $http.get('http://www.baldesparisiennes.com/admin/client.php?idClient='+id)
		}
	};
  }
]);
app.controller("DashboardCtrl", ["$scope", "invites", "clientsFactory",
  function($scope, invites, clientsFactory) {
    $scope.invites = invites;
	$scope.clients = clientsFactory;
  }
]);

app.controller("InvitesCtrl", ["$scope", "$http", "$timeout", "ref", "invites",
  function($scope, $http, $timeout, ref, invites) {
    $scope.invites = invites;
	$scope.orderBy = 'prenom';
	$scope.validInvite = false;
	
	$scope.valider = function(invite) {
		$http.get('http://www.baldesparisiennes.com/admin/valid.php?inviteId='+invite.id).success(function(result) {
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
	
	$scope.cancelValidation = function(invite) {
		swal({
		  title: "Etes-vous sûr ?",
		  text: "Vous voulez vraiment annuler l'entrée au bal de "+invite.prenom+" "+invite.nom,
		  type: "warning",
		  showCancelButton: true,
		  confirmButtonClass: "btn-warning",
		  confirmButtonText: "Oui",
		  closeOnConfirm: false
		},
		function(){
			$http.get('http://www.baldesparisiennes.com/admin/valid.php?cancel=true&inviteId='+invite.id).success(function(result) {
				swal("Fait!", "L'entrée au bal de "+invite.prenom+" "+invite.nom+" est bien annulé", "success");
			})
			.error(function(error) {
				$scope.error(error);
			});
			ref.child(''+invite.id).child('valider').set("false");
		});
		
	}
	$scope.openAddInviteModal = function() {
		$('#addInvite').modal();
	}
	$scope.addInvite = function(invite) {
		invite['idCommande'] = 1;
		$http.post('http://www.baldesparisiennes.com/admin/invite.php', invite).success(function(result) {
				$scope.validInvites = validInvites;
				$timeout(function() {
					$scope.validInvites = false;
				}, 3000);
				$('#addInvite').modal('hide');
			})
			.error(function(error) {
				$scope.error(error);
			});
		
	}
  }
]);
app.controller("HeaderCtrl", ["$scope", "$http", "ref",
  function($scope, $http, ref) {
	$scope.updateInvites = function() {
		$http.get('http://www.baldesparisiennes.com/admin/export.php'+(confirm("Réinitialiser les invités ? /!\\ Cette action est irreversible")?'?reset=true':'')).success(function(result) {
			ref.set(result);
		})
		.error(function(error) {
			$scope.error(error);
		});
	};
  }
]);
app.controller("ClientsCtrl", ["$scope", "$http", "$timeout", "ref", "clientsFactory",
  function($scope, $http, $timeout, ref, clientsFactory) {
    $scope.clients = clientsFactory.clients();
	$scope.infoClient = function(id) {
		clientsFactory.getClient(id).success(function(result) {
			$scope.client = result;
			$('#infoClient').modal();
		});
	}
	$scope.validerInvites = function(invites) {
		var ids = [];
		var validInvites = [];
		angular.forEach(invites, function(invite) {
			if (invite.valider != 'true' && invite.check) {
				this.push(invite.id);
				validInvites.push(invite.prenom + ' '+invite.nom);
				ref.child(''+invite.id).child('valider').set("true");
			}
		}, ids);
		if (ids.length > 0) {
			console.log(ids);
			$http.get('http://www.baldesparisiennes.com/admin/valid.php?inviteId='+ids.join(',')).success(function(result) {
				$scope.validInvites = validInvites;
				$timeout(function() {
					$scope.validInvites = false;
				}, 3000);
			})
			.error(function(error) {
				$scope.error(error);
			});
		}
		$('#infoClient').modal('hide');
	};
	
  }
]);