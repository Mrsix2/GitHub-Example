<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Sayfası</title>
    <style>
        body {
            background-size: cover;
            background-position: center;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .overlay {
            background-color: rgba(255, 255, 255, 0.8);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .divbox {
            background-color: #f2f2f2;
            padding: 30px;
            width: 400px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            text-align: left;
        }
        .divbox h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .textbox {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .textbox:focus {
            border-color: #00aaff;
            outline: none;
            box-shadow: 0 0 8px rgba(0, 170, 255, 0.5);
        }
        .button {
            width: 100%;
            padding: 12px;
            background-color: #00aaff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .button:hover {
            background-color: #0088cc;
        }
        .secondary-button {
            background-color: lightgray;
            color: black;
            margin-top: 10px;
        }
        .secondary-button:hover {
            background-color: gray;
            color: white;
        }
        a {
            text-decoration: none;
            color: #00aaff;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        .message {
            margin-top: 15px;
            font-weight: bold;
            color: green;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="overlay">
        <div class="divbox">
            <h2>Kayıt Ol</h2>
            <form method="post" enctype="multipart/form-data">
                <label for="FullName">Tam İsim:</label>
                <input type="text" id="FullName" name="FullName" required class="textbox">

                <label for="kimlikNo">Kimlik:</label>
                <input type="text" id="kimlikNo" name="kimlikNo" required class="textbox">

                <label for="Email">Email:</label>
                <input type="email" id="Email" name="Email" required class="textbox">

                <label for="PhoneNumber">Tel NO:</label>
                <input type="text" id="PhoneNumber" name="PhoneNumber" required class="textbox" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                
                <input type="submit" name="kayit" value="Kayıt Ol" class="button">
                <input type="button" value="Geri" class="button secondary-button" onclick="redirectToPage()">
                
                <br><br>Zaten Bir Hesabın Varmı? <a href="login.php">Giriş Yap</a>
            </form>
        </div>
    </div>

    <script>
        function redirectToPage() {
            window.location.href = "index.php";
        }
    </script>

    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "kktcrentalcar";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $FullName = $_POST['FullName'];
        $kimlikNo = $_POST['kimlikNo'];
        $Email = $_POST['Email'];
        $PhoneNumber = $_POST['PhoneNumber'];

        $conn = new mysqli($servername, $username, $password, $database);

        if ($conn->connect_error) {
            die("Bağlantı hatası: " . $conn->connect_error);
        }

        $sql = "INSERT INTO users (FullName, kimlikNo, Email, PhoneNumber)
                VALUES ('$FullName', '$kimlikNo', '$Email', '$PhoneNumber')";

        if ($conn->query($sql) === TRUE) {
            echo "<div class='message'>Kayıt başarıyla eklendi</div>";
        } else {
            echo "<div class='message' style='color:red;'>Hata: " . $sql . "<br>" . $conn->error . "</div>";
        }

        $conn->close();
    }
    ?>
</body>
</html>
