<?php

require_once('bootstrap.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Src\Controller\AdminController;

class Broadsheet
{
    private $spreadsheet = null;
    private $writer = null;
    private $admin = null;
    private $sheet = null;
    private $dataSheet = [];
    private $fileName = null;

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
        $this->writer = new Xlsx($this->spreadsheet);
        $this->admin = new AdminController();
    }

    public function prepareBSData()
    {
        $awaitingApps = $this->admin->fetchAllAwaitingApplicationsBS();
        if (empty($awaitingApps)) return 0;
        $this->dataSheet = $awaitingApps;
        return 1;
    }

    public function formatSpreadsheet()
    {
        $this->sheet->setCellValue('A1', "AdmissionNumber");
        $this->sheet->setCellValue('B1', "IndexNumber");
        $this->sheet->setCellValue('C1', "ExamMonth");
        $this->sheet->setCellValue('D1', "ExamYear");

        $this->sheet->getColumnDimension('A')->setAutoSize(true);
        $this->sheet->getColumnDimension('B')->setAutoSize(true);
        $this->sheet->getColumnDimension('C')->setAutoSize(true);
        $this->sheet->getColumnDimension('D')->setAutoSize(true);

        $this->sheet->getStyle('A1:D1')->getAlignment()->setHorizontal('center');
    }

    private function makeSpreadsheetContent($datasheet)
    {
        $row = 2;
        foreach ($datasheet as $data) {
            $this->sheet->setCellValue("A" . $row, $data["AdmissionNumber"]);
            $this->sheet->setCellValue("B" . $row, $data["IndexNumber"]);
            $this->sheet->setCellValue("C" . $row, $data["ExamMonth"]);
            $this->sheet->setCellValue("D" . $row, $data["ExamYear"]);
            $row += 1;
        }
    }

    private function saveSpreadsheetFile($filename)
    {
        $file = $filename . '.xlsx';

        if (file_exists($file)) {
            unlink($file);
        }
        $this->writer->save($file);
        $this->spreadsheet->disconnectWorksheets();
        unset($this->spreadsheet);
    }

    public function createFileName()
    {
        $dateData = $this->admin->getAcademicPeriod();
        $this->fileName = "List of Applicants Awaiting results (";
        $this->fileName .= $dateData[0]["start_year"] . " - " . $dateData[0]["end_year"] . ")";
    }

    public function generateFile()
    {
        if ($this->prepareBSData()) {
            $this->createFileName();
            $this->formatSpreadsheet();
            $this->makeSpreadsheetContent($this->dataSheet);
            $this->saveSpreadsheetFile($this->fileName);
            return $this->fileName;
        }
    }

    public function downloadFile($file)
    {
        $file_url = './' . $file . ".xlsx";
        header('Content-Type:application/octet-stream');
        header("Content-Transfer-Encoding:utf-8");
        header("Content-disposition:attachment;filename=\"" . basename($file_url) . "\"");
        readfile($file_url);
    }
}

$broadsheet = new Broadsheet();
$file = $broadsheet->generateFile();
$broadsheet->downloadFile($file);
