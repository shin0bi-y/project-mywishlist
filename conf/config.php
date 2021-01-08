<?php
use Slim\Views\PhpRenderer;

$config = [
    'settings' => ['displayErrorDetails'=>true],
    'creds' => "conf/creds.ini",
    'view' => function ($container) {
        $vars = [
            "rootUri" => $container->request->getUri()->getBasePath(),
            "router" => $container->router,
            "user" => isset($_SESSION['user']) ? $_SESSION['user'] : null
        ];
        $renderer = new PhpRenderer(__DIR__ . '/../app/views', $vars);
        $renderer->setLayout("layout.phtml");
        return $renderer;
    },
    'flash' => function () {
        return new Slim\Flash\Messages();
    }
];

return $config;