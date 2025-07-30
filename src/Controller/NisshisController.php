<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\Table;
use cake\ORM\TableRegistry;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use setasign\Fpdi;
use Yasumi\Yasumi;

class NisshisController extends AppController
{
    public function index() {
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            
            // index2からリダイレクトされた場合
            $pdf = $this->request->getSession()->read('pdf');
            $this->set(compact('pdf'));
            $this->request->getSession()->delete('pdf');

            $usersTable = TableRegistry::get('Users');

            //日付の定義
            $this->set('years', array("2022"=>"2022","2023"=>"2023"));
            $this->set('months', array("01"=>"1","02"=>"2","03"=>"3","04"=>"4","05"=>"5","06"=>"6","07"=>"7","08"=>"8","09"=>"9","10"=>"10","11"=>"11","12"=>"12"));
            
            // スタッフの一覧を取得
            $staffs0 = $usersTable
            ->find('list',['keyField' => 'id','valueField' => 'name'])
            ->where(['Users.adminfrag'=>0,'Users.display'=>0])
            ->order(['Users.narabi'=>'ASC']);
            $useresults = $staffs0->toArray();
            $this->set("users",$useresults);
        } else {
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }
    }

    public function printout() {
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            
            $usersTable = TableRegistry::get('Users');

            // フォームの情報を取得
            $year = $this->request->getData('year');
            $month = $this->request->getData('month');
            $sdate = $this->request->getData('sdate');
            $edate = $this->request->getData('edate');
            $user_id = $this->request->getData('user_id');
            $timestamp = mktime(0,0,0,$month,1,$year);
            $weekday = array('日','月','火','水','木','金','土');

            // ユーザーの名前を取得
            $user = $usersTable
            ->find('list',['keyField'=>'id','valueField'=>'name']);
            $user = $user->toArray();

            // スタッフを一覧で取得
            $staff = $usersTable
            ->find('list',['keyField'=>'id','valueField'=>'lastname'])
            ->where(['Users.adminfrag'=>1]);
            $staff = $staff->toArray();

            // エクセルテンプレートの読み込み
            $filePath = './template/nippo.xlsx';
            $reader = new XlsxReader();
            $spreadsheet = $reader->load($filePath);
            $spreadsheet->setActiveSheetIndex(0);
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle($year."年".$month."月");

            // 該当の日報の情報を取得
            $reportsTable = TableRegistry::get('Reports');
            $getRep = $reportsTable
            ->find()
            ->where(['Reports.user_id' => $user_id, 'Reports.date >=' => date('Y-m',$timestamp)."-".$sdate, 'Reports.date <=' => date('Y-m',$timestamp)."-".$edate])
            ->EnableHydration(false);
            $count = $getRep->toArray();

            // ページ数を判定しその分を先にコピーしておく
            $counts = count($count);
            $pages = ceil($counts / 2);
            $pages--;
            
            for($j=1; $j<=$pages; $j++) {
                $cloneSheet = clone $spreadsheet->getSheet(0);
                $cloneSheet->setTitle($year."年".$month."月 (".$j.")");
                $spreadsheet->addSheet($cloneSheet);
            }

            $reportsTable = TableRegistry::get('Reports');
            $reportdetailsTable = TableRegistry::get('ReportDetails');

            $jouge = 0;
            $k = 0;
            for($i=$sdate; $i<=$edate; $i++) {
                $getRep = $reportsTable
                ->find()
                ->where(['Reports.user_id'=>$user_id, 'Reports.date' => date('Y-m',$timestamp)."-".$i])
                ->EnableHydration(false);
                $rep = $getRep->first();
                if(!empty($rep)) {
                    $getDet = $reportdetailsTable
                    ->find()
                    ->where(['ReportDetails.report_id' => $rep["id"]])
                    ->EnableHydration(false);
                    $det = $getDet->toArray();

                    if($jouge == 0) {
                        // 作業日報
                        $sheet->setCellValue('E2',$year.'年 '.$month.'月'.$i.'日');
                        $sheet->setCellValue('F2',$user[$user_id]);
                        $sheet->setCellValue('C3',$rep["content"]);
                        if(!empty($det)){
                            $sheet->setCellValue('C6',$det[0]["item"]);
                            $sheet->setCellValue('E6',$det[0]["reportcontent"]);
                            $sheet->setCellValue('C7',$det[1]["item"]);
                            $sheet->setCellValue('E7',$det[1]["reportcontent"]);
                            $sheet->setCellValue('C8',$det[2]["item"]);
                            $sheet->setCellValue('E8',$det[2]["reportcontent"]);
                        }
                        $sheet->setCellValue('C9',$rep["notice"]);
                        $sheet->setCellValue('C11',$rep["plan"]);
                        // 業務日誌
                        $sheet->setCellValue('C13',$rep["state"]);
                        $sheet->setCellValue('C16',$rep["information"]);
                        $sheet->setCellValue('C18',$rep["bikou"]);
                        $sheet->setCellValue('C21',$rep["recorder"]);
                        $jouge++;
                    } elseif($jouge == 1) {
                        // 作業日報
                        $sheet->setCellValue('E24',$year.'年 '.$month.'月'.$i.'日');
                        $sheet->setCellValue('F24',$user[$user_id]);
                        $sheet->setCellValue('C25',$rep["content"]);
                        if(!empty($det)){
                            $sheet->setCellValue('C28',$det[0]["item"]);
                            $sheet->setCellValue('E28',$det[0]["reportcontent"]);
                            $sheet->setCellValue('C29',$det[1]["item"]);
                            $sheet->setCellValue('E29',$det[1]["reportcontent"]);
                            $sheet->setCellValue('C30',$det[2]["item"]);
                            $sheet->setCellValue('E30',$det[2]["reportcontent"]);
                        }
                        $sheet->setCellValue('C31',$rep["notice"]);
                        $sheet->setCellValue('C33',$rep["plan"]);
                        // 業務日誌
                        $sheet->setCellValue('C35',$rep["state"]);
                        $sheet->setCellValue('C38',$rep["information"]);
                        $sheet->setCellValue('C40',$rep["bikou"]);
                        $sheet->setCellValue('C43',$rep["recorder"]);
                        $k++;
                        if($k > $pages) {
                            continue;
                        } else {
                            $sheet = $spreadsheet->getSheet($k);
                        }
                        $jouge--;
                    }
                }
            }

            $title = date("Y-m",$timestamp)."　".$user[$user_id];

            // 直接ダウンロード　ヘッダー
            // 詳細はCompilesController.phpの1182行目から
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename='.$title.'　作業日報/業務日誌.xlsx');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public');

            $writer = new XlsxWriter($spreadsheet);
            $writer->save('php://output');
            exit;
        }
    }
}