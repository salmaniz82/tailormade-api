<?php

namespace App;

use \Framework\Route;

$route = new Route();
$route->get('/', 'swatchesCtrl@index');

$route->get('/swatches?', 'swatchesCtrl@listSwatches');

$route->get('/dashboard/{param}', 'dashboardCtrl@dasboardLanding');

$route->get('/dashboard?', 'dashboardCtrl@dasboardLanding');

/*make sure this is not running twice*/

$route->get('/logout', 'userCtrl@logout');


$route->get('/login', function() {

	echo "your login procedure here...";

});



$route->get('/mock', function() {

	$mockType = false;

	if($mockType) {

		$data["message"] = "this is for the success";
		$statusCode = 200;
	}
	else {

		$data["message"] = "Backend error message";
		$statusCode = 406;

	}
	


	return \Framework\View::responseJson($data, $statusCode);


});



/*
DISABLED ROUTES
$route->post('/swatch', 'swatchesCtrl@store');
$route->post('/request-swatch', 'swatchesCtrl@processRequestSwatches');
$route->get('/testmail', 'swatchesCtrl@testEmail');
$route->get('/template', 'swatchesCtrl@loadSwatchTemplate');
$route->get('/filters', 'swatchesCtrl@testBuildFilter');
*/

$route->otherwise(function () {

	$data["message"] = "request not found";
	return \Framework\View::responseJson($data, 404);
});
