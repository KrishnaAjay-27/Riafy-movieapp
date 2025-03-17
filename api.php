<?php
function fetchMovieData($title) {
    $apiKey = 'c51f139';
    $url = "http://www.omdbapi.com/?t=" . urlencode($title) . "&apikey=" . $apiKey;
    $response = file_get_contents($url);
    return json_decode($response, true);
}

function searchMovies($query) {
    $apiKey = 'c51f139'; // Replace with your actual OMDB API key
    $url = "http://www.omdbapi.com/?s=" . urlencode($query) . "&apikey=" . $apiKey;
    $response = file_get_contents($url);
    return json_decode($response, true);
}
?>
