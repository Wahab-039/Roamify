<?php
require_once dirname(__DIR__) . '/config/bootstrap.php';
require_once dirname(__DIR__) . '/controllers/AuthController.php';

AuthController::logout('admin/login.php');

