<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use setasign\Fpdi;
use Yasumi\Yasumi;
use Cake\ORM\Table;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use \DateTime;

use Cake\Log\Log;

class PrintsController extends AppController
{
    public function indexn() 
    {
        $holidays = Yasumi::create('Japan', '2023', 'ja_JP');
        //$ver = $holidays->getVersion();
        //LOG::debug($ver);

        $updf = $this->request->getSession()->read('updf');
        $this->set(compact('updf'));
        $this->request->getSession()->delete('updf');
        $umpdf = $this->request->getSession()->read('umpdf');
        $this->set(compact('umpdf'));
        $this->request->getSession()->delete('umpdf');
        $spdf = $this->request->getSession()->read('spdf');
        $this->set(compact('spdf'));
        $this->request->getSession()->delete('spdf');
        $paid = $this->request->getSession()->read('paid');
        $this->set(compact('paid'));
        $this->request->getSession()->delete('paid');
        $chouka = $this->request->getSession()->read('chouka');
        $this->set(compact('chouka'));
        $this->request->getSession()->delete('chouka');
        $sancho = $this->request->getSession()->read('sancho');
        $this->set(compact('sancho'));
        $this->request->getSession()->delete('sancho');

        $usersTable = TableRegistry::get('Users');
        // 一覧取得
        $staffs = $usersTable
        ->find('list',['valueField'=>'name'])
        ->where(['Users.adminfrag'=>1,'Users.display'=>0])
        ->order(['Users.narabi'=>'ASC','Users.id'=>'ASC'])
        ->toArray();
        $users = $usersTable
        ->find('list',['valueField'=>'name'])
        ->where(['Users.adminfrag'=>0,'Users.display'=>0])
        ->order(['Users.narabi'=>'ASC','Users.id'=>'ASC'])
        ->toArray();
        $remotes = $usersTable
        ->find('list',['keyField'=>'id','valueField'=>'name'])
        ->where(['Users.display'=>0,'Users.remote'=>1])
        ->order(['Users.workplace'=>'ASC','Users.id'=>'ASC'])
        ->toArray();

        //日付の定義
        for($i=2021;$i<=date('Y')+1;$i++) {
            $years["$i"] = $i;
            $hyears["$i"] = $i;
            $oyears["$i"] = $i;
        }
        for($i=1;$i<=12;$i++) {
            $months[sprintf('%02d',$i)] = $i;
            $hmonths[sprintf('%02d',$i)] = $i;
            $omonths[sprintf('%02d',$i)] = $i;
            if($i==11) {
                $sanchom[11] = "11月 ～ 1月";
            } elseif($i==12) {
                $sanchom[12] = "12月 ～ 2月";
            } else {
                $x = $i + 2;
                $sanchom[$i] = $i."月 ～ ".$x."月";
            }
            if($i==1) {
                $defsan[$i] = 10;
            } elseif($i==2) {
                $defsan[$i] = 11;
            } elseif($i==3) {
                $defsan[$i] = 12;
            } else {
                $x = $i-3;
                $defsan[$i] = $x;
            }
        }

        //施設外就労場所読み込み
        $workPlacesTable = TableRegistry::get("Workplaces");
        $workName = $workPlacesTable
        ->find('list', ['keyField'=>'id','valueField'=>'name'])
        ->select( ['id','name'])->where(['Workplaces.sub'=>1])
        ->all()
        ->toArray();

        $sort = ['ユーザー順','日付順'];
        $kikan = ['直近12ヶ月'];
        $this->set(compact("staffs","users","years","hyears","oyears","months","hmonths","omonths",
                           "sort","remotes","sanchom","defsan","kikan", "workName"));
    }

    public function service()
    {
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            // 基本的な情報を取得
            $usersTable = TableRegistry::get('Users');
            $reportsTable = TableRegistry::get('Reports');
            $year = $this->request->getData('year');
            $month = $this->request->getData('month');
            $user_id = $this->request->getData('id');
            $timestamp = mktime(0,0,0,$month,1,$year);
            $getusers = $usersTable -> find('list',['keyField' => 'id','valueField' => 'name']);
            $users = $getusers->toArray();
            $getRep = $reportsTable
            ->find()
            ->select(['Reports.date','Reports.state','Reports.information','Reports.recorder'])
            ->where(['Reports.user_id' => $user_id, 'Reports.date >=' => date('Y-m',$timestamp).'-01', 'Reports.date <=' => date('Y-m',$timestamp)."-".date("t",$timestamp)])
            ->order(['Reports.date'=>'ASC']);
            $reps = $getRep->toArray();

            if(empty($reps)) {
                $this->Flash->error(__('該当ユーザーの日報は見つかりませんでした'));
                return $this->redirect(['action' => 'indexn']);
            } else {
                // エクセルテンプレートの読み込み
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('./template/service.xlsx');
                $sheet = $spreadsheet->getActiveSheet();
                $spreadsheet->getDefaultStyle()->getFont()->setSize(11);

                $sheet->setCellValue('A3',"【2022年".$month."月就労分】");
                $sheet->setCellValue('C4',"氏名　".$users[$user_id]);

                for($i = 1; $i <= date("t",$timestamp); $i++) {
                    $timestamp = mktime(0,0,0,$month,$i+1,$year);
                    $weekday = array('日','月','火','水','木','金','土');
                    $sheet->setCellValue('B'.($i + 10),$weekday[date("w",$timestamp)]);
                }
                $timestamp = mktime(0,0,0,$month,1,$year);

                foreach($reps as $rep) {
                    $d = $rep["date"]->i18nFormat("d");
                    $state = str_replace(array("\r\n", "\r", "\n"," ","　"), '', $rep["state"]);
                    $information = str_replace(array("\r\n", "\r", "\n"," ","　"), '', $rep["information"]);
                    // state 全角78文字、半角159文字
                    // information 全角42文字、半角87文字
                    if(strlen($state) >= 420) {
                        $sheet->getRowDimension($d + 10)->setRowHeight(105);
                        $sheet->getStyle('C'.($d + 10))->getFont()->setSize(9);
                        if(strlen($information) >= 160) {
                            $sheet->getStyle('I'.($d + 10))->getFont()->setSize(9);
                        }
                    } elseif(strlen($state) >= 300) {
                        $sheet->getRowDimension($d + 10)->setRowHeight(85);
                        if(strlen($information) >= 160) {
                            $sheet->getStyle('I'.($d + 10))->getFont()->setSize(9);
                        }
                    } elseif(strlen($state) >= 150) {
                        $sheet->getRowDimension($d + 10)->setRowHeight(60);
                        if(strlen($information) >= 160) {
                            $sheet->getStyle('I'.($d + 10))->getFont()->setSize(9);
                        }
                    } elseif(strlen($information) >= 160) {
                        $sheet->getRowDimension($d + 10)->setRowHeight(60);
                        $sheet->getStyle('I'.($d + 10))->getFont()->setSize(9);
                    } elseif(strlen($state) < 80 || strlen($information) < 40) {
                        $sheet->getRowDimension($d + 10)->setRowHeight(15);
                    } else {
                        $sheet->getRowDimension($d + 10)->setRowHeight(60);
                    }
                    $sheet->setCellValue('C'.($d + 10),str_replace(array("\r\n", "\r", "\n"), '',$state));
                    $sheet->setCellValue('I'.($d + 10),str_replace(array("\r\n", "\r", "\n"), '',$information));
                }

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

    public function csv() 
    {
        // データの取得
        $year = $this->request->getData('year');
        $month = $this->request->getData('month');
        $user_id = $this->request->getData('id');
        $usersTable = TableRegistry::get('Users');
        $Uresult = $usersTable
        ->find()
        ->select(['Users.id','Users.name'])
        ->where(['Users.id' => $user_id])
        ->EnableHydration(false)
        ->first();

        // 出勤情報
        $timestamp = mktime(0,0,0,$month,1,$year);
        $weekday = array('日','月','火','水','木','金','土');
        $attendanceTable = TableRegistry::get('Attendances');
        $getQuery = $attendanceTable
        ->find()
        ->where(['Attendances.user_id' => $user_id, 
                 'Attendances.date >=' => date('Y-m',$timestamp).'-01', 
                 'Attendances.date <=' => date('Y-m',$timestamp)."-".date("t",$timestamp)])
        ->order(['Attendances.id'])
        ->EnableHydration(false);
        $results = $getQuery->toArray();

        if(empty($results)) {
            $this->Flash->error(__('該当する記録は存在しません'));
            return $this->redirect(['action' => 'indexn']);
        } else {
            // csvファイルのヘッダー
            $header = ["日付","曜日","開始時間","終了時間","就労時間数","送迎(往)","送迎(復)","昼休憩","食事提供","医療連携","施設外支援","公休","有給","欠勤","欠勤加算","備考"];
            $data = [];

            // 欠勤加算数初期化        
            $kasan = 0;

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
                        }
                        $worktime[$i] = sprintf('%02d',$oneh[$i]).":".sprintf('%02d',$onem[$i]);
                    } else {
                        $worktime[$i] = sprintf('%02d',$workh[$i]).":".sprintf('%02d',$workm[$i]);
                    }
                } else {
                    $worktime[$i] = "";
                }

                //欠勤加算の処理
                $kekkinkasan = "0";
                if($kasan < 4) {
                    // 欠勤かつ4回未満の場合のみabsentsテーブルを参照
                    $absentsTable = TableRegistry::get('Absents');
                    $absent = $absentsTable->find()
                        ->where([
                            'Absents.user_id' => $user_id,
                            'Absents.kekkindate' => date('Y-m-d',$timestamp)
                        ])
                        ->first();

                    if($absent && $absent['kekkinkasan'] == 1) {
                        $kekkinkasan = "1";
                        $kasan++;
                    }
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
                    $kekkinkasan,
                    $bikou[$i]
                );

                // $dataに一日の内容をそれぞれ追加する
                array_push($data,$ichinichi[$i]);
            }

