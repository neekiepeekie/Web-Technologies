var app = angular.module('productSearchApp', ['ngMaterial', 'ngMessages', 'angular-svg-round-progressbar']);
app.controller('AppController', function ($scope, $http) {
	$scope.keyword = "";
	$scope.category = "";
	$scope.distance = "";
	$scope.location = "current_location";
	$scope.zipcodeCheckPass = false;
	$scope.currentZipcode = "90007";
	$scope.emptyZipcode = true;
	$scope.userZipcode = "";
	var facebookAppId = "277375383184972";
	$scope.totalWishlistPrice = 0.0;

	if ($scope.location == "current_location") {
		$http.get("http://ip-api.com/json")
			.then(function (response) {
				$scope.currentZipcode = response.data.zip;
			});
	}

	$scope.query = function (searchText) {
		return $http.get("http://csci571hw8-nodejs.us-west-2.elasticbeanstalk.com/getZipcodeAutoComplete?searchText=" + searchText)
			.then(function (res) {
				return res.data;
			});
	}

	$scope.reset = function () {
		$scope.keyword = "";
		$scope.category = "";
		$scope.distance = "";
		$scope.location = "current_location";
		$scope.zipcodeCheckPass = false;
		$scope.currentZipcode = "90007";
		$scope.emptyZipcode = true;
		$scope.userZipcode = "";
		$scope.show_detail = false;
		$scope.progress = false;
		$scope.detailprevious = "";
		$scope.table_content = "";
	}

	$scope.selectedItemChange = function (item) {
		if (item)
			$scope.emptyZipcode = false;
		else
			$scope.emptyZipcode = true;
		if (item && /^[0-9]{5}$/.test(item)) {
			$scope.userZipcode = item;
			$scope.zipcodeCheckPass = true;
		} else {
			$scope.zipcodeCheckPass = false
		}
	};

	$scope.searchTextChange = function (text) {
		if (text)
			$scope.emptyZipcode = false;
		else
			$scope.emptyZipcode = true;
		if (text && /^[0-9]{5}$/.test(text)) {
			$scope.userZipcode = text;
			$scope.zipcodeCheckPass = true;
		} else {
			$scope.zipcodeCheckPass = false

		}
	};

	$scope.show_detail = false;
	$scope.detailprevious = "results";
	$scope.progress = false;
	$scope.objects = [];
	$scope.totalItems = 0;
	$scope.pagedItems = [];
	$scope.wishlistContent = []

	var chunks = function (array, size) {
		var results = [];
		while (array.length) {
			results.push(array.splice(0, size));
		}
		return results;
	};

	$scope.wishlist_to_results = function () {
		$scope.detailprevious = "results";
		$scope.show_detail = false;
		$scope.wishlistClicked = false;
	}

	$scope.results_to_wishlist = function () {
		$scope.detailprevious = "wishlist";
		$scope.show_detail = false;
		$scope.wishlistContent = [];
		$.each($scope.favor, function (key, val) {
			if (key != null && val != null)
				$scope.wishlistContent.push(val);
		});
		$scope.wishlistContent = _.sortBy($scope.wishlistContent, 'index');
		$scope.wishlistClicked = true;
	}

	$scope.getProductsList = function () {
		$scope.progress = true;
		var condition = [],
			shipping = [],
			searchZipcode = "",
			maxDistance = "10";
		if ($scope.new)
			condition.push("New");
		if ($scope.distance && /^[0-9]{1,}$/.test($scope.distance))
			maxDistance = $scope.distance;
		else
			maxDistance = 10;
		if ($scope.used)
			condition.push("Used");
		if ($scope.unspecified)
			condition.push("Unspecified");
		if ($scope.localPickup)
			shipping.push("LocalPickupOnly");
		if ($scope.freeShipping)
			shipping.push("FreeShippingOnly");
		if ($scope.location == "current_location")
			searchZipcode = $scope.currentZipcode;
		else
			searchZipcode = $scope.userZipcode;
		$http.get("http://csci571hw8-nodejs.us-west-2.elasticbeanstalk.com/getProductsList?keyword=" + encodeURI($scope.keyword) + "&category=" + $scope.category + "&distance=" + maxDistance + "&condition=" + condition.join(",") + "&shipping=" + shipping.join(",") + "&zipcode=" + searchZipcode)
			.then(function (response) {
				if (response.data.products) {
					$scope.table_content = response.data.products;
					$scope.objects = response.data.products;
					$scope.totalItems = $scope.objects.length;
					$scope.itemsPerPage = 10;
					$scope.pagedItems = chunks(JSON.parse(JSON.stringify($scope.objects)), $scope.itemsPerPage);
					$scope.currentPage = 0;
				} else {
					$scope.table_content = "";
				}
				$scope.table_status = response.data.status;
				$scope.table_statustext = response.data.statusText;
				$scope.detailprevious = "results";

				document.getElementById("result_button").classList.add("btn-dark");
				document.getElementById("wishlist_button").classList.remove("btn-dark");
				$scope.enableDetailsButton = false;
				$scope.recentDetailId = "";
				$scope.enableWishlistDetailsButton = false;
				$scope.recentWishlistDetailId = "";
				productDetailsCallFrom = "results";
				resultProductContent = {};
				wishlistProductContent = {};
				$scope.show_detail = false;
				$scope.progress = false;
			}, function (error) {
				$scope.progress = false;
				$scope.table_status = "failure";
				$scope.table_content = null;
			});

	}

	$scope.getPagedItemsLength = function () {
		var response = [];
		for (var i = 0; i < $scope.pagedItems.length; i++)
			response.push(i);
		return response;
	}

	$scope.prevPage = function () {
		if ($scope.currentPage > 0) {
			$scope.currentPage--;
		}
	};

	$scope.nextPage = function () {
		if ($scope.currentPage < $scope.pagedItems.length - 1) {
			$scope.currentPage++;
		}
	};

	$scope.setPage = function (n) {
		$scope.currentPage = n;
	};

	$scope.favor = {};

	$scope.show_favor = false;


	for (x in localStorage) {
		if (x != null && localStorage.getItem(x) != null) {
			$scope.favor[x] = JSON.parse(localStorage.getItem(x));
			$scope.totalWishlistPrice += parseFloat($scope.favor[x].price.substring(1));
		}
	}
	$scope.add_to_wishlist = function (x, $event) {
		var i = $($event.currentTarget).find("i")[0];
		x = x.toString();
		var prodObj = getProductById(x);
		if (i.innerHTML == "add_shopping_cart") {
			i.innerHTML = "remove_shopping_cart";

			$(i).addClass("yellow-cart");

			localStorage.setItem(x, JSON.stringify(prodObj));
			$scope.totalWishlistPrice += parseFloat(prodObj.price.substring(1));

			$scope.favor[x] = prodObj;
			$scope.wishlistContent.push(prodObj);
			$scope.wishlistContent = _.sortBy($scope.wishlistContent, 'index');

		} else {

			i.innerHTML = "add_shopping_cart";

			$(i).removeClass("yellow-cart");
			$scope.totalWishlistPrice -= parseFloat($scope.favor[x].price.substring(1));
			localStorage.removeItem(x);

			delete $scope.favor[x];
			$scope.wishlistContent = _.reject($scope.wishlistContent, function (obj) {
				return obj.id == x;
			});
			if (productDetailsCallFrom == "wishlist") {
				$scope.enableWishlistDetailsButton = false;
				$scope.recentWishlistDetailId = "";
				wishlistProductContent = {};
			}
		}
	}

	$scope.remove_from_wishlist = function (id) {
		localStorage.removeItem(id);
		$scope.totalWishlistPrice -= parseFloat($scope.favor[id].price.substring(1));
		delete $scope.favor[id];
		$scope.wishlistContent = _.reject($scope.wishlistContent, function (obj) {
			return obj.id == id;
		});
	}

	$scope.showIfInWishList = function (id) {
		return $scope.favor.hasOwnProperty(id.toString());
	}

	$scope.enableDetailsButton = false;
	$scope.recentDetailId = "";
	$scope.enableWishlistDetailsButton = false;
	$scope.recentWishlistDetailId = "";
	productDetailsCallFrom = "results";
	resultProductContent = {};
	wishlistProductContent = {};

	var getFacebookText = function (jsonObj) {
		var facebookStr = "Buy " + jsonObj.Title + " at " + jsonObj.Price + " from link below.";
        var facebookApiText = "https://www.facebook.com/dialog/share?app_id="+facebookAppId+"&display=popup&href="+encodeURIComponent(jsonObj.ItemURL)+"&quote="+encodeURIComponent(facebookStr);
		return facebookApiText;
	}
	var productDetailsFromEbay = function (itemId, itemName, requestCallFrom) {
		$scope.progress = true;
		$scope.productContent = {
			"name": itemName,
			"id": itemId
		};
		$http.get("http://csci571hw8-nodejs.us-west-2.elasticbeanstalk.com/getProductDetails?itemId=" + itemId)
			.then(function (response) {
				$scope.product_status = response.data.status.toLowerCase();
				$scope.productContent["product"] = response.data.item;
				$scope.show_detail = true;
				$scope.facebook_text = getFacebookText(response.data.item);
				productDetailsCallFrom = requestCallFrom;
				if (requestCallFrom == "results") {
					$scope.enableDetailsButton = true;
					$scope.recentDetailId = itemId;
					resultProductContent = $scope.productContent;
				} else if (requestCallFrom == "wishlist") {
					$scope.enableWishlistDetailsButton = true;
					$scope.recentWishlistDetailId = itemId;
					wishlistProductContent = $scope.productContent;
				}
				$("#pills-details-tab a.nav-link").removeClass("active");
				$("#pills-details-tab #pills-product-tab").addClass("active");
				$("#pills-tabContent .tab-pane").removeClass("active");
				$("#pills-tabContent #pills-product").addClass("show active");
				$scope.progress = false;

			}, function (error) {
				$scope.progress = false;
			});

	}

	$scope.getProductDetails = function (itemId, itemName) {
		productDetailsFromEbay(itemId, itemName, "results");
	}

	$scope.getWishlistProductDetails = function (itemId, itemName) {
		productDetailsFromEbay(itemId, itemName, "wishlist");
	}

	$scope.recent_detail = function () {
		$scope.show_detail = true;
		$scope.productContent = resultProductContent;
		$scope.facebook_text = getFacebookText($scope.productContent.product);
	}

	$scope.recent_wishlist_detail = function () {
		$scope.show_detail = true;
		$scope.productContent = wishlistProductContent;
		$scope.facebook_text = getFacebookText($scope.productContent.product);
	}

	$scope.list_function = function () {
		$scope.show_detail = false;
	}

	$scope.getProductPhotos = function () {
		if (!$scope.productContent.photos) {
			$http.get("http://csci571hw8-nodejs.us-west-2.elasticbeanstalk.com/getProductPhotos?product=" + encodeURI($scope.productContent.name))
				.then(function (response) {
					if (response.data.error) {
						$scope.productContent["photos"] = {}
						$scope.productContent["photos"].data = [];
						$scope.productContent["photos"].status = "failure";
					} else {
						$scope.productContent["photos"] = {}
						$scope.productContent["photos"].data = response.data.items;
						$scope.productContent["photos"].status = "success";
						if (productDetailsCallFrom == "results") {
							resultProductContent = $scope.productContent;
						} else {
							wishlistProductContent = $scope.productContent;
						}
					}
				}, function (error) {});
		}
	}

	var getProductById = function (id) {
		return _.findWhere($scope.objects, {
			id: id
		});
	}

	$scope.getShippingDetails = function (id) {
		if (!$scope.productContent.shipping) {
			$scope.productContent["shipping"] = {}
			var productObj = getProductById(id);
			$scope.productContent["shipping"].data = productObj.shippingInfo;
			$scope.productContent["shipping"].status = "success";
			if (productDetailsCallFrom == "results") {
				resultProductContent = $scope.productContent;
			} else {
				wishlistProductContent = $scope.productContent;
			}
		}
	}

	var getFeedbackRatingStar = function (ratingStar) {
		var result = {
			color: 'gray',
			icon: 'star_border'
		};
		switch (ratingStar) {
			case "Yellow":
				result.color = 'yellow';
				result.icon = 'star_border';
				break;
			case "Blue":
				result.color = 'blue';
				result.icon = 'star_border';
				break;
			case "Turquoise":
				result.color = 'turquoise';
				result.icon = 'star_border';
				break;
			case "Purple":
				result.color = 'purple';
				result.icon = 'star_border';
				break;
			case "Red":
				result.color = 'red';
				result.icon = 'star_border';
				break;
			case "Green":
				result.color = 'green';
				result.icon = 'stars';
				break;
			case "YellowShooting":
				result.color = 'yellow';
				result.icon = 'stars';
				break;
			case "TurquoiseShooting":
				result.color = 'turquoise';
				result.icon = 'stars';
				break;
			case "PurpleShooting":
				result.color = 'purple';
				result.icon = 'stars';
				break;
			case "RedShooting":
				result.color = 'red';
				result.icon = 'stars';
				break;
			case "GreenShooting":
				result.color = 'green';
				result.icon = 'stars';
				break;
			case "SilverShooting":
				result.color = 'silver';
				result.icon = 'stars';
				break;
			case "None":
			default:
				result.color = 'gray';
				result.icon = 'star_border';
		}
		return result;
	}

	$scope.getSellerDetails = function (id) {
		if (!$scope.productContent.seller) {
			$scope.productContent["seller"] = {}
			var productObj = getProductById(id);
			$scope.productContent["seller"].data = productObj.sellerInfo;
			$scope.productContent["seller"].data["feedbackRatingStar"] = getFeedbackRatingStar(productObj.sellerInfo.feedbackRatingStar[0]);
			$scope.productContent["seller"].status = "success";
			if (productDetailsCallFrom == "results") {
				resultProductContent = $scope.productContent;
			} else {
				wishlistProductContent = $scope.productContent;
			}
		}
	}

	$scope.getSimilarProducts = function (id) {
		if (!$scope.productContent.similar) {
			$http.get("http://csci571hw8-nodejs.us-west-2.elasticbeanstalk.com/getSimilarProducts?itemId=" + $scope.productContent.id)
				.then(function (response) {
					$scope.productContent["similar"] = {}
					$scope.productContent["similar"].data = response.data.output;
					$scope.productContent["similar"].status = response.data.status;
					if (response.data.output.length == 0 || response.data.status != "success") {
						$scope.productContent["similar"].status = "failure";
					}
					$scope.productContent["similar"].pagedItems = chunks(JSON.parse(JSON.stringify(response.data.output)), 5);
					if (productDetailsCallFrom == "results") {
						resultProductContent = $scope.productContent;
					} else {
						wishlistProductContent = $scope.productContent;
					}
				}, function (error) {});
		}
	}

	$scope.fetchMoreSimilarItems = function (showMore) {
		if (showMore) {
			$scope.productContent.similar.pagedItems = chunks(JSON.parse(JSON.stringify($scope.productContent.similar.data)), 20);
		} else {
			$scope.productContent.similar.pagedItems = chunks(JSON.parse(JSON.stringify($scope.productContent.similar.data)), 5);
		}
	}

	$scope.similarOrderBy = "";
	$scope.similarOrder = "false";
	$scope.propertyName = null;
	$scope.reverse = false;

	$scope.updateSimilarItems = function () {

		if ($scope.similarOrderBy == "") {
			$scope.propertyName = null;
			$scope.reverse = false;
		} else {
			$scope.propertyName = $scope.similarOrderBy;
			$scope.reverse = $scope.similarOrder == "true";
		}
	}


});