<?php
include "db.php";

// Fetch retailer data
$retailerSql = "SELECT * FROM retailer";
$retailerResult = $conn->query($retailerSql);

// Fetch wholesaler data
$wholesalerSql = "SELECT * FROM wholesaler";
$wholesalerResult = $conn->query($wholesalerSql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Directory</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    .table-container {
      margin-bottom: 3rem;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #f0f8ff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    table th,
    table td {
      padding: 1.5rem 2rem;
      text-align: left;
      font-size: 16px;
      font-weight: 500;
    }

    table th {
      background-color: #00bcd4;
      color: #fff;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border-bottom: 2px solid #0097a7;
    }

    table td {
      background: #ffffff;
      color: #333;
      border-bottom: 1px solid #e0f7fa;
    }

    table tr:nth-child(even) {
      background: #e0f7fa;
    }

    table tr:hover {
      background: #00bcd4;
      color: #fff;
      transform: scale(1.02);
      transition: background 0.3s ease, transform 0.2s ease;
    }

    table tr:last-child td {
      border-bottom: none;
    }

    table th,
    table td {
      border-right: 1px solid rgba(0, 188, 212, 0.2);
    }

    table td:last-child {
      border-right: none;
    }

    /* Button styling */
    .btn-container {
      text-align: center;
      margin-top: 20px;
    }

    .btn {
      padding: 12px 25px;
      font-size: 16px;
      text-align: center;
      color: white;
      background-color: #4CAF50;
      border-radius: 8px;
      text-decoration: none;
      cursor: pointer;
      transition: background-color 0.3s ease;
      border: none;
    }

    .btn:hover {
      background-color: #45a049;
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {

      table th,
      table td {
        padding: 1rem;
        font-size: 14px;
      }

      .table-container {
        margin: 2rem;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <h2>Retailer and Wholesaler Directory</h2>

    <!-- Retailer Table -->
    <div class="table-container">
      <h3>Retailers</h3>
      <table>
        <thead>
          <tr>
            <th>Retailer ID</th>
            <th>Retailer Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($retailerResult->num_rows > 0) {
            while ($row = $retailerResult->fetch_assoc()) {
              echo "<tr>
                      <td>" . $row['RetailerID'] . "</td>
                      <td>" . $row['name'] . "</td>
                      <td>" . $row['area'] . "</td>
                      <td>" . $row['city'] . "</td>
                      <td>" . $row['contact'] . "</td>
                    </tr>";
            }
          } else {
            echo "<tr><td colspan='5'>No retailers found.</td></tr>";
          }
          ?>
        </tbody>
      </table>

      <!-- Add Retailer Button -->
      <div class="btn-container">
        <a href="addRetailer.php" class="btn">Add Retailer</a>
      </div>
    </div>

    <!-- Wholesaler Table -->
    <div class="table-container">
      <h3>Wholesalers</h3>
      <table>
        <thead>
          <tr>
            <th>Wholesaler ID</th>
            <th>Wholesaler Name</th>
            <th>Distribution Area</th>
            <th>Area</th>
            <th>City</th>
            <th>Contact</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($wholesalerResult->num_rows > 0) {
            while ($row = $wholesalerResult->fetch_assoc()) {
              echo "<tr>
                      <td>" . $row['WholesalerID'] . "</td>
                      <td>" . $row['name'] . "</td>
                      <td>" . $row['DistributionArea'] . "</td>
                      <td>" . $row['area'] . "</td>
                      <td>" . $row['city'] . "</td>
                      <td>" . $row['contact'] . "</td>
                    </tr>";
            }
          } else {
            echo "<tr><td colspan='6'>No wholesalers found.</td></tr>";
          }
          ?>
        </tbody>
      </table>

      <!-- Add Wholesaler Button -->
      <div class="btn-container">
        <a href="addWholesaler.php" class="btn">Add Wholesaler</a>
      </div>
    </div>
  </div>
</body>

</html>

<?php
// Close connection
$conn->close();
?>