<?php
use Illuminate\Database\Capsule\Manager as Capsule;

require 'vendor/autoload.php';


$capsule = new Capsule;


$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost:3306',
    'database'  => 'idw',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

// Disponibiliza a instância do Capsule globalmente via métodos estáticos
$capsule->setAsGlobal();

// Configura o Eloquent ORM...
$capsule->bootEloquent();
$capsule->getConnection()->getPdo()->exec("SET NAMES 'utf8mb4'"); //emoji
