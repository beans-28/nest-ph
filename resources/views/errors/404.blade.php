<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found</title>
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f7f7f7;
            color: #192e1b;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        .error-page {
            text-align: center;
            padding: 20px;
        }
        .error-page h1 {
            font-size: 8rem;
            margin: 0;
            letter-spacing: -0.05em;
        }
        .error-page h2 {
            font-size: 1.5rem;
            margin: 0.5rem 0 1rem;
            font-weight: 600;
        }
        .error-page p {
            color: #666;
            margin: 0 0 2rem;
            line-height: 1.6;
        }
        .error-page a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.85rem 1.6rem;
            background: #356135;
            color: #fff;
            text-decoration: none;
            border-radius: 999px;
            transition: background 0.2s ease;
        }
        .error-page a:hover {
            background: #333;
        }
        .error-page a::before {
            content: '←';
            margin-right: 0.6rem;
        }
    </style>
</head>
<body>
    <div class="error-page">
        <h1>404</h1>
        <h2>This page doesn't exist</h2>
        <p>The link may be broken, or the page may have moved. Either way, there's nothing here but very large numbers.</p>
        <a href="/">Back to homepage</a>
    </div>
</body>
</html>
