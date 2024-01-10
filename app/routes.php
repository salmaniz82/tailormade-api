<?php

namespace App;

use \Framework\Route;


$route = new Route();
$route->get('/', 'swatchesCtrl@index');
$route->get('/swatches?', 'swatchesCtrl@listSwatches');

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
