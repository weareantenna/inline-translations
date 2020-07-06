<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inline translations | Overview</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
    <div id="antenna-inline-translator-list"></div>
    <script type="text/javascript">
        window.config = @json($config);
    </script>
    <script src="{{ $js }}" defer></script>
</body>
</html>
