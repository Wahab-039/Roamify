<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/config/config.php';

final class Weather
{
    public static function forDestination(array $destination): ?array
    {
        $cached = self::cached((int) $destination['id']);
        if ($cached) {
            return $cached;
        }

        $fresh = self::fetchFresh($destination);
        if (!$fresh) {
            return self::latestCached((int) $destination['id']);
        }

        self::save((int) $destination['id'], $fresh);

        return $fresh;
    }

    private static function cached(int $destinationId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM weather_cache WHERE destination_id = ? AND expires_at > NOW() ORDER BY fetched_at DESC LIMIT 1'
        );
        $stmt->execute([$destinationId]);

        return $stmt->fetch() ?: null;
    }

    private static function latestCached(int $destinationId): ?array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM weather_cache WHERE destination_id = ? ORDER BY fetched_at DESC LIMIT 1'
        );
        $stmt->execute([$destinationId]);

        return $stmt->fetch() ?: null;
    }

    private static function save(int $destinationId, array $weather): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO weather_cache
                (destination_id, temperature, humidity, weather_condition, icon, raw_json, fetched_at, expires_at)
             VALUES
                (?, ?, ?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? MINUTE))'
        );
        $stmt->execute([
            $destinationId,
            $weather['temperature'],
            $weather['humidity'],
            $weather['weather_condition'],
            $weather['icon'],
            $weather['raw_json'],
            WEATHER_CACHE_MINUTES,
        ]);
    }

    private static function fetchFresh(array $destination): ?array
    {
        if (WEATHER_API_KEY !== '') {
            $openWeather = self::fetchOpenWeather($destination);
            if ($openWeather) {
                return $openWeather;
            }
        }

        return self::fetchOpenMeteo($destination);
    }

    private static function fetchOpenWeather(array $destination): ?array
    {
        $query = '';
        if (!empty($destination['latitude']) && !empty($destination['longitude'])) {
            $query = 'lat=' . urlencode((string) $destination['latitude']) . '&lon=' . urlencode((string) $destination['longitude']);
        } else {
            $city = trim(($destination['city'] ?? '') . ',' . ($destination['country'] ?? ''));
            $query = 'q=' . urlencode($city);
        }

        $endpoint = 'https://api.openweathermap.org/data/2.5/weather?' . $query . '&units=metric&appid=' . urlencode(WEATHER_API_KEY);
        $context = stream_context_create([
            'http' => [
                'timeout' => 4,
                'ignore_errors' => true,
                'header' => "Accept: application/json\r\n",
            ],
        ]);

        $response = @file_get_contents($endpoint, false, $context);
        if (!$response) {
            return null;
        }

        $payload = json_decode($response, true);
        if (!is_array($payload) || empty($payload['main']) || empty($payload['weather'][0])) {
            return null;
        }

        $condition = (string) $payload['weather'][0]['description'];

        return [
            'temperature' => (float) $payload['main']['temp'],
            'humidity' => (int) $payload['main']['humidity'],
            'weather_condition' => $condition,
            'icon' => self::iconFromText($condition),
            'raw_json' => json_encode($payload),
            'fetched_at' => date('Y-m-d H:i:s'),
            'expires_at' => date('Y-m-d H:i:s', strtotime('+' . WEATHER_CACHE_MINUTES . ' minutes')),
        ];
    }

    private static function fetchOpenMeteo(array $destination): ?array
    {
        if (empty($destination['latitude']) || empty($destination['longitude'])) {
            return null;
        }

        $endpoint = sprintf(
            'https://api.open-meteo.com/v1/forecast?latitude=%s&longitude=%s&current=temperature_2m,relative_humidity_2m,weather_code&timezone=auto',
            urlencode((string) $destination['latitude']),
            urlencode((string) $destination['longitude'])
        );

        $context = stream_context_create([
            'http' => [
                'timeout' => 4,
                'ignore_errors' => true,
                'header' => "Accept: application/json\r\n",
            ],
        ]);

        $response = @file_get_contents($endpoint, false, $context);
        if (!$response) {
            return null;
        }

        $payload = json_decode($response, true);
        if (!is_array($payload) || empty($payload['current'])) {
            return null;
        }

        $current = $payload['current'];
        $code = (int) ($current['weather_code'] ?? -1);
        $condition = self::conditionFromCode($code);

        return [
            'temperature' => (float) ($current['temperature_2m'] ?? 0),
            'humidity' => (int) ($current['relative_humidity_2m'] ?? 0),
            'weather_condition' => $condition,
            'icon' => self::iconFromCode($code),
            'raw_json' => json_encode($payload),
            'fetched_at' => date('Y-m-d H:i:s'),
            'expires_at' => date('Y-m-d H:i:s', strtotime('+' . WEATHER_CACHE_MINUTES . ' minutes')),
        ];
    }

    private static function conditionFromCode(int $code): string
    {
        return match (true) {
            $code === 0 => 'Clear sky',
            in_array($code, [1, 2], true) => 'Partly cloudy',
            $code === 3 => 'Overcast',
            in_array($code, [45, 48], true) => 'Fog',
            in_array($code, [51, 53, 55, 56, 57], true) => 'Drizzle',
            in_array($code, [61, 63, 65, 66, 67, 80, 81, 82], true) => 'Rain',
            in_array($code, [71, 73, 75, 77, 85, 86], true) => 'Snow',
            in_array($code, [95, 96, 99], true) => 'Thunderstorm',
            default => 'Weather unavailable',
        };
    }

    private static function iconFromCode(int $code): string
    {
        return match (true) {
            $code === 0 => 'clear',
            in_array($code, [1, 2], true) => 'partly-cloudy',
            $code === 3 => 'cloudy',
            in_array($code, [45, 48], true) => 'fog',
            in_array($code, [51, 53, 55, 56, 57, 61, 63, 65, 66, 67, 80, 81, 82], true) => 'rain',
            in_array($code, [71, 73, 75, 77, 85, 86], true) => 'snow',
            in_array($code, [95, 96, 99], true) => 'storm',
            default => 'cloudy',
        };
    }

    private static function iconFromText(string $condition): string
    {
        $condition = strtolower($condition);

        return match (true) {
            str_contains($condition, 'clear') => 'clear',
            str_contains($condition, 'rain') || str_contains($condition, 'drizzle') => 'rain',
            str_contains($condition, 'snow') => 'snow',
            str_contains($condition, 'storm') || str_contains($condition, 'thunder') => 'storm',
            str_contains($condition, 'fog') || str_contains($condition, 'mist') || str_contains($condition, 'haze') => 'fog',
            str_contains($condition, 'few') || str_contains($condition, 'part') => 'partly-cloudy',
            default => 'cloudy',
        };
    }
}
