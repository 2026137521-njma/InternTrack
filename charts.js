// Display application data using Chart.js.
// Data is retrieved from LocalStorage by app.js instead of PHP.
function displayCharts(labels, values, weekLabels, weeks, total) {

    if (typeof Chart === "undefined") {
        document.querySelectorAll("canvas").forEach(canvas => {
            canvas.replaceWith(document.createTextNode("Chart library unavailable."));
        });
        return;
    }

    Chart.defaults.color = "#cbd5e1";

    // BAR CHART
    if (document.getElementById("progressChart")) {

        new Chart(document.getElementById("progressChart"), {
            type: "bar",

            data: {
                labels: weekLabels,
                datasets: [{
                    label: "Applications Submitted",
                    data: weeks,
                    backgroundColor: "#a5b4fc"
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0
                        }
                    }
                }
            }
        });
    }

    // DOUGHNUT CHART
    if (document.getElementById("statusChart")) {

        if (total === 0) {
            document.getElementById("statusChart").replaceWith(
                document.createTextNode("No applications available for visualization.")
            );
            return;
        }

        new Chart(document.getElementById("statusChart"), {
            type: "doughnut",

            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: [
                        "#fbbf24",
                        "#60a5fa",
                        "#f87171",
                        "#4ade80",
                        "#94a3b8"
                    ]
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
}
