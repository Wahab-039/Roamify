<?php
declare(strict_types=1);

define('APP_NAME', 'Roamify');
define('APP_ENV', 'development');
define('BASE_URL', '');

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'smart_travel_planner');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Optional: put your OpenWeatherMap key here. If blank, the app uses Open-Meteo with no API key.
define('WEATHER_API_KEY', getenv('WEATHER_API_KEY') ?: '');
define('WEATHER_CACHE_MINUTES', 45);

define('UPLOAD_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'destinations');
define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024);

date_default_timezone_set('Asia/Karachi');
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);
