<?php
class DataExporter
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
            die("Connection failed: " . $this->conn->connect_error);
        }
    }
//Metoda de export nume si email in fisier.
    public function exportData()
    {
        $sql = "SELECT email, name FROM data";
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            $filename = "export.csv";
            $fp = fopen('php://output', 'w');

            fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));

            $header = array('Email', 'Name');
            fputcsv($fp, $header);

            while ($row = $result->fetch_assoc()) {
                array_walk($row, function (&$item, $key) {
                    $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
                });
                fputcsv($fp, $row);
            }
            fclose($fp);

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            exit();
        } else {
            echo "No data available to export.";
        }

        $this->conn->close();
    }
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "register";

$dataExporter = new DataExporter($servername, $username, $password, $dbname);
$dataExporter->exportData();
?>