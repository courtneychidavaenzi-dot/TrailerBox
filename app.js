const API_KEY = "6c77e0b30add4f1ffae25ee2d63bef19";
const BASE_URL = "https://api.themoviedb.org/3";
const IMAGE_BASE_URL = "https://image.tmdb.org/t/p/w500";

const movieGrid = document.getElementById("movieGrid");
const sectionTitle = document.getElementById("sectionTitle");
const navLinks = document.querySelectorAll(".nav-link");
const searchInput = document.getElementById("searchInput");
const searchCategory = document.getElementById("searchCategory");
const searchBtn = document.getElementById("searchBtn");
const loadMoreBtn = document.getElementById("loadMoreBtn");
const videoModal = document.getElementById("videoModal");
const videoPlayer = document.getElementById("videoPlayer");
const closeModal = document.getElementById("closeModal");
const bodyCategory = document.body.dataset.category || 'home';

const categories = {
    home: {
        label: "Home",
        title: "Trending Movies",
        url: `${BASE_URL}/trending/movie/week?api_key=${API_KEY}`
    },
    trending: {
        label: "Trending",
        title: "Trending Movies",
        url: `${BASE_URL}/trending/movie/week?api_key=${API_KEY}`
    },
    latest: {
        label: "Latest",
        title: "Latest Releases",
        url: `${BASE_URL}/movie/now_playing?api_key=${API_KEY}&language=en-US`
    },
    movies: {
        label: "Movies",
        title: "Popular Movies",
        url: `${BASE_URL}/movie/popular?api_key=${API_KEY}&language=en-US`
    },
    anime: {
        label: "Anime",
        title: "Popular Anime Movies",
        url: `${BASE_URL}/discover/movie?api_key=${API_KEY}&with_genres=16&language=en-US&sort_by=popularity.desc`
    }
};

let currentCategory = "home";
let currentPage = 1;
let currentMode = "category";
let currentQuery = "";

function setActiveCategory(category) {
    navLinks.forEach(link => {
        if (link.dataset.category === category) {
            link.classList.add("active");
        } else {
            link.classList.remove("active");
        }
    });
}

function buildUrl(url, page) {
    const separator = url.includes("?") ? "&" : "?";
    return `${url}${separator}page=${page}`;
}

function updateLoadMore(visible) {
    loadMoreBtn.style.display = visible ? "inline-flex" : "none";
}

async function fetchMovies(url, title, append = false) {
    try {
        sectionTitle.textContent = title;
        if (!append) {
            movieGrid.innerHTML = "<p class='fetch-loading'>Loading...</p>";
        }

        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`TMDB API error ${response.status}`);
        }

        const data = await response.json();
        if (!data.results || data.results.length === 0) {
            if (!append) {
                movieGrid.innerHTML = '<p class="fetch-error">No results found.</p>';
            }
            updateLoadMore(false);
            return;
        }

        showMovies(data.results, append);
        updateLoadMore(data.page < data.total_pages);
    } catch (error) {
        console.error(error);
        if (!append) {
            movieGrid.innerHTML = '<p class="fetch-error">Unable to load content. Please check your network or API key.</p>';
        }
        updateLoadMore(false);
    }
}

function showMovies(movies, append = false) {
    if (!append) {
        movieGrid.innerHTML = "";
    }

    movies.forEach(movie => {
        const poster = movie.poster_path ? `${IMAGE_BASE_URL}${movie.poster_path}` : "https://via.placeholder.com/500x750?text=No+Image";
        const releaseDate = movie.release_date || movie.first_air_date || "Unknown date";
        const voteAverage = movie.vote_average ? movie.vote_average.toFixed(1) : "N/A";
        const title = movie.title || movie.name || "Untitled";
        const overview = movie.overview || "No description available.";

        const movieCard = document.createElement("div");
        movieCard.classList.add("movie-card");
        movieCard.dataset.movieId = movie.id;

        movieCard.innerHTML = `
            <img src="${poster}" alt="${title}">
            <div class="play-overlay">
                <button class="play-btn" data-movie-id="${movie.id}">▶ Watch Trailer</button>
            </div>
            <div class="movie-info">
                <h3>${title}</h3>
                <p>${releaseDate}</p>
                <p>⭐ ${voteAverage}</p>
                <p class="movie-overview">${overview}</p>
            </div>
        `;

        movieGrid.appendChild(movieCard);
    });
}

