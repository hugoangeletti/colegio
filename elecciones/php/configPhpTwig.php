<?php

if (file_exists("../Twig-1.14.2/lib/Twig/Autoloader.php")) {
    require_once '../Twig-1.14.2/lib/Twig/Autoloader.php';
} else {
    require_once 'Twig-1.14.2/lib/Twig/Autoloader.php';
}
Twig_Autoloader::register();

if (file_exists("../html")) {
    $templateDirHtml = "../html";
} else {
    $templateDirHtml = "html";
}
$loaderHtml = new Twig_Loader_Filesystem($templateDirHtml);
$twigHtml = new Twig_Environment($loaderHtml);


$paths = array('PATH_HOME' => PATH_HOME,
    'PATH_CSS' => PATH_CSS,
    'PATH_PHP' => PATH_PHP,
    'PATH_HTML' => PATH_HTML,
    'PATH_JS' => PATH_JS,
    'PATH_ADMIN' => PATH_ADMIN,
    'PATH_IMAGES' => PATH_IMAGES,
    'PATH_CONTROLLER' => PATH_CONTROLLER,
    'PATH_DOCUMENTS' => PATH_DOCUMENTS
);

$pathsJson = array('PATH_HOME' => json_encode(PATH_HOME),
    'PATH_CSS' => json_encode(PATH_CSS),
    'PATH_PHP' => json_encode(PATH_PHP),
    'PATH_HTML' => json_encode(PATH_HTML),
    'PATH_JS' => json_encode(PATH_JS),
    'PATH_ADMIN' => json_encode(PATH_ADMIN),
    'PATH_CONTROLLER' => json_encode(PATH_CONTROLLER),
    'PATH_IMAGES' => json_encode(PATH_IMAGES)
);

$info = array(
    'paths' => $paths,
    'pathsJson' => $pathsJson,
    'localhost' => $localhost
);
