<?php

require_once('bootstrap.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Src\Controller\AdminController;

class Broadsheet
{
    private $admin = null;
    private $dataSheet = [];
    private $admin_period = null;
    public $admission_data = null;

    public function __construct($admin_period)
    {
        $db   = getenv('DB_ADMISSION_DATABASE');
        $user = getenv('DB_ADMISSION_USERNAME');
        $pass = getenv('DB_PASSWORD');

        $this->admin = new AdminController($db, $user, $pass);
        $this->admin_period = $admin_period;
    }

    public function prepareBSData()
    {
        $this->admission_data = $this->admin->getAcademicPeriod($this->admin_period);
        $awaitingApps = $this->admin->fetchAllAwaitingApplicationsBS($this->admin_period);
        $awaitingAppsGrp = $this->admin->fetchAllAwaitingApplicationsBSGrouped($this->admin_period);
        if (empty($awaitingApps) || empty($awaitingAppsGrp)) return 0;
        if (empty($this->admin->saveDownloadedAwaitingResults($awaitingApps))) return 0;
        $this->dataSheet = array("awaitingApps" => $awaitingApps, "awaitingAppsGrp" => $awaitingAppsGrp);
        return 1;
    }

    public function generateFile(): mixed
    {
        if ($this->prepareBSData()) {
            $count = 0;

            $zip = new ZipArchive();
            $zipFileName = $this->admission_data[0]["info"]; // The name of the zip file you want to create
            if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {

                foreach ($this->dataSheet["awaitingAppsGrp"] as $grp) {
                    $sanitizedFileName = str_replace('/', '_', $grp["Program"]);
                    $sanitizedFileName = preg_replace('/[^A-Za-z0-9_. -]/', '', $sanitizedFileName);
                    $sanitizedFileName = trim($sanitizedFileName);

                    $fileName = "{$sanitizedFileName} - {$zipFileName}";
                    $spreadsheet = new Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    //$writer = new Xlsx($spreadsheet);
                    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);

                    //$this->formatSpreadsheet();
                    $sheet->setCellValue('A1', "AdmissionNumber");
                    $sheet->setCellValue('B1', "IndexNumber");
                    $sheet->setCellValue('C1', "ExamMonth");
                    $sheet->setCellValue('D1', "ExamYear");
                    $sheet->getColumnDimension('A')->setAutoSize(true);
                    $sheet->getColumnDimension('B')->setAutoSize(true);
                    $sheet->getColumnDimension('C')->setAutoSize(true);
                    $sheet->getColumnDimension('D')->setAutoSize(true);
                    $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal('center');

                    $row = 2;

                    foreach ($this->dataSheet["awaitingApps"] as $appData) {
                        if ($grp["Program"] == $appData["Program"]) {
                            $sheet->setCellValue("A" . $row, $appData["AdmissionNumber"]);
                            $sheet->setCellValue("B" . $row, $appData["IndexNumber"]);
                            $sheet->setCellValue("C" . $row, in_array($appData["ExamMonth"], ["May", "Jun", "Jul", "Aug", "Sep"]) ? 6 : 12);
                            $sheet->setCellValue("D" . $row, $appData["ExamYear"]);
                            $sheet->getStyle("A" . $row . ":D" . $row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                            $row += 1;
                        }
                    }

                    // Save spreadsheet file
                    $filepath = "awaiting_results/" . $fileName . '.xls';
                    if (file_exists($filepath)) unlink($filepath);
                    $writer->save($filepath);
                    $spreadsheet->disconnectWorksheets();

                    // Add files to the zip archive
                    $zip->addFile($filepath);
                    $count += 1;
                }
            } else {
                echo 'Failed to create the zip archive';
            }
            unset($spreadsheet);

            // Close the zip archive
            $zip->close();

            return array("fileCount" => $count, "zipFile" => $zipFileName);
        }
        return 0;
    }

    public function downloadFile($file)
    {
        header('Content-Type:application/zip');
        header("Content-Transfer-Encoding:utf-8");
        header("Content-disposition:attachment;filename=\"" . basename($file) . "\"");
        readfile($file);
        unlink($file);
    }
}

$broadsheet = new Broadsheet($_GET["ap"]);
$result = $broadsheet->generateFile();
if (!empty($result)) $broadsheet->downloadFile($result["zipFile"]);
