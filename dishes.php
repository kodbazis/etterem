<?php

function deleteDishHandler($urlParameterek)
{
    redirectToLoginPageIfNotLoggedIn();
    $pdo = getConnection();
    $stmt = $pdo->prepare("DELETE FROM `dishes` WHERE id = ?");
    $stmt->execute([
        $urlParameterek['dishId']
    ]);
    header('Location: /admin');
}

function createDishHandler()
{
    $pdo = getConnection();
    $stmt = $pdo->prepare(
        "INSERT INTO `dishes` 
        (`name`, `slug`, `description`, `price`, `isActive`, `dishTypeId`) 
        VALUES 
        (:nev, :slug, :leiras, :ar, :aktiv, :dishTypeId);"
    );

    $stmt->execute([
        "nev" => $_POST['name'],
        "slug" => slugify($_POST['name']),
        "leiras" => $_POST['description'],
        "ar" =>  $_POST['price'],
        "aktiv" =>  (int)isset($_POST['isActive']),
        "dishTypeId" =>  $_POST['dishTypeId'],
    ]);
    header('Location: /admin');
}

function dishCreatePageHandler()
{
    redirectToLoginPageIfNotLoggedIn();
    $pdo = getConnection();
    $dishTypes = getAllDishTypes($pdo);
    echo render('admin-wrapper.phtml', [
        'content' => render('create-dish.phtml', [
            'dishTypes' => $dishTypes
        ])
    ]);
}

function updateDishHandler($urlParams)
{
    redirectToLoginPageIfNotLoggedIn();
    $pdo = getConnection();
    $stmt = $pdo->prepare(
        "UPDATE `dishes` SET
            name = ?,
            slug = ?,
            description = ?,
            price = ?,
            dishTypeId = ?,
            isActive = ?
        WHERE id = ?"
    );

    $stmt->execute([
        $_POST['name'],
        $_POST['slug'],
        $_POST['description'],
        $_POST['price'],
        $_POST['dishTypeId'],
        (int)isset($_POST['isActive']),
        $urlParams['dishId']
    ]);

    header('Location: /admin');
}

function dishEditHandler($vars)
{
    redirectToLoginPageIfNotLoggedIn();
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM `dishes` WHERE slug = ?");
    $stmt->execute([$vars['keresoBaratNev']]);
    $dish = $stmt->fetch(PDO::FETCH_ASSOC);

    $dishTypes = getAllDishTypes($pdo);

    echo render('admin-wrapper.phtml', [
        'content' => render('edit-dish.phtml', [
            'dish' => $dish,
            'dishTypes' => $dishTypes
        ])
    ]);
}