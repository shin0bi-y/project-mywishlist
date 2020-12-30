<?php
$config = [
    'dbfile' => __DIR__ . '/../conf/db.conf.ini',
    'settings' => ['displayErrorDetails'=>true],
    'creds' => "conf/creds.txt",
    'view' => function ($container) {
        $vars = [
            "rootUri" => $container->request->getUri()->getBasePath(),
            "router" => $container->router,
            "user" => isset($_SESSION['user']) ? $_SESSION['user'] : null
        ];
        $renderer = new PhpRenderer(__DIR__ . '/app/views/', $vars);
        $renderer->setLayout("layout.phtml");
        return $renderer;
    }
];

return $config;