function loadCategory(category, append = false) {
    if (!append) {
        currentMode = "category";
        currentPage = 1;
        currentQuery = "";
        if (searchCategory) {
            searchCategory.value = "all";
        }
        searchInput.value = "";
    }

    currentCategory = category;
    const categoryData = categories[category] || categories.home;
    setActiveCategory(category);
    const targetUrl = buildUrl(categoryData.url, currentPage);
    fetchMovies(targetUrl, categoryData.title, append);
}

async function filterResultsByCategory(results, category) {
    if (category  === "anime") {
        return results.filter(movie => Array.isArray(movie.genre_ids) && movie.genre_ids.includes(16));
    }
    return results;
}

async function searchMovies(query, append = false) {
    if (!query) {
        loadCategory(currentCategory);
        return;
    }

    const selectedCategory = searchCategory?.value || "all";
    if (!append) {
        currentMode = "search";
        currentQuery = query;
        currentPage = 1;
        setActiveCategory("");
    }

    const searchUrl = `${BASE_URL}/search/movie?api_key=${API_KEY}&language=en-US&query=${encodeURIComponent(query)}&page=${currentPage}&include_adult=false`;
    const titleSuffix = selectedCategory === 'anime' ? ' (Anime)' : '';
    const titleText = `Search results for "${query}"${titleSuffix}`;
    try {
        if (!append) {
            movieGrid.innerHTML = "<p class='fetch-loading'>Loading...</p>";
        }
        const response = await fetch(searchUrl);
        if (!response.ok) {
            throw new Error(`TMDB API error ${response.status}`);
        }
        const data = await response.json();
        let results = data.results || [];
        results = await filterResultsByCategory(results, selectedCategory);
        if ((!results || results.length === 0) && !append) {
            movieGrid.innerHTML = '<p class="fetch-error">No matching results found for your query.</p>';
            updateLoadMore(false);
            sectionTitle.textContent = titleText;
            return;
        }
        sectionTitle.textContent = titleText;
        showMovies(results, append);
        updateLoadMore(data.page < data.total_pages);
    } catch (error) {
        console.error(error);
        if (!append) {
            movieGrid.innerHTML = '<p class="fetch-error">Unable to load search results. Please check your network or API key.</p>';
        }
        updateLoadMore(false);
    }
}

searchBtn.addEventListener("click", () => {
    searchMovies(searchInput.value.trim());
});

searchInput.addEventListener("keypress", event => {
    if (event.key === "Enter") {
        event.preventDefault();
        searchMovies(searchInput.value.trim());
    }
});

loadMoreBtn.addEventListener("click", () => {
    currentPage += 1;
    if (currentMode === "search") {
        searchMovies(currentQuery, true);
    } else {
        loadCategory(currentCategory, true);
    }
});

movieGrid.addEventListener("click", event => {
    const button = event.target.closest(".play-btn");
    if (button) {
        const movieId = button.dataset.movieId;
        openTrailer(movieId);
    }
});

closeModal.addEventListener("click", closeTrailerModal);
videoModal.addEventListener("click", event => {
    if (event.target === videoModal) {
        closeTrailerModal();
    }
});

loadCategory(bodyCategory);

async function openTrailer(movieId) {
    try {
        videoPlayer.innerHTML = "<p class='fetch-loading'>Loading trailer...</p>";
        videoModal.classList.add("open");
        const response = await fetch(`${BASE_URL}/movie/${movieId}/videos?api_key=${API_KEY}&language=en-US`);
        if (!response.ok) {
            throw new Error(`TMDB API error ${response.status}`);
        }
        const data = await response.json();
        const trailer = data.results.find(video => video.site === "YouTube" && (video.type === "Trailer" || video.type === "Teaser"));
        if (!trailer) {
            videoPlayer.innerHTML = '<p class="fetch-error">No playable trailer available for this item.</p>';
            return;
        }
        videoPlayer.innerHTML = `
            <iframe src="https://www.youtube.com/embed/${trailer.key}?autoplay=1&rel=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
        `;
    } catch (error) {
        console.error(error);
        videoPlayer.innerHTML = '<p class="fetch-error">Unable to load trailer. Please try again later.</p>';
    }
}

function closeTrailerModal() {
    videoModal.classList.remove("open");
    videoPlayer.innerHTML = "";
}

loadCategory(currentCategory);