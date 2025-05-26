// js/scoreboard_refresh.js
document.addEventListener('DOMContentLoaded', function() {
    const scoreboardDiv = document.getElementById('scoreboard-data');

    function fetchScoreboard() {
        fetch('scoreboard_data.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                if (data.html) {
                    scoreboardDiv.innerHTML = data.html;
                } else if (data.error) {
                    scoreboardDiv.innerHTML = '<p style="color: red;">Error loading scoreboard: ' + data.error + '</p>';
                }
            })
            .catch(error => {
                console.error('There was a problem with the fetch operation:', error);
                scoreboardDiv.innerHTML = '<p style="color: red;">Failed to load scoreboard data. Please try again later.</p>';
            });
    }

    // Initial fetch when the page loads
    fetchScoreboard();

    // Set interval to refresh every 10 seconds
    setInterval(fetchScoreboard, 10000); // 10000 milliseconds = 10 seconds
});