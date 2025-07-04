<?php
require 'db_connection.php';
require 'PHPExcel.php';

$objPHPExcel = new PHPExcel();
$objPHPExcel->setActiveSheetIndex(0);
$sheet = $objPHPExcel->getActiveSheet();
$sheet->setCellValue('A1', 'ID')
      ->setCellValue('B1', 'Item Name')
      ->setCellValue('C1', 'Description')
      ->setCellValue('D1', 'Quantity');

$stmt = $conn->prepare("SELECT * FROM inventory");
$stmt->execute();
$result = $stmt->get_result();

$rowNumber = 2;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNumber, $row['id'])
          ->setCellValue('B' . $rowNumber, $row['item_name'])
          ->setCellValue('C' . $rowNumber, $row['description'])
          ->setCellValue('D' . $rowNumber, $row['quantity']);
    $rowNumber++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="inventory_report.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>
