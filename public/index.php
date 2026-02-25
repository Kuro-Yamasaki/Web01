<?php

declare(strict_types=1);
session_start();

const INCLUDES_DIR = __DIR__ . '/../includes';
const ROUTE_DIR = __DIR__ . '/../routes';
const TEMPLATES_DIR = __DIR__ . '/../templates';
const DATABASES_DIR = __DIR__ . '/../databases';

require_once INCLUDES_DIR . '/router.php';
require_once INCLUDES_DIR . '/view.php';
require_once INCLUDES_DIR . '/database.php';

dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
?>