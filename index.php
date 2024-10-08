<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "kktcrentalcar");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$filters = [];
$query = "
    SELECT Car.CarID, Brand.BrandName, Car.Model, CarClass.ClassName, City.CityName, Car.Transmission, Car.DailyPrice, Car.Image, Car.IsAvailable 
    FROM Car
    JOIN Brand ON Car.BrandID = Brand.BrandID
    JOIN CarClass ON Car.Class = CarClass.ClassID
    JOIN City ON Car.City = City.CityID
    WHERE 1=1
";

if (isset($_POST['submit'])) {
    if (!empty($_POST['brand'])) {
        $brand = $conn->real_escape_string(trim($_POST['brand']));
        $filters[] = "Brand.BrandName LIKE '%$brand%'";
    }
    
    if (!empty($_POST['class'])) {
        $class = $conn->real_escape_string(trim($_POST['class']));
        $filters[] = "CarClass.ClassName = '$class'";
    }
    
    if (!empty($_POST['transmission'])) {
        $transmission = $conn->real_escape_string($_POST['transmission']);
        $filters[] = "Car.Transmission = '$transmission'";
    }
    
    if (!empty($_POST['city'])) {
        $city = $conn->real_escape_string(trim($_POST['city']));
        $filters[] = "City.CityName = '$city'";
    }

    if (isset($_POST['available'])) {
        $filters[] = "Car.IsAvailable = 1";
    }
}

if (count($filters) > 0) {
    $query .= " AND " . implode(" AND ", $filters);
}

$classQuery = "SELECT ClassID, ClassName FROM CarClass";
$classResult = $conn->query($classQuery);

$cityQuery = "SELECT CityID, CityName FROM City";
$cityResult = $conn->query($cityQuery);

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anasayfa</title>
    <link rel="icon" href="images/logo.png" type="image/png">
    <link rel="stylesheet" href="indexstyle.css">
</head>
<body>
<nav class="navbar">
    <h1><a href="index.php" class="logout">KKTCRentalCar</a></h1>
    <h1>
        <?php if(isset($_SESSION['FullName'])): ?>
            <span><?php echo ($_SESSION['FullName']); ?>,</span>
            <a href="logout.php" class="logout" onclick="return confirm('Çıkış Yapılsın mı?');">Çıkış Yap</a>
        <?php else: ?>
            <a href="login.php" class="login">Giriş Yap</a>
        <?php endif; ?>
    </h1>
</nav>
<form method="post">
    <div class="container">
        <div class="sidebar">
            <h3>Filtreler</h3>
            
            <label for="brand">Marka:</label><br>
            <input type="text" name="brand" id="brand"><br><br>
            <hr>
            
            <label for="class">Sınıf:</label><br>
            <select name="class" id="class">
                <option value="">Seçiniz</option>
                <?php while ($row = mysqli_fetch_assoc($classResult)): ?>
                    <option value="<?php echo $row['ClassName']; ?>">
                        <?php echo $row['ClassName']; ?>
                    </option>
                <?php endwhile; ?>
            </select><br><br>
            <hr>

            <label for="city">Şehir:</label><br>
            <select name="city" id="city">
                <option value="">Seçiniz</option>
                <?php while ($row = mysqli_fetch_assoc($cityResult)): ?>
                    <option value="<?php echo $row['CityName']; ?>">
                        <?php echo $row['CityName']; ?>
                    </option>
                <?php endwhile; ?>
            </select><br><br>
            <hr>

            <label>Vites Türü:</label><br>
            <input type="radio" name="transmission" value="Otomatik" id="automatic">
            <label for="automatic">Otomatik</label><br>
            <input type="radio" name="transmission" value="Düz" id="manual">
            <label for="manual">Düz</label><br><br>

            <input type="checkbox" name="available" id="available">
            <label for="available">Sadece Mevcut Olanlar</label><br><br>

            <button type="submit" name="submit">Filtrele</button>
        </div>

    <div class="divbox">
        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="car" onclick="goToDetails(<?php echo $row['CarID']; ?>)">
                <h2 class="<?php echo $row['IsAvailable'] ? 'available' : 'not-available'; ?>">
                    <?php echo ($row['BrandName']) . " " . ($row['Model']); ?>
                </h2>
                <p>Sınıfı: <?php echo ($row['ClassName']); ?></p>
                <p>Vites: <?php echo ($row['Transmission']); ?></p>
                <p>Günlük Ücret: ₺<?php echo ($row['DailyPrice']); ?></p>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($row['Image']); ?>" alt="Car Image">
                <p class="<?php echo $row['IsAvailable'] ? 'available' : 'not-available'; ?>">
                    <?php echo $row['IsAvailable'] ? 'Mevcut' : 'Mevcut Değil'; ?>
                </p>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</form>
<script>
    function goToDetails(carId) {
        window.location.href = 'cardetails.php?id=' + carId;
    }
</script>
</body>
</html>

<?php
mysqli_close($conn);
?>