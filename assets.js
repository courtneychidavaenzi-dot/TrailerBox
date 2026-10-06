const searchInput = document.getElementById("searchInput");

searchInput.addEventListener("keyup", async () => {

const query = searchInput.value;

if(query.length < 2) return;

const response = await fetch(
`https://api.themoviedb.org/3/search/movie?api_key=${API_KEY}&query=${query}`
);

const data = await response.json();

showSearchResults(data.results);

});

function showSearchResults(results){

const container = document.getElementById("searchResults");

container.innerHTML = "";

results.slice(0,5).forEach(movie => {

container.innerHTML += `
<div class="search-item">
${movie.title}
</div>
`;

});
}