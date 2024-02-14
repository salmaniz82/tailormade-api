<?php

namespace App;

use \Framework\Route;

$route = new Route();

$route->get('/', 'swatchesCtrl@index');

$route->get('/swatchemeta', 'swatchesCtrl@swatchMeta');

$route->get('/swatches?', 'swatchesCtrl@listSwatches');

$route->get('/swatch/{id}', 'swatchesCtrl@getSingle');

$route->delete('/swatches/{id}', 'swatchesCtrl@deleteSwatches');

$route->get('/devtest', 'swatchesCtrl@test');

/* STORE SINGLE  */

$route->post('/swatches', 'swatchesCtrl@store');

/* BATCH INSERT */
$route->post('/swatche-batchStore', 'swatchesCtrl@batchStore');

$route->put('/swatches/{id}', 'swatchesCtrl@update');

$route->get('/dashboard/{param}', 'dashboardCtrl@dasboardLanding');

$route->get('/dashboard?', 'dashboardCtrl@dasboardLanding');

$route->get('/dashboard/editswatch/{param}', 'dashboardCtrl@dasboardLanding');


$route->get('/stocks', 'stockCtrl@listCollections');

$route->post('/stocks', 'stockCtrl@save');

$route->delete('/stocks/{id}', 'stockCtrl@delete');

$route->put('/stocks/{id}', 'stockCtrl@update');

$route->get('/login', 'userCtrl@showLogin');

$route->post('/login', 'userCtrl@doLogin');

$route->get('/logout', 'userCtrl@logout');

$route->get('/filters?', 'swatchesCtrl@updateFilters');


/*
DISABLED ROUTES
$route->post('/swatch', 'swatchesCtrl@store');
$route->post('/request-swatch', 'swatchesCtrl@processRequestSwatches');
$route->get('/testmail', 'swatchesCtrl@testEmail');
$route->get('/template', 'swatchesCtrl@loadSwatchTemplate');

*/

$route->otherwise(function () {

	$data["message"] = "request not found";
	return \Framework\View::responseJson($data, 404);
});
