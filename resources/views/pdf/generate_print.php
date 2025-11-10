<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
</head>
<body>
    <h2></h2>
    <iframe src="/printPdf?token=" style="width: 100%; height: 400px;"></iframe>

    <script>
        // Menerima pesan dari parent page
        window.addEventListener('message', (event) => {
            if (event.data === 'print') {
                window.print();
                window.parent.postMessage('print-success', '*');
            }
        });
    </script>
</body>
</html>