<?php
namespace App\Controller;
use App\Controller\AppController;

use Cake\ORM\Table;
use cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportsController extends AppController
{
    public function absent()
    {
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            $usersTable = TableRegistry::get('Users');

            $staffs0 = $usersTable
            ->find('list',['valueField' => 'name'])
            ->where(['Users.adminfrag'=>0,'Users.display'=>0])
            ->order(['Users.narabi'=>'ASC']);
            $useresults = $staffs0->toArray();
            $this->set("users",$useresults);

            //日付の定義
            $this->set('years', array("2021"=>"2021","2022"=>"2022","2023"=>"2023"));
            $this->set('months', array("01"=>"1","02"=>"2","03"=>"3","04"=>"4","05"=>"5","06"=>"6","07"=>"7","08"=>"8","09"=>"9","10"=>"10","11"=>"11","12"=>"12"));
 
        } else {
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }
    }

    public function srecords()
    {
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            $usersTable = TableRegistry::get('Users');

            $staffs0 = $usersTable
            ->find('list',['valueField' => 'name'])
            ->where(['Users.adminfrag'=>0,'Users.display'=>0])
            ->order(['Users.narabi'=>'ASC']);
            $useresults = $staffs0->toArray();
            $this->set("users",$useresults);

            //日付の定義
            $this->set('years', array("2022"=>"2022","2023"=>"2023"));
            $this->set('months', array("01"=>"1","02"=>"2","03"=>"3","04"=>"4","05"=>"5","06"=>"6","07"=>"7","08"=>"8","09"=>"9","10"=>"10","11"=>"11","12"=>"12"));
 
        } else {
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }
    }

    public function csv()
    {
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            $usersTable = TableRegistry::get('Users');

            $staffs0 = $usersTable
            ->find('list',['valueField' => 'name'])
            ->where(['Users.adminfrag'=>0,'Users.display'=>0])
            ->order(['Users.narabi'=>'ASC']);
            $useresults = $staffs0->toArray();
            $this->set("users",$useresults);

            //日付の定義
            $this->set('years', array("2022"=>"2022","2023"=>"2023"));
            $this->set('months', array("01"=>"1","02"=>"2","03"=>"3","04"=>"4","05"=>"5","06"=>"6","07"=>"7","08"=>"8","09"=>"9","10"=>"10","11"=>"11","12"=>"12"));
 
        } else {
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }
    }

    public function service()
    {
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            // テーブルを取得
            $usersTable = TableRegistry::get('Users');
            $reportsTable = TableRegistry::get('Reports');

            // フォームの情報を取得
            $year = $this->request->getData('year');
            $month = $this->request->getData('month');
            $user_id = $this->request->getData('user_id');
            $timestamp = mktime(0,0,0,$month,1,$year);

            // ユーザーの一覧を取得
            $getusers = $usersTable
            ->find('list',['keyField' => 'id','valueField' => 'name']);
            $users = $getusers->toArray();

            $getRep = $reportsTable
            ->find()
            ->select(['Reports.date','Reports.state','Reports.information','Reports.recorder'])
            ->where(['Reports.user_id' => $user_id, 'Reports.date >=' => date('Y-m',$timestamp).'-01', 'Reports.date <=' => date('Y-m',$timestamp)."-".date("t",$timestamp)])
            ->order(['Reports.date'=>'ASC']);
            $reps = $getRep->toArray();

            if(empty($reps)) {
                $this->Flash->error(__('該当ユーザーの日報は見つかりませんでした'));
                return $this->redirect(['action' => 'srecords']);
            } else {
                // エクセルテンプレートの読み込み
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('./template/service.xlsx');
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setCellValue('A3',"【2022年".$month."月就労分】");
                $sheet->setCellValue('C4',"氏名　".$users[$user_id]);

                // 曜日を記入
                for($i = 1; $i <= date("t",$timestamp); $i++) {
                    $timestamp = mktime(0,0,0,$month,$i+1,$year);
                    $weekday = array('日','月','火','水','木','金','土');
                    $sheet->setCellValue('B'.($i + 10),$weekday[date("w",$timestamp)]);
                }

                // 日報の情報を記入
                foreach($reps as $rep) {
                    $d = $rep["date"]->i18nFormat("d");
                    $sheet->setCellValue('C'.($d + 10),$rep["state"]);
                    $sheet->setCellValue('I'.($d + 10),$rep["information"]);
                }

                // 直接ダウンロード　ヘッダー
                // 詳細はCompilesController.phpの1182行目から
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename='.date("Y-m",$timestamp)."　".$users[$user_id]."　サービス提供記録.xlsx");
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
        } else {
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }
    }

    public function export() 
    {
        // データの取得
        $year = $this->request->getData('year');
        $month = $this->request->getData('month');
        $user_id = $this->request->getData('user_id');

        // ユーザー
        $usersTable = TableRegistry::get('Users');
        $getUser = $usersTable
        ->find()
        ->select(['Users.id','Users.name'])
        ->where(['Users.id' => $user_id])
        ->EnableHydration(false);
        $Uresult = $getUser->first();

        // 出勤情報
        $timestamp = mktime(0,0,0,$month,1,$year);
        $weekday = array('日','月','火','水','木','金','土');
        $attendanceTable = TableRegistry::get('Attendances');
        $getQuery = $attendanceTable
        ->find()
        ->where(['Attendances.user_id' => $user_id, 'Attendances.date >=' => date('Y-m',$timestamp).'-01', 'Attendances.date <=' => date('Y-m',$timestamp)."-".date("t",$timestamp)])
        ->order(['Attendances.id'])
        ->EnableHydration(false);
        $results = $getQuery->toArray();

        if(empty($results)) {
            $this->Flash->error(__('該当する記録は存在しません'));
            return $this->redirect(['action' => 'csv']);
        } else {
            // csvファイルのヘッダー
            $header = ["日付","曜日","開始時間","終了時間","就労時間数","送迎(往)","送迎(復)","昼休憩","食事提供","医療連携","施設外支援","公休","有給","欠勤","備考"];
            // csvに一日ずつデータを表示するため、空の配列を用意する
            $data = [];
            
            for($i = 0; $i < date("t",$timestamp); $i++) {
                $timestamp = mktime(0,0,0,$month,$i+1,$year);

                // null回避用
                if(!empty($results[$i]["intime"])) {
                    $intime[$i] = $results[$i]["intime"]->i18nFormat("H:mm");
                } else {
                    $intime[$i] = "";
                }
                if(!empty($results[$i]["outtime"])) {
                    $outtime[$i] = $results[$i]["outtime"]->i18nFormat("H:mm");
                } else {
                    $outtime[$i] = "";
                }
                if(!empty($results[$i]["resttime"])) {
                    $resttime[$i] = $results[$i]["resttime"]->i18nFormat("H:mm");
                } else {
                    $resttime[$i] = "";
                }
                if(!empty($results[$i]["bikou"])) {
                    $bikou[$i] = $results[$i]["bikou"];
                } else {
                    $bikou[$i] = "";
                }
                
                // 出勤時間の設定
                if(!empty($results[$i]["intime"]) && !empty($results[$i]["outtime"])) {
                    $workh[$i] = $results[$i]["outtime"]->i18nFormat("H") - $results[$i]["intime"]->i18nFormat("H");
                    $workm[$i] = $results[$i]["outtime"]->i18nFormat("m") - $results[$i]["intime"]->i18nFormat("m");
                    if($workm[$i] < 0) {
                        $workh[$i]--;
                        $workm[$i] += 60;
                    }
                    if(!empty($results[$i]["resttime"])) {
                        $oneh[$i] = $workh[$i] - $results[$i]["resttime"]->i18nFormat("H");
                        $onem[$i] = $workm[$i] - $results[$i]["resttime"]->i18nFormat("m");
                        if($onem[$i] < 0) {
                            $oneh[$i]--;
                            $onem[$i] = 60 + $onem[$i];
                            if($onem[$i] < 10) {
                                $worktime[$i] = $oneh[$i].":0".$onem[$i];
                            } else {
                                $worktime[$i] = $oneh[$i].":".$onem[$i];
                            }
                        } else {
                            if($onem[$i] < 10) {
                                $worktime[$i] = $oneh[$i].":0".$onem[$i];
                            } else {
                                $worktime[$i] = $oneh[$i].":".$onem[$i];
                            }
                        }
                    } else {
                        if($workm[$i] < 10) {
                            $worktime[$i] = $workh[$i].":0".$workm[$i];
                        } else {
                            $worktime[$i] = $workh[$i].":".$workm[$i];
                        }
                    }
                } else {
                    $worktime[$i] = "";
                }

                $ichinichi[$i] = array(
                    $i+1,
                    $weekday[date("w",$timestamp)],
                    $intime[$i],
                    $outtime[$i],
                    $worktime[$i], 
                    $results[$i]["ou"],
                    $results[$i]["fuku"],
                    $resttime[$i],
                    $results[$i]["meshi"],
                    $results[$i]["medical"],
                    $results[$i]["support"],
                    $results[$i]["koukyu"],
                    $results[$i]["paid"],
                    $results[$i]["kekkin"],
                    $bikou[$i]
                );

                // 空の配列$dataに一日の内容をそれぞれ追加する
                array_push($data,$ichinichi[$i]);
            }

            // ダウンロードするファイルのタイトルを設定
            $filetitle = date('Y-m',$timestamp)."　".$Uresult["name"].".csv";
            $f = fopen('php://output', 'w');
            if($f) {
                // UTF-8からSJIS-winへ変換するフィルター
                stream_filter_append($f, 'convert.iconv.UTF-8/CP932//TRANSLIT', STREAM_FILTER_WRITE);
                // 書き込み
                fputcsv($f,$header);
                foreach ($data as $line) {
                    fputcsv($f, $line);
                }
                fclose($f);
            } else {
                $this->Flash->error(__('ファイルを開くことができませんでした'));
                return $this->redirect(['action' => 'index']);
            }
            
            // HTTPヘッダ設定
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment;filename ='.$filetitle);
            header('Content-Length: '.filesize($filetitle));
            header('Content-Transfer-Encoding: binary');
            exit;
        }
    }
}