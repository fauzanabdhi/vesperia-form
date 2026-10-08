<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operational Risk Report</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main
        id="app"
        data-form-url="{{ url('/api/v1/forms/operational-risk-report') }}"
    >
        <p id="loading-message">Loading form…</p>

        <section id="form-container" hidden>
            <h1 id="form-title"></h1>

            <form id="dynamic-form">
                <div id="sections"></div>

                <button id="submit-button" type="submit">
                    Submit
                </button>
            </form>

            <p id="success-message" class="success-message" hidden></p>
            <div id="submission-errors" class="error-message" hidden></div>
        </section>

        <p id="load-error" class="error-message" hidden></p>
    </main>
</body>
</html>