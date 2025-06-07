document.addEventListener('DOMContentLoaded', () => {
    const alertFeed = document.getElementById('alert-feed');

    function fetchGarrardAlerts() {
        fetch('https://api.weather.gov/alerts/active?area=KY')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                const garrardAlerts = data.features.filter(alert =>
                    alert.properties.areaDesc.toLowerCase().includes('garrard')
                );

                alertFeed.innerHTML = ''; // Clear loading message or previous content

                if (garrardAlerts.length === 0) {
                    alertFeed.innerHTML = '<p>No active alerts for Garrard County at this time.</p>';
                    return;
                }

                garrardAlerts.forEach(alert => {
                    const alertBox = document.createElement('div');
                    alertBox.classList.add('alert-box');
                    alertBox.innerHTML = `
                        <h3>${alert.properties.event}</h3>
                        <p><strong>${alert.properties.headline}</strong></p>
                        <p>${alert.properties.description}</p>
                        <p><em>Effective: ${new Date(alert.properties.effective).toLocaleString()}</em><br>
                        Expires: ${new Date(alert.properties.expires).toLocaleString()}</em></p>
                        <a href="${alert.properties.uri}" target="_blank">View Full Alert</a>
                    `;
                    alertFeed.appendChild(alertBox);
                });
            })
            .catch(error => {
                console.error('Failed to fetch NWS alerts:', error);
                alertFeed.innerHTML = '<p>Error loading alerts. Please try again later.</p>';
            });
    }

    // Initial load
    if (alertFeed) {
        fetchGarrardAlerts();

        // Auto-refresh every 5 minutes (300,000 ms)
        setInterval(fetchGarrardAlerts, 300000);
    }
});

// Optional: Smooth scrolling helper
function scrollToSection(id) {
    document.getElementById(id).scrollIntoView({ behavior: 'smooth' });
}
