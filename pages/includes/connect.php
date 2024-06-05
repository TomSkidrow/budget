<?php 
    error_reporting(E_ALL); // เปิด Error ทั้งหมด
    //error_reporting(0); // ปิด error 
    
    $conn = new mysqli('localhost','root','','budget');
    
    $conn->set_charset('utf8'); 
    if ($conn->connect_errno) {
        echo "Connect Error :".$conn->connect_error; 
        exit(); 
    }
    
    date_default_timezone_set('Asia/Bangkok');
?>