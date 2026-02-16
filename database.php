<?php
$servername = "localhost";
$username = "root";
$password = "";
$database_name = "pacteres_bucio_gerona_db";

$conn = mysqli_connect($servername, $username, $password, $database_name);


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error($conn));
} 
echo "
<style>
 
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        text-align: center;
    }
    h1 {
        color: #333;
    }
    table {
        border-collapse: collapse;
        width: 80%;
        margin-bottom: 30px;
        box-shadow: 0 2px 8px rgba(14, 3, 3, 0.1);
        margin-left: auto;
        margin-right: auto;
    }
    th, td {
        border: 1px solid #faf0f0ff;
        padding: 10px;
        text-align: center;
    }
    th {
        background-color: blue;
        color: white;
        background: white;
    border: none;
    color: #f7f2f2ff;
    outline: none;
    margin-left: 8px;   
    width: 20%;
    font-size: 16px;
    padding: 10px;
    border-radius: 1px;
    background: #0a43b4ff;
    }
    
    td {
        background-color: #fefefe;
        background: white;
    border: 1px solid #f0e8e8ff;
    color: #060a11ff;
    outline: none;
    margin-left: 5px;   
    width: 20%;
    font-size: 16px;
    padding: 10px;
    border-radius: 1px;
    background: #f5f5faff;

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    tr:hover {
        background-color: #f1f1f1;
    }
       
</style>
";

?>