<?php
// /dashboard.php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

require_login(); // Protect this page

// Get unique locations for the search dropdowns
$locations_query = "SELECT DISTINCT departure_location FROM flight_routes UNION SELECT DISTINCT arrival_location FROM flight_routes ORDER BY departure_location ASC";
$locations_result = $conn->query($locations_query);
$locations = [];
while($row = $locations_result->fetch_assoc()) {
    $locations[] = $row['departure_location'];
}

// Handle search submission
$search_results = [];
if (isset($_GET['from']) && isset($_GET['to'])) {
    $from = $_GET['from'];
    $to = $_GET['to'];
    $stmt = $conn->prepare("SELECT * FROM flight_routes WHERE departure_location = ? AND arrival_location = ?");
    $stmt->bind_param("ss", $from, $to);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $search_results[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Airline Booking</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background-image: url('assets/images/dashboard_background.jpg');
            background-color: #eaf6ff;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .navbar {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); }
        .modal-content { background-color: #fefefe; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 700px; border-radius: 8px; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .logo img {
            height: 50px;
            vertical-align: middle;
        }
        .logo span {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        /* --- Updated Nav Link Styles with !important --- */
        .nav-links span {
            color: #555555 !important; /* Gray for "Welcome" text */
            margin-right: 15px;
        }
        .nav-links a {
            color: #000000 !important; /* Black for links */
            text-decoration: none;
            margin-left: 15px;
        }
        .nav-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">
        <img src="assets/images/logo.jpg" alt="SkyBooker Logo">
        <span>SkyBooker</span>
    </div>
    <div class="nav-links">
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Find Your Flight</h2>
    <div class="card">
        <form action="dashboard.php" method="GET">
            <div style="display: flex; gap: 20px;">
                <div class="form-group" style="flex: 1;">
                    <label for="from">From</label>
                    <select id="from" name="from" required>
                        <option value="">Select Departure</option>
                        <?php foreach($locations as $loc): ?>
                        <option value="<?php echo htmlspecialchars($loc); ?>"><?php echo htmlspecialchars($loc); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="to">To</label>
                    <select id="to" name="to" required>
                        <option value="">Select Arrival</option>
                        <?php foreach($locations as $loc): ?>
                        <option value="<?php echo htmlspecialchars($loc); ?>"><?php echo htmlspecialchars($loc); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn">Search Flights</button>
        </form>
    </div>

    <?php if (!empty($search_results)): ?>
    <div class="card" style="margin-top: 30px;">
        <h3>Available Routes</h3>
        <table>
            <thead><tr><th>Flight Name</th><th>Route</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach($search_results as $route): ?>
                <tr>
                    <td><?php echo htmlspecialchars($route['flight_name']); ?></td>
                    <td><?php echo htmlspecialchars($route['departure_location']) . ' to ' . htmlspecialchars($route['arrival_location']); ?></td>
                    <td><button class="btn" onclick="viewSchedules(<?php echo $route['route_id']; ?>)">View Dates & Prices</button></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php elseif (isset($_GET['from'])): ?>
    <div class="card" style="margin-top: 30px;"><p>No flights found for the selected route.</p></div>
    <?php endif; ?>
</div>

<div id="schedulesModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <h3>Select Date and Class</h3>
    <div id="schedulesContent">
        </div>
  </div>
</div>

<script src="assets/js/script.js"></script>
</body>
</html>