<?php
require_once "../includes/config.php";
require_once "../includes/connection.php";

// Fetch jobs
function fetch_jsearch(string $keyword, string $location): array {
    $query = urlencode(trim($keyword) . ' in ' . trim($location));
    $url = JSEARCH_BASE_URL . '?query=' . $query . '&page=1&num_pages=1';

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-RapidAPI-Key: '  . JSEARCH_API_KEY,
        'X-RapidAPI-Host: ' . JSEARCH_API_HOST,
    ]);

    $response = curl_exec($ch);

    // Check for cURL errors
    if ($response === false) {
        error_log('JSearch cURL error: ' . curl_error($ch));
        curl_close($ch);
        return [];
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code !== 200) {
        error_log('JSearch API returned HTTP ' . $http_code);
        return [];
    }
    
    // Decode response
    $data = json_decode($response, true);

    if (empty($data['data']) || !is_array($data['data'])) {
        return [];
    }

    // Normalise
    $jobs = [];
    foreach ($data['data'] as $item) {
        $jobs[] = [
            'source' => 'JSearch',
            'job_id' => $item['job_id'] ?? '',
            'title' => $item['job_title'] ?? 'N/A',
            'company' => $item['employer_name'] ?? 'N/A',
            'location' => $item['job_city'] ?? $item['job_country'] ?? 'N/A',
            'description' => $item['job_description'] ?? '',
            'url' => $item['job_apply_link'] ?? '',
            'posted_at' => $item['job_posted_at_datetime_utc'] ?? '',
        ];
    }

    return $jobs;
}
?>