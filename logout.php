<?php

require_once __DIR__ . '/session.php';
require_once __DIR__ . '/helpers.php';

session_unset();
session_destroy();
header('Location: ' . BASE_URL . '/index.php');
exit;