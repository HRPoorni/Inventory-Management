<?php
require 'db_connection.php';
require 'fpdf.php';

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Inventory Report', 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

$stmt = $conn->prepare("SELECT * FROM inventory");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $pdf->Cell(40, 10, $row['item_name'], 1);
    $pdf->Cell(100, 10, $row['description'], 1);
    $pdf->Cell(30, 10, $row['quantity'], 1);
    $pdf->Ln();
}

$pdf->Output('D', 'inventory_report.pdf');
?>
