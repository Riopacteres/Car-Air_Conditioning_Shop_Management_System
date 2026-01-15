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
        color: #080808;
    }
    table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 10px;
        box-shadow: 0 4px 12px rgba(14, 3, 3, 0.1);
        margin-left: auto;
        margin-right: auto;
    }
    th, td {
        border: 1px solid #f3ebeb;
        padding: 0px;
        text-align: center;
    }
    th {
        background-color: blue;
        color: white;
        background: white;
    border: none;
    color: rgb(247, 246, 246);
    outline: none;
    margin-left: 8px;   
    width: 20%;
    font-size: 30px;
    padding: 0px;
    border-radius: 0px;
    background: #0a43b4ff;
    }
    
    td {
        background-color: #fefefe;
        background: white;
    border: 1px solid #050505;
    color: #060a11ff;
    outline: none;
    margin-left: 5px;   
    width: 20%;
    font-size: 16px;
    padding: 5px;
    border-radius: 2px;
    background: rgb(243, 243, 250);

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    tr:hover {
        background-color: #f1f1f1;
    }
       
</style>
";

?>