<?php
session_start();
include 'config/db.php';

$validTypes = [
    'home' => 'Trending Movies',
    'trending' => 'Trending Movies',
    'latest' => 'Latest Releases',
    'movies' => 'Popular Movies',
    'anime' => 'Popular Anime Movies',
];

$type = $_GET['type'] ?? 'home';
if (!array_key_exists($type, $validTypes)) {
    $type = 'home';
}
$pageTitle = $validTypes[$type];
$heroTitle = $type === 'anime' ? 'Discover Anime Trailers' : ($type === 'latest' ? 'Latest Movie Trailers' : ($type === 'movies' ? 'Popular Movie Trailers' : 'Watch Movie Trailers'));
$heroSubtitle = $type === 'anime' ? 'Explore top animated films and new anime trailers.' : ($type === 'latest' ? 'See the newest releases in theaters and streaming.' : ($type === 'movies' ? 'Browse the biggest movies everyone is talking about.' : 'Catch the most trending trailers this week.'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($pageTitle); ?> - Trailerbox</title>
<link rel="stylesheet" href="style.css">
</head>
<body data-category="<?php echo htmlspecialchars($type); ?>">

<div class="overlay"></div>

<header>
    <div class="logo">
        <a href="index.php">Trailer<span>box</span></a>
    </div>

    <nav>
        <a href="index.php" class="nav-link" data-category="home">Home</a>
        <a href="category.php?type=trending" class="nav-link" data-category="trending">Trending</a>
        <a href="category.php?type=latest" class="nav-link" data-category="latest">Latest</a>
        <a href="category.php?type=movies" class="nav-link" data-category="movies">Movies</a>
        <a href="category.php?type=anime" class="nav-link" data-category="anime">Anime</a>
    </nav>

    <div class="header-actions">
        <div class="search-box">
            <input id="searchInput" type="search" placeholder="Search movies, anime, trailers..." autocomplete="off">
            <select id="searchCategory">
                <option value="all">All</option>
                <option value="movies">Movies</option>
                <option value="anime">Anime</option>
            </select>
            <button type="button" id="searchBtn">Search</button>
        </div>
        <div>
            <?php if (!empty($_SESSION['user_id'])): ?>
                <span class="nav-user">Hi, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" class="btn">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn">Login</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<section class="hero">
    <div class="hero-content">
        <h1><?php echo htmlspecialchars($heroTitle); ?></h1>
        <p><?php echo htmlspecialchars($heroSubtitle); ?></p>
        <button class="watch-btn">Explore Now</button>
    </div>
</section>

<section class="movies-section">
    <h2 id="sectionTitle"><?php echo htmlspecialchars($pageTitle); ?></h2>
    <div class="movie-grid" id="movieGrid"></div>
    <div class="load-more-wrap">
        <button id="loadMoreBtn" class="load-more-btn">Load More Trailers</button>
    </div>
</section>

<div id="videoModal" class="video-modal">
    <div class="video-modal-content">
        <button id="closeModal" class="close-modal" aria-label="Close video">×</button>
        <div id="videoPlayer" class="video-player"></div>
    </div>
</div>

<script src="app.js"></script>
</body>
</html>
