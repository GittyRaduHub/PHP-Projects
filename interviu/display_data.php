<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="display_data.css">
    <title>Afisare Date</title>
</head>
<body>
    <table>
        <tr>
            <th>Email</th>
            <th>Name</th>
            <th>Image</th>
        </tr>

        <?php
        class DataDisplayer
//Probabil puteam sa folosesc mostenide "class x extends y" ca sa scriu mai putin cod. Dar mi s-a parut mult mai citet asa pentru a arata cum am gandit overall.
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

                $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);

                if ($this->conn->connect_error) {
                    throw new Exception("Connection failed: " . $this->conn->connect_error);
                }
            }
//Metoda de afisare a datelor ca si elemente din tabel
            public function displayData()
            {
                $sql = "SELECT * FROM data";
                $result = $this->conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr><td>" . $row["email"] . "</td><td>" . $row["name"] . "</td><td><img src='data:image/jpeg;base64," . base64_encode($row['image']) . "' alt='" . $row["email"] . " Image' style='width:100px;height:100px;'></td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>0 results</td></tr>";
                }
                $this->conn->close();
            }
        }

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "register";

        try {
            $dataDisplayer = new DataDisplayer($servername, $username, $password, $dbname);
            $dataDisplayer->displayData();
        } catch (Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
        ?>
    </table>
    <form action="export.php" method="post">
        <input type="submit" name="export" value="Export to CSV">
    </form>
    <button><a href="index.php">Register Page</a></button>
</body>
</html>
