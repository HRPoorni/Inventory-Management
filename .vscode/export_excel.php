<?php
require 'vendor/autoload.php';
require 'db_connection.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setTitle('Inventory');
$sheet->setCellValue('A1', 'ID');
$sheet->setCellValue('B1', 'Item Name');
$sheet->setCellValue('C1', 'Description');
$sheet->setCellValue('D1', 'Quantity');

$query = $conn->query("SELECT * FROM inventory");
$rowIndex = 2;

while ($row = $query->fetch_assoc()) {
    $sheet->setCellValue("A{$rowIndex}", $row['id']);
    $sheet->setCellValue("B{$rowIndex}", $row['item_name']);
    $sheet->setCellValue("C{$rowIndex}", $row['description']);
    $sheet->setCellValue("D{$rowIndex}", $row['quantity']);
    $rowIndex++;
}

$writer = new Xlsx($spreadsheet);
$filename = 'inventory_report.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
?>
