<?php
require_once 'data.php';

$selectedCountry = $_GET['country'] ?? '';
$selectedCity = $_GET['city'] ?? '';
$weatherDetails = null;

function retrieveForecastInfo($location) {
    $apiKey = '077111eeb2644a4155f635c73b39437c';
    $query = urlencode($location);
    $endpoint = "http://api.weatherstack.com/current?access_key=$apiKey&query=$query";

    $response = @file_get_contents($endpoint);
    if (!$response) return null;

    $data = json_decode($response, true);
    if (isset($data['error']) || !isset($data['current'])) return null;

    $current = $data['current'];
    $conditionKey = strtolower(str_replace(' ', '_', $current['weather_descriptions'][0] ?? 'partly_cloudy'));

    return [
        'condition' => $conditionKey,
        'description' => $current['weather_descriptions'][0] ?? 'Unknown',
        'temperature' => $current['temperature'] . '°C',
        'feels_like' => $current['feelslike'] . '°C',
        'humidity' => $current['humidity'] . '%',
        'wind' => $current['wind_speed'] . ' km/h',
        'wind_direction' => $current['wind_dir'] ?? 'N',
        'datetime' => $data['location']['localtime'] ?? date('Y-m-d H:i')
    ];
}

if (!empty($selectedCountry) && !empty($selectedCity)) {
    $weatherDetails = retrieveForecastInfo($selectedCity);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Weather App (Weatherstack)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Check the Weather</h1>
    <form method="get">
        <label for="country">Select Country:</label>
        <select name="country" id="country" onchange="this.form.submit()">
            <option value="">-- choose country --</option>
            <?php foreach ($countries as $key => $label): ?>
                <option value="<?= $key ?>" <?= $selectedCountry === $key ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
        </select>

        <?php if ($selectedCountry): ?>
            <label for="city">Select City:</label>
            <select name="city" id="city">
                <?php foreach ($cities[$selectedCountry] as $city): ?>
                    <option value="<?= $city ?>" <?= $selectedCity === $city ? 'selected' : '' ?>><?= $city ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Check Weather</button>
        <?php endif; ?>
    </form>

    <?php if ($weatherDetails): ?>
        <?php
            $iconFile = "icons/{$weatherDetails['condition']}.svg";
            if (!file_exists($iconFile)) {
                $iconFile = "icons/partly_cloudy.svg";
            }
        ?>
        <div class="weather">
            <h2>Weather for: <?= htmlspecialchars($selectedCity) ?></h2>
            <div style="text-align: center; margin-bottom: 10px;">
                <img src="<?= $iconFile ?>" alt="<?= $weatherDetails['condition'] ?>" style="width: 80px; height: 80px;"><br>
                <strong style="font-size: 1.2em;"><?= ucfirst(str_replace('_', ' ', $weatherDetails['condition'])) ?></strong>
            </div>
            <div style="line-height: 1.7;">
                📅 <strong>Date & Time:</strong> <?= $weatherDetails['datetime'] ?><br>
                🌡️ <strong>Temperature:</strong> <?= $weatherDetails['temperature'] ?> | Feels Like: <?= $weatherDetails['feels_like'] ?><br>
                ☁️ <strong>Description:</strong> <?= $weatherDetails['description'] ?><br><br>
                💧 <strong>Humidity:</strong> <?= $weatherDetails['humidity'] ?><br>
                💨 <strong>Wind:</strong> <?= $weatherDetails['wind'] ?> (<?= $weatherDetails['wind_direction'] ?>)<br>
            </div>
        </div>
    <?php elseif ($selectedCity): ?>
        <p style="color: red;">❗ Unable to fetch weather data for <?= htmlspecialchars($selectedCity) ?>.</p>
    <?php endif; ?>
</div>
</body>
</html>
