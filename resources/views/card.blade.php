<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { box-sizing: border-box; margin: 0; }
        body { width: 100vw; height: 100vh; display: flex; flex-direction: column; justify-content: center;
               padding: 64px; font-family: sans-serif; color: #fff;
               background: linear-gradient(135deg, #1e3a8a, #7c3aed); }
        h1 { font-size: 72px; line-height: 1.1; }
        p { margin-top: 24px; font-size: 32px; opacity: .85; }
    </style>
</head>
<body>
    <h1>{{ $data['title'] ?? 'Untitled' }}</h1>
    @isset($data['subtitle'])
        <p>{{ $data['subtitle'] }}</p>
    @endisset
</body>
</html>
