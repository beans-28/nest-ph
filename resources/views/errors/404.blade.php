<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found - NEST.PH</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 28px;
            background: #f2f4f8;
            color: #292420;
            font-family: 'Roboto', -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        .logo{ display:flex; align-items:center; gap:8px; color:#567357; font-weight:700; font-size:17px; }
        .logo img{ height:28px; width:auto; }
        .error-page {
            text-align: center;
            padding: 20px;
        }
        .error-page h1 {
            font-size: 7rem;
            font-weight: 900;
            color: #567357;
            margin: 0;
            letter-spacing: -0.03em;
            line-height: 1;
        }
        .error-page h2 {
            font-size: 1.4rem;
            margin: 0.6rem 0 0.8rem;
            font-weight: 700;
        }
        .error-page p {
            color: #5b6b60;
            margin: 0 0 2rem;
            line-height: 1.6;
            max-width: 380px;
        }
        .error-page a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
            padding: 0.85rem 1.7rem;
            background: #567357;
            color: #fff;
            font-weight: 700;
            font-size: 13.5px;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.2s ease;
        }
        .error-page a:hover {
            background: #197335;
        }
        .error-page a:focus-visible {
            outline: 2px solid #197335;
            outline-offset: 2px;
        }
        .error-page a svg{ width:15px; height:15px; }
    </style>
</head>
<body>
    <div class="logo"><img src="{{ asset('images/nestph.png') }}" alt="">NEST.PH</div>
    <div class="error-page">
        <h1>404</h1>
        <h2>This page doesn't exist</h2>
        <p>The link may be broken, or the page may have moved. Either way, there's nothing here.</p>
        <a href="/">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
            Back to homepage
        </a>
    </div>
</body>
</html>
