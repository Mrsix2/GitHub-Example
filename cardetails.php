<?php 
session_start();
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$host = "localhost";
$username = "root";
$password = "";
$dbname = "kktcrentalcar";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Veritabanı Bağlantı Hatası: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $car_id = $_GET['id'];

    if (isset($_POST['rent'])) {
        if (!isset($_SESSION['FullName'])) {
            header("Location: login.php");
            exit();
        } else {
            $user_id = $_SESSION['userID'];
            $start_date = date("Y-m-d");
            $end_date = $_POST['endDate'];

            if ($end_date < $start_date) {
                echo "<script>alert('Bitiş tarihi, başlangıç tarihinden önce olamaz.');</script>";
            } else {
                $daily_price_query = "SELECT DailyPrice FROM Car WHERE CarID = $car_id";
                $result = mysqli_query($conn, $daily_price_query);
                $car = mysqli_fetch_assoc($result);
                $daily_price = $car['DailyPrice'];

                $datetime1 = new DateTime($start_date);
                $datetime2 = new DateTime($end_date);
                $interval = $datetime1->diff($datetime2);
                $days_rented = $interval->days + 1;

                $total_price = $daily_price * $days_rented;

                $rental_query = "INSERT INTO Rental (CarID, UserID, StartDate, EndDate, TotalPrice) 
                                 VALUES ($car_id, $user_id, '$start_date', '$end_date', $total_price)";
                
                $update_query = "UPDATE Car SET IsAvailable = 0 WHERE CarID = $car_id";

                if (mysqli_query($conn, $rental_query) && mysqli_query($conn, $update_query)) {
                    echo "<script>alert('Araç başarıyla kiralandı.');</script>";
                } else {
                    echo "<script>alert('Kiralama işlemi başarısız oldu.');</script>";
                }
            }
        }
    }

    $query = "
    SELECT Car.CarID, Brand.BrandName, Car.Model, CarClass.ClassName, City.CityName, Car.Transmission, Car.DailyPrice, Car.Image, Car.IsAvailable, Car.LicensePlate 
    FROM Car
    JOIN Brand ON Car.BrandID = Brand.BrandID
    JOIN CarClass ON Car.Class = CarClass.ClassID
    JOIN City ON Car.City = City.CityID
    WHERE Car.CarID = $car_id
    ";

    $result = mysqli_query($conn, $query);

    if ($car = mysqli_fetch_assoc($result)) {
        $availability = $car['IsAvailable'] ? "Mevcut" : "Mevcut Değil";
    } else {
        echo "Araç bulunamadı.";
    }
} else {
    echo "Geçersiz araç ID'si.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Araç Bilgileri</title>
    <link rel="stylesheet" href="cardetailsstyle.css">
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
<br><br><br><br><br><br><br><br>

<?php if ($car): ?>
<div class='card'>
    <img src='data:image/jpeg;base64,<?php echo base64_encode($car['Image']); ?>' alt='<?php echo $car['Brand']; ?>' />
    <div class='details'>
        <h2><?php echo $car['BrandName'] . " " . $car['Model']; ?></h2>
        <p>Sınıfı: <?php echo $car['ClassName']; ?></p>
        <p>Vites Türü: <?php echo $car['Transmission']; ?></p>
        <p>Ücret: ₺<?php echo $car['DailyPrice']; ?> Günlük</p>
        <p>Şehir: <?php echo $car['CityName']; ?></p>
        <p>Durum: <?php echo $availability; ?></p>
        <p>Plaka: <?php echo $car['LicensePlate']; ?></p>
        <?php if ($car['IsAvailable']): ?>
            <form method="POST">
                <label for="endDate">Bitiş Tarihi:</label>
                <input type="date" id="endDate" name="endDate" class="textbox" required><br><br>
                <button class='rent-button' type='submit' name='rent'>Rent</button>
            </form>
        <?php else: ?>
            <p>Bu araç şu anda kiralanamaz.</p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

</body>
</html>