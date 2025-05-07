<?php
$conn = new mysqli("localhost", "root", "", "meatsupplydb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch types
$types = $conn->query("SELECT DISTINCT Type FROM meat_product");

// AJAX: get cuts
if (isset($_GET['Type']) && !isset($_GET['Cut'])) {
    $type = $_GET['Type'];
    $result = $conn->query("SELECT DISTINCT Cut FROM meat_product WHERE Type = '$type'");
    $cuts = [];
    while ($row = $result->fetch_assoc()) {
        $cuts[] = $row['Cut'];
    }
    echo json_encode($cuts);
    exit;
}

// AJAX: get matching records
if (isset($_GET['Type']) && isset($_GET['Cut'])) {
    $type = $_GET['Type'];
    $cut = $_GET['Cut'];
    $result = $conn->query("SELECT Price, Seasonality FROM meat_product WHERE Type = '$type' AND Cut = '$cut'");
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    echo json_encode($rows);
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $type = $_POST['type'];
    $cut = $_POST['cut'];
    $price = $_POST['price'];
    $seasonality = $_POST['seasonality'];

    $stmt = $conn->prepare("INSERT INTO meat_product (Type, Cut, Price, Seasonality) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $type, $cut, $price, $seasonality);
    $stmt->execute();
    $stmt->close();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Meat Product Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 8px 12px; border: 1px solid #ccc; }
        label { display: inline-block; width: 100px; }
        input, select { padding: 5px; margin: 5px 0; }
        canvas { max-width: 600px; margin-top: 20px; }
    </style>
</head>
<body>
    <h2>🔍 Meat Product Lookup</h2>
    <label for="meatType">Meat Type:</label>
    <select id="meatType" onchange="updateCuts()">
        <option disabled selected>Select Meat Type</option>
        <?php while ($row = $types->fetch_assoc()): ?>
            <option value="<?= htmlspecialchars($row['Type']) ?>"><?= htmlspecialchars($row['Type']) ?></option>
        <?php endwhile; ?>
    </select>

    <br><br>

    <label for="meatCut">Meat Cut:</label>
    <select id="meatCut" onchange="updatePrice()">
        <option disabled selected>Select Cut</option>
    </select>

    <p id="price">Matching records will appear here.</p>
    <canvas id="priceChart"></canvas>

    <hr>

    <h2>➕ Add New Meat Product</h2>
    <form method="POST" action="">
        <label>Type:</label>
        <input type="text" name="type" required><br>

        <label>Cut:</label>
        <input type="text" name="cut" required><br>

        <label>Price:</label>
        <input type="number" name="price" step="0.01" required><br>

        <label>Seasonality:</label>
        <input type="text" name="seasonality" required><br><br>

        <button type="submit">Add Product</button>
    </form>

    <script>
        let chartInstance = null;

        function updateCuts() {
            const type = document.getElementById("meatType").value;
            fetch(`?Type=${type}`)
                .then(res => res.json())
                .then(data => {
                    const cutSelect = document.getElementById("meatCut");
                    cutSelect.innerHTML = "<option disabled selected>Select Cut</option>";
                    data.forEach(cut => {
                        const option = document.createElement("option");
                        option.value = cut;
                        option.text = cut;
                        cutSelect.appendChild(option);
                    });
                });
        }

        function updatePrice() {
            const type = document.getElementById("meatType").value;
            const cut = document.getElementById("meatCut").value;

            fetch(`?Type=${type}&Cut=${cut}`)
                .then(res => res.json())
                .then(data => {
                    // Table
                    let output = "<strong>Matching Records:</strong><br><table><tr><th>Price</th><th>Seasonality</th></tr>";
                    data.forEach(row => {
                        output += `<tr><td>${row.Price}</td><td>${row.Seasonality}</td></tr>`;
                    });
                    output += "</table>";
                    document.getElementById("price").innerHTML = output;

                    // Chart
                    const labels = data.map((_, index) => `Entry ${index + 1}`);
                    const prices = data.map(row => row.Price);

                    if (chartInstance) chartInstance.destroy();

                    const ctx = document.getElementById('priceChart').getContext('2d');
                    chartInstance = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: `Price Trend for ${type} - ${cut}`,
                                data: prices,
                                borderColor: 'rgba(75, 192, 192, 1)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                fill: true,
                                tension: 0.3,
                                pointRadius: 4,
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { display: true }},
                            scales: {
                                x: { title: { display: true, text: 'Entry Order' }},
                                y: { title: { display: true, text: 'Price' }, beginAtZero: false }
                            }
                        }
                    });
                });
        }
    </script>
</body>
</html>