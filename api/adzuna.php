<?php
require_once "../includes/config.php";
require_once "../includes/connection.php";

// Fetch jobs
function fetch_adzuna(string $keyword, string $location): array {
    $params = http_build_query([
        'app_id' => ADZUNA_APP_ID,
        'app_key' => ADZUNA_APP_KEY,
        'results_per_page' => 10,
        'what' => trim($keyword),
        'where' => trim($location),
        'content-type' => 'application/json'
    ]);

    $url = ADZUNA_BASE_URL . '?' . $params;

    // Initialize cURL
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);

    // Check for cURL errors
    if ($response === false) {
        error_log('Adzuna cURL error: ' . curl_error($ch));
        curl_close($ch);
        return [];
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        error_log('Adzuna API returned HTTP ' . $http_code);
        return [];
    }

    // Decode response
    $data = json_decode($response, true);

    if (empty($data['results']) || !is_array($data['results'])) {
        return [];
    }

    // Normalise
    $jobs = [];
    foreach ($data['results'] as $item) {
        $jobs[] = [
            'source' => 'Adzuna',
            'job_id' => (string) ($item['id'] ?? ''),
            'title' => $item['title'] ?? 'N/A',
            'company' => $item['company']['display_name'] ?? 'N/A',
            'location' => $item['location']['display_name'] ?? 'N/A',
            'description' => $item['description'] ?? '',
            'url' => $item['redirect_url'] ?? '',
            'posted_at' => $item['created'] ?? '',
        ];
    }

    return $jobs;
}
?>