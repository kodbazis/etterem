<?php

require './router.php';
require './slugifier.php';
require './dishes.php';
require './dishTypes.php';
require './auth.php';

$method = $_SERVER["REQUEST_METHOD"];
$parsed = parse_url($_SERVER['REQUEST_URI']);
$path = $parsed['path'];

// Útvonalak regisztrálása
$routes = [
    // [method, útvonal, handlerFunction],
    ['GET', '/', 'homeHandler'],
    ['GET', '/admin/etel-szerkesztese/{keresoBaratNev}', 'dishEditHandler'],
    ['GET', '/admin', 'adminDashboardHandler'],
    ['POST', '/login', 'loginHandler'],
    ['POST', '/update-dish/{dishId}', 'updateDishHandler'],
    ['GET', '/admin/uj-etel-letrehozasa', 'dishCreatePageHandler'],
    ['POST', '/create-dish', 'createDishHandler'],
    ['POST', '/delete-dish/{dishId}', 'deleteDishHandler'],
    ['GET', '/admin/etel-tipusok', 'adminDishTypeHandler'],
    ['POST', '/create-dish-type', 'createDishTypeHandler'],
    ['POST', '/logout', 'logoutHandler'],
];

// Útvonalválasztó inicializálása
$dispatch = registerRoutes($routes);
$matchedRoute = $dispatch($method, $path);
$handlerFunction = $matchedRoute['handler'];
$handlerFunction($matchedRoute['vars']);

function adminDashboardHandler()
{
    if (!isLoggedIn()) {
        echo render('wrapper.phtml', [
            'content' => render('login.phtml')
        ]);
        return;
    }

    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM dishes ORDER BY id DESC");
    $stmt->execute();
    $dishes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo render('admin-wrapper.phtml', [
        'content' => render('dish-list.phtml', [
            'dishes' => $dishes
        ])
    ]);
}

function homeHandler()
{
    $pdo = getConnection();
    $dishTypes = getAllDishTypes($pdo);

    foreach ($dishTypes as $index => $dishType) {
        $stmt = $pdo->prepare("SELECT * FROM dishes WHERE isActive = 1 AND dishTypeId = ?");
        $stmt->execute([$dishType['id']]);
        $dishes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $dishTypes[$index]['dishes'] = $dishes;
    }

    echo render("wrapper.phtml", [
        'content' => render('public-menu.phtml', [
            'dishTypesWithDishes' => $dishTypes
        ])
    ]);
}

function notFoundHandler()
{
    http_response_code(404);
    echo render('wrapper.phtml', [
        'content' => render('404.phtml')
    ]);
}

function render($path, $params = [])
{
    ob_start();
    require __DIR__ . '/views/' . $path;
    return ob_get_clean();
}

function getConnection()
{
    return new PDO(
        'mysql:host=' . $_SERVER['DB_HOST'] . ';dbname=' . $_SERVER['DB_NAME'],
        $_SERVER['DB_USER'],
        $_SERVER['DB_PASSWORD']
    );
}
