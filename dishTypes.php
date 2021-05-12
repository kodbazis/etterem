<?php

function createDishTypeHandler() 
{
    redirectToLoginPageIfNotLoggedIn();
    $pdo = getConnection();
    $stmt = $pdo->prepare(
        "INSERT INTO `dishTypes` 
        (`name`, `slug`, `description`) 
        VALUES 
        (?, ?, ?);"
    );

    $stmt->execute([
        $_POST['name'],
        slugify($_POST['name']),
        $_POST['description'],
    ]);
    header('Location: /admin/etel-tipusok');
}

function adminDishTypeHandler()
{
    redirectToLoginPageIfNotLoggedIn();
    $pdo = getConnection();
    $dishTypes = getAllDishTypes($pdo);

    echo render('admin-wrapper.phtml', [
        'content' => render("dish-type-list.phtml", [
            'dishTypes' => $dishTypes,
        ])
    ]);
}

function getAllDishTypes($pdo)
{
    $stmt = $pdo->prepare("SELECT * FROM `dishTypes`");
    $stmt->execute();
    $dishTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $dishTypes;
}