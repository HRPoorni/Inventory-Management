<?php
//require('fpdf.php');
//require 'db_connection.php'; // your DB connection file

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Inventory Report', 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Table Headers
$pdf->Cell(10, 10, 'ID', 1);
$pdf->Cell(50, 10, 'Item Name', 1);
$pdf->Cell(90, 10, 'Description', 1);
$pdf->Cell(30, 10, 'Quantity', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
$result = $conn->query("SELECT * FROM inventory");

while ($row = $result->fetch_assoc()) {
    $pdf->Cell(10, 10, $row['id'], 1);
    $pdf->Cell(50, 10, $row['item_name'], 1);
    $pdf->Cell(90, 10, $row['description'], 1);
    $pdf->Cell(30, 10, $row['quantity'], 1);
    $pdf->Ln();
}

$pdf->Output('D', 'inventory_report.pdf'); // download file
?>
