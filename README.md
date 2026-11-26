# InternTrack V4
Static academic prototype using HTML, CSS, Bootstrap 5.3.3, JavaScript and Chart.js. No PHP or database server. Open using GitHub Pages (main branch, root) or a local HTTP server. Upload all files at repository root, including index.html.

## Demo
Register a student with dummy details, upload a sample PDF (under 1 MB), apply to Nexa Digital, log out, sign in as hr1@interntrack.test / employer123, offer or reject, log out and sign in as the student to accept or decline. Other employer accounts hr2 through hr6 use the same demo password. Use the same browser and site origin.

## Organisation
Separate HTML pages, shared style.css, data.js for sample data and storage, app.js for commented page logic. Bootstrap responsive grid and viewport metadata; Chart.js status and weekly charts. Internet required for CDN libraries.

## Limitations
Simulated authentication only: passwords and data are visible in browser storage; not secure or suitable for real personal data. Resume copies consume browser storage. No cross-device synchronization. Clearing browser data resets the prototype. Use dummy information only.


`charts.js` contains the bar and doughnut chart configuration, following the lecturer’s Chart.js example. `app.js` prepares the application data and calls `displayCharts`.
