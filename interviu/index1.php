<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Iomundo Form</title>
  </head>
  <body>
    <!-- Form fara framework -->
    <div class="container-1">
      <div class="elemente">
      <div class="poza">
      <img src="poza.jpg" alt="">
      </div>
      <div class="form-container">
        <form class="form-style" action="" method="post" enctype="multipart/form-data">
          <div class="form-text">
            <label for="">Email:</label>
            <input type="email" name="email" placeholder="Enter your email" />
            <label for="">Name</label>
            <input type="text" name="name" placeholder="Enter your name" />
            <label for="">Image</label>
            <input
            type="file"
            name="fileToUpload"
            />
          </div>
          <div class="form-btn">
            <input type="checkbox" name="consent" id="" />
            <label for="">Consent</label>
          </div>
          <input type="submit" value="Submit" name="submit" />
        </form>
        </div>
      </div>

      <?php
class FormProcessor
{
    private $servername;
    private $username;
    private $password;
    private $dbname;
    private $conn;

    public function __construct($servername, $username, $password, $dbname)
    {
        $this->servername = $servername;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;

        // Create connection
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);

        // Check connection
        if ($this->conn->connect_error) {
            throw new Exception("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function processForm($email, $name, $consent, $image)
    {
        try {
            if (empty($email) || empty($name)) {
                throw new Exception('All fields are required!');
            }

            if (!$consent) {
                throw new Exception("Bad Request!");
            }

            list($width, $height) = getimagesize($image);

            // Check image resolution
            if ($width > 500 || $height > 500) {
                $newWidth = $width;
                $newHeight = $height;
                if ($width > $height) {
                    $newWidth = 500;
                    $newHeight = $height * (500 / $width);
                } else {
                    $newHeight = 500;
                    $newWidth = $width * (500 / $height);
                }

                $newImage = imagecreatetruecolor($newWidth, $newHeight);
                $source = imagecreatefromjpeg($image);
                imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                ob_start();
                imagejpeg($newImage, null, 80);
                $imgContent = ob_get_contents();
                ob_end_clean();
            } else {
                $imgContent = file_get_contents($image);
            }

            $imgContent = addslashes($imgContent);

            $sql = "INSERT INTO data (email, name, image) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sss", $email, $name, $imgContent);

            if ($stmt->execute() === TRUE) {
                $this->conn->close();
                header('Location: display_data.php');
                exit;
            } else {
                throw new Exception("Error: " . $sql . "<br>" . $this->conn->error);
            }
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $name = $_POST['name'] ?? '';
    $consent = isset($_POST['consent']) ? 1 : 0;
    $image = $_FILES['fileToUpload']['tmp_name'];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "register";

    $formProcessor = new FormProcessor($servername, $username, $password, $dbname);
    $formProcessor->processForm($email, $name, $consent, $image);
}
?>


    </div>
    <script src="script.js"></script>
  </body>
</html>
