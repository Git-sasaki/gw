<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use setasign\Fpdi;
use Yasumi\Yasumi;
use Cake\ORM\Table;

use Cake\Log\Log;

class PdfsController extends AppController
{
    public function getquery0() {            
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            $usersTable = TableRegistry::get("Users");
            $attendanceTable = TableRegistry::get('Attendances');

            // ユーザーの取得
            $data = $this->request->getData();
                if($data["hidden"]==0 || $data["hidden"]==2) {
                $timestamp = mktime(0,0,0,$data["month"],1,$data["year"]);
                if($data["id"] != 0) {
                    // 就労タイプに基づいてデータをフィルタリング
                    $work_type = isset($data["work_type"]) ? $data["work_type"] : '0';
                    
                    // work_typeとattendanceTableのuser_typeフィールドが一致するデータを取得
                    $results = $attendanceTable
                    ->find()
                    ->where(['Attendances.user_id' => $data["id"], 
                             'Attendances.date >=' => date('Y-m',$timestamp).'-01', 
                             'Attendances.date <=' => date('Y-m',$timestamp)."-".date("t",$timestamp),
                             'Attendances.user_type' => $work_type])
                    ->order(['Attendances.date'=>'ASC'])
                    ->EnableHydration(false)
                    ->toArray();
                    
                    if(empty($results)) {
                        // データが存在しない場合でも、設定値をセッションに保存
                        $this->request->getSession()->write([
                            'type' => $data["hidden"],
                            'uyear' => $data["year"],
                            'umonth' => $data["month"],
                            'uuser_id' => $data["id"],
                            'work_type' => isset($data["work_type"]) ? $data["work_type"] : '0',
                        ]);
                        $this->Flash->error(__('該当のデータが存在しません'));
                        return $this->redirect(["controller" => "prints", "action" => "indexn"]);
                    }
                } elseif($data["id"] == 0) {
                    // ALLが選択された場合、就労タイプに基づいてデータの存在確認
                    $work_type = isset($data["work_type"]) ? $data["work_type"] : '0';
                    
                    $flag = 0;
                    $users = $usersTable->find('list',['valueField'=>'id'])->toArray();
                    foreach($users as $user) {
                        $results = $attendanceTable
                        ->find()
                        ->select(['Attendances.koukyu','Attendances.paid','Attendances.kekkin'])
                        ->where(['Attendances.user_id' => $user, 
                                 'Attendances.date >=' => date('Y-m',$timestamp).'-01', 
                                 'Attendances.date <=' => date('Y-m',$timestamp)."-".date("t",$timestamp),
                                 'Attendances.user_type' => $work_type])
                        ->order(['Attendances.date'=>'ASC'])
                        ->EnableHydration(false)
                        ->first();
                        pr($results);
                        if(!empty($results)) {
                            $flag = 1;
                            break;
                        }
                    }
                    if($flag == 0) {
                        // データが存在しない場合でも、設定値をセッションに保存
                        $this->request->getSession()->write([
                            'type' => $data["hidden"],
                            'uyear' => $data["year"],
                            'umonth' => $data["month"],
                            'uuser_id' => $data["id"],
                            'work_type' => isset($data["work_type"]) ? $data["work_type"] : '0',
                        ]);
                        $this->Flash->error(__('該当のデータが存在しません'));
                        return $this->redirect(["controller" => "prints", "action" => "indexn"]);
                    }
                } elseif($data["hidden"]==1) {
                    $flag = 0;
                    $users = $usersTable->find('list',['valueField'=>'id'])->toArray();
                    foreach($users as $user) {
                        $results = $attendanceTable
                        ->find()
                        ->select(['Attendances.koukyu','Attendances.paid','Attendances.kekkin'])
                        ->where(['Attendances.user_id' => $user, 
                                 'Attendances.date >=' => date('Y-m',$timestamp).'-01', 
                                 'Attendances.date <=' => date('Y-m',$timestamp)."-".date("t",$timestamp)])
                        ->order(['Attendances.date'=>'ASC'])
                        ->EnableHydration(false)
                        ->first();
                        pr($results);
                        if(!empty($results)) {
                            $flag = 1;
                            break;
                        }
                    }
                    if($flag == 0) {
                        $this->Flash->error(__('該当のデータが存在しません'));
                        return $this->redirect(["controller" => "prints", "action" => "indexn"]);
                    }
                }
            }
            // セッションでopenwindowのフラグを立ててindexに戻す
            if($data["hidden"]==0) {
                $this->request->getSession()->write([
                    'type' => $data["hidden"],
                    'uyear' => $data["year"],
                    'umonth' => $data["month"],
                    'uuser_id' => $data["id"],
                    'work_type' => isset($data["work_type"]) ? $data["work_type"] : '0', // 就労タイプ（A型=0またはB型=1）
                ]);
                $this->request->getSession()->write('updf',true);
            } elseif($data["hidden"]==1) {
                $this->request->getSession()->write([
                    'type' => $data["hidden"],
                    'hyear' => $data["hyear"],
                    'hmonth' => $data["hmonth"],
                    'oyear' => $data["oyear"],
                    'omonth' => $data["omonth"],
                    'muser_id' => $data["id"],
                ]);
                $this->request->getSession()->write('umpdf',true);
            } elseif($data["hidden"]==2) {
                $this->request->getSession()->write([
                    'type' => $data["hidden"],
                    'syear' => $data["year"],
                    'smonth' => $data["month"],
                    'suser_id' => $data["id"],
                ]);
                $this->request->getSession()->write('spdf',true);
            } elseif($data["hidden"]==3) {
                $this->request->getSession()->write([
                    'pyear' => $data["year"],
                    'pmonth' => $data["month"],
                    'psort' => $data["sort"],
                ]);
                $this->request->getSession()->write('paid',true);
            } elseif($data["hidden"]==4) {
                $this->request->getSession()->write([
                    'choyear' => $data["year"],
                    'chomonth' => $data["month"],
                ]);
                $this->request->getSession()->write('chouka',true);
            } elseif($data["hidden"]==5) {
                if($data["sanchom"] == 11) {
                    $toshi = $data["year"]+1;
                    $owari = 1;
                } elseif($data["sanchom"] == 12) {
                    $toshi = $data["year"]+1;
                    $owari = 2;
                } else {
                    $toshi = $data["year"];
                    $owari = $data["sanchom"]+2;
                }
                $this->request->getSession()->write([
                    'sanchohyear' => $data["year"],
                    'sanchohmonth' => $data["sanchom"],
                    'sanchooyear' => $toshi,
                    'sanchoomonth' => $owari,
                ]);
                $this->request->getSession()->write('sancho',true);
            } else {
                $this->Flash->error(__('該当データがありません。'));
                return $this->redirect(['controller'=>"prints", "action" => "indexn"]);
            }
            return $this->redirect(["controller" => "prints", "action" => "indexn"]);
        } else {
            $this->Flash->error(__('アクセス権限がありません'));
            return $this->redirect(['controller'=>'users','action'=>'login']);
        }
    }

    public function spdf() {
        // 基本設定
        $this->RequestHandler->respondAs('application/pdf');
        $pdf = new Fpdi\TcpdfFpdi();
        mb_internal_encoding('UTF-8');
        $pdf->SetMargins(0, 0, 0); 
        $pdf->setPrintHeader(false); 
        $pdf->setPrintFooter(false);
        
        // テンプレートの読み込み
        $pdf->setSourceFile(WWW_ROOT."pdf/admtemplate.pdf");
        $torf = array("","〇");
        $weekday = array('日','月','火','水','木','金','土');

        // データを取得
        $year = $this->request->getSession()->read('syear');
        $month = $this->request->getSession()->read('smonth');
        $staff = $this->request->getSession()->read('suser_id');
        
        // カレンダーの設定
        $timestamp = mktime(0,0,0,$month,1,$year);
        $timestamp2 = mktime(0,0,0,$month,date('t',$timestamp),$year);
        $holidays = \Yasumi\Yasumi::create('Japan', $year, 'ja_JP');
        $usersTable = TableRegistry::get("Users");
        $attendanceTable = TableRegistry::get('Attendances');

        if($staff == 0) {
            $ichi_users = [];
            $getusers = $usersTable
            ->find()
            ->where(['Users.adminfrag'=>1])
            ->order(['Users.narabi'=>'ASC','Users.id'=>'ASC'])
            ->toArray();
            foreach($getusers as $getuser) {
                // narabi99：退職者ではないものの出勤がない者
                if($getuser["narabi"] != 99 && strtotime($getuser["created"]) < $timestamp2) {
                    if(empty($getuser["retired"]) || strtotime($getuser["retired"]) >= $timestamp) {
                        array_push($ichi_users,$getuser);
                    }
                }
            }
        } else {
            $ichi_users = $usersTable
            ->find()
            ->where(['Users.id' => $staff])
            ->toArray();
        }

        foreach($ichi_users as $ichi_user) {
            // 出勤情報の取得
            $results = $attendanceTable
            ->find()
            ->where(['Attendances.user_id' => $ichi_user["id"], 
                     'Attendances.date >=' => date('Y-m',$timestamp).'-01', 
                     'Attendances.date <=' => date('Y-m',$timestamp)."-".date("t",$timestamp)])
            ->order(['Attendances.date'=>'ASC'])
            ->EnableHydration(false)
            ->toArray();


            // デフォルト値の設定
            $stampA = 0;                                    // 合計勤務時間のタイムスタンプの初期値
            $stampO = 0;                                    // 合計残業時間のタイムスタンプの初期値
            $work_day = 0;                                  // 出勤日数合計

            //有休時間算出の為に1日の基本労働時間を算出
            $worktime = strtotime(date($ichi_user["douttime"]->i18nFormat("yyyy/MM/dd H:m"))) - strtotime(date($ichi_user["dintime"]->i18nFormat("yyyy/MM/dd H:m")));
            $w1 = date($ichi_user["douttime"]->i18nFormat("yyyy/MM/dd H:m"));
            if ( !empty($ichi_user["dresttime"])) {
                $worktime_h = date($ichi_user["dresttime"]->i18nFormat("H"));
                $worktime_m = date($ichi_user["dresttime"]->i18nFormat("m"));
                $worktime -= ($worktime_h * 3600 + $worktime_m * 60);
            }

            if(empty($results)) continue;

            // ページの追加
            $pdf->AddPage(); 
            $index = $pdf->importPage(1); 
            $pdf->useTemplate($index, 0, 0);

            // 上部の年月と名前の表示
            $pdf -> SetFont('kozminproregular','',20);
            $pdf -> Text(19,26.5,$year);
            $pdf -> Text(48.5,26.5,$month);
            if(mb_strlen($ichi_user["name"] )<= 5) {
                $pdf -> Text(130,26.5,$ichi_user["name"]);
            } elseif(mb_strlen($ichi_user["name"]) <= 7) {
                $pdf -> Text(127.5,26.5,$ichi_user["name"]);
            } elseif(mb_strlen($ichi_user["name"]) <= 10) {
                $pdf -> Text(120,26.5,$ichi_user["name"]);
            }

            // 内容の表示
            $yukyu = 0;
            $meshi_total = 0;
            for($i = 1; $i<=date("t",$timestamp); $i++) {
                $pdf -> SetFont('kozminproregular','',12);
                $timestamp = mktime(0,0,0,$month,$i,$year);
                    if(date("w",$timestamp) == 0){
                        $color = "red";
                    } elseif(date("w",$timestamp) == 6) {
                        $color = "blue";
                    } elseif($holidays->isHoliday(new \DateTime(date("Y-m-d",$timestamp))) == 1) {
                        $color = "red";
                    } else {
                        $color = "black";
                    }
                $days = $weekday[date("w",$timestamp)];            
                $datehtml = '<span style = "color:'.$color.'">'.$i.'</span>';
                $dayhtml = '<span style = "color:'.$color.'">'.$days.'</span>';
                //$pdf -> writeHTMLCell(15.6,6.68,8.8,53.51+($i - 1)*6.69,$datehtml,0,0,0,true,"C",true);
                //$pdf -> writeHTMLCell(15.6,6.68,23.8,53.51+($i - 1)*6.69,$dayhtml,0,0,0,true,"C",true);
                $pdf -> writeHTMLCell(15.6,6,9.5,49.5+($i - 1)*5.96,$datehtml,0,0,0,true,"C",true);
                $pdf -> writeHTMLCell(15.6,6,23,49.5+($i - 1)*5.96,$dayhtml,0,0,0,true,"C",true);

                // 出社、退社、休憩時間のどれかに空欄があると時間は表示されない
                $d = $results[$i-1]["date"]->i18nFormat('d') - 1;
                if(!empty($results[$i-1]["intime"] && !empty($results[$i-1]["outtime"]))) {
                    // 出勤・退勤時間
                    //$pdf -> writeHTMLCell(15.6,6.68,41.5,53.51+($d)*6.69,$results[$i-1]["intime"]->i18nFormat("HH:mm"),0,0,0,true,"C",true);
                    //$pdf -> writeHTMLCell(15.6,6.68,62.5,53.51+($d)*6.69,$results[$i-1]["outtime"]->i18nFormat("HH:mm"),0,0,0,true,"C",true);
                    $pdf -> writeHTMLCell(15.6,6.68,41,49.5+($d)*5.96,$results[$i-1]["intime"]->i18nFormat("HH: mm"),0,0,0,true,"C",true);
                    $pdf -> writeHTMLCell(15.6,6.68,62,49.5+($d)*5.96,$results[$i-1]["outtime"]->i18nFormat("HH: mm"),0,0,0,true,"C",true);
                    $ink = strtotime(date('Y-m-d')." ".$results[$i-1]["intime"]->i18nFormat("H:m"));
                    $ouk = strtotime(date('Y-m-d')." ".$results[$i-1]["outtime"]->i18nFormat("H:m"));
                    $stamp1 = $ouk - $ink;
                    // 休憩時間がある場合はカウント
                    if(!empty($results[$i-1]["resttime"])) {
                        //$pdf -> writeHTMLCell(15.6,6.68,83,53.51+($d)*6.69,$results[$i-1]["resttime"]->i18nFormat("H:mm"),0,0,0,true,"C",true);
                        $pdf -> writeHTMLCell(15.6,6.68,82,49.5+($d)*5.96,$results[$i-1]["resttime"]->i18nFormat("H: mm"),0,0,0,true,"C",true);
                        $exrest = explode(":",$results[$i-1]["resttime"]->i18nFormat("H:m"));
                        $rek = $exrest[0]*3600 + $exrest[1]*60;
                        $stamp1 -= $rek;
                    }
                    // 残業時間がある場合はカウント
                    if(!empty($results[$i-1]["overtime"]) && $results[$i-1]["overtime"]->i18nFormat("H:mm") != "0:00") {
                        //$pdf -> writeHTMLCell(15.6,6.68,127.4,53.51+($d)*6.69,$results[$i-1]["overtime"]->i18nFormat("H:mm"),0,0,0,true,"C",true);
                        $pdf -> writeHTMLCell(15.6,6.68,126,49.5+($d)*5.96,$results[$i-1]["overtime"]->i18nFormat("H: mm"),0,0,0,true,"C",true);
                        $exover = explode(":",$results[$i-1]["overtime"]->i18nFormat("H:m"));
                        $ovk = $exover[0]*3600 + $exover[1]*60;
                        $stampO += $ovk;
                    }
                    // 一日の勤務時間をカウント
                    $stampA += $stamp1;
                    $oneh = floor($stamp1 / 3600);
                    $onem = floor($stamp1 % 3600 / 60);
                    $work1 = sprintf('%02d',$oneh).": ".sprintf('%02d',$onem);
                    //$pdf -> writeHTMLCell(15.6,6.68,104.8,53.51+($d)*6.69,$work1,0,0,0,true,"C",true);
                    $pdf -> writeHTMLCell(15.6,6.68,103,49.5+($d)*5.96,$work1,0,0,0,true,"C",true);

                    //出勤日数をカウント
                    ++$work_day;

                } else if ($results[$i-1]["paid"] == 1) {

                    $oneh = floor($worktime / 3600);
                    $onem = $worktime % 3600 / 60;
                    $work1 = sprintf('%02d',$oneh).":".sprintf('%02d',$onem);
                    $pdf -> writeHTMLCell(15.6,6.68,103,49.5+($d)*5.96,$work1,0,0,0,true,"C",true);

                    //有休を勤務時間に加算する場合の為の処理
                    $yukyu += $worktime;

                    //有休を出勤日数にカウントするかしないか？
                    $work_day = (false) ? ++$work_day : $work_day;
                }

                //食事提供
                if(!empty($results[$i-1]["meshi"])) {
                    ++$meshi_total;
                    $pdf -> writeHTMLCell(15.6,6.68,141.3,49.5+($d)*5.96,"〇",0,0,0,true,"C",true);
                }

                // 各種休みの場合は記載
                if(!empty($results[$i-1]["koukyu"])) {
                    $d = $results[$i-1]["date"]->i18nFormat('d') - 1;
                    $pdf -> writeHTMLCell(15.6,6.68,154,49.5+($d)*5.96,"公休",0,0,0,true,"C",true);
                } elseif(!empty($results[$i-1]["paid"])) {
                    $d = $results[$i-1]["date"]->i18nFormat('d') - 1;
                    $pdf -> writeHTMLCell(15.6,6.68,154,49.5+($d)*5.96,"有休",0,0,0,true,"C",true);
                } elseif(!empty($results[$i-1]["kekkin"])) {
                    $d = $results[$i-1]["date"]->i18nFormat('d') - 1;
                    $pdf -> writeHTMLCell(15.6,6.68,154,49.5+($d)*5.96,"欠勤",0,0,0,true,"C",true);
                }  
                // 備考がある場合は記載
                if(!empty($results[$i-1]["bikou"])){
                    $d = $i-1;
                    $pdf -> SetFont('kozminproregular','',9);
                    $pdf -> writeHTMLCell(33,6.68,166,50.2+($d)*5.96,$results[$i-1]["bikou"],0,0,0,true,"C",true);
                }
            }
            // 合計の勤務時間と残業時間の算出
            //有休を勤務時間に含める場合
            $stampA = (true) ? $stampA + $yukyu : $stampA;

            //勤務日数を印字
            $work_day_tmp = sprintf('%2d日', $work_day);
            $pdf -> SetFont('kozminproregular','',12);
            $pdf -> writeHTMLCell(20,6.68,60.8,235,$work_day_tmp,0,0,0,true,"C",true);

            $allh = floor($stampA / 3600);
            $allm = floor($stampA % 3600 / 60);
            $alloh = floor($stampO / 3600);
            $allom = floor($stampO % 3600 / 60);
            $pdf -> SetFont('kozminproregular','',12);
            $allwork = sprintf('%3d',$allh).": ".sprintf('%02d',$allm);
            $allovertime = sprintf('%02d',$alloh).": ".sprintf('%02d',$allom);
            //$pdf -> writeHTMLCell(16,6.68,104.8,260.8,$allwork,0,0,0,true,"C",true);
            //$pdf -> writeHTMLCell(15.6,6.68,127.4,260.8,$allovertime,0,0,0,true,"C",true);
            $pdf -> writeHTMLCell(20,6.68,99.8,235,$allwork,0,0,0,true,"C",true);  //104.8
            $pdf -> writeHTMLCell(15.6,6.68,127.4,235,$allovertime,0,0,0,true,"C",true);
            $pdf -> writeHTMLCell(15.6,6.68,141.8,235,$meshi_total,0,0,0,true,"C",true);
        }
        $pdf->Output();
    }

    public function updf() {
        // 基本設定
        $this->RequestHandler->respondAs('application/pdf');
        $pdf = new Fpdi\TcpdfFpdi();
        mb_internal_encoding('UTF-8');
        $pdf->SetMargins(0, 0, 0); 
        $pdf->setPrintHeader(false); 
        $pdf->setPrintFooter(false);
        $torf = array("","〇");
        $weekday = array('日','月','火','水','木','金','土');
        
        // セッションからデータを取得
        $year = $this->request->getSession()->read('uyear');
        $month = $this->request->getSession()->read('umonth');
        $staff = $this->request->getSession()->read('uuser_id');
        $work_type = $this->request->getSession()->read('work_type') ?: '0'; // 就労タイプ（デフォルトはA型=0）
    
        // テンプレートファイルの読み込み（A型・B型共通）
        $template_file = WWW_ROOT."pdf/template.pdf";
        $pdf->setSourceFile($template_file); 
        
        // カレンダーの設定
        $timestamp = mktime(0,0,0,$month,1,$year);
        $timestamp2 = mktime(0,0,0,$month,date('t',$timestamp),$year);
        $holidays = \Yasumi\Yasumi::create('Japan', $year, 'ja_JP');
        $usersTable = TableRegistry::get('Users');
        $attendanceTable = TableRegistry::get('Attendances');
        $reportTable = TableRegistry::get('Reports');
        $jigyoushasTable = TableRegistry::get('Jigyoushas');

        $getCompany = $jigyoushasTable
        ->find()
        ->where(['Jigyoushas.id'=>1])
        ->first();

        if($staff == 0) {
            $zero_users = [];
            $getusers = $usersTable
            ->find()
            ->where(['Users.adminFrag'=>0])
            ->order(['Users.id'=>'ASC'])
            ->toArray();
            foreach($getusers as $getuser) {
                // narabi99：退職者ではないものの出勤がない者
                if($getuser["narabi"] != 99 && strtotime($getuser["created"]) < $timestamp2) {
                    if(empty($getuser["retired"]) || strtotime($getuser["retired"]) >= $timestamp) {
                        array_push($zero_users,$getuser);
                    }
                }
            }
        } else {
            // ユーザーの取得
            $zero_users = $usersTable
            ->find()
            ->where(['Users.id' => $staff])
            ->order(['Users.narabi'=>'ASC','Users.id'=>'ASC'])
            ->toArray();
        }

        // 一ヶ月の全員分の合計出勤時間を算出する隠し変数
        $sumsum = 0;
        $nobenin = 0;
        $usercount = 0;

        foreach($zero_users as $zero_user) {
            // 就労タイプに基づいてデータをフィルタリング（A型=0, B型=1）
            $results = $attendanceTable
            ->find()
            ->where(['Attendances.user_id' => $zero_user["id"], 
                     'Attendances.date >=' => date('Y-m',$timestamp).'-01', 
                     'Attendances.date <=' => date('Y-m',$timestamp)."-".date("t",$timestamp),
                     'Attendances.user_type' => $work_type])
            ->order(['Attendances.date'=>'ASC'])
            ->EnableHydration(false)
            ->toArray();

            // $resultsが空の場合はcontinue
            if (empty($results)) {
                continue;
            }

            //有休時間算出の為に1日の基本労働時間を算出
            $worktime = strtotime(date($zero_user["douttime"]->i18nFormat("yyyy/MM/dd H:m"))) - strtotime(date($zero_user["dintime"]->i18nFormat("yyyy/MM/dd H:m")));
            $w1 = date($zero_user["douttime"]->i18nFormat("yyyy/MM/dd H:m"));
            if ( !empty($zero_user["dresttime"])) {
                $worktime_h = date($zero_user["dresttime"]->i18nFormat("H"));
                $worktime_m = date($zero_user["dresttime"]->i18nFormat("m"));
                $worktime -= ($worktime_h * 3600 + $worktime_m * 60);
            }

            $flag = 1;
            if(time() > $timestamp2) {
                for($i=0; $i<date('t',$timestamp); $i++) {
                    if(!empty($results[$i]["intime"]) && $results[$i]["intime"] != NULL) {
                        $flag = 1;
                        $usercount++;
                        break;
                    }
                }
            } else {
                if(!empty($results)) {
                    $flag = 1;
                    $usercount++;
                }
            }

            // 初期値
            $attenddays = 0;                        // 出勤日数
            $kasan = 0;                             // 欠席時対応加算用
            $sumkekkin = 0;                         // 通常の欠勤
            $stampA = 0;                            // 勤務時間の合計のタイムスタンプ
            $stampR = 0;                            // 休憩時間の合計のタイムスタンプ
            $yukyu = 0;                             // 有休の時間合計

            // 合計時間を算出する隠しコマンド
            if(empty($zero_user["dintime"]) || empty($zero_user["douttime"])) {
                $defaultin = 0; $defaultout = 0; $paidbun = 0;
            } else {
                $defaultin = strtotime($zero_user["dintime"]->i18nFormat('HH:mm'));
                $defaultout = strtotime($zero_user["douttime"]->i18nFormat('HH:mm'));
                $paidbun = $defaultout - $defaultin;
            }
            
            if($flag == 1) {
                // ページの追加   
                $pdf->AddPage(); 
                $index = $pdf->importPage(1); 
                $pdf->useTemplate($index, 0, 0);
                
                // 名前とidの出力
                $pdf -> SetFont('kozminproregular','',10);
                $pdf -> Text(17.1,24.7,$year." 年 ".$month." 月");         
                // 事業所名の後に空白を1文字入れてA型もしくはB型を表示
                $work_type_label = ($work_type == '1') ? 'B型' : 'A型';
                $pdf -> Text(148,34,$getCompany["jname"]." ".$work_type_label);
                $pdf -> SetFont('kozminproregular','',8);
                $pdf -> Text(76,29,$zero_user['id']);    
                $pdf -> Text(33.5,32.5,$zero_user["sjnumber"]);         
                $pdf -> Text(162,29,$getCompany["jnumber"]);

                // 就労タイプの表示（B型の場合のみ）
                if ($work_type == 'B') {
                    $pdf -> SetFont('kozminproregular','',10);
                    $pdf -> Text(17.1,20,"就労タイプ：B型");
                }

                if(mb_strlen($zero_user["name"]."　さん") <= 8) {
                    $pdf -> SetFont('kozminproregular','',12);
                    $pdf -> Text(80,33.5,$zero_user["name"]."　さん");
                } elseif(mb_strlen($zero_user["name"]."　さん") <= 9) {
                    $pdf -> SetFont('kozminproregular','',12);
                    $pdf -> Text(78,33.5,$zero_user["name"]."　さん");
                } else {
                    $pdf -> SetFont('kozminproregular','',10);
                    $pdf -> Text(77,33.5,$zero_user["name"]."　さん");
                }
    
                $pdf -> SetFont('kozminproregular','',10);
                for($i = 1; $i <= date("t",$timestamp); $i++){
                    $timestamp = mktime(0,0,0,$month,$i,$year);
                    if(date("w",$timestamp) == 0){
                        $color = "red";
                    } elseif(date("w",$timestamp) == 6) {
                        $color = "blue";
                    } elseif($holidays->isHoliday(new \DateTime(date("Y-m-d",$timestamp))) == 1) {
                        $color = "red";
                    } else {
                        $color = "black";
                    }
                    $days = $weekday[date("w",$timestamp)];            
                    $datehtml = '<span style = "color:'.$color.'">'.$i.'</span>';
                    $dayhtml = '<span style = "color:'.$color.'">'.$days.'</span>';
                    $pdf -> writeHTMLCell(6.4,5.8,18.1,51.3 + ($i - 1)*5.85,$datehtml,0,0,0,true,"C",true);
                    $pdf -> writeHTMLCell(6.4,5.8,24.3,51.3 + ($i - 1)*5.85,$dayhtml,0,0,0,true,"C",true);

                    $pdf -> SetFont('kozminproregular','',9);
                    if(!empty($results[$i-1]["intime"]) && !empty($results[$i-1]["outtime"])) {
                        // 出勤日を+1
                        $attenddays++;
                        // 出勤・退勤時間
                        $pdf -> Text(31,45.5 + date($i*5.85),date($results[$i-1]["intime"]->i18nFormat("HH:mm")));
                        $pdf -> Text(43.7,45.5 + date($i*5.85),date($results[$i-1]["outtime"]->i18nFormat("HH:mm")));
                        $ink = strtotime(date('Y-m-d')." ".$results[$i-1]["intime"]->i18nFormat("HH:mm"));
                        $ouk = strtotime(date('Y-m-d')." ".$results[$i-1]["outtime"]->i18nFormat("HH:mm"));
                        $stamp1 = $ouk - $ink;
                        // 休憩時間がある場合はカウント
                        if(!empty($results[$i-1]["resttime"])) {
                            $exrest[0] = date($results[$i-1]["resttime"]->i18nFormat("H"));
                            $exrest[1] = date($results[$i-1]["resttime"]->i18nFormat("m"));
                            $pdf -> Text(76.7,45.5 + date($i*5.85), $exrest[0].":".sprintf("%02d",$exrest[1]));
                            $rek = $exrest[0]*3600 + $exrest[1]*60;
                            $stamp1 -= $rek;
                            $stampR += $rek;
                        } elseif(!empty($results[$i-1]["outtime"]) && !empty($results[$i-1]["intime"])) {
                            $pdf -> Text(76.7,45.5 + date($i*5.85), "0:00");
                        }
                        // 一日の勤務時間をカウント
                        $stampA += $stamp1;
                        $oneh = floor($stamp1 / 3600);
                        $onem = $stamp1 % 3600 / 60;
                        $pdf -> Text(56.2,45.5 + $i*5.85,$oneh.":".sprintf('%02d',$onem));
                    } else if ($results[$i-1]["paid"] == 1) {
                        $oneh = floor($worktime / 3600);
                        $onem = $worktime % 3600 / 60;
                        $pdf -> Text(56.2,45.5 + $i*5.85,$oneh.":".sprintf('%02d',$onem));
                    }
                }

                // その他出勤情報の出力
                foreach($results as $result){
                    $d = date($result["date"]->i18nFormat('d'));
                    $pdf -> SetFont('kozminproregular','',10);
                    $pdf -> Text(65.2,45.5 + $d*5.85,$torf[$result["ou"]]);
                    $pdf -> Text(70.6,45.5 + $d*5.85,$torf[$result["fuku"]]);
                    $pdf -> Text(87.8,45.5 + $d*5.85,$torf[$result["meshi"]]);
                    //$pdf -> Text(97.4,45.5 + $d*5.85,$torf[$result["medical"]]);
                    //$pdf -> Text(107.2,45.5 + $d*5.85,$torf[$result["support"]]);
                    $pdf -> Text(107.2,45.5 + $d*5.85,$torf[($result["support"] == 0) ? 0 : 1]);
                    $pdf -> Text(125.2,45.5 + $d*5.85,$torf[$result["koukyu"]]);
                    $pdf -> Text(132.2,45.5 + $d*5.85,$torf[$result["paid"]]);

                    //有休を勤務時間に加算する場合の為の処理
                    $yukyu = ($result["paid"] == 1) ? $yukyu + $worktime : $yukyu;

                    if($result["kekkin"] == 1) {
                        $pdf -> Text(139.2,45.5 + $d*5.85,$torf[$result["kekkin"]]);

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
                                $pdf -> Text(146.2,45.5 + $d*5.85,$torf[$result["kekkin"]]);
                                $kasan++;
                            }
                        }
                    }
                    
                    if(!empty($result["remote"])) {
                        $pdf -> Text(116.8,45.5 + $d*5.85,$torf[$result["remote"]]);
                    } else {
                        $pdf -> Text(116.8,45.5 + $d*5.85,$torf[0]);
                    }

                    if(strlen($result["bikou"]) < 30) {
                        $pdf -> SetFont('kozminproregular','',8);
                        $pdf -> Text(152,45.5 + $d*5.85,$result["bikou"]);
                    } else {
                        $pdf -> SetFont('kozminproregular','',7);
                        $pdf -> Text(152,46.4 + $d*5.85,$result["bikou"]);
                    }
                }
    
                //有休を勤務時間に含める場合
                $stampA = (true) ? $stampA + $yukyu : $stampA;

                $pdf -> SetFont('kozminproregular','',10);
                $allh = floor($stampA / 3600);
                $allm = $stampA % 3600 / 60;
                $allrh = floor($stampR / 3600);
                $allrm = $stampR % 3600 / 60;

                $pdf -> SetFont('kozminproregular','',9);
                // 合計休憩時間の表示
                $pdf -> Text(75.7,233.2,sprintf('%02d',$allrh).":".sprintf('%02d',$allrm));
                // 合計時間の表示
                if($allh >= 100) {
                    $pdf -> SetFont('kozminproregular','',8);
                    $pdf -> Text(54.5,233.2,sprintf('%02d',$allh).":".sprintf('%02d',$allm));
                } else {
                    $pdf -> Text(55.3,233.2,sprintf('%02d',$allh).":".sprintf('%02d',$allm));
                }
    
                // サービス提供関連の合計値
                $query = $attendanceTable->find('all');
                $sums = $query
                ->where(['Attendances.user_id' => $zero_user["id"], 
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
                $attenddays += $sums["sumpaid"];
    
                // 合計値の表示
                $pdf -> SetFont('kozminproregular','',8);
                if($attenddays < 10) {
                    $pdf -> Text(33.2,234.6,$attenddays." 日");
                } else {
                    $pdf -> Text(32.2,234.6,$attenddays." 日");
                }
                if($sums["sumkekkin"] < 10) {
                    $pdf -> Text(46,234.6,$sums["sumkekkin"]." 日");
                } else {
                    $pdf -> Text(45,234.6,$sums["sumkekkin"]." 日");
                }
                $pdf -> Text(65.2,231.5,$sums["sumou"]);
                $pdf -> Text(70.5,231.5,$sums["sumfuku"]);
                $pdf -> Text(68.7,234.6,$sums["sumou"] + $sums["sumfuku"]." 回");
                if($sums["sumeshi"] < 10) {
                    $pdf -> Text(87.1,234.6,$sums["sumeshi"]." 回");
                } else {
                    $pdf -> Text(87.3,234.6,$sums["sumeshi"]." 回");
                }
                if($sums["summedical"] < 10) { 
                    $pdf -> Text(96.5,234.6,$sums["summedical"]." 回");
                } else {
                    $pdf -> Text(95.8,234.6,$sums["summedical"]." 回");
                }
                if($sums["sumsupport"] < 10) {
                    $pdf -> Text(106.4,234.6,$sums["sumsupport"]." 回");
                } else {
                    $pdf -> Text(105.6,234.6,$sums["sumsupport"]." 回");
                }
                if(empty($sums["sumremote"])) {
                    $pdf -> Text(115.8,234.6,"0 回");
                } elseif($sums["sumremote"] < 10) {
                    $pdf -> Text(115.8,234.6,$sums["sumremote"]." 回");
                } else {
                    $pdf -> Text(114.8,234.6,$sums["sumremote"]." 回");
                }
                if($sums["sumkoukyu"] < 10) {
                    $pdf -> Text(124.3,234.6,$sums["sumkoukyu"]." 回");    
                } else {
                    $pdf -> Text(123.2,234.6,$sums["sumkoukyu"]." 回");  
                }
                if($sums["sumpaid"] < 10) {
                    $pdf -> Text(131.2,234.6,$sums["sumpaid"]." 回");    
                } else {
                    $pdf -> Text(130.7,234.6,$sums["sumpaid"]." 回");  
                }
                if($sums["sumkekkin"] < 10) {
                    $pdf -> Text(138.3,234.6,$sums["sumkekkin"]." 回");
                } else {
                    $pdf -> Text(137.3,234.6,$sums["sumkekkin"]." 回");
                }

                $pdf -> Text(145.3,234.6,$kasan." 回");

                if($workdays < 10) {
                    $pdf -> Text(153.5,234.6,$workdays." 日");
                } else {
                    $pdf -> Text(152.8,234.6,$workdays." 日");
                }
                $pdf -> Text(167.5,234.6,round($attenddays/$workdays*100) ." %");

                // 合計時間算出用の計算式
                $sumsum += $stampA + $paidbun * $sums["sumpaid"];
                $nobenin += $attenddays;
            }
        }

        // 出勤人数を把握するための隠しコマンド
        // pr($year."年".$month."月　出勤データ");
        // pr("人数：".$usercount);
        // pr("延べ人数：".$nobenin."人");
        // pr("合計勤務時間：".$sumsum);
        // exit;

        $pdf->Output();
    }
    
    public function umpdf() {
        // 基本設定
        $this->RequestHandler->respondAs('application/pdf');
        $pdf = new Fpdi\TcpdfFpdi();
        mb_internal_encoding('UTF-8');
        $pdf->SetMargins(0, 0, 0); 
        $pdf->setPrintHeader(false); 
        $pdf->setPrintFooter(false);
        $pdf->setSourceFile(WWW_ROOT."pdf/template.pdf"); 
        $torf = ["","〇"];
        $weekday = ['日','月','火','水','木','金','土'];
        
        // セッションからデータを取得
        $hyear = $this->request->getSession()->read('hyear');
        $hmonth = $this->request->getSession()->read('hmonth');
        $oyear = $this->request->getSession()->read('oyear');
        $omonth = $this->request->getSession()->read('omonth');
        $staff = $this->request->getSession()->read('muser_id');

        // カレンダーの設定
        $usersTable = TableRegistry::get('Users');
        $attendanceTable = TableRegistry::get('Attendances');
        $reportTable = TableRegistry::get('Reports');

        // ユーザーの取得
        $userdata = $usersTable
        ->find()
        ->where(['Users.id' => $staff])
        ->first();

        function attchk($year,$month,$user_id) 
        {
            $attendanceTable = TableRegistry::get('Attendances');
            $timestamp = mktime(0,0,0,$month,1,$year);
            $results = $attendanceTable
            ->find()
            ->where(['Attendances.user_id' => $user_id, 
                     'Attendances.date >=' => date('Y-m',$timestamp).'-01', 
                     'Attendances.date <=' => date('Y-m',$timestamp)."-".date("t",$timestamp)])
            ->order(['Attendances.date'=>'ASC'])
            ->EnableHydration(false)
            ->toArray();
            return $results;
        }

        function sums($year,$month,$user_id)
        {
            // サービス提供関連の合計値
            $attendanceTable = TableRegistry::get('Attendances');
            $timestamp = mktime(0,0,0,$month,1,$year);
            $query = $attendanceTable->find('all');
            $sums = $query
            ->where(['Attendances.user_id' => $user_id, 
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
            return $sums;
        }

        function cmpny()
        {
            $jigyoushasTable = TableRegistry::get('Jigyoushas');
            $cmpny = $jigyoushasTable
            ->find()
            ->where(['Jigyoushas.id'=>1])
            ->first();
            return $cmpny;
        }

        function oneday($tstamp)
        {
            $H = floor($tstamp / 3600);
            $M = ($tstamp % 3600) / 60;
            return $H.":".sprintf('%02d',$M);
        }

        function color($timestamp)
        {
            $holidays = \Yasumi\Yasumi::create('Japan', date('Y',$timestamp), 'ja_JP');
            if(date("w",$timestamp) == 0){
                return "red";
            } elseif(date("w",$timestamp) == 6) {
                return "blue";
            } elseif($holidays->isHoliday(new \DateTime(date("Y-m-d",$timestamp))) == 1) {
                return "red";
            } else {
                return "black";
            }
        }

        if($hyear == $oyear) {
            $forLast = $omonth;
        } else {
            $forLast = 12 + $omonth;
        }
        
        for($i=$hmonth; $i<=$forLast; $i++) {
            if($i >= 13) {
                $timestamp = mktime(0,0,0,$i-12,1,$oyear);
                $displayMonth = $i-12;
            } else {
                $timestamp = mktime(0,0,0,$i,1,$hyear);
                $displayMonth = $i;
            }
            $workdays = date('t',$timestamp)-8;

            $cmpny = cmpny();

            // 初期値
            $attenddays = 0;                        // 出勤日数
            $kasan = 0;                             // 欠席時対応加算用
            $sumkekkin = 0;                         // 通常の欠勤
            $stampA = 0;                            // 勤務時間の合計のタイムスタンプ
            $stampR = 0;                            // 休憩時間の合計のタイムスタンプ
            
            $results = attchk(date('Y',$timestamp),date('n',$timestamp),$staff);

            $frag = 1;
            foreach($results as $result) {
                if(!empty($result["intime"])) {
                    $frag = 1;
                    break;
                }
            }

            if($frag == 1) {
                // ページの追加   
                $pdf->AddPage(); 
                $index = $pdf->importPage(1); 
                $pdf->useTemplate($index, 0, 0);
                
                // 名前とidの出力
                $pdf -> SetFont('kozminproregular','',10);
                $pdf -> Text(17.1,24.7,date('Y',$timestamp)." 年 ".$displayMonth." 月");
                $pdf -> Text(148,34,$cmpny["jname"]);
                $pdf -> SetFont('kozminproregular','',8);
                $pdf -> Text(76,29,$userdata['id']);    
                $pdf -> Text(33.5,32.5,$userdata["sjnumber"]);         
                $pdf -> Text(162,29,$cmpny["jnumber"]);

                if(mb_strlen($userdata["name"]."　さん") <= 8) {
                    $pdf -> SetFont('kozminproregular','',12);
                    $pdf -> Text(80,33.5,$userdata["name"]."　さん");
                } elseif(mb_strlen($userdata["name"]."　さん") <= 9) {
                    $pdf -> SetFont('kozminproregular','',12);
                    $pdf -> Text(78,33.5,$userdata["name"]."　さん");
                } else {
                    $pdf -> SetFont('kozminproregular','',10);
                    $pdf -> Text(77,33.5,$userdata["name"]."　さん");
                }
    
                $pdf -> SetFont('kozminproregular','',10);
                for($j = 1; $j <= date("t",$timestamp); $j++){
                    $timestamp2 = mktime(0,0,0,date('n',$timestamp),$j,date('Y',$timestamp));
                    $sums = sums(date('Y',$timestamp),date('n',$timestamp),$staff);

                    $days = $weekday[date("w",$timestamp2)];            
                    $datehtml = '<span style = "color:'.color($timestamp2).'">'.$j.'</span>';
                    $dayhtml = '<span style = "color:'.color($timestamp2).'">'.$days.'</span>';
                    $pdf -> writeHTMLCell(6.4,5.8,18.1,51.3 + ($j - 1)*5.85,$datehtml,0,0,0,true,"C",true);
                    $pdf -> writeHTMLCell(6.4,5.8,24.3,51.3 + ($j - 1)*5.85,$dayhtml,0,0,0,true,"C",true);

                    $pdf -> SetFont('kozminproregular','',9);
                    if(!empty($results[$j-1]["intime"]) && !empty($results[$j-1]["outtime"])) {
                        // 出勤日を+1
                        $attenddays++;
                        // 出勤・退勤時間
                        $pdf -> Text(31,45.5 + date($j*5.85),date($results[$j-1]["intime"]->i18nFormat("HH:mm")));
                        $pdf -> Text(43.7,45.5 + date($j*5.85),date($results[$j-1]["outtime"]->i18nFormat("HH:mm")));
                        $ink = strtotime(date('Y-m-d')." ".$results[$j-1]["intime"]->i18nFormat("HH:mm"));
                        $ouk = strtotime(date('Y-m-d')." ".$results[$j-1]["outtime"]->i18nFormat("HH:mm"));
                        $stamp1 = $ouk - $ink;
                        // 休憩時間がある場合はカウント
                        if(!empty($results[$j-1]["resttime"])) {
                            $exrest[0] = date($results[$j-1]["resttime"]->i18nFormat("H"));
                            $exrest[1] = date($results[$j-1]["resttime"]->i18nFormat("m"));
                            $pdf -> Text(76.7,45.5 + date($j*5.85), $exrest[0].":".sprintf("%02d",$exrest[1]));
                            $rek = $exrest[0]*3600 + $exrest[1]*60;
                            $stamp1 -= $rek;
                            $stampR += $rek;
                        } elseif(!empty($results[$j-1]["outtime"]) && !empty($results[$j-1]["intime"])) {
                            $pdf -> Text(76.7,45.5 + date($j*5.85), "0:00");
                        }
                        // 一日の勤務時間をカウント
                        $stampA += $stamp1;
                        $oneh = floor($stamp1 / 3600);
                        $onem = $stamp1 % 3600 / 60;
                        $pdf -> Text(56.2,45.5 + $j*5.85,$oneh.":".sprintf('%02d',$onem));
                    }
                }

                // その他出勤情報の出力
                foreach($results as $result){
                    $d = date($result["date"]->i18nFormat('d'));
                    $pdf -> SetFont('kozminproregular','',10);
                    $pdf -> Text(65.2,45.5 + $d*5.85,$torf[$result["ou"]]);
                    $pdf -> Text(70.6,45.5 + $d*5.85,$torf[$result["fuku"]]);
                    $pdf -> Text(87.8,45.5 + $d*5.85,$torf[$result["meshi"]]);
                    //$pdf -> Text(97.4,45.5 + $d*5.85,$torf[$result["medical"]]);
                    //$pdf -> Text(107.2,45.5 + $d*5.85,$torf[$result["support"]]);
                    $pdf -> Text(107.2,45.5 + $d*5.85,$torf[($result["support"] == 0) ? 0 : 1]);
                    $pdf -> Text(125.2,45.5 + $d*5.85,$torf[$result["koukyu"]]);
                    $pdf -> Text(132.2,45.5 + $d*5.85,$torf[$result["paid"]]);

                    if($result["kekkin"] == 1) {
                        $pdf -> Text(139.2,45.5 + $d*5.85,$torf[$result["kekkin"]]);

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
                                $pdf -> Text(146.2,45.5 + $d*5.85,$torf[$result["kekkin"]]);
                                $kasan++;
                            }
                        }
                    }

/*
                    if($kasan < 4 && $result["kekkin"] == 1) {
                        $pdf -> Text(139.2,45.5 + $d*5.85,$torf[$result["kekkin"]]);
                        $pdf -> Text(146.2,45.5 + $d*5.85,$torf[$result["kekkin"]]);
                        $kasan++;
                    } elseif($result["kekkin"] == 1) {
                        $pdf -> Text(139.2,45.5 + $d*5.85,$torf[$result["kekkin"]]);
                    }
*/

                    if(!empty($result["remote"])) {
                        $pdf -> Text(116.8,45.5 + $d*5.85,$torf[$result["remote"]]);
                    } else {
                        $pdf -> Text(116.8,45.5 + $d*5.85,$torf[0]);
                    }

                    if(strlen($result["bikou"]) < 30) {
                        $pdf -> SetFont('kozminproregular','',8);
                        $pdf -> Text(152,45.5 + $d*5.85,$result["bikou"]);
                    } else {
                        $pdf -> SetFont('kozminproregular','',7);
                        $pdf -> Text(152,46.4 + $d*5.85,$result["bikou"]);
                    }
                }
    
                $pdf -> SetFont('kozminproregular','',10);
                $allh = floor($stampA / 3600);
                $allm = $stampA % 3600 / 60;
                $allrh = floor($stampR / 3600);
                $allrm = $stampR % 3600 / 60;

                $pdf -> SetFont('kozminproregular','',9);
                // 合計休憩時間の表示
                $pdf -> Text(75.7,233.2,sprintf('%02d',$allrh).":".sprintf('%02d',$allrm));
                // 合計時間の表示
                if($allh >= 100) {
                    $pdf -> SetFont('kozminproregular','',8);
                    $pdf -> Text(54.6,233.2,sprintf('%02d',$allh).":".sprintf('%02d',$allm));
                } else {
                    $pdf -> Text(55.3,233.2,sprintf('%02d',$allh).":".sprintf('%02d',$allm));
                }
    
                // 合計値の表示
                $pdf -> SetFont('kozminproregular','',8);
                if($attenddays < 10) {
                    $pdf -> Text(33.2,234.6,$attenddays." 日");
                } else {
                    $pdf -> Text(32.2,234.6,$attenddays." 日");
                }
                if($sums["sumkekkin"] < 10) {
                    $pdf -> Text(46,234.6,$sums["sumkekkin"]." 日");
                } else {
                    $pdf -> Text(45,234.6,$sums["sumkekkin"]." 日");
                }
                $pdf -> Text(65.2,231.5,$sums["sumou"]);
                $pdf -> Text(70.5,231.5,$sums["sumfuku"]);
                $pdf -> Text(68.7,234.6,$sums["sumou"] + $sums["sumfuku"]." 回");
                if($sums["sumeshi"] < 10) {
                    $pdf -> Text(87.1,234.6,$sums["sumeshi"]." 回");
                } else {
                    $pdf -> Text(87.3,234.6,$sums["sumeshi"]." 回");
                }
                if($sums["summedical"] < 10) { 
                    $pdf -> Text(96.5,234.6,$sums["summedical"]." 回");
                } else {
                    $pdf -> Text(95.8,234.6,$sums["summedical"]." 回");
                }
                if($sums["sumsupport"] < 10) {
                    $pdf -> Text(106.4,234.6,$sums["sumsupport"]." 回");
                } else {
                    $pdf -> Text(105.6,234.6,$sums["sumsupport"]." 回");
                }
                if(empty($sums["sumremote"])) {
                    $pdf -> Text(115.8,234.6,"0 回");
                } elseif($sums["sumremote"] < 10) {
                    $pdf -> Text(115.8,234.6,$sums["sumremote"]." 回");
                } else {
                    $pdf -> Text(114.8,234.6,$sums["sumremote"]." 回");
                }
                if($sums["sumkoukyu"] < 10) {
                    $pdf -> Text(124.3,234.6,$sums["sumkoukyu"]." 回");    
                } else {
                    $pdf -> Text(123.2,234.6,$sums["sumkoukyu"]." 回");  
                }
                if($sums["sumpaid"] < 10) {
                    $pdf -> Text(131.2,234.6,$sums["sumpaid"]." 回");    
                } else {
                    $pdf -> Text(130.7,234.6,$sums["sumpaid"]." 回");  
                }
                if($sums["sumkekkin"] < 10) {
                    $pdf -> Text(138.3,234.6,$sums["sumkekkin"]." 回");
                } else {
                    $pdf -> Text(137.3,234.6,$sums["sumkekkin"]." 回");
                }

                $pdf -> Text(145.3,234.6,$kasan." 回");

                if($workdays < 10) {
                    $pdf -> Text(153.5,234.6,$workdays." 日");
                } else {
                    $pdf -> Text(152.8,234.6,$workdays." 日");
                }
                $pdf -> Text(167.5,234.6,round($attenddays/$workdays*100) ." %");
            }
        }
        $pdf->Output();
    }

    public function schedule()
    {
        // 基本設定
        $this->RequestHandler->respondAs('application/pdf');
        $pdf = new Fpdi\TcpdfFpdi();
        mb_internal_encoding('UTF-8');
        $pdf->SetMargins(0, 0, 0); 
        $pdf->setPrintHeader(false); 
        $pdf->setPrintFooter(false);
        $pdf->setSourceFile(WWW_ROOT."pdf/paidlist.pdf");

        $year = $this->request->getSession()->read('pyear');
        $month = $this->request->getSession()->read('pmonth');
        $sort = $this->request->getSession()->read('psort');
        $timestamp = mktime(0,0,0,$month,1,$year);

        // テーブルの設定
        $usersTable = TableRegistry::get('Users');
        $attendanceTable = TableRegistry::get('Attendances');

        // ページの追加
        $pdf->AddPage(); 
        $index = $pdf->importPage(1); 
        $pdf->useTemplate($index, 0, 0);
        
        $pdf -> SetFont('kozminproregular','',15);
        $pdf -> Text(125,31.2,$year." 年 ".$month." 月");

        // ユーザーの一覧を取得
        $users = [];
        $getalls = $usersTable->find()->toArray();
        foreach($getalls as $getall) {
            if($getall["narabi"] != 99 && strtotime($getall["created"]) < $timestamp) {
                if(empty($getall["retired"]) || strtotime($getall["retired"]) >= $timestamp) {
                    array_push($users,$getall);
                }
            }
        }

        if($sort == 0) {
            $x = 0; $y = 1;
            foreach($users as $user) {
                for($i=1; $i<=date('t',$timestamp); $i++) {
                    $timestamp = mktime(0,0,0,$month,$i,$year);
                    $getpaid = $attendanceTable
                    ->find()
                    ->where(["Attendances.user_id"=>$user["id"],"Attendances.date"=>date('Y-m-d',$timestamp)])
                    ->first();
                    if(!empty($getpaid) && $getpaid["paid"] == 1) {
                        if(mb_strlen($user["name"]) == 4) {
                            $pdf -> Text(29.5+$x,45.35+6.5*$y,$user["name"]);      
                        } elseif(mb_strlen($user["name"]) == 5) {
                            $pdf -> Text(27+$x,45.35+6.5*$y,$user["name"]);
                        } else {
                            $pdf -> Text(24.5+$x,45.35+6.5*$y,$user["name"]);
                        }
                        $pdf -> Text(67.4+$x,45.6+6.5*$y,$month."月".sprintf('%02d',$i)."日");
                        $y++;
                    }
                    if($y == 34) {
                        $x = 82.1;
                        $y = 0;
                    }
                }
            }
        } else {
            $x = 0; $y = 1;
            for($i=1; $i<=date('t',$timestamp); $i++) {
                $timestamp = mktime(0,0,0,$month,$i,$year);
                foreach($users as $user) {
                    $getpaid = $attendanceTable
                    ->find()
                    ->where(["Attendances.user_id"=>$user["id"],"Attendances.date"=>date('Y-m-d',$timestamp)])
                    ->first();
                    if(!empty($getpaid) && $getpaid["paid"] == 1) {
                        if(mb_strlen($user["name"]) == 4) {
                            $pdf -> Text(29.5+$x,45.35+6.5*$y,$user["name"]);      
                        } elseif(mb_strlen($user["name"]) == 5) {
                            $pdf -> Text(27+$x,45.35+6.5*$y,$user["name"]);
                        } else {
                            $pdf -> Text(24.5+$x,45.35+6.5*$y,$user["name"]);
                        }
                        $pdf -> Text(67.4+$x,45.6+6.5*$y,$month."月".sprintf('%02d',$i)."日");
                        $y++;
                    }
                    if($y == 34) {
                        $x = 82.1;
                        $y = 0;
                    }
                }
            }
        }
        $pdf->Output();
    }

    public function chouka() 
    {
        // 基本設定
        $this->RequestHandler->respondAs('application/pdf');
        $pdf = new Fpdi\TcpdfFpdi();
        mb_internal_encoding('UTF-8');
        $pdf->SetMargins(0, 0, 0); 
        $pdf->setPrintHeader(false); 
        $pdf->setPrintFooter(false);
        $pdf->setSourceFile(WWW_ROOT."pdf/chouka.pdf");

        $year = $this->request->getSession()->read('choyear');
        $month = $this->request->getSession()->read('chomonth');
        $timestamp = mktime(0,0,0,$month,1,$year);
        $weekList = ["日","月","火","水","木","金","土"];

        $extoday = explode("-",date('Y-m-d'));
        $todaystamp = mktime(0,0,0,$extoday[1],$extoday[2],$extoday[0]);
        $holidays = \Yasumi\Yasumi::create('Japan', $year, 'ja_JP');

        // テーブルの設定
        $jigyoushaTable = TableRegistry::get('Jigyoushas');
        $attendanceTable = TableRegistry::get('Attendances');

        // 定員を取得
        $teiin = $jigyoushaTable
        ->find('list',['valueField'=>'teiin'])
        ->where(['Jigyoushas.id'=>1])
        ->first();

        //人員配置区分取得
        $jinkubun = $jigyoushaTable
        ->find('list',['valueField'=>'jinkubun'])
        ->where(['Jigyoushas.id'=>1])
        ->first();

        // ページの追加
        $pdf->AddPage(); 
        $index = $pdf->importPage(1); 
        $pdf->useTemplate($index, 0, 0);
        $pdf -> SetFont('kozminproregular','',12);

        // 年月の出力
        $pdf -> Text(136,32,$year." 年 ".$month." 月");

        // 色を判別する関数
        function color($timestamp)
        {
            $holidays = \Yasumi\Yasumi::create('Japan', date('Y',$timestamp), 'ja_JP');
            if(date("w",$timestamp) == 0){
                return "red";
            } elseif(date("w",$timestamp) == 6) {
                return "blue";
            } elseif($holidays->isHoliday(new \DateTime(date("Y-m-d",$timestamp))) == 1) {
                return "red";
            } else {
                return "black";
            }
        }
        // 出勤超過率で色を判別する関数
        function r2color($ritsu)
        {
            if($ritsu > 150){
                return "red";
            } elseif($ritsu > 100 && $ritsu <= 150) {
                return "orange";
            } elseif($ritsu > 0 && $ritsu <= 100) {
                return "blue";
            } else {
                return "black";
            }
        }
        // 必要職員数と出勤職員数で色を判別する関数
        function staffcolor($kannin,$stanin)
        {
            if($kannin > $stanin){
                return "red";
            } else {
                return "black";
            }
        }

        //通常出勤者数（利用者）
        $timestamp1 = mktime(0,0,0,$month,1,$year);
        $timestamp2 = mktime(0,0,0,$month,date('t',$timestamp),$year);
        $dbresult = $attendanceTable
        ->find('all')
        ->select(['date'=>'date','cnt'=>'COUNT(*)'])
        ->where(['date >='=>date('Y-m-d',$timestamp1),'date <='=>date('Y-m-d',$timestamp2),'koukyu'=>0,'paid'=>0,'kekkin'=>0,'remote'=>0,'support'=>0,'user_staffid IS NOT NULL'])
        ->group(['date'])
        ->EnableHydration(false)
        ->toArray();
        //日付を文字列型に
        foreach ($dbresult as &$row) {
            $row['date'] = strftime('%Y-%m-%d', strtotime($row['date']));
        }
        unset($row);
        $syukin_riyousya = [];
        foreach ($dbresult as $row) {
            $syukin_riyousya[$row['date']] = $row['cnt'];
        }

        //施設外出勤者数（利用者）
        $dbresult = $attendanceTable
        ->find('all')
        ->select(['date'=>'date','cnt'=>'COUNT(*)'])
        ->where(['date >='=>date('Y-m-d',$timestamp1),'date <='=>date('Y-m-d',$timestamp2),'koukyu'=>0,'paid'=>0,'kekkin'=>0,'remote'=>0,'support !='=>0,'user_staffid IS NOT NULL'])
        ->group(['date'])
        ->EnableHydration(false)
        ->toArray();
        //日付を文字列型に
        foreach ($dbresult as &$row) {
            $row['date'] = strftime('%Y-%m-%d', strtotime($row['date']));
        }
        unset($row);
        $shisetsugai_riyousya = [];
        foreach ($dbresult as $row) {
            $shisetsugai_riyousya[$row['date']] = $row['cnt'];
        }

        //在宅勤者数（利用者）
        $dbresult = $attendanceTable
        ->find('all')
        ->select(['date'=>'date','cnt'=>'COUNT(*)'])
        ->where(['date >='=>date('Y-m-d',$timestamp1),'date <='=>date('Y-m-d',$timestamp2),'koukyu'=>0,'paid'=>0,'kekkin'=>0,'remote !='=>0,'support'=>0,'user_staffid IS NOT NULL'])
        ->group(['date'])
        ->EnableHydration(false)
        ->toArray();
        //日付を文字列型に
        foreach ($dbresult as &$row) {
            $row['date'] = strftime('%Y-%m-%d', strtotime($row['date']));
        }
        unset($row);
        $zaitaku_riyousya = [];
        foreach ($dbresult as $row) {
            $zaitaku_riyousya[$row['date']] = $row['cnt'];
        }

        //出勤者数（スタッフ）
        $dbresult = $attendanceTable
        ->find('all')
        ->join(['table' => 'users','type' => 'INNER', 'conditions' => 'user_id = users.id'])
        ->select(['date'=>'date','cnt'=>'COUNT(*)'])
        ->where(['date >='=>date('Y-m-d',$timestamp1),'date <='=>date('Y-m-d',$timestamp2),'intime IS NOT NULL','koukyu'=>0,'paid'=>0,'kekkin'=>0,'Attendances.remote'=>0,'support'=>0,'user_staffid IS NULL','users.adminfrag'=>1,'bikou !='=>'リモートワーク'])
        ->group(['date'])
        ->EnableHydration(false)
        ->toArray();

        //日付を文字列型に
        foreach ($dbresult as &$row) {
            $row['date'] = strftime('%Y-%m-%d', strtotime($row['date']));
        }
        unset($row);
        $staff = [];
        foreach ($dbresult as $row) {
            $staff[$row['date']] = $row['cnt'];
        }

        for($i=1; $i<=date('t',$timestamp); $i++) {
            $timestamp = mktime(0,0,0,$month,$i,$year);
            $formatted_date = strftime('%Y-%m-%d', $timestamp);

            // 日付の表示
            $datehtml = '<span style = "color:'.color($timestamp).';">'.$i.'</span>';
            $pdf -> writeHTMLCell(15,6.5,27.3,51.7+$i*6.515,$datehtml,0,0,0,true,"C",true);

            // 曜日の表示
            $dayhtml = '<span style = "color:'.color($timestamp).'">'.$weekList[date('w',$timestamp)].'</span>';
            $pdf -> writeHTMLCell(15,6.5,37.6,51.7+$i*6.515,$dayhtml,0,0,0,true,"C",true);

            // 人数の初期化
            $one = 0; $seven = 0; $remnin = 0; $stanin = 0;

            //通常出勤者数（利用者）
            $cnt_riyousya = isset($syukin_riyousya[$formatted_date]) ? $syukin_riyousya[$formatted_date] : 0;
            $pdf -> Text(($cnt_riyousya >= 10) ? 54.5 : 57,51.7+$i*6.515,$cnt_riyousya." 人");

            //施設外出勤者数（利用者）
            $cnt_shisetsugai_riyousya = isset($shisetsugai_riyousya[$formatted_date]) ? $shisetsugai_riyousya[$formatted_date] : 0;
            $pdf -> Text(($cnt_shisetsugai_riyousya >= 10) ? 74 : 76.5,51.7+$i*6.515,$cnt_shisetsugai_riyousya." 人");

            //在宅出勤者数（利用者）
            $cnt_zaitaku_riyousya = isset($zaitaku_riyousya[$formatted_date]) ? $zaitaku_riyousya[$formatted_date] : 0;
            $pdf -> Text(($cnt_zaitaku_riyousya >= 10) ? 93.6 : 96.1,51.7+$i*6.515,$cnt_zaitaku_riyousya." 人");

            // 出勤率の算出
            $ritsu = round(($cnt_riyousya/$teiin)*100);
            $r2html = '<span style = "color:'.r2color($ritsu).'">'.$ritsu." %".'</span>';
            $pdf -> writeHTMLCell(15,6.5,112.9,51.7+$i*6.515,$r2html,0,0,0,true,"C",true);

            // 必要職員数の算出
            $kannin = ceil($cnt_riyousya / $jinkubun) + ceil($cnt_shisetsugai_riyousya / $jinkubun);
            $pdf -> Text(136.5,51.7+$i*6.515,$kannin." 人");
            
            // 出勤職員数の算出
            $cnt_staff = isset($staff[$formatted_date]) ? $staff[$formatted_date] : 0;
            $staffhtml = '<span style = "color:'.staffcolor($kannin,$cnt_staff).'">'.$cnt_staff." 人".'</span>';
            $pdf -> writeHTMLCell(15,6.5,153.8,51.7+$i*6.515,$staffhtml,0,0,0,true,"C",true);

        }
        // 定員数の表示
        $pdf -> Text(143,261.2,$teiin." 人");
        $pdf->Output();
    }

    public function sanchouka()
    {
        // 基本設定
        $this->RequestHandler->respondAs('application/pdf');
        $pdf = new Fpdi\TcpdfFpdi();
        mb_internal_encoding('UTF-8');
        $pdf->SetMargins(0, 0, 0); 
        $pdf->setPrintHeader(false); 
        $pdf->setPrintFooter(false);
        $pdf->setSourceFile(WWW_ROOT."pdf/chouka2.pdf");

        // ページの追加
        $pdf->AddPage(); 
        $index = $pdf->importPage(1); 
        $pdf->useTemplate($index, 0, 0);

        // テーブルの設定
        $jigyoushaTable = TableRegistry::get('Jigyoushas');
        $attendanceTable = TableRegistry::get('Attendances');

        // データを取得
        $sancho["hyear"] = $this->request->getSession()->read('sanchohyear');
        $sancho["hmonth"] = $this->request->getSession()->read('sanchohmonth');
        $sancho["oyear"] = $this->request->getSession()->read('sanchooyear');
        $sancho["omonth"] = $this->request->getSession()->read('sanchoomonth');

        //定員数取得
        $teiin = $jigyoushaTable
        ->find('list',['valueField'=>'teiin'])
        ->where(['Jigyoushas.id'=>1])
        ->first();

        // タイトルの出力
        $pdf -> SetFont('kozminproregular','',18);
        if($sancho["hmonth"] >= 11) {
            $title = "<p>".$sancho["hyear"]."年 ".$sancho["hmonth"]."月 ～ ".$sancho["oyear"]."年 ".$sancho["omonth"]."月　定 員 超 過 率";
        } else {
            $title = "<p>".$sancho["hyear"]."年 ".$sancho["hmonth"]."月 ～ ".$sancho["omonth"]."月　定 員 超 過 率";
        }
        $pdf -> writeHTMLCell(151.6,10,20.9,22.9,$title,0,0,0,true,"C",true);

        //日付調整
        $timestamp = mktime(0,0,0,$this->request->getSession()->read('sanchoomonth'),1,$this->request->getSession()->read('sanchooyear'));
        $timestamp1 = mktime(0,0,0,$this->request->getSession()->read('sanchohmonth'),1,$this->request->getSession()->read('sanchohyear'));
        $timestamp2 = mktime(0,0,0,$this->request->getSession()->read('sanchoomonth'),date('t',$timestamp),$this->request->getSession()->read('sanchooyear'));
    
        //合計出勤者数取得
        $rsltsyukkin = $attendanceTable
        ->find('all')
        ->select(['month'=>'MONTH(date)','cnt'=>'COUNT(*)'])
        ->where(['date >='=>date('Y-m-d',$timestamp1),'date <='=>date('Y-m-d',$timestamp2),'koukyu'=>0,'paid'=>0,'kekkin'=>0,'remote'=>0,'support'=>0,'user_staffid IS NOT NULL'])
        ->group(['YEAR(date)', 'MONTH(date)'])
        ->EnableHydration(false)
        ->toArray();

        //合計営業日数取得
        $rslteigyo = $attendanceTable
        ->find('all')
        ->join(['table' => 'users','type' => 'INNER', 'conditions' => 'user_id = users.id'])
        ->select(['month'=>'MONTH(date)','cnt'=>'COUNT(DISTINCT date)'])
        ->where(['date >='=>date('Y-m-d',$timestamp1),'date <='=>date('Y-m-d',$timestamp2),'intime IS NOT NULL','koukyu'=>0,'paid'=>0,'kekkin'=>0,'Attendances.remote'=>0,'support'=>0,'user_staffid IS NULL','users.adminfrag'=>1,'bikou !='=>'リモートワーク'])
        ->group(['YEAR(date)', 'MONTH(date)'])
        ->EnableHydration(false)
        ->toArray();

        // 表の中の数値を出力
        $mitsukiman = 0; $mitsukibi = 0;
        $pdf -> SetFont('kozminproregular','',14);
        $toshichk = $sancho["hyear"];
        $monthchk = $sancho["hmonth"];
        for( $i = 0; $i < 3; $i++) {
            
            // 月の表示
            $tsukihtml = "<p>". date('n',$timestamp1) ." 月</p>";
            $pdf -> writeHTMLCell(28.1,11.4,40.5,62.5+$i*11.7,$tsukihtml,0,0,0,true,"C",true);
            $timestamp1 = strtotime('+1 month', $timestamp1);

            // 合計営業日の出力
            if ( isset($rslteigyo[$i]['cnt'])) {
                $eihtml = "<p>".$rslteigyo[$i]['cnt']." 日</p>";
                $pdf -> writeHTMLCell(28.1,11.4,97.5,62.5+$i*11.7,$eihtml,0,0,0,true,"C",true);
                $mitsukibi += $rslteigyo[$i]['cnt'];

                // 合計出勤人数の出力
                $oneshtml = "<p>".$rsltsyukkin[$i]['cnt']." 人</p>";
                $pdf -> writeHTMLCell(28.1,11.4,69,62.5+$i*11.7,$oneshtml,0,0,0,true,"C",true);
                $mitsukiman += $rsltsyukkin[$i]['cnt'];
            }

            // 月ごとの出勤率を計算
            if ( isset($rsltsyukkin[$i]['cnt']) && isset($rslteigyo[$i]['cnt'])) {
                $hitoritsu = round($rsltsyukkin[$i]['cnt']/($rslteigyo[$i]['cnt']*$teiin)*100);
                $hritsuhtml = "<p>".$hitoritsu." ％</p>";
                $pdf -> writeHTMLCell(28.1,11.4,126,62.5+$i*11.7,$hritsuhtml,0,0,0,true,"C",true);
            }
        }

        // 出勤超過率で色を判別する関数
        function color($ritsu)
        {
            if($ritsu > 125){
                return "red";
            } elseif($ritsu > 100 && $ritsu <= 125) {
                return "orange";
            } elseif($ritsu > 0 && $ritsu <= 100) {
                return "blue";
            } else {
                return "black";
            }
        }

        $pdf -> setFillcolor(255,0,0);
        $teiinhtml = "<p>".$teiin." 人</p>";
        $mimanhtml = "<p>".$mitsukiman." 人</p>";
        $mippihtml = "<p>".$mitsukibi." 日</p>";
        $pdf -> writeHTMLCell(56.2,10,97.4,103.7,$teiinhtml,0,0,0,true,"C",true);
        $pdf -> writeHTMLCell(56.2,10,97.4,114.1,$mimanhtml,0,0,0,true,"C",true);
        $pdf -> writeHTMLCell(56.2,10,97.4,124.5,$mippihtml,0,0,0,true,"C",true);

        $sanchouka = round($mitsukiman/($mitsukibi*$teiin)*100);
        $sanchohtml = '<span style = "color:'.color($sanchouka).';">'.$sanchouka.' ％</span>';
        $pdf -> writeHTMLCell(56.2,10,97.4,145.9,$sanchohtml,0,0,0,true,"C",true);

        $pdf->Output();
    }
}