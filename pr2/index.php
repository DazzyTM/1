<?php
// P1
$items = [];
$searchQuery = '';
$errorMessage = '';

if (!empty($_GET['search'])) {
    $searchQuery = $_GET['search'];

    // Параметри для Google Custom Search API
    $apiKey = 'AIzaSyA43KsyCAefJwaQLAMRbnOHWJTdNm1Z6Q4';
    $cx = '51267b402471a4029';

    $url = "https://www.googleapis.com/customsearch/v1?key=" . $apiKey . "&cx=" . $cx . "&q=" . urlencode($searchQuery);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $resultJson = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($resultJson) {
        $result = json_decode($resultJson, true);

        // Перевіряємо на помилки API
        if (isset($result['error'])) {
            $errorMessage = "Помілка API: " . $result['error']['message'];
        } elseif (isset($result['items'])) {
            $items = $result['items'];
        }
    } else {
        $errorMessage = "Не вдалося виконати запит";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Browser</title>
</head>
<body>
<h2>My Browser</h2>

<form method="GET" action="">
    <label for="search">Search:</label>
    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>"><br><br>
    <input type="submit" value="Submit">
</form>

<?php
// P2
if (!empty($errorMessage)) {
    echo "<p style='color: red;'>" . htmlspecialchars($errorMessage) . "</p>";
    echo "<p><strong>Примітка:</strong> Переконайтеся, що ви вказали правильні значення для API_KEY та CX.</p>";
} elseif (!empty($items)) {
    echo "<h3>Результати пошуку для: " . htmlspecialchars($searchQuery) . "</h3>";

    foreach ($items as $item) {
        echo "<div style='margin-bottom: 20px; border-bottom: 1px solid #ccc; padding-bottom: 10px;'>";

        // Заголовок з посиланням
        if (isset($item['title']) && isset($item['link'])) {
            echo "<h4><a href='" . htmlspecialchars($item['link']) . "' target='_blank'>" .
                htmlspecialchars($item['title']) . "</a></h4>";
        }

        // URL
        if (isset($item['displayLink'])) {
            echo "<p style='color: green; margin: 5px 0;'>" . htmlspecialchars($item['displayLink']) . "</p>";
        }

        // Опис
        if (isset($item['snippet'])) {
            echo "<p>" . htmlspecialchars($item['snippet']) . "</p>";
        }

        echo "</div>";
    }
} elseif (!empty($searchQuery)) {
    echo "<p>Результатів не знайдено.</p>";
}
?>
</body>
</html>