            // ダウンロードするファイルのタイトルを設定
            $filetitle = date('Y-m',$timestamp)."　".$Uresult["name"].".csv";
            $f = fopen('php://output', 'w');
            if($f) {
                stream_filter_append($f, 'convert.iconv.UTF-8/CP932//TRANSLIT', STREAM_FILTER_WRITE);
                fputcsv($f,$header);
                foreach ($data as $line) {
                    fputcsv($f, $line);
                }
                fclose($f);
            } else {
                $this->Flash->error(__('ファイルを開くことができませんでした'));
                return $this->redirect(['action' => 'indexn']);
            }
            // HTTPヘッダ設定
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment;filename ='.$filetitle);
            header('Content-Transfer-Encoding: binary');
            exit;
        }
    }

    public function view() 
    {
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            // 基本的な情報の取得
            $usersTable = TableRegistry::get('Users');
            $reportTable = TableRegistry::get('Reports');
            $attendanceTable = TableRegistry::get('Attendances');
            $jigyoushasTable = TableRegistry::get('Jigyoushas');
            $data = $this->request->getData();
            $timestamp = mktime(0,0,0,$data["month"],1,$data["year"]);
            $holidays = \Yasumi\Yasumi::create('Japan', $data["year"], 'ja_JP');
            $weekday = ['日','月','火','水','木','金','土'];
            $torf = ['','〇'];
            $filePath = './template/template.xlsx';
            $reader = new XlsxReader();
            $spreadsheet = $reader->load($filePath);
            $spreadsheet->setActiveSheetIndex(0);
            $sheet = $spreadsheet->getActiveSheet();
            $getCompany = $jigyoushasTable->find()->where(['Jigyoushas.id'=>1])->first();

            // ユーザーの取得
            if($data["user_id"] == 0) {
                $users = [];
                $getusers = $usersTable
                ->find()
                ->where(['Users.adminFrag'=>0])
                ->order(['Users.narabi'=>'ASC','Users.id'=>'ASC'])
                ->toArray();
                foreach($getusers as $getuser) {
                    if($data["year"] < $getuser["created"]->i18nFormat("yyyy")) {
                        continue;
                    } elseif($data["year"] == $getuser["created"]->i18nFormat("yyyy") 
                            && date('n',$timestamp) < $getuser["created"]->i18nFormat("M")) {
                        continue;
                    } else {
                        if(empty($getuser["retired"])) {
                            array_push($users,$getuser);
                        } elseif($data["year"] < $getuser["retired"]->i18nFormat("yyyy")) {
                            array_push($users,$getuser);
                        } elseif($data["year"] == $getuser["retired"]->i18nFormat("yyyy")) {
                            if(date('n',$timestamp) <= $getuser["retired"]->i18nFormat("M")) {
                                array_push($users,$getuser);
                            }
                        }
                    }
                }
            } else {
                $users = $usersTable
                ->find()
                ->where(['Users.id' => $data["user_id"]])
                ->order(['Users.narabi'=>'ASC','Users.id'=>'ASC'])
                ->toArray();
                $name = $usersTable
                ->find('list',['valueField'=>'name'])
                ->where(['Users.id' => $data["user_id"]])
                ->first();
            }

            $attusers = [];
            foreach($users as $user) {    
                $attcheck = $attendanceTable
                ->find()
                ->select(['Attendances.intime'])
                ->where(['Attendances.user_id' => $user["id"], 
                         'Attendances.date >=' => date('Y-m',$timestamp).'-01', 
                         'Attendances.date <=' => date('Y-m',$timestamp)."-".date("t",$timestamp)])
                ->order(['Attendances.date'=>'ASC'])
                ->toArray();
                if(!empty($attcheck)) {
                    array_push($attusers,$user);
                }
            }
            if(empty($attusers)) {
                $this->Flash->error(__('該当する記録は存在しません'));
                return $this->redirect(['action' => 'indexn']);
            }
            for($i=1; $i<count($attusers); $i++) {
                $pagename = $i+1;
                $namaes = $attusers[$i]["name"];
                $cloneSheet = clone $spreadsheet->getSheet(0);
                $cloneSheet->setTitle("$namaes");
                $spreadsheet->addSheet($cloneSheet);
            }

            // 月間の出勤者の出力
            // for($i=0; $i<count($attusers); $i++) {
            //     pr($attusers[$i]["name"]);
            // }
            // exit;

            $p = 0;
            foreach($attusers as $attuser) {
                $sheet = $spreadsheet->getSheet($p);
                if($p == 0) {
                    $namae = $attuser["name"];
                    $sheet->setTitle("$namae");
                }
                $sheet->setCellValue('B3',$data["year"]." 年"."　".$data["month"]." 月");
                $sheet->setCellValue('D4',$attuser["sjnumber"]);
                $sheet->setCellValue('H5',$attuser["name"]."　さん");
                $sheet->setCellValue('I4',$attuser["id"]);
                $sheet->setCellValue('P5',$getCompany["jname"]);
                $sheet->setCellValue('Q4',$getCompany["jnumber"]);

                $allh = 0; $allm = 0; $allrh = 0; $allrm = 0; $shukkin = 0;
                $kasan = 0;
                for($i=1; $i<=date("t",$timestamp); $i++) {
                    $timestamp = mktime(0,0,0,$data["month"],$i,$data["year"]);
                    $sheet->setCellValue('B'.(9+$i),$i);
                    $sheet->setCellValue('C'.(9+$i),$weekday[date('w',$timestamp)]);
    
                    $result = $attendanceTable
                    ->find()
                    ->where(['Attendances.user_id' => $attuser["id"], 
                             'Attendances.date >=' => date('Y-m',$timestamp)."-".$i])
                    ->order(['Attendances.date'=>'ASC'])
                    ->first();

                    $oneh = 0;
                    $onem = 0;
                    if(!empty($result["intime"]) || !empty($result["outtime"])) {
                        $sheet->setCellValue('D'.(9+$i),$result["intime"]->i18nFormat("HH:mm"));
                        $sheet->setCellValue('E'.(9+$i),$result["outtime"]->i18nFormat("HH:mm"));
                        $oneh = $result["outtime"]->i18nFormat("H") - $result["intime"]->i18nFormat("H");
                        $onem = $result["outtime"]->i18nFormat("m") - $result["intime"]->i18nFormat("m");
                        $shukkin++;
                    } else {
                        $sheet->setCellValue('D'.(9+$i),"");
                        $sheet->setCellValue('E'.(9+$i),"");
                    }
                    if(!empty($result["resttime"])) {
                        $sheet->setCellValue('I'.(9+$i),$result["resttime"]->i18nFormat("H:mm"));
                        $oneh -= $result["resttime"]->i18nFormat("H");
                        $onem -= $result["resttime"]->i18nFormat("m");
                        $allrh += $result["resttime"]->i18nFormat("H");
                        $allrm += $result["resttime"]->i18nFormat("m");
                    } else {
                        $sheet->setCellValue('I'.(9+$i),"");
                    }
                    if($onem < 0) {
                        $oneh--;
                        $onem += 60;
                    }
                    $allh += $oneh;
                    $allm += $onem;

                    if(!empty($oneh)) {
                        $sheet->setCellValue('F'.(9+$i),sprintf('%02d',$oneh).":".sprintf('%02d',$onem));
                    } else {
                        $sheet->setCellValue('F'.(9+$i)," ");
                    }
                    $sheet->setCellValue('G'.(9+$i),$torf[$result["ou"]]);
                    $sheet->setCellValue('H'.(9+$i),$torf[$result["fuku"]]);
                    $sheet->setCellValue('J'.(9+$i),$torf[$result["meshi"]]);
                    //$sheet->setCellValue('K'.(9+$i),$torf[$result["medical"]]);
                    $sheet->setCellValue('L'.(9+$i),$torf[$result["support"]]);
                    if(!empty($result["remote"])) {
                        $sheet->setCellValue('M'.(9+$i),$torf[$result["remote"]]);
                    }
                    $sheet->setCellValue('N'.(9+$i),$torf[$result["koukyu"]]);
                    $sheet->setCellValue('O'.(9+$i),$torf[$result["paid"]]);
                    $sheet->setCellValue('P'.(9+$i),$torf[$result["kekkin"]]);

                    //欠勤加算の処理
                    if($kasan < 4) {
                        // 欠勤かつ4回未満の場合のみabsentsテーブルを参照
                        $absentsTable = TableRegistry::get('Absents');
                        $absent = $absentsTable->find()
                            ->where([
                                'Absents.user_id' => $result["user_id"],
                                'Absents.kekkindate' => $result["date"]->i18nFormat('yyyy-MM-dd')
                            ])
                            ->first();

                        if($absent && $absent['kekkinkasan'] == 1) {
                            $sheet->setCellValue('Q'.(9+$i),$torf[$result["kekkin"]]);
                            $kasan++;
                        }
                    }

                    $sheet->setCellValue('R'.(9+$i),$result["bikou"]);
                }

                if($allm >= 60) {
                    $allh += floor($allm / 60);
                    $allm = $allm % 60;
                }
                $sheet->setCellValue('F41',sprintf('%02d',$allh).":".sprintf('%02d',$allm));

                if($allrm >= 60) {
                    $allrh += floor($allrm / 60);
                    $allrm = $allrm % 60;
                }
                $sheet->setCellValue('I41',sprintf('%02d',$allrh).":".sprintf('%02d',$allrm));
                $query = $attendanceTable->find('all');
                // 合計のデータを出力
                $sums = $query
                ->where(['Attendances.user_id' => $attuser["id"], 
                         'Attendances.date >=' => date('Y-m',$timestamp).'-01', 
                         'Attendances.date <=' => date('Y-m',$timestamp)."-".date("t",$timestamp)])
                ->select([
                    'sumou' => $query->func()->sum('Attendances.ou'),
                    'sumfuku' => $query->func()->sum('Attendances.fuku'),
                    'sumeshi' => $query->func()->sum('Attendances.meshi'),
                    'summedical' => $query->func()->sum('Attendances.medical'),
                    'sumsupport' => $query->func()->sum('Attendances.support'),
                    'sumremote' => $query->func()->sum('Attendances.remote'),
                    'sumkoukyu' => $query->func()->sum('Attendances.koukyu'),
                    'sumpaid' => $query->func()->sum('Attendances.paid'),
                    'sumkekkin' => $query->func()->sum('Attendances.kekkin'),
                    ])
                ->first();
                $workdays = date("t",$timestamp) - 8;
                $sheet->setCellValue('D42',$shukkin." 日");
                $sheet->setCellValue('E42',$sums["sumkekkin"]." 日");
                $sheet->setCellValue('G42',$sums["sumou"] + $sums["sumfuku"]." 回");
                $sheet->setCellValue('G41',$sums["sumou"]);
                $sheet->setCellValue('H41',$sums["sumfuku"]);
                $sheet->setCellValue('J41',$sums["sumeshi"]." 回");
                $sheet->setCellValue('K41',$sums["summedical"]." 回");
                $sheet->setCellValue('L41',$sums["sumsupport"]." 回");
                $sheet->setCellValue('M41',$sums["sumremote"]." 回");
                $sheet->setCellValue('N41',$sums["sumkoukyu"]." 日");
                $sheet->setCellValue('O41',$sums["sumpaid"]." 日");
                $sheet->setCellValue('P41',$sums["sumkekkin"]." 日");
                $sheet->setCellValue('Q42',$workdays." 日");
                $sheet->setCellValue('R42',round($shukkin/$workdays*100)." %");
                $p++;
            }
        
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            if($data["user_id"] == 0) {
                header('Content-Disposition: attachment;filename='.date("Y-m",$timestamp)."　出勤簿.xlsx");
            } else {
                header('Content-Disposition: attachment;filename='.date("Y-m",$timestamp)."　".$name."　出勤簿.xlsx");
            }
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

    public function remote() 
    {
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            // 基本的な情報を取得
            $usersTable = TableRegistry::get('Users');
            $attendanceTable = TableRegistry::get('Attendances');
            $jigyoushasTable = TableRegistry::get('Jigyoushas');
            $weeklyTable = TableRegistry::get('Weeklies');
            $data = $this->request->getData();
            $timestamp = mktime(0,0,0,$data["month"],1,$data["year"]);
            $torf = ['','〇'];
            $j = 0;
            $filePath = './template/remote.xlsx';
            $reader = new XlsxReader();
            $spreadsheet = $reader->load($filePath);
            $spreadsheet->setActiveSheetIndex(0);
            $sheet = $spreadsheet->getActiveSheet();
            $getCompany = $jigyoushasTable->find()->where(['Jigyoushas.id'=>1])->first();

            $users = $usersTable
            ->find()
            ->where(['Users.adminfrag'=>0,'Users.remote'=>1,'Users.display'=>0])
            ->EnableHydration(false)
            ->toArray();

            // 基本情報を入力
            $sheet->setCellValue('C4',$getCompany["jname"]);
            $sheet->setCellValue('J4',$getCompany["jnumber"]);
            $sheet->setCellValue('Q4',$getCompany["skubun"]);
            $sheet->setCellValue('Y4',$this->Auth->user("name"));
            $sheet->setCellValue('Q4',"info@nbg-rd.com");
            $sheet->setCellValue('AE4',"011-211-0001");

            $sheet->setCellValue('AK4',$data["year"]);
            $sheet->setCellValue('AN4',$data["month"]);

            // 日付の入力
            for($i=1; $i<=date('t',$timestamp); $i++) {
                $sheet->setCellValueByColumnAndRow(3+$i,9,$i);
            }

            // 内容の入力
            foreach($users as $user) {
                // 在宅勤務の回数がゼロかどうか判別
                $attendances = $attendanceTable
                ->find('list',['valueField'=>'remote'])
                ->where(['Attendances.user_id' => $user["id"],
                         'Attendances.date >=' => date('Y-m-d',$timestamp),
                         'Attendances.date <=' => date('Y-m-t',$timestamp)])
                ->toArray();
                $remotecheck = array_sum($attendances);
                $all = 0;

                if($remotecheck != 0) {
                    $sheet->setCellValueByColumnAndRow(2,10+$j,$user["name"]);
                    $sheet->setCellValueByColumnAndRow(3,10+$j,$user["sjnumber"]);
                    // 一ヶ月のデータを取得
                    for($i=1;$i<=date('t',$timestamp);$i++) {
                        $remote = $attendanceTable
                        ->find()
                        ->where(['Attendances.user_id' => $user["id"],
                                 'Attendances.date' => date('Y-m',$timestamp)."-".$i])
                        ->EnableHydration(false)
                        ->first();
                        if($remote["remote"] == 1) {
                            $sheet->setCellValueByColumnAndRow(3+$i,10+$j,'〇');
                            $all++;
                            $finalNippo = $i;
                        } elseif(!empty($remote["intime"])) {
                            $sheet->setCellValueByColumnAndRow(3+$i,10+$j,'通所');
                        }
                    }
                    $sheet->setCellValueByColumnAndRow(35,10+$j,$all);

                    // 週報データ取得
                    $kons = $weeklyTable
                    ->find()
                    ->where(['Weeklies.user_id' => $user["id"],
                             'Weeklies.jdate >=' => date('Y-m-d',$timestamp),
                             'Weeklies.jdate <=' => date('Y-m-t',$timestamp)])
                    ->EnableHydration(false)
                    ->toArray();
                    $k = 0;
                    if(!empty($kons)) {
                        foreach($kons as $kon) {
                            $sheet->setCellValueByColumnAndRow(36+$k,10+$j,$kon["jdate"]->i18nFormat('MM月dd日'));
                            $k++;
                        }
                        $finalShuho = $kons[count($kons)-1]["jdate"]->i18nFormat('dd');

                        // 週報の最終日と日報の最終日を比較し、日報の方が後の場合は週報が来月になるため、来月のデータを取得する
                        if($finalNippo > $finalShuho) {
                            $raistamp = strtotime("first day of next month",$timestamp);
                            $rai = $weeklyTable
                            ->find()
                            ->where(['Weeklies.user_id' => $user["id"],
                                        'Weeklies.jdate >=' => date('Y-m-d',$raistamp),
                                        'Weeklies.jdate <=' => date('Y-m-t',$raistamp)])
                            ->EnableHydration(false)
                            ->first();
                            if(!empty($rai)) {
                                $sheet->setCellValueByColumnAndRow(36+$k,10+$j,$rai["jdate"]->i18nFormat('MM月dd日'));
                                $sheet->setCellValueByColumnAndRow(43,10+$j,$rai["jdate"]->i18nFormat('MM月dd日'));
                            }
                        } else {
                            $sheet->setCellValueByColumnAndRow(43,10+$j,$kon["jdate"]->i18nFormat('MM月dd日'));
                        }
                    }
                    $j++;
                }
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename='.date("Y-m",$timestamp)."　在宅就労支援実施一覧.xlsx");
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

    public function jisseki()
    {
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            // 基本的な情報を取得
            $usersTable = TableRegistry::get('Users');
            $reportTable = TableRegistry::get('Reports');
            $attendanceTable = TableRegistry::get('Attendances');
            $jigyoushasTable = TableRegistry::get('Jigyoushas');
            $data = $this->request->getData();
            LOG::debug($data["syuroutype"]);
            $timestamp = mktime(0,0,0,$data["month"],1,$data["year"]);
            $matsu = mktime(0,0,0,$data["month"]+1,date('t',$timestamp),$data["year"]);
            $weekday = ['日','月','火','水','木','金','土'];
            $filePath = './template/jisseki.xlsx';
            $reader = new XlsxReader();
            $spreadsheet = $reader->load($filePath);
            $spreadsheet->setActiveSheetIndex(0);
            $sheet = $spreadsheet->getActiveSheet();

            // 日付と会社名を出力
            $getCompany = $jigyoushasTable->find()->where(['Jigyoushas.id'=>1])->first();
            $sheet->setCellValue('B4',$data["year"]." 年 ".$data["month"]." 月");
            $sheet->setCellValue('D5',$getCompany["jnumber"]);
            $sheet->setCellValue('D6',$getCompany["jname"]);

            $users = [];
            $getusers = $usersTable
            ->find()
            ->where(['Users.adminfrag' => 0])
            ->EnableHydration(false)
            ->toArray();
            
            // A型/B型に基づいてフィルタリング
            if (isset($data['type']) && $data['type'] !== '') {
                $filteredUsers = [];
                foreach ($getusers as $getuser) {
                    // wrkCaseフィールドでA型/B型を判定
                    if (isset($getuser['wrkCase']) && $getuser['wrkCase'] == $data['type']) {
                        $filteredUsers[] = $getuser;
                    }
                }
                $getusers = $filteredUsers;
            }
            foreach($getusers as $getuser) {
                if($data["year"] < $getuser["created"]->i18nFormat("yyyy")) {
                    continue;
                } elseif($data["year"] == $getuser["created"]->i18nFormat("yyyy") 
                        && date('n',$timestamp) < $getuser["created"]->i18nFormat("M")) {
                    continue;
                } else {
                    if(empty($getuser["retired"])) {
                        array_push($users,$getuser);
                    } elseif($data["year"] < $getuser["retired"]->i18nFormat("yyyy")) {
                        array_push($users,$getuser);
                    } elseif($data["year"] == $getuser["retired"]->i18nFormat("yyyy")) {
                        if(date('n',$timestamp) <= $getuser["retired"]->i18nFormat("M")) {
                            array_push($users,$getuser);
                        }
                    }
                }
            }
            $sheet->setCellValue('I5',count($users)."　名");

            // 初期化
            $allshukkin = 0; $allkoukyu = 0; $allkekkin = 0; $allpaid = 0; $allmedical = 0;$allsougei = 0;
            $allh = 0; $allm = 0; $allage = 0;

            for($i=1; $i<=date('t',$timestamp); $i++) {
                // 初期化
                $shukkin = 0; $koukyu = 0; $kekkin = 0; $paid = 0;
                $oneh = 0; $onem = 0; $kuriage = 0; $medical = 0;$sougei = 0;
                $timestamp = mktime(0,0,0,$data["month"],$i,$data["year"]);

                $sheet->setCellValue('B'.(10+$i),$i);
                $sheet->setCellValue('C'.(10+$i),$weekday[date('w',$timestamp)]);

                // 出勤チェック
                foreach($users as $user) {
                    $attendance = $attendanceTable
                    ->find()
                    ->where(['Attendances.date'=>date('Y-m-d',$timestamp),'Attendances.user_id'=>$user["id"]])
                    ->first();
                    if(!empty($attendance["koukyu"])) {
                        if($attendance["koukyu"] == 1) {
                            $koukyu++;
                        }
                    }
                    if(!empty($attendance["paid"])) {
                        if($attendance["paid"] == 1) {
                            $paid++;
                        }
                    }
                    if(!empty($attendance["kekkin"])) {
                        if($attendance["kekkin"] == 1) {
                            $kekkin++;
                        }
                    }

                    //送迎加算（迎）
                    if(!empty($attendance["ou"]) && $attendance["ou"] == 1) {
                        $sougei++;
                    }

                    //送迎加算（送）
                    if(!empty($attendance["fuku"]) && $attendance["fuku"] == 1) {
                        $sougei++;
                    }

                    if(!empty($attendance["medical"])) {
                        if($attendance["medical"] == 1) {
                            $medical++;
                        }
                    }
                    if(!empty($attendance["intime"]) && !empty($attendance["outtime"])) {
                        $shukkin++;
                        $zanh = $attendance["outtime"]->i18nFormat("H") - $attendance["intime"]->i18nFormat("H");
                        $zanm = $attendance["outtime"]->i18nFormat("m") - $attendance["intime"]->i18nFormat("m");
                        if($zanm < 0) {
                            $zanh--;
                            $zanm += 60;
                        }
                        if(!empty($attendance["resttime"])) {
                            $zanh -= $attendance["resttime"]->i18nFormat("H");
                            $zanm -= $attendance["resttime"]->i18nFormat("m");
                            if($zanm < 0) {
                                $zanh--;
                                $zanm += 60;
                            }
                        }
                        $oneh += $zanh;
                        $onem += $zanm;
                    }
                }
                
                if($onem >= 60) {
                    $oneh += floor($onem / 60);
                    $onem = $onem % 60;
                }
                // 出勤率の算出
                $zentai = $shukkin + $kekkin;
                if($zentai == 0) {
                    $ritsu = 0;
                } else {
                    $ritsu = round($shukkin / $zentai,2)*100;
                }

                $sheet->setCellValue('D'.(10+$i),$shukkin);
                $sheet->setCellValue('E'.(10+$i),sprintf('%02d',$oneh).":".sprintf('%02d',$onem));
                $sheet->setCellValue('F'.(10+$i),$koukyu);
                $sheet->setCellValue('G'.(10+$i),$paid);
                $sheet->setCellValue('H'.(10+$i),$kekkin);
                $sheet->setCellValue('I'.(10+$i),$sougei);
                $sheet->setCellValue('J'.(10+$i),$medical);
                $sheet->setCellValue('K'.(10+$i),$ritsu." %");

                // 合計値に加算
                $allshukkin += $shukkin;
                $allkoukyu += $koukyu; 
                $allkekkin += $kekkin;
                $allsougei += $sougei;
                $allmedical += $medical; 
                $allpaid += $paid;
                $allh += $oneh;
                $allm += $onem;
            }
            if($allm >= 60) {
                $allh += floor($allm / 60);
                $allm = $allm % 60;               
            }

            // 出勤率の算出
            $allzentai = $allshukkin + $allkekkin;
            if($allzentai == 0) {
                $allritsu = 0;
            } else {
                $allritsu = round($allshukkin / $allzentai,2)*100;
            }

            $sheet->setCellValue('D42',$allshukkin);           
            $sheet->setCellValue('E42',sprintf('%02d',$allh).":".sprintf('%02d',$allm));
            $sheet->setCellValue('F42',$allkoukyu);
            $sheet->setCellValue('G42',$allpaid);           
            $sheet->setCellValue('H42',$allkekkin);           
            $sheet->setCellValue('I42',$allsougei);
            $sheet->setCellValue('J42',$allmedical);
            $sheet->setCellValue('K42',$allritsu." %");            

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            // ファイル名にタイプを含める
            $typeLabel = isset($data['type']) && $data['type'] !== '' ? $data['type'] . '型' : '';
            $filename = date("Y-m",$timestamp) . "　月間実績票" . $typeLabel . ".xlsx";
            header('Content-Disposition: attachment;filename=' . $filename);
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

    public function support() 
    {
        if($this->request-> getSession()->read('Auth.User.adminfrag') != 1) exit;

        // 基本的な情報を取得
        $usersTable = TableRegistry::get('Users');
        $attendanceTable = TableRegistry::get('Attendances');
        $workPlacesTable = TableRegistry::get("Workplaces");
        $filePath = './template/support.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($filePath);
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();
        
        $data = $this->request->getData();
        if($data["year"] == 2019) {
            $gen = "元";
        } elseif($data["year"] > 2019) {
            $gen = $data["year"] - 2018;
        }
        $sheet->setCellValue('C14',"令和 ".$gen." 年 ".$data["month"]." 月分の施設外就労実績について、以下のとおり報告します。");
        $timestamp = mktime(0,0,0,$data["month"],1,$data["year"]);

        //施設外就労に関する項目
        $workData = $workPlacesTable
        ->find()
        ->select( ['company','address','stdate','eddate','wrkcontentsu','mokuhyo'])
        ->where(['id'=>$data["support"]])
        ->hydrate(false)
        ->first();

        //就労先企業名
        $sheet->setCellValue('J16',$workData["company"]);
        //所在地
        $sheet->setCellValue('J17',$workData["address"]);
        //契約期間
        if ( !empty($workData["stdate"]) && !empty($workData["eddate"])) {
            $stdate = new DateTime($workData["stdate"]);
            $eddate = new DateTime($workData["eddate"]);
            $sheet->setCellValue('J18','  '.wareki($stdate->format('Y')).' '.$stdate->format('n').'月'.' '.$stdate->format('j').'日'.
                                                                      '～'.wareki($eddate->format('Y')).' '.$eddate->format('n').'月'.' '.$eddate->format('j').'日');
        }
        //目標・目的等
        $sheet->setCellValue('J19',$workData["mokuhyo"]);
        //受注作業内容
        $sheet->setCellValue('J20',$workData["wrkcontentsu"]);

        // 利用者
        $users = [];
        $getusers = $usersTable
        ->find()
        ->where(['Users.adminfrag' => 0])
        ->EnableHydration(false)
        ->toArray();
        foreach($getusers as $getuser) {
            $timestamp = mktime(0,0,0,$data["month"],1,$data["year"]);
            $getsupports = $attendanceTable->find();
            $getsupports
            ->select(['id'])
            ->where(['user_id' => $getuser["id"],
                        'support' => $data["support"],
                        'date >=' =>  date('Y-m',$timestamp).'-01',
                        'date <=' =>  date('Y-m',$timestamp)."-".date("t",$timestamp)]);
            $sumsupport = $getsupports->count();
            if($sumsupport != 0) {
                array_push($users,$getuser);
            }
        }
        $ninzu = count($users);

        for($i=1; $i<=count($users)-2; $i++) {
            $sheet -> insertNewRowBefore(24, 1);
            $sheet -> mergeCells('J24:P24');
            $sheet -> mergeCells('Q24:W24');
            $sheet -> mergeCells('X24:AD24');
            $sheet -> mergeCells('AE24:AK24');
        }
        $x = 0;
        $rowchk = ($ninzu == 1) ? 1 : 0;
        $rowchk2 = ($ninzu == 1) ? 2 : 0;
        foreach($users as $user) {
            $sheet->getStyle('J'.(23+$x))->getAlignment()->setHorizontal('center');
            $sheet->setCellValue('J'.(23+$x),$user["name"]);
            $sheet->setCellValue('Q'.(23+$x),$user["sjnumber"]);
            $x++;
        }
        for($i=1; $i<=count($users)-2; $i++) {
            $sheet -> insertNewRowBefore(27-2+$ninzu, 1);
            $sheet -> mergeCells('AI'.(27-2+$ninzu).':AK'.(27-2+$ninzu));
        }
        $x = 0;
        foreach($users as $user) {
            $sheet->getStyle('J'.(24+$ninzu+$x))->getAlignment()->setHorizontal('center');
            if(mb_strlen($user["name"])>=7) {
                $sheet->getStyle('C'.(24+$ninzu+$x))->getFont()->setSize(10);
                $un = str_replace('　', '', $user["name"]);
                $sheet->setCellValue('C'.(24+$ninzu+$x),$un);
            } elseif(mb_strlen($user["name"])>=6) {
                $sheet->getStyle('C'.(24+$ninzu+$x))->getFont()->setSize(10);
                $sheet->setCellValue('C'.(24+$ninzu+$x+$rowchk),$user["name"]);
            } else {
                $sheet->setCellValue('C'.(24+$ninzu+$x+$rowchk),$user["name"]);                   
            }
            $x++;
        }

        for($i=1; $i<=date('t',$timestamp); $i++) {
            $timestamp = mktime(0,0,0,$data["month"],$i,$data["year"]);
            if(date('w',$timestamp) == 0) {
                $sheet->getCellByColumnAndRow(3+$i,23+$ninzu+$rowchk)
                ->getStyle()
                ->getFill()
                ->setFillType('solid')
                ->getStartColor()
                ->setARGB('FFFF0000');
            }
            if ($ninzu != 0) {
                $sheet->setCellValueByColumnAndRow(3+$i,23+$ninzu+$rowchk,$i);
            }
            $y = 0;
            $supninzu = 0;
            foreach($users as $user) {
                $sup = $attendanceTable
                ->find()
                ->select(['Attendances.support'])
                ->where(['support' => $data["support"],'Attendances.user_id' => $user["id"],'Attendances.date' => date('Y-m-d',$timestamp)])
                ->first();
                if( !empty($sup)) {
                    $sheet->setCellValueByColumnAndRow(3+$i,24+$ninzu+$y+$rowchk,"〇");
                    $supninzu++;
                }
                $y++;
            }
            if ( $ninzu != 0) {
                $sheet->setCellValueByColumnAndRow(3+$i,24+$ninzu+$y+$rowchk2,$supninzu);
            }
        }
        
        $x = 0;
        $sumsum = 0;
        foreach($users as $user) {
            $hitosum = 0;
            for($i=1;$i<=date('t',$timestamp);$i++) {
                $timestamp = mktime(0,0,0,$data["month"],$i,$data["year"]);
                $sup = $attendanceTable
                ->find()
                ->select(['Attendances.support'])
                ->where(['support' => $data["support"],'Attendances.user_id' => $user["id"],'Attendances.date' => date('Y-m-d',$timestamp)])
                ->first();
                if ( !empty($sup)) {
                    $hitosum++;
                }
            }
            $sheet->setCellValue('X'.(23+$x),$hitosum);
            $sheet->setCellValue('AI'.(24+$ninzu+$x+$rowchk),$hitosum);
            $sumsum += $hitosum;
            $x++;
        }
        $sheet->setCellValue('AI'.(24+$ninzu+$x+$rowchk2),$sumsum);

        // 職員
        $rowchk = ($ninzu == 1) ? 2 : 0;
        $staffs = [];
        $getstaffs = $usersTable
        ->find()
        ->where(['Users.adminfrag' => 1])
        ->EnableHydration(false)
        ->toArray();
        foreach($getstaffs as $getstaff) {
            $timestamp = mktime(0,0,0,$data["month"],1,$data["year"]);
            $getsupports = $attendanceTable->find();
            $getsupports
            ->select(['id'])
            ->where(['user_id' => $getstaff["id"],
                        'support' => $data["support"],
                        'date >=' =>  date('Y-m',$timestamp).'-01',
                        'date <=' =>  date('Y-m',$timestamp)."-".date("t",$timestamp)]);
            $sumsupport = $getsupports->count();
            if($sumsupport != 0) {
                array_push($staffs,$getstaff);
            }
        }
        $ninzu0 = count($staffs);

        if($ninzu0 >= 6) {
            for($i=1; $i<=count($users)-5; $i++) {
                $sheet -> insertNewRowBefore(26+$ninzu*2, 1);
            }
        }
        $x = 0;
        foreach($staffs as $staff) {
            $sheet->getStyle('J'.(25+$ninzu*2+$x))->getAlignment()->setHorizontal('center');
            if(mb_strlen($staff["name"])>=7) {
                $sheet->getStyle('C'.(25+$ninzu*2+$x+$rowchk))->getFont()->setSize(10);
                $un = str_replace('　', '', $staff["name"]);
                $sheet->setCellValue('C'.(25+$ninzu*2+$x+$rowchk),$un);
            } elseif(mb_strlen($staff["name"])>=6) {
                $sheet->getStyle('C'.(25+$ninzu*2+$x+$rowchk))->getFont()->setSize(10);
                $sheet->setCellValue('C'.(25+$ninzu*2+$x+$rowchk),$staff["name"]);
            } else {
                $sheet->setCellValue('C'.(25+$ninzu*2+$x+$rowchk),$staff["name"]);                   
            }
            $supportsu = 0;
            for($i=1;$i<=date('t',$timestamp);$i++) {
                $timestamp = mktime(0,0,0,$data["month"],$i,$data["year"]);
                $sup = $attendanceTable
                ->find()
                ->select(['Attendances.support'])
                ->where(['support' => $data["support"],'Attendances.user_id' => $staff["id"],'Attendances.date' => date('Y-m-d',$timestamp)])
                ->first();
                if( !empty($sup)) {
                    $sheet->setCellValueByColumnAndRow(3+$i,25+$ninzu*2+$x+$rowchk,"〇");
                    $supportsu++;
                }
            }
            $sheet->setCellValue('AI'.(25+$ninzu*2+$x+$rowchk),$supportsu);
            $x++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename='.date("Y-m",$timestamp)."　施設外就労実績報告書.xlsx");
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

    public function nisshis()
    {
        // 基本的な情報を取得
        $usersTable = TableRegistry::get('Users');
        $reportsTable = TableRegistry::get('Reports');
        $reportdetailsTable = TableRegistry::get('ReportDetails');
        $data = $this->request->getData();
        $timestamp = mktime(0,0,0,$data["month"],1,$data["year"]);
        $user = $usersTable
        ->find('list',['keyField'=>'id','valueField'=>'name'])
        ->toArray();
        $staff = $usersTable
        ->find('list',['keyField'=>'id','valueField'=>'name'])
        ->where(['Users.adminfrag'=>1])
        ->toArray();
        $filePath = './template/nippo2.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($filePath);
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();
   
        $count = $reportsTable->find()
        ->where(['Reports.user_id' => $data["id"], 
                 'Reports.date >=' => date('Y-m',$timestamp)."-1", 
                 'Reports.date <=' => date('Y-m',$timestamp)."-".date('t',$timestamp)])
        ->toArray();
        $counts = count($count);
        $pages = ceil($counts / 2) - 1;
        
        for($j=1; $j<=$pages; $j++) {
            $cloneSheet = clone $spreadsheet->getSheet(0);
            $titlej = $j+1;
            $cloneSheet->setTitle("sheet"."$titlej");
            $spreadsheet->addSheet($cloneSheet);
        }

        $k = 0; $m = 0;
        for($i=1; $i<=date('t',$timestamp); $i++) {
            $rep = $reportsTable
            ->find()
            ->where(['Reports.user_id'=>$data["id"], 'Reports.date' => date('Y-m',$timestamp)."-".$i])
            ->first();

            if(!empty($rep)) {
                $row = 18;
                $det = $reportdetailsTable
                ->find()
                ->where(['ReportDetails.report_id' => $rep["id"]])
                ->EnableHydration(false)
                ->toArray();

                // 改行とスペースの削除
                $content = str_replace(array("\r\n", "\r", "\n"," ","　"), '', $rep["content"]);
                $notice = str_replace(array("\r\n", "\r", "\n"," ","　"), '', $rep["notice"]);
                $plan = str_replace(array("\r\n", "\r", "\n"," ","　"), '', $rep["plan"]);
                $state = str_replace(array("\r\n", "\r", "\n"," ","　"), '', $rep["state"]);
                $information = str_replace(array("\r\n", "\r", "\n"," ","　"), '', $rep["information"]);
                $bikou = str_replace(array("\r\n", "\r", "\n"," ","　"), '', $rep["bikou"]);

                $sheet->setCellValue('E'.(2+$m),$data["year"].'年 '.$data["month"].'月'.sprintf('%02d',$i).'日');
                $sheet->setCellValue('F'.(2+$m),$user[$data["id"]]);

                $a = ceil(strlen($content) / 117);
                if($a == 0) {$a++;}
                $sheet->setCellValue('C'.(3+$m),$content);
                $row -= $a;

                // 勤務内容の詳細を記述
                for($j=0; $j<=2; $j++) {
                    if(empty($det[$j]["item"])) {
                        $x = 0;
                        $item = "";
                    } else {
                        $x = strlen($det[$j]["item"]);
                        $item = $det[$j]["item"];
                    }
                    if(empty($det[$j]["reportcontent"])) {
                        $y = 0;
                        $reportcontent = "";
                    } else {
                        $y = strlen($det[$j]["reportcontent"]);
                        $reportcontent = $det[$j]["reportcontent"];
                    }
                    $l = ceil(max($x,$y) / 57);
                    if($l == 0) {$l++;}
                    $sheet->getRowDimension(4+$m+$j)->setRowHeight(19.5 * $l);
                    $sheet->setCellValue('C'.(4+$m+$j),$item);
                    $sheet->setCellValue('E'.(4+$m+$j),$reportcontent);
                    $row -= $l;
                }

                $b = ceil(strlen($notice) / 117);
                if($b == 0) {$b++;}
                $sheet->setCellValue('C'.(7+$m),$notice);
                $row -= $b;

                $l = ceil(strlen($plan) / 117);
                if($l == 0) {$l++;}
                $sheet->getRowDimension(8+$m)->setRowHeight(19.5 * $l);
                $sheet->setCellValue('C'.(8+$m),$plan);
                $row -= $l;

                $c = ceil(strlen($state) / 117);
                if($c == 0) {$c++;}
                $sheet->setCellValue('C'.(9+$m),$state);
                $row -= $c;

                $l = ceil(strlen($information) / 117);
                if($l == 0) {$l++;}
                $sheet->getRowDimension(10+$m)->setRowHeight(19.5 * $l);
                $sheet->setCellValue('C'.(10+$m),$information);
                $row -= $l;

                $d = ceil(strlen($bikou) / 117);
                if($d == 0) {$d++;}
                $sheet->setCellValue('C'.(11+$m),$bikou);
                $row -= $d;

                if(!empty($staff[$rep["recorder"]])) {
                    $sheet->setCellValue('C'.(12+$m),$staff[$rep["recorder"]]);
                } else {
                    $sheet->setCellValue('C'.(12+$m),$rep["recorder"]);
                }

                // あまり
                $r = 0;
                if($row > 0) {
                    while($r != $row) {
                        if($r % 4 == 0) {
                            $c++;
                        } elseif($r % 4 == 1) {
                            $a++;
                        } elseif($r % 4 == 2) {
                            $d++;
                        } else {
                            $b++;
                        }
                        $r++;
                    }
                }
                $sheet->getRowDimension(3+$m)->setRowHeight(19.5 * $a);
                $sheet->getRowDimension(7+$m)->setRowHeight(19.5 * $b);
                $sheet->getRowDimension(9+$m)->setRowHeight(19.5 * $c);
                $sheet->getRowDimension(11+$m)->setRowHeight(19.5 * $d);

                // 次のページへ行く
                if($m == 0) {
                    $m = 13;
                } else {
                    $k++;
                    $m = 0;
                    if($k > $pages) {
                        continue;
                    } else {
                        $sheet = $spreadsheet->getSheet($k);
                    }
                }
            }
        }

        $title = date("Y-m",$timestamp)."　".$user[$data["id"]];
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename='.$title.'_作業日報_業務日誌.xlsx');
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

    public function viewdetail()
    {
        // 基本的な情報の取得
        $usersTable = TableRegistry::get('Users');
        $attendanceTable = TableRegistry::get('Attendances');
        $remoteTable = TableRegistry::get('Remotes');
        $weeklyTable = TableRegistry::get('Weeklies');
        $filePath = './template/remrecord.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($filePath);
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet(); 
        $data = $this->request->getData();
        $timestamp = mktime(0,0,0,$data["month"],1,$data["year"]);

        $user = $usersTable->find()->where(['Users.id'=>$data["user_id"]])->first();
        $staffs = $usersTable
        ->find('list',['keyField'=>'id','valueField'=>'name'])
        ->where(['Users.adminfrag'=>1])
        ->toArray();

        $count = 0;
        $exportarrs = [];
        for($i=1; $i<=date('t',$timestamp); $i++) {
            $timestamp = mktime(0,0,0,$data["month"],$i,$data["year"]);
            $remchk = $remoteTable
            ->find()
            ->where(['Remotes.user_id'=>$data["user_id"],'Remotes.date'=>date('Y-m-d',$timestamp)])
            ->EnableHydration(false)
            ->first();
            if(empty($remchk["date"])) {
                $weechk = $weeklyTable
                ->find()
                ->where(['Weeklies.user_id'=>$data["user_id"],'Weeklies.jdate'=>date('Y-m-d',$timestamp)])
                ->EnableHydration(false)
                ->first();
                if(!empty($weechk["id"])) {
                    array_push($exportarrs,$weechk);
                    $exportarrs[$count]["type"] = 1;
                    $count++;
                }
            } else {
                array_push($exportarrs,$remchk);
                $exportarrs[$count]["type"] = 0;
                $count++;
            }
        }

        for($i=1; $i<$count; $i++) {
            $cloneSheet = clone $spreadsheet->getSheet(0);
            $cloneSheet->setTitle("sheet");
            $spreadsheet->addSheet($cloneSheet);
        }

        $p = 0;
        foreach($exportarrs as $exportarr) {
            $sheet = $spreadsheet->getSheet($p);
            if($exportarr["type"] == 0) {
                $namae = $exportarr["date"]->i18nFormat('MM月dd日');
            } else {
                $namae = $exportarr["jdate"]->i18nFormat('MM月dd日');
            }
            $sheet->setTitle("$namae");
            $sheet->setCellValue('E4',$user["name"]);
            $sheet->setCellValue('P4',$user["sjnumber"]);

            if($exportarr["type"] == 1) {
                $sheet->getTabColor()->setRGB('B0B0B0');
                $jexdate = explode("-",$exportarr["jdate"]->i18nFormat('yyyy-MM-dd'));
                $hexdate = explode("-",$exportarr["hdate"]->i18nFormat('yyyy-MM-dd'));
                $oexdate = explode("-",$exportarr["odate"]->i18nFormat('yyyy-MM-dd'));
                if(!empty($exportarr["lasttime"])) {
                    $lexdate = explode("-",$exportarr["lasttime"]->i18nFormat('yyyy-MM-dd'));
                } else {
                    $lexdate = ["","",""];
                }

                // 日付を出力
                $sheet->setCellValue('I20',$jexdate[0]);
                $sheet->setCellValue('L20',$jexdate[1]);
                $sheet->setCellValue('N20',$jexdate[2]);
                $sheet->setCellValue('I21',$hexdate[0]);
                $sheet->setCellValue('L21',$hexdate[1]);
                $sheet->setCellValue('N21',$hexdate[2]);
                $sheet->setCellValue('Q21',$oexdate[1]);
                $sheet->setCellValue('S21',$oexdate[2]);
                $sheet->setCellValue('T28',$lexdate[1]);
                $sheet->setCellValue('V28',$lexdate[2]);

                $sheet->setCellValue('E22',$exportarr["content"]);
                $sheet->setCellValue('E29',$staffs[$exportarr["user_staffid"]]);
                $sheet->setCellValue('R29',$staffs[$exportarr["sabikan"]]);
            } else {
                $exdate = explode("-",$exportarr["date"]->i18nFormat('yyyy-MM-dd'));
                $sheet->setCellValue('F6',$exdate[0]);
                $sheet->setCellValue('I6',$exdate[1]);
                $sheet->setCellValue('K6',$exdate[2]);
                $sheet->setCellValue('P6',$exportarr["intime"]->i18nFormat('HH:mm'));
                $sheet->setCellValue('U6',$exportarr["outtime"]->i18nFormat('HH:mm'));
                $sheet->setCellValue('E7',$exportarr["work"]);
                $sheet->setCellValue('E9',$exportarr["shudan"]);

                $text1 = $exportarr["time1"]->i18nFormat('HH:mm')."  ".$exportarr["content1"];
                $text2 = $exportarr["time2"]->i18nFormat('HH:mm')."  ".$exportarr["content2"];
                $text = $text1."\n".$text2;
                $sheet->setCellValue('E10',$text);

                $sheet->setCellValue('H17',$staffs[$exportarr["user_staffid"]]);
                $sheet->setCellValue('E18',$exportarr["health"]);
            }
            $p++;
        }
        
        $title = $user["lastname"]."さん ".date('Ym',$timestamp)." ";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename='.$title.'在宅就労における支援記録.xlsx');
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

    public function srecords()
    {
        // テーブルを取得
        $schedulesTable = TableRegistry::get('Schedules');
    
        // 基本情報・基本設定
        $user_id = $this->Auth->user("id");
        $user_name = $this->Auth->user("name");
        $query = $this->request->getQuery();
        $timestamp = mktime(0,0,0,$query["month"],1,$query["year"]);
        $holidays = \Yasumi\Yasumi::create('Japan', $query["year"], 'ja_JP');
        $mojiColumn = ['B','C','D','E','F','G','H'];

        // エクセルテンプレートの読み込み
        $filePath = './template/schedule.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($filePath);
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();
        $title = $query["year"]."年 ".$query["month"]."月　　".$user_name." さん";

        $sheet->setCellValue('B2',$title);

        // 横と縦のずれを表示する変数のデフォルト値を設定
        $tate = 0;
        $yoko = date('w',$timestamp);

        for($i=1; $i<=date('t',$timestamp); $i++) {
            $stampi = mktime(0,0,0,$query["month"],$i,$query["year"]);
            $getData = $schedulesTable
            ->find()
            ->where(['Schedules.user_id'=>$user_id,'Schedules.date'=>date('Y-m-d',$stampi)])
            ->first();
            $dateCell = $mojiColumn[$yoko].(5+$tate);
            
            if($holidays->isHoliday(new \DateTime(date('Y-m-d',$stampi))) == 1 || date('w',$stampi) == 0) {
                $sheet->getStyle($dateCell)->getFont()->getColor()->setARGB('FFFF0000');
            } elseif(date('w',$stampi) == 6) {
                $sheet->getStyle($dateCell)->getFont()->getColor()->setARGB('FF0000FF');
            } else {
                $sheet->getStyle($dateCell)->getFont()->getColor()->setARGB('FF000000');
            }
            $sheet->setCellValue($dateCell,$i);

            if(!empty($getData["plana"])) {
                $planaCell = $mojiColumn[$yoko].(6+$tate);
                $sheet->setCellValue($planaCell,$getData["plana"]);
            }
            if(!empty($getData["planb"])) {
                $planbCell = $mojiColumn[$yoko].(7+$tate);
                $sheet->setCellValue($planbCell,$getData["planb"]);
            }
            if(!empty($getData["planc"])) {
                $plancCell = $mojiColumn[$yoko].(8+$tate);
                $sheet->setCellValue($plancCell,$getData["planc"]);
            }

            if($yoko == 6) {
                $tate += 4;
                $yoko = 0;
            } else {
                $yoko++;
            }
        }
        
        $title = $user_name." ".$query["year"]."年 ".$query["month"]."月";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename='.$title.'スケジュール.xlsx');
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

    public function hoken()
    {
        // 基本的な情報を取得
        $usersTable = TableRegistry::get('Users');
        $attendanceTable = TableRegistry::get('Attendances');
        $data = $this->request->getData();
 
        if($data["kikan"] == 0) {
                $hajime = mktime(0,0,0,date('m'),1,date('Y')-1);
            if(date('m')==1) {
                $owari = mktime(0,0,0,12,1,date('Y')-1);
                $owarinoowari = mktime(0,0,0,12,date('t',$owari),date('Y')-1);
            } else {
                $owari = mktime(0,0,0,date('m')-1,1,date('Y'));
                $owarinoowari = mktime(0,0,0,date('m')-1,date('t',$owari),date('Y'));
            }
        }

        if($data["user_id"] == 0) {
            $getusers = $usersTable
            ->find()
            ->select(['Users.id','Users.name','Users.retired','Users.joined','Users.wrkCase'])
            ->where(['Users.adminfrag'=>0])
            ->EnableHydration(false)
            ->toArray();
            $zanusers = []; $zanretires = [];
            foreach($getusers as $getuser) {
                if($getuser["wrkCase"] == 0 ) {
                    if(empty($getuser["retired"]) && strtotime($getuser["joined"]->i18nFormat("yyyy-MM-dd")) <= $owarinoowari) {
                        array_push($zanusers,$getuser);
                    } elseif(!empty($getuser["retired"]) && strtotime($getuser["retired"]) > $hajime) {
                        array_push($zanretires,$getuser);
                    } else {
                        continue;
                    }
                }
            }

            $sheetname = []; $users = []; $p1 = 0; $count = 0;

            foreach($zanusers as $zanuser) {
                $pagenumber = $p1+1;
                $users[$p1][$count] = $zanuser;
                $sheetname[$p1] = "利用者".$pagenumber;
                $count++;
                if($count == 22) {
                    $p1++;
                    $count = 0;
                }
            }
            $userPages = count($users);
            $retires = []; $p2 = 0; $n = 0; $count = 0;
            foreach($zanretires as $zanretire) {
                $pagenumber = $p2+1;
                $retires[$p2][$count] = $zanretire;
                $sheetname[$p1+$p2+1] = "退職者".$pagenumber;
                $count++;
                if($count == 21) {
                    $p2++;
                    $count = 0;
                }
            }
            $retirePages = count($retires);
            $pages = ceil(count($zanusers) / 21) + ceil(count($zanretires) / 21);

            $filePath = './template/hokenall.xlsx';
            $reader = new XlsxReader();
            $spreadsheet = $reader->load($filePath);
            $spreadsheet->setActiveSheetIndex(0);
            $sheet = $spreadsheet->getActiveSheet();

            for($i=1; $i<$pages; $i++) {
                $cloneSheet = clone $spreadsheet->getSheet(0);
                $cloneSheet->setTitle($sheetname[$i]);
                $spreadsheet->addSheet($cloneSheet);
            }

            for($i=0; $i<$pages; $i++) {
                $sheet = $spreadsheet->getSheet($i);
                $sheet->setTitle($sheetname[$i]);
                $sheet->setCellValue('K3',date('Y年m月',$hajime));
                $sheet->setCellValue('N3',date('Y年m月',$owari));
                for($j=0; $j<12; $j++) {
                    if(date('m',$hajime) + $j > 12) {
                        $month = $j + date('m',$hajime) - 12;
                    } else {
                        $month = $j + date('m',$hajime);
                    }
                    $sheet->setCellValueByColumnAndRow(4+$j, 5, $month."月");
                }
                if($i < $userPages) {
                    for($j=0; $j<count($users[$i]); $j++) {
                        $sheet->setCellValueByColumnAndRow(2, 6+$j, $users[$i][$j]["name"]);
                    }
                    for($j=0; $j<12; $j++) {
                        if(date('m',$hajime) + $j > 12) {
                            $month = $j + date('m',$hajime) - 12;
                            $year = date('Y');
                        } else {
                            $month = $j + date('m',$hajime);
                            $year = date('Y') - 1;
                        }

                        $hajimeStamp = mktime(0,0,0,$month,1,$year);
                        $owariStamp = mktime(0,0,0,$month,date('t',$hajimeStamp),$year);

                        for($k=0; $k<count($users[$i]); $k++) {
                            if(strtotime($users[$i][$k]["joined"]->i18nFormat("yyyy-MM-dd")) < $owariStamp) {
                                $worktimes = $attendanceTable
                                ->find()
                                ->select(['Attendances.intime','Attendances.outtime','Attendances.resttime'])
                                ->where(['Attendances.user_id' => $users[$i][$k]["id"],
                                         'Attendances.date >=' => date('Y-m-d',$hajimeStamp),
                                         'Attendances.date <=' => date('Y-m-d',$owariStamp)])
                                ->EnableHydration(false)
                                ->toArray();
                                
                                $hataraki = 0;
                                foreach($worktimes as $worktime) {
                                    if(!empty($worktime["intime"]) && !empty($worktime["outtime"])) {
                                        $intimeStamp = strtotime($worktime["intime"]->i18nFormat("HH:mm"));
                                        $outtimeStamp = strtotime($worktime["outtime"]->i18nFormat("HH:mm"));
                                        $ichinichi = $outtimeStamp - $intimeStamp;
                                        if(!empty($worktime["resttime"])) {
                                            $resttimeh = $worktime["resttime"]->i18nFormat("H")+9;
                                            $resttimem = $worktime["resttime"]->i18nFormat("m");
                                            $resttimeStamp = mktime($resttimeh,$resttimem,0,1,1,1970);
                                            $ichinichi -= $resttimeStamp;
                                        } else {
                                            $resttimeh = 0;
                                            $resttimem = 0;
                                        }
                                        $hataraki += $ichinichi / 60;

                                    } else {
                                        $intimeStamp = 0;
                                        $outtimeStamp = 0;
                                        $ichinichi = 0;
                                    }
                                }
                                $hatarakiten = $hataraki / 60;
                                $hatarakih = floor($hataraki / 60);
                                $hatarakim = $hataraki % 60;
                                
                                if($hatarakiten < 72) {
                                    $sheet->getCellByColumnAndRow(4+$j, 6+$k)
                                    ->getStyle()
                                    ->getFill()
                                    ->setFillType('solid')
                                    ->getStartColor()
                                    ->setARGB('FFDCE6F1');
                                    $sheet->getCellByColumnAndRow(4+$j, 6+$k)
                                    ->getStyle()
                                    ->getFont()
                                    ->getColor()
                                    ->setARGB('FF0F243E');
                                } elseif($hatarakiten < 80) {
                                    $sheet->getCellByColumnAndRow(4+$j, 6+$k)
                                    ->getStyle()
                                    ->getFill()
                                    ->setFillType('solid')
                                    ->getStartColor()
                                    ->setARGB('FFFFFF99');
                                    $sheet->getCellByColumnAndRow(4+$j, 6+$k)
                                    ->getStyle()
                                    ->getFont()
                                    ->getColor()
                                    ->setARGB('FF747100');
                                } else {
                                    $sheet->getCellByColumnAndRow(4+$j, 6+$k)
                                    ->getStyle()
                                    ->getFill()
                                    ->setFillType('solid')
                                    ->getStartColor()
                                    ->setARGB('00FFFFFF');
                                    $sheet->getCellByColumnAndRow(4+$j, 6+$k)
                                    ->getStyle()
                                    ->getFont()
                                    ->getColor()
                                    ->setARGB('FF000000');
                                }
                                $sheet->setCellValueByColumnAndRow(4+$j, 6+$k, $hatarakih.":".sprintf('%02d',$hatarakim));
                            } else {
                                $sheet->setCellValueByColumnAndRow(4+$j, 6+$k, "-");
                            }
                        }
                    }
                } else {
                    for($j=0; $j<count($retires[$i-$userPages]); $j++) {
                        $sheet->setCellValueByColumnAndRow(2, 6+$j, $retires[$i-$userPages][$j]["name"]);
                    }
                    for($j=0; $j<12; $j++) {
                        if(date('m',$hajime) + $j > 12) {
                            $month = $j + date('m',$hajime) - 12;
                            $year = date('Y');
                        } else {
                            $month = $j + date('m',$hajime);
                            $year = date('Y') - 1;
                        }

                        $hajimeStamp = mktime(0,0,0,$month,1,$year);
                        $owariStamp = mktime(0,0,0,$month,date('t',$hajimeStamp),$year);

                        for($k=0; $k<count($retires[$i-$userPages]); $k++) {
                            if(strtotime($retires[$i-$userPages][$k]["joined"]->i18nFormat("yyyy-MM-dd")) < $owariStamp &&
                               strtotime($retires[$i-$userPages][$k]["retired"]->i18nFormat("yyyy-MM-dd")) > $hajimeStamp) {
                                $worktimes = $attendanceTable
                                ->find()
                                ->select(['Attendances.intime','Attendances.outtime','Attendances.resttime'])
                                ->where(['Attendances.user_id' => $retires[$i-$userPages][$k]["id"],
                                         'Attendances.date >=' => date('Y-m-d',$hajimeStamp),
                                         'Attendances.date <=' => date('Y-m-d',$owariStamp)])
                                ->EnableHydration(false)
                                ->toArray();
                                
                                $hataraki = 0;
                                foreach($worktimes as $worktime) {
                                    if(!empty($worktime["intime"]) && !empty($worktime["outtime"])) {
                                        $intimeStamp = strtotime($worktime["intime"]->i18nFormat("HH:mm"));
                                        $outtimeStamp = strtotime($worktime["outtime"]->i18nFormat("HH:mm"));
                                        $ichinichi = $outtimeStamp - $intimeStamp;
                                        if(!empty($worktime["resttime"])) {
                                            $resttimeh = $worktime["resttime"]->i18nFormat("H")+9;
                                            $resttimem = $worktime["resttime"]->i18nFormat("m");
                                            $resttimeStamp = mktime($resttimeh,$resttimem,0,1,1,1970);
                                            $ichinichi -= $resttimeStamp;
                                        }
                                        $hataraki += $ichinichi / 60;
                                    }
                                }
                                $hatarakiten = $hataraki / 60;
                                $hatarakih = floor($hataraki / 60);
                                $hatarakim = $hataraki % 60;

                                if($hatarakiten < 72) {
                                    $sheet->getCellByColumnAndRow(4+$j, 6+$k)
                                    ->getStyle()
                                    ->getFill()
                                    ->setFillType('solid')
                                    ->getStartColor()
                                    ->setARGB('FFDCE6F1');
                                    $sheet->getCellByColumnAndRow(4+$j, 6+$k)
                                    ->getStyle()
                                    ->getFont()
                                    ->getColor()
                                    ->setARGB('FF0F243E');
                                } elseif($hatarakiten < 80) {
                                    $sheet->getCellByColumnAndRow(4+$j, 6+$k)
                                    ->getStyle()
                                    ->getFill()
                                    ->setFillType('solid')
                                    ->getStartColor()
                                    ->setARGB('FFFFFF99');
                                    $sheet->getCellByColumnAndRow(4+$j, 6+$k)
                                    ->getStyle()
                                    ->getFont()
                                    ->getColor()
                                    ->setARGB('FF747100');
                                } else {
                                    $sheet->getCellByColumnAndRow(4+$j, 6+$k)
                                    ->getStyle()
                                    ->getFill()
                                    ->setFillType('solid')
                                    ->getStartColor()
                                    ->setARGB('00FFFFFF');
                                    $sheet->getCellByColumnAndRow(4+$j, 6+$k)
                                    ->getStyle()
                                    ->getFont()
                                    ->getColor()
                                    ->setARGB('FF000000');
                                }
                                $sheet->setCellValueByColumnAndRow(4+$j, 6+$k, $hatarakih.":".sprintf('%02d',$hatarakim));
                            } else {
                                $sheet->setCellValueByColumnAndRow(4+$j, 6+$k, "-");
                            }
                        }
                    }
                }
            }
            $title = "直近12ヶ月 全利用者出勤時間一覧表";
        } else {
            $user = $usersTable
            ->find()
            ->where(['Users.id'=>$data["user_id"]])
            ->EnableHydration(false)
            ->first();
            $filePath = './template/hokensolo.xlsx';
            $reader = new XlsxReader();
            $spreadsheet = $reader->load($filePath);
            $spreadsheet->setActiveSheetIndex(0);
            $sheet = $spreadsheet->getActiveSheet();
      
            $sheet->setCellValue('H4',$user["name"]);
            $sheet->setCellValue('H5',date('Y年m月',$hajime)."～".date('Y年m月',$owari));

            $j = 0; $sumHataraki = 0; $tsuki = 0;
            for($i=0; $i<12; $i++) {
                if(date('m',$hajime) + $i > 12) {
                    $month = $i + date('m',$hajime) - 12;
                    $year = date('Y');
                } else {
                    $month = $i + date('m',$hajime);
                    $year = date('Y') - 1;
                }

                $sheet->setCellValueByColumnAndRow(2 + ($j * 4), 7 + floor($i / 2), $month."月");
                $hajimeStamp = mktime(0,0,0,$month,1,$year);
                $owariStamp = mktime(0,0,0,$month,date('t',$hajimeStamp),$year);

                if(strtotime($user["joined"]->i18nFormat("yyyy-MM-dd")) < $owariStamp &&
                    (empty($user["retired"]) || strtotime($user["retired"]->i18nFormat("yyyy-MM-dd")) > $hajimeStamp)) {
                    $worktimes = $attendanceTable
                    ->find()
                    ->select(['Attendances.intime','Attendances.outtime','Attendances.resttime'])
                    ->where(['Attendances.user_id' => $data["user_id"],
                                'Attendances.date >=' => date('Y-m-d',$hajimeStamp),
                                'Attendances.date <=' => date('Y-m-d',$owariStamp)])
                    ->EnableHydration(false)
                    ->toArray();
                    
                    $hataraki = 0;
                    foreach($worktimes as $worktime) {
                        if(!empty($worktime["intime"]) && !empty($worktime["outtime"])) {
                            $intimeStamp = strtotime($worktime["intime"]->i18nFormat("HH:mm"));
                            $outtimeStamp = strtotime($worktime["outtime"]->i18nFormat("HH:mm"));
                            $ichinichi = $outtimeStamp - $intimeStamp;
                            if(!empty($worktime["resttime"])) {
                                $resttimeh = $worktime["resttime"]->i18nFormat("H")+9;
                                $resttimem = $worktime["resttime"]->i18nFormat("m");
                                $resttimeStamp = mktime($resttimeh,$resttimem,0,1,1,1970);
                                $ichinichi -= $resttimeStamp;
                            }
                            $hataraki += $ichinichi / 60;
                        }
                    }
                    $hatarakiten = $hataraki / 60;
                    $hatarakih = floor($hataraki / 60);
                    $hatarakim = $hataraki % 60;
                    $sumHataraki += $hatarakiten;

                    if($hatarakiten != 0) {
                        $tsuki++;
                    }

                    if($hataraki < 4320) {
                        $sheet->getCellByColumnAndRow(5 + ($j * 4), 7 + floor($i / 2))
                        ->getStyle()
                        ->getFill()
                        ->setFillType('solid')
                        ->getStartColor()
                        ->setARGB('FFDCE6F1');
                        $sheet->getCellByColumnAndRow(5 + ($j * 4), 7 + floor($i / 2))
                        ->getStyle()
                        ->getFont()
                        ->getColor()
                        ->setARGB('FF0F243E');
                    } elseif($hataraki < 4800) {
                        $sheet->getCellByColumnAndRow(5 + ($j * 4), 7 + floor($i / 2))
                        ->getStyle()
                        ->getFill()
                        ->setFillType('solid')
                        ->getStartColor()
                        ->setARGB('FFFFFF99');
                        $sheet->getCellByColumnAndRow(5 + ($j * 4), 7 + floor($i / 2))
                        ->getStyle()
                        ->getFont()
                        ->getColor()
                        ->setARGB('FF747100');
                    } else {
                        $sheet->getCellByColumnAndRow(5 + ($j * 4), 7 + floor($i / 2))
                        ->getStyle()
                        ->getFill()
                        ->setFillType('solid')
                        ->getStartColor()
                        ->setARGB('00FFFFFF');
                        $sheet->getCellByColumnAndRow(5 + ($j * 4), 7 + floor($i / 2))
                        ->getStyle()
                        ->getFont()
                        ->getColor()
                        ->setARGB('FF000000');
                    }

                    $sheet->setCellValueByColumnAndRow(5 + ($j * 4), 7 + floor($i / 2),$hatarakih.":".sprintf("%02d",$hatarakim));
                    $j++;

                    if($j==2) {
                        $j = 0;
                    }
                }
                $sumh = floor($sumHataraki / $tsuki);
                $summ = $sumHataraki % $tsuki;
                $sheet->setCellValue('I14',$sumh.":".sprintf("%02d",$summ));
            }
            $title = $user["name"]."　雇用保険水準";
        }
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename='.$title.'.xlsx');
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

    public function syokuji() 
    {
        if($this->request->getSession()->read('Auth.User.adminfrag') == 0) exit;
        
        //データ取得
        $data = $this->request->getData();

        //検索期間の作成
        $sdate = mktime(0,0,0,$data["month"],1,$data["year"]); 
        $edate = date('Y-m-d',mktime(0,0,0,$data["month"]+1,0,$data["year"]));
        $lastDay = date('t', $sdate);

        //利用者基本データ取得
        $attendanceTable = TableRegistry::get('Attendances');
        $RiyousyaList = $attendanceTable
        ->find()
        ->select( ['user_id' => 'user_id','name' => 'name'])
        ->join(['type' => 'inner', 'table' => 'users', 'conditions' => 'user_id = users.id'])
        ->where(['date >=' => date('Y-m-d',$sdate),'date <=' => $edate,'adminfrag' => 0, 'meshi' => 1])
        ->distinct(['user_id']) 
        ->order(['user_id' => 'ASC'])
        ->enableHydration(false)
        ->toArray(); 

        //スタッフ基本データ取得
        $attendanceTable = TableRegistry::get('Attendances');
        $StaffList = $attendanceTable
        ->find()
        ->select( ['user_id' => 'user_id','name' => 'name'])
        ->join(['type' => 'inner', 'table' => 'users', 'conditions' => 'user_id = users.id'])
        ->where(['date >=' => date('Y-m-d',$sdate),'date <=' => $edate,'adminfrag' => 1, 'meshi' => 1])
        ->distinct(['user_id']) 
        ->order(['user_id' => 'ASC'])
        ->enableHydration(false)
        ->toArray();        
        
        $holidays = \Yasumi\Yasumi::create('Japan', $data["year"], 'ja_JP');
        $weekday = ['日','月','火','水','木','金','土'];
        $filePath = './template/syokuji.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($filePath);

        // 食事数シートを取得（2番目のシート）
        $shokujiSheet = $spreadsheet->getSheetByName('食事数');
        if ($shokujiSheet === null) {
            // シートが見つからない場合は、2番目のシートを取得
            $shokujiSheet = $spreadsheet->getSheet(1);
            if ($shokujiSheet === null) {
                throw new \Exception('食事数シートが見つかりません。');
            }
        }

        // タイトルを更新
        $shokujiSheet->setCellValue('B2', $data["year"].'年'.intval($data["month"]).'月 食事数');

        // Get user food counts by day
        $userCounts = $attendanceTable
            ->find()
            ->select(['day' => 'DAY(date)', 'count' => 'COUNT(*)'])
            ->join([
                'table' => 'users',
                'type' => 'INNER',
                'conditions' => 'Attendances.user_id = users.id'
            ])
            ->where([
                'date >=' => date('Y-m-d', $sdate),
                'date <=' => $edate,
                'users.adminfrag' => 0,
                'meshi' => 1
            ])
            ->group('DAY(date)')
            ->enableHydration(false)
            ->toArray();

        $staffCounts = $attendanceTable
            ->find()
            ->select(['day' => 'DAY(date)', 'count' => 'COUNT(*)'])
            ->join([
                'table' => 'users',
                'type' => 'INNER',
                'conditions' => 'Attendances.user_id = users.id'
            ])
            ->where([
                'date >=' => date('Y-m-d', $sdate),
                'date <=' => $edate,
                'users.adminfrag' => 1,
                'meshi' => 1
            ])
            ->group('DAY(date)')
            ->enableHydration(false)
            ->toArray();

        $userCountsMap = [];
        foreach ($userCounts as $count) {
            $userCountsMap[$count['day']] = $count['count'];
        }
        
        $staffCountsMap = [];
        foreach ($staffCounts as $count) {
            $staffCountsMap[$count['day']] = $count['count'];
        }

        // 食事数シートにデータを入力
        $rowTotal = 0;
        $userTotal = 0;
        $staffTotal = 0;

        // 末日処理：その月の最終日より後の行を削除
        if ($lastDay < 31) {
            for ($day = 31; $day > $lastDay; $day--) {
                $row = $day + 4;
                $shokujiSheet->removeRow($row, 1);
            }
        }

        for ($day = 1; $day <= $lastDay; $day++) {
            $row = $day + 4;
            
            $userCount = isset($userCountsMap[$day]) ? $userCountsMap[$day] : 0;
            $staffCount = isset($staffCountsMap[$day]) ? $staffCountsMap[$day] : 0;
            $dailyTotal = $userCount + $staffCount;
            
            $shokujiSheet->setCellValue('C'.$row, $userCount);
            $shokujiSheet->setCellValue('D'.$row, $staffCount);
            $shokujiSheet->setCellValue('E'.$row, $dailyTotal);
            
            // 土日祝日の背景色設定
            $timestamp = mktime(0, 0, 0, $data["month"], $day, $data["year"]);
            $weekdayNum = date('w', $timestamp);
            $isHoliday = $holidays->isHoliday(new \DateTime(date('Y-m-d', $timestamp)));
            
            if ($weekdayNum == 0 || $isHoliday) { // 日曜日または祝日
                $shokujiSheet->getStyle('B'.$row)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FFFF0000');
            } elseif ($weekdayNum == 6) { // 土曜日
                $shokujiSheet->getStyle('B'.$row)
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('FF34dbeb');
            }
            
            $userTotal += $userCount;
            $staffTotal += $staffCount;
            $rowTotal += $dailyTotal;
        }

        // 合計行を入力（末日の次の行）
        $totalRow = $lastDay + 5;
        $shokujiSheet->setCellValue('C'.$totalRow, $userTotal);
        $shokujiSheet->setCellValue('D'.$totalRow, $staffTotal);
        $shokujiSheet->setCellValue('E'.$totalRow, $rowTotal);

        // 元のシートに戻る
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();

        //指定月日を表示
        $sheet->setCellValueByColumnAndRow(2, 1, date('Y年',$sdate));
        $sheet->setCellValueByColumnAndRow(3, 1, date('n月',$sdate));

        //曜日・土日着色
        for ($i = 1; $i <= $lastDay; $i++) {
            $timestamp = mktime(0, 0, 0, $data["month"], $i, $data["year"]);
            $weekdayNum = date('w', $timestamp);
            $isHoliday = $holidays->isHoliday(new \DateTime(date('Y-m-d', $timestamp)));
            
            // 日付と曜日を表示（1段目）
            $sheet->setCellValueByColumnAndRow($i + 2, 2, $i);
            $sheet->setCellValueByColumnAndRow($i + 2, 3, $weekday[$weekdayNum]);

            // 2段目の日付と曜日を表示
            $sheet->setCellValueByColumnAndRow($i + 2, 5, $i);
            $sheet->setCellValueByColumnAndRow($i + 2, 6, $weekday[$weekdayNum]);
            
            // 土日祝日の文字色設定
            if ($weekdayNum == 0 || $isHoliday) { // 日曜日または祝日
                // 1段目
                $cell1 = $sheet->getCellByColumnAndRow($i + 2, 2)->getCoordinate();
                $cell2 = $sheet->getCellByColumnAndRow($i + 2, 3)->getCoordinate();
                // 2段目
                $cell3 = $sheet->getCellByColumnAndRow($i + 2, 5)->getCoordinate();
                $cell4 = $sheet->getCellByColumnAndRow($i + 2, 6)->getCoordinate();
                
                foreach ([$cell1, $cell2, $cell3, $cell4] as $cell) {
                    $sheet->getStyle($cell)
                        ->getFont()
                        ->getColor()
                        ->setARGB('FFFF0000'); // 赤色
                }
            } elseif ($weekdayNum == 6) { // 土曜日
                // 1段目
                $cell1 = $sheet->getCellByColumnAndRow($i + 2, 2)->getCoordinate();
                $cell2 = $sheet->getCellByColumnAndRow($i + 2, 3)->getCoordinate();
                // 2段目
                $cell3 = $sheet->getCellByColumnAndRow($i + 2, 5)->getCoordinate();
                $cell4 = $sheet->getCellByColumnAndRow($i + 2, 6)->getCoordinate();
                
                foreach ([$cell1, $cell2, $cell3, $cell4] as $cell) {
                    $sheet->getStyle($cell)
                        ->getFont()
                        ->getColor()
                        ->setARGB('FF0000FF'); // 青色
                }
            }
        }

        //末尾の日データ削除処理
        if (substr($edate, -2) < 31 ) {
            //罫線スタイルを取得
            $sourceBorders = $sheet->getStyle("AG2:AG7")->getBorders();
            $rightBorderColor = $sourceBorders->getRight()->getColor()->getARGB();
            if (!$rightBorderColor) { 
                $rightBorderColor = \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK; // デフォルトで黒を設定
            }

            //罫線作成
            $destinationRange = columnNumberToLetter( intval(substr($edate, -2)) - 1) . "2:" . columnNumberToLetter( intval(substr($edate, -2)) - 1)  . "7";
            $styleArray = [
                'borders' => [
                    'right' => [
                        'borderStyle' => $sourceBorders->getRight()->getBorderStyle(),
                        'color' => ['argb' => $sourceBorders->getRight()->getColor()->getARGB()]
                    ],
                ],
            ];
            $sheet->getStyle($destinationRange)->applyFromArray($styleArray);
            
            //列削除
            $sheet->removeColumn(columnNumberToLetter( intval(substr($edate, -2))) ,31 - substr($edate, -2));
        }

        //スタッフ食事予定欄拡張
        if ( count($StaffList) >= 2) {
            //コピー元のセルアドレス
            $fromAddress = "A7:H7";
            //コピー先のセル
            $dustAddress = "A8";

            //セル値を配列で取得
            $range = $sheet->rangeToArray($fromAddress);

            //行挿入
            $sheet->insertNewRowBefore(8, count($StaffList) - 1);

            //B4セルを始点に、配列でコピーした値を貼り付け
            $sheet->fromArray($range, null, $dustAddress);

            //罫線修正
            $cellRange = 'B7:BE7';
            $endcell = columnNumberToLetter( intval(substr($edate, -2)));
            for ( $i = 0; $i < count($StaffList) - 1; $i++) {
                $cellRange = 'B' . strval($i + 7) . ':'. columnNumberToLetter( intval(substr($edate, -2)) - 1) . strval($i + 7);
                $objStyle = $sheet->getStyle($cellRange);
                $objBorders = $objStyle->getBorders();
                $objBorders->getBottom()->setBorderStyle(Border::BORDER_THIN);
            }
        }

        //スタッフ食事情報出力
        for ( $i = 0; $i < count($StaffList); $i++) {
            //氏名出力
            $sheet->setCellValueByColumnAndRow(2, $i + 7, $StaffList[$i]['name']);

            //食事情報取得
            $StafMeshi = $attendanceTable
            ->find()
            ->select( ['day' => 'RIGHT(date, 2)','meshi' => 'meshi'])
            ->where(['user_id' => $StaffList[$i]['user_id'], 'date >=' => date('Y-m-d',$sdate),'date <=' => $edate, 'meshi' => 1])
            ->enableHydration(false)
            ->toArray();

            //食事情報出力
            for ( $j = 0; $j < count($StafMeshi); $j++) {
                $cell = getCellRange(2 + $StafMeshi[$j]['day'], $i + 7);
                $sheet->setCellValueByColumnAndRow(2 + $StafMeshi[$j]['day'], $i + 7, '〇');
                $sheet->getStyle($cell)
                    ->getFont()
                    ->getColor()
                    ->setARGB('FF000000'); // 黒色
            }
        }

        //利用者食事予定欄拡張
        if ( count($RiyousyaList) >= 2) {
            //コピー元のセルアドレス
            $fromAddress = "A4:H4";
            //コピー先のセル
            $dustAddress = "A5";

            //セル値を配列で取得
            $range = $sheet->rangeToArray($fromAddress);

            //行挿入
            $sheet->insertNewRowBefore(5, count($RiyousyaList) - 1);

            //始点に、配列でコピーした値を貼り付け
            $sheet->fromArray($range, null, $dustAddress);

            //罫線・背景色修正
            $endcell = columnNumberToLetter( intval(substr($edate, -2)));
            for ( $i = 0; $i < count($RiyousyaList); $i++) {
                //罫線
                $cellRange = 'B' . strval($i + 4) . ':'. columnNumberToLetter( intval(substr($edate, -2)) - 1 ) . strval($i + 4);
                $objStyle = $sheet->getStyle($cellRange);
                $objBorders = $objStyle->getBorders();
                $objBorders->getBottom()->setBorderStyle(Border::BORDER_THIN);

                //背景色
                if ( ($i % 2) == 1) {
                    $objFill = $objStyle->getFill();
                    $objFill->getStartColor()->setARGB('ffffffff');
                }
            }
        }
        //利用者食事情報出力
        for ( $i = 0; $i < count($RiyousyaList); $i++) {
            //氏名出力
            $sheet->setCellValueByColumnAndRow(2, $i + 4, $RiyousyaList[$i]['name']);

            //食事情報取得
            $RiyousyaSyukkin = $attendanceTable
            ->find()
            ->select( ['day' => 'RIGHT(date, 2)','meshi' => 'meshi'])
            ->where(['user_id' => $RiyousyaList[$i]['user_id'], 'date >=' => date('Y-m-d',$sdate),'date <=' => $edate, 'meshi' => 1])
            ->enableHydration(false)
            ->toArray();        

            //食事情報出力
            for ( $j = 0; $j < count($RiyousyaSyukkin); $j++) {
                $cell = getCellRange(2 + $RiyousyaSyukkin[$j]['day'], $i + 4);
                $sheet->setCellValueByColumnAndRow(2 + $RiyousyaSyukkin[$j]['day'], $i + 4, '〇');
            }       

        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename='.date("Y-m",$sdate)."　食事管理表.xlsx");
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

    public function syukkinhyou() 
    {
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 0) exit;
        
        //データ取得
        $data = $this->request->getData();

        //検索期間の作成
        $sdate = mktime(0,0,0,$data["month"],1,$data["year"]); 
        $edate = date('Y-m-d',mktime(0,0,0,$data["month"]+1,0,$data["year"]));

        //利用者基本データ取得
        $attendanceTable = TableRegistry::get('Attendances');
        $RiyousyaList = $attendanceTable
        ->find()
        ->select( ['work_hours' => $attendanceTable->find()->func()->concat([
            $attendanceTable->find()->func()->date_format(['dintime' => 'identifier', "'%H:%i'" => 'literal']),
            '～',
            $attendanceTable->find()->func()->date_format(['douttime' => 'identifier', "'%H:%i'" => 'literal'])
        ]),'user_id' => 'user_id','name' => 'name'])
        ->join(['type' => 'inner', 'table' => 'users', 'conditions' => 'user_id = users.id'])
        ->where(['date >=' => date('Y-m-d',$sdate),'date <=' => $edate,'adminfrag' => 0])
        ->distinct(['user_id']) 
        ->order(['user_id' => 'ASC'])
        ->enableHydration(false)
        ->toArray(); 

        //スタッフ基本データ取得
        $attendanceTable = TableRegistry::get('Attendances');
        $StaffList = $attendanceTable
        ->find()
        ->select( ['user_id' => 'user_id','name' => 'name'])
        ->join(['type' => 'inner', 'table' => 'users', 'conditions' => 'user_id = users.id'])
        ->where(['date >=' => date('Y-m-d',$sdate),'date <=' => $edate,'adminfrag' => 1])
        ->distinct(['user_id']) 
        ->order(['user_id' => 'ASC'])
        ->enableHydration(false)
        ->toArray();        
        
        $holidays = \Yasumi\Yasumi::create('Japan', $data["year"], 'ja_JP');
        $weekday = ['日','月','火','水','木','金','土'];
        $filePath = './template/syukkinyotei.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($filePath);
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();
        
        //指定月日を表示
        $sheet->setCellValueByColumnAndRow(2, 1, date('Y年',$sdate));
        $sheet->setCellValueByColumnAndRow(3, 1, date('n月',$sdate));

        //曜日・土日着色
        for ( $i = 1; $i <= date('t',$sdate); $i++) {
            $timestamp = mktime(0,0,0,$data["month"],$i,$data["year"]);
            
            //曜日設定
            $sheet->setCellValueByColumnAndRow(3 + $i, 3, $weekday[date('w',$timestamp)]);
            $sheet->setCellValueByColumnAndRow(3 + $i, 6, $weekday[date('w',$timestamp)]);

            //土日祝日着色
            if ( ($weekday[date('w',$timestamp)] == '土') || 
                 ($weekday[date('w',$timestamp)] == '日') ||
                 ($holidays->isHoliday(new \DateTime(date('Y-m-d',$timestamp))) == 1)){
                $cell = getCellRange(2 + $i,2);
                $sheet->getStyle($cell)
                ->getFont()
                ->getColor()
                ->setRGB(($weekday[date('w',$timestamp)] == '土') ? '0000FF' : 'FF0000' );

                $cell = getCellRange(2 + $i,5);
                $sheet->getStyle($cell)
                ->getFont()
                ->getColor()
                ->setRGB(($weekday[date('w',$timestamp)] == '土') ? '0000FF' : 'FF0000' );
            }
        }

        //末尾の日データ削除処理
        if (substr($edate, -2) < 31 ) {
            //罫線スタイルを取得
            $sourceBorders = $sheet->getStyle("AH2:AH7")->getBorders();

            //罫線作成
            $destinationRange = columnNumberToLetter( intval(substr($edate, -2))) . "2:" . columnNumberToLetter( intval(substr($edate, -2)))  . "7";
            $sheet->getStyle($destinationRange)->getBorders()->getRight()->setBorderStyle($sourceBorders->getTop()->getBorderStyle()); 

            //列削除
            $sheet->removeColumn(columnNumberToLetter( intval(substr($edate, -2)) + 1) ,31 - substr($edate, -2));
        }

        //スタッフ出勤予定欄拡張
        if ( count($StaffList) >= 2) {
            //コピー元のセルアドレス
            $fromAddress = "A7:H7";
            //コピー先のセル
            $dustAddress = "A8";

            //セル値を配列で取得
            $range = $sheet->rangeToArray($fromAddress);

            //行挿入
            $sheet->insertNewRowBefore(8, count($StaffList) - 1);

            //B4セルを始点に、配列でコピーした値を貼り付け
            $sheet->fromArray($range, null, $dustAddress);

            //罫線修正
            $cellRange = 'C7:CE7';
            $endcell = columnNumberToLetter( intval(substr($edate, -2)));
            for ( $i = 0; $i < count($StaffList) - 1; $i++) {
                $cellRange = 'C' . strval($i + 7) . ':'. columnNumberToLetter( intval(substr($edate, -2))) . strval($i + 7);
                $objStyle = $sheet->getStyle($cellRange);
                $objBorders = $objStyle->getBorders();
                $objBorders->getBottom()->setBorderStyle(Border::BORDER_THIN);
            }
        }

        //スタッフ出勤情報出力
        for ( $i = 0; $i < count($StaffList); $i++) {
            //氏名出力
            $sheet->setCellValueByColumnAndRow(3, $i + 7, $StaffList[$i]['name']);

            //出勤情報取得
            $StafSyukkin = $attendanceTable
            ->find()
            ->select( ['day' => 'RIGHT(date, 2)','paid' => 'paid','support' => 'support','bikou' => 'bikou'])
            ->where(['koukyu' => 0, 'kekkin' => 0, 'user_id' => $StaffList[$i]['user_id'], 'date >=' => date('Y-m-d',$sdate),'date <=' => $edate])
            ->enableHydration(false)
            ->toArray();        
    
            //出勤情報出力
            for ( $j = 0; $j < count($StafSyukkin); $j++) {
                if ( $StafSyukkin[$j]['paid'] == 1) {
                    $sheet->setCellValueByColumnAndRow(3 + $StafSyukkin[$j]['day'], $i + 7, '有');
                } elseif ($StafSyukkin[$j]['support'] == 3) {
                    $sheet->setCellValueByColumnAndRow(3 + $StafSyukkin[$j]['day'], $i + 7, 'Ｃ');
                } elseif ($StafSyukkin[$j]['support'] == 6) {
                    $sheet->setCellValueByColumnAndRow(3 + $StafSyukkin[$j]['day'], $i + 7, 'Ｋ');
                } elseif  ($StafSyukkin[$j]['bikou'] == 'リモートワーク') {
                    $sheet->setCellValueByColumnAndRow(3 + $StafSyukkin[$j]['day'], $i + 7, 'Ｒ');
                } else {
                    $sheet->setCellValueByColumnAndRow(3 + $StafSyukkin[$j]['day'], $i + 7, '〇');
                }
            }
        }

        //利用者出勤予定欄拡張
        if ( count($RiyousyaList) >= 2) {
            //コピー元のセルアドレス
            $fromAddress = "A4:H4";
            //コピー先のセル
            $dustAddress = "A5";

            //セル値を配列で取得
            $range = $sheet->rangeToArray($fromAddress);

            //行挿入
            $sheet->insertNewRowBefore(5, count($RiyousyaList) - 1);

            //始点に、配列でコピーした値を貼り付け
            $sheet->fromArray($range, null, $dustAddress);

            //罫線・背景色修正
            $endcell = columnNumberToLetter( intval(substr($edate, -2)));
            for ( $i = 0; $i < count($RiyousyaList); $i++) {
                //罫線
                $cellRange = 'B' . strval($i + 4) . ':'. columnNumberToLetter( intval(substr($edate, -2))) . strval($i + 4);
                $objStyle = $sheet->getStyle($cellRange);
                $objBorders = $objStyle->getBorders();
                $objBorders->getBottom()->setBorderStyle(Border::BORDER_THIN);

                //背景色
                if ( ($i % 2) == 1) {
                    $objFill = $objStyle->getFill();
                    $objFill->getStartColor()->setARGB('ffffffff');
                }
            }

            //罫線
            $cellRange = 'B' . strval($i + 4 - 1) . ':B' . strval($i + 4 - 1);
            $objStyle = $sheet->getStyle($cellRange);
            $objBorders = $objStyle->getBorders();
            $objBorders->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);
        }

        //利用者出勤情報出力
        for ( $i = 0; $i < count($RiyousyaList); $i++) {
            //勤務時間・氏名出力
            $sheet->setCellValueByColumnAndRow(2, $i + 4, $RiyousyaList[$i]['work_hours']);
            $sheet->setCellValueByColumnAndRow(3, $i + 4, $RiyousyaList[$i]['name']);

            //出勤情報取得
            $RiyousyaSyukkin = $attendanceTable
            ->find()
            ->select( ['day' => 'RIGHT(date, 2)','remote' => 'remote','support' => 'support'])
            ->where(['koukyu' => 0, 'kekkin' => 0, 'paid' => 0, 'user_id' => $RiyousyaList[$i]['user_id'], 'date >=' => date('Y-m-d',$sdate),'date <=' => $edate])
            ->enableHydration(false)
            ->toArray();        
    
            //出勤情報出力
            for ( $j = 0; $j < count($RiyousyaSyukkin); $j++) {
                if ( $RiyousyaSyukkin[$j]['remote'] == 1) {
                    $sheet->setCellValueByColumnAndRow(3 + $RiyousyaSyukkin[$j]['day'], $i + 4, '在');
                } elseif ($RiyousyaSyukkin[$j]['support'] == 3) {
                    $sheet->setCellValueByColumnAndRow(3 + $RiyousyaSyukkin[$j]['day'], $i + 4, 'C');
                } else {
                    $sheet->setCellValueByColumnAndRow(3 + $RiyousyaSyukkin[$j]['day'], $i + 4, '〇');
                }
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename='.date("Y-m",$sdate)."　勤務予定表.xlsx");
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

    public function kissyokuhyou() 
    {
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 0) exit;
        
        //データ取得
        $data = $this->request->getData();

        //検索期間の作成
        $sdate = mktime(0,0,0,$data["month"],1,$data["year"]); 
        $edate = date('Y-m-d',mktime(0,0,0,$data["month"]+1,0,$data["year"]));

        //利用者基本データ取得
        $reportsTable = TableRegistry::get('Reports');
        $RiyousyaList = $reportsTable
        ->find()
        ->select( ['user_id' => 'user_id','name' => 'users.name'])
        ->join(['type' => 'inner', 'table' => 'users', 'conditions' => 'user_id = users.id'])
        ->where(['date >=' => date('Y-m-d',$sdate),'date <=' => $edate, 'kissyoku <>' => 0])
        ->distinct(['user_id']) 
        ->order(['user_id' => 'ASC'])
        ->enableHydration(false)
        ->toArray(); 
        
        $holidays = \Yasumi\Yasumi::create('Japan', $data["year"], 'ja_JP');
        $weekday = ['日','月','火','水','木','金','土'];
        $filePath = './template/kissyoku.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($filePath);
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();
        
        //指定月日を表示
        $sheet->setCellValueByColumnAndRow(2, 1, date('Y年',$sdate));
        $sheet->setCellValueByColumnAndRow(3, 1, date('n月',$sdate));

        //曜日・土日着色
        for ( $i = 1; $i <= date('t',$sdate); $i++) {
            $timestamp = mktime(0,0,0,$data["month"],$i,$data["year"]);
            
            //曜日設定
            $sheet->setCellValueByColumnAndRow(2 + $i, 3, $weekday[date('w',$timestamp)]);

            //土日祝日着色
            if ( ($weekday[date('w',$timestamp)] == '土') || 
                 ($weekday[date('w',$timestamp)] == '日') ||
                 ($holidays->isHoliday(new \DateTime(date('Y-m-d',$timestamp))) == 1)){
                $cell = getCellRange(2 + $i,2);
                $sheet->getStyle($cell)
                ->getFont()
                ->getColor()
                ->setRGB(($weekday[date('w',$timestamp)] == '土') ? '0000FF' : 'FF0000' );
            }
        }

        //末尾の日データ削除処理
        if (substr($edate, -2) < 31 ) {
            //罫線スタイルを取得
            $sourceBorders = $sheet->getStyle("AG2:AG7")->getBorders();
            $rightBorderColor = $sourceBorders->getRight()->getColor()->getARGB();
            if (!$rightBorderColor) { 
                $rightBorderColor = \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK; // デフォルトで黒を設定
            }

            //罫線作成
            $destinationRange = columnNumberToLetter( intval(substr($edate, -2)) - 1) . "2:" . columnNumberToLetter( intval(substr($edate, -2)) - 1)  . "4";
            $styleArray = [
                'borders' => [
                    'right' => [
                        'borderStyle' => $sourceBorders->getRight()->getBorderStyle(),
                        'color' => ['argb' => $sourceBorders->getRight()->getColor()->getARGB()]
                    ],
                ],
            ];
            $sheet->getStyle($destinationRange)->applyFromArray($styleArray);
            
            //列削除
            $sheet->removeColumn(columnNumberToLetter( intval(substr($edate, -2))) ,31 - substr($edate, -2));
        }

        //利用者食事予定欄拡張
        if ( count($RiyousyaList) >= 2) {
            //コピー元のセルアドレス
            $fromAddress = "A4:H4";
            //コピー先のセル
            $dustAddress = "A5";

            //セル値を配列で取得
            $range = $sheet->rangeToArray($fromAddress);

            //行挿入
            $sheet->insertNewRowBefore(5, count($RiyousyaList) - 1);

            //始点に、配列でコピーした値を貼り付け
            $sheet->fromArray($range, null, $dustAddress);

            //罫線・背景色修正
            $endcell = columnNumberToLetter( intval(substr($edate, -2)));
            for ( $i = 0; $i < count($RiyousyaList); $i++) {
                //罫線
                $cellRange = 'B' . strval($i + 4) . ':'. columnNumberToLetter( intval(substr($edate, -2)) - 1 ) . strval($i + 4);
                $objStyle = $sheet->getStyle($cellRange);
                $objBorders = $objStyle->getBorders();
                $objBorders->getBottom()->setBorderStyle(Border::BORDER_THIN);

                //背景色
                if ( ($i % 2) == 1) {
                    $objFill = $objStyle->getFill();
                    $objFill->getStartColor()->setARGB('ffffffff');
                }
            }

            $objStyle = $sheet->getStyle($cellRange);
            $objBorders = $objStyle->getBorders();
            $objBorders->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);
    }

        //利用者食事情報出力
        for ( $i = 0; $i < count($RiyousyaList); $i++) {
            //氏名出力
            $sheet->setCellValueByColumnAndRow(2, $i + 4, $RiyousyaList[$i]['name']);

            //食事情報取得
            $RiyousyaKissyoku = $reportsTable
            ->find()
            ->select( ['day' => 'RIGHT(date, 2)','kissyoku' => 'kissyoku'])
            ->where(['user_id' => $RiyousyaList[$i]['user_id'], 'date >=' => date('Y-m-d',$sdate),'date <=' => $edate, 'kissyoku <>' => 0])
            ->enableHydration(false)
            ->toArray();        
    
            //食事情報出力
            for ( $j = 0; $j < count($RiyousyaKissyoku); $j++) {
                switch ($RiyousyaKissyoku[$j]['kissyoku']) {
                    case 1 :
                        $kissyoku = "〇";
                        break;
                    case 2 :
                        $kissyoku = "1/2";
                        break;
                    case 3 :
                        $kissyoku = "1/3";
                        break;
                    case 4 :
                        $kissyoku = "1/4";
                        break;
                }
                $sheet->setCellValueByColumnAndRow(2 + $RiyousyaKissyoku[$j]['day'], $i + 4, $kissyoku);
            }
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename='.date("Y-m",$sdate)."　喫食表.xlsx");
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

    public function sougeikirokubo()
    {
        $this->autoRender = false;
        $data = $this->request->getData();
        $year = isset($data['year']) ? $data['year'] : date('Y');
        $month = isset($data['month']) ? sprintf('%02d', $data['month']) : date('m');
        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        // データ取得
        $transportsTable = TableRegistry::get('Transports');
        $transports = $transportsTable->find()
            ->contain(['Users'])
            ->where([
                'date >=' => $startDate,
                'date <=' => $endDate
            ])
            ->order(['date' => 'ASC', 'kind' => 'ASC', 'user_id' => 'ASC'])
            ->toArray();

        // 名称変換用リスト
        $usersTable = TableRegistry::get('Users');
        $users = $usersTable->find('list', ['keyField' => 'id', 'valueField' => 'name'])->toArray();
        $staffs = $usersTable->find('list', ['keyField' => 'id', 'valueField' => 'name'])
            ->where(['Users.adminfrag' => 1])->toArray();
        $sougeicarTable = TableRegistry::get('sougeicar');
        $cars = $sougeicarTable->find()->where(['del' => 0])->toArray();
        $carNoList = [];
        $carNameList = [];
        foreach ($cars as $car) {
            $carNoList[$car['id']] = $car['no'];
            $carNameList[$car['id']] = $car['name'];
        }

        // テンプレート読み込み
        $templatePath = WWW_ROOT . 'template' . DS . 'sougeikirokubo.xlsx';
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // タイトル・ヘッダ・罫線行の取得
        $titleRow = $sheet->rangeToArray('B2:L2')[0];
        $headerRow1 = $sheet->rangeToArray('B5:L5')[0];
        $headerRow2 = $sheet->rangeToArray('B6:L6')[0];
        $borderRow = $sheet->rangeToArray('B7:L7')[0];
        $borderStyle = $sheet->getStyle('B7:L7');

        // 1ページ50件
        $perPage = 50;
        $page = 1;
        $row = 7; // 1ページ目データ開始行
        $dataCount = 0;
        $pageCount = ceil(count($transports) / $perPage);
        $currentRow = $row;
        $pageRowStart = $row;

        // 1ページ目タイトル・ページ番号
        $sheet->setCellValue('B3', "{$year}年" . intval($month) . "月 送迎記録簿");
        $sheet->setCellValue('L3', "ページ1");

        foreach ($transports as $i => $t) {
            // 改ページ処理
            if ($i > 0 && $i % $perPage == 0) {
                $sheet->setBreak('B' . ($currentRow), \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
                $page++;
                $currentRow += 3; // 空白行2行追加（実質的に2行空くように調整）

                // --- 1ページ目のB3:L6を値＋書式ごとコピー ---
                $copyRows = 4; // B3:L6は4行分
                for ($r = 0; $r < $copyRows; $r++) {
                    $srcRow = 3 + $r;
                    $dstRow = $currentRow + $r;
                    for ($c = 0; $c < 11; $c++) { // B〜Lは11列
                        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(2 + $c);
                        $srcCell = $colLetter . $srcRow;
                        $dstCell = $colLetter . $dstRow;
                        // 値コピー
                        $sheet->setCellValue($dstCell, $sheet->getCell($srcCell)->getValue());
                        // 書式コピー
                        $sheet->duplicateStyle($sheet->getStyle($srcCell), $dstCell);
                    }
                }
                // セル結合もコピー（B3:L6範囲のみ）
                foreach ($sheet->getMergeCells() as $mergeRange) {
                    if (preg_match('/([A-Z]+)([0-9]+):([A-Z]+)([0-9]+)/', $mergeRange, $m)) {
                        $startCol = $m[1];
                        $startRow = (int)$m[2];
                        $endCol = $m[3];
                        $endRow = (int)$m[4];
                        if ($startRow >= 3 && $startRow <= 6) { // ヘッダー範囲のみ
                            $offset = $currentRow - 3;
                            $newStartRow = $startRow + $offset;
                            $newEndRow = $endRow + $offset;
                            $newRange = $startCol . $newStartRow . ':' . $endCol . $newEndRow;
                            $sheet->mergeCells($newRange);
                        }
                    }
                }
                // ページ番号・タイトル上書き
                $sheet->setCellValue('L' . $currentRow, "ページ{$page}");
                $sheet->setCellValue('B' . $currentRow, "{$year}年" . intval($month) . "月 送迎記録簿");
                // ヘッダー最下線を太線に
                $headerBottomRow = $currentRow + 3; // ヘッダー2行目の行番号
                $sheet->getStyle('B' . $headerBottomRow . ':L' . $headerBottomRow)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
                $currentRow += 4; // データ行開始位置
                $pageRowStart = $currentRow;
            }
            // データ行
            $sheet->fromArray($borderRow, null, 'B' . $currentRow);
            $sheet->duplicateStyle($borderStyle, 'B' . $currentRow . ':L' . $currentRow);
            $sheet->setCellValue('B' . $currentRow, $t->date->format('j日'));
            $sheet->setCellValue('C' . $currentRow, $users[$t->user_id] ?? '');
            $sheet->setCellValue('D' . $currentRow, $t->kind == 1 ? '迎' : ($t->kind == 2 ? '送' : ''));
            $sheet->setCellValue('E' . $currentRow, $t->hatsutime ? $t->hatsutime->format('H:i') : '');
            $sheet->setCellValue('F' . $currentRow, $t->hatsuplace);
            $sheet->setCellValue('G' . $currentRow, $t->taykutime ? $t->taykutime->format('H:i') : '');
            $sheet->setCellValue('H' . $currentRow, $t->tyakuplace);
            $sheet->setCellValue('I' . $currentRow, $staffs[$t->staff_id] ?? '');
            $sheet->setCellValue('J' . $currentRow, $staffs[$t->substaff_id] ?? '');
            $sheet->setCellValue('K' . $currentRow, $carNoList[$t->car] ?? '');
            $sheet->setCellValue('L' . $currentRow, $carNameList[$t->car] ?? '');
            // データ行の縦罫線を細線に
            for ($col = 2; $col <= 12; $col++) { // B=2, L=12
                $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $currentRow;
                $sheet->getStyle($cell)->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $sheet->getStyle($cell)->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            }
            // 表全体の左端・右端罫線を太線に
            $sheet->getStyle('B' . $currentRow)->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
            $sheet->getStyle('L' . $currentRow)->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
            // データ行の横罫線（上・下）を細線に
            $sheet->getStyle('B' . $currentRow . ':L' . $currentRow)->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $sheet->getStyle('B' . $currentRow . ':L' . $currentRow)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $currentRow++;
            $dataCount++;
            // ページ最終行の下罫線を太線に
            if ($dataCount % $perPage == 0 || $dataCount == count($transports)) {
                $sheet->getStyle('B' . ($currentRow - 1) . ':L' . ($currentRow - 1))
                    ->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);
            }
        }

        // 1ページ目のヘッダー最下線も太線に
        $sheet->getStyle('B6:L6')->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM);

        // 出力
        $filename = "送迎記録簿_{$year}{$month}.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}



// 西暦 => 和暦
function wareki($year) {
    $eras = array(
        array('year' => 2018, 'name' => '令和'),
        array('year' => 1988, 'name' => '平成'),
        array('year' => 1925, 'name' => '昭和'),
        array('year' => 1911, 'name' => '大正'),
        array('year' => 1867, 'name' => '明治')
    );

    foreach($eras as $era) {
        if ($year > $era['year']) {
            $era_year = $year - $era['year'];
            return $era['name'] . ($era_year === 1 ? '元年' : ' '.$era_year.' '.'年');
        }
    }

    return null;
}

function getCellRange($i,$j) {
    // 26文字（A-Z）のアルファベットを処理
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    
    // 列番号を計算
    $column = '';
    
    // 26進法でアルファベットを作成（Z -> AA -> AB -> ...）
    while ($i >= 0) {
        $column = $alphabet[$i % 26] . $column;  // 現在のアルファベットを追加
        $i = floor($i / 26) - 1;  // 次の桁へ進む
    }

    // セル範囲を作成（例：A2:A3、B2:B3など）
    return sprintf("%s%d:%s%d", $column, $j, $column, $j + 1);
}

function columnNumberToLetter($colNum) {
    $offset = 3; // 1を'D'に対応させるためのオフセット
    $colNum += $offset;

    $letter = '';
    while ($colNum > 0) {
        $colNum--; // 1-based index を 0-based に変換
        $letter = chr(65 + ($colNum % 26)) . $letter;
        $colNum = intdiv($colNum, 26);
    }
    return $letter;
}
