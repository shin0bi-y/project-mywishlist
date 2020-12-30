<?php

use Slim\Views\PhpRenderer;

$config = [
    'dbfile' => __DIR__ . '/../conf/db.conf.ini',
    'settings' => ['displayErrorDetails'=>true],
    'creds' => "conf/creds.ini",
    'view' => function ($container) {
        $vars = [
            "rootUri" => $container->request->getUri()->getBasePath(),
            "router" => $container->router,
            "user" => isset($_SESSION['user']) ? $_SESSION['user'] : null
        ];
        $renderer = new PhpRenderer(__DIR__ . '/../app/views/', $vars);
        $renderer->setLayout(__DIR__ . "/../app/views/layout.phtml");
        return $renderer;
    }
];

return $config;