<?php
namespace App\Controller;

use Cake\ORM\Table;
use cake\ORM\TableRegistry;
use App\Controller\AppController;
use setasign\Fpdi;
use Yasumi\Yasumi;
use DateTime;

use Cake\Log\Log;

class TimeCardsController extends AppController
{
   //スタッフがスタッフの出勤簿の登録・更新
   public function register()
   {
   $user = $this->Auth->user();
   if(is_null($user)){
       $this->Flash->error('ログインしていません');
       return $this->redirect(['action' => 'editn']);
   }
   // 基本的なデータを取得
   $flag = 0;
   $usersTable = TableRegistry::get('Users');
   $workplacesTable = TableRegistry::get('Workplaces');
   $attendanceTable = TableRegistry::get('Attendances');
   $query = $this->request->getQuery();
   $year = $query["year"];
   $month = $query["month"];
   $user = $query["staff_id"];
   $douttime = $usersTable
   ->find('list',['valueField'=>'douttime'])
   ->where(['Users.id' => $user])
   ->first();

   $userData = $usersTable->find()->where(['Users.id'=>$user])->first();
   $mainPlace = $workplacesTable->find('list',['valueField'=>'id'])->where(['Workplaces.sub'=>0])->first();

   // 配列化
   $data = $this->request->getData();
   $intime = $data["intime"];
   $outtime = $data["outtime"];
   $resttime = $data["resttime"];
   $overtime = $data["overtime"];
   $meshi = $data["meshi"]; 
   $support = $data["support"];
   $koukyu = $data["koukyu"];
   $paid = $data["paid"];
   $kekkin = $data["kekkin"];
   $bikou = $data["bikou"];
   $remote = $data["remote"];
   if ($userData["adminfrag"] == 0) {
       $ou = $data["ou"];
       $fuku = $data["fuku"];
   }
       
   $timestamp = mktime(0,0,0,$month,1,$year);
   
   for($i = 1; $i <= date("t",$timestamp); $i++){
       $timestamp = mktime(0,0,0,$month,$i,$year);
       $attResult = $attendanceTable
       ->find()
       ->select(['Attendances.id','Attendances.user_id','Attendances.date'])
       ->where(['Attendances.user_id' => $user,'Attendances.date' => date("Y-m-d",$timestamp)])
       ->first();

       if($intime[$i] == "00:00" || empty($intime[$i])){
           $intime[$i] = null;
       }
       if($outtime[$i] == "00:00" || empty($outtime[$i])){
           $outtime[$i] = null;
       }
       if($resttime[$i] == "00:00" || empty($resttime[$i])){
           $resttime[$i] = null;
       }
       if($overtime[$i] == "00:00" || empty($overtime[$i])){
           $overtime[$i] = null;
       } else {
           if(!empty($douttime) && $douttime->i18nFormat("HH:mm") == $outtime[$i]) {
               $exout = explode(":",$outtime[$i]);
               $exover = explode(":",$overtime[$i]);
               $exout[0] += $exover[0];
               $exout[1] += $exover[1];
               if($exout[1] >= 60) {
                   $exout[0]++;
                   $exout[1] -= 60;
               }
               $outtime[$i] = $exout[0].":".$exout[1];
           }
       }
       if(!empty($intime[$i]) && empty($intime[$i])){
           $this->Flash->error('退勤時間のみ空欄になっている箇所があります');
           return $this->redirect(['controller' => 'TimeCards','action' => 'editn']);
       } elseif(empty($intime[$i]) && !empty($intime[$i])) {
           $this->Flash->error('出勤時間のみ空欄になっている箇所があります');
           return $this->redirect(['controller' => 'TimeCards','action' => 'editn']);
       }
       if(!empty($intime[$i]) && !empty($outtime[$i]) && $koukyu[$i]+$paid[$i]+$kekkin[$i] >= 1) {
           $this->Flash->error('出勤日に欠勤情報が設定されています');
           return $this->redirect(['controller' => 'TimeCards','action' => 'editn']);
       }
       if($koukyu[$i]+$paid[$i]+$kekkin[$i] >= 2) {
           $this->Flash->error('欠勤情報に重複があります');
           return $this->redirect(['controller' => 'TimeCards','action' => 'editn']);
       }

       if($userData["adminfrag"] == 0) {
           if($userData["sapporo"] == 1 && $userData["workplace"] != $mainPlace && 
              $remote[$i] == 0 && $koukyu[$i] == 0 && $paid[$i] == 0 && $kekkin[$i] == 0) {
               $support[$i] = 1;
           }
       }

       if(!empty($attResult) && $attResult->date->i18nFormat("yyyy-MM-dd") == date("Y-m-d",$timestamp) && $attResult->user_id == $user){
           $attendance = $attendanceTable->get($attResult->id);     
       } else {
           $attendance = $attendanceTable->newEntity();
       }
       $attendance->user_id = $user;
       $attendance->intime = $intime[$i];
       $attendance->outtime = $outtime[$i];
       $attendance->resttime = $resttime[$i];
       $attendance->overtime = $overtime[$i];
       $attendance->date = date("Y-m-d",$timestamp);
       $attendance->meshi = $meshi[$i];
       $attendance->support = $support[$i];
       $attendance->koukyu = $koukyu[$i];
       $attendance->paid = $paid[$i];
       $attendance->kekkin = $kekkin[$i];
       $attendance->bikou = $bikou[$i];
       $attendance->remote = $remote[$i];
       if ($userData["adminfrag"] == 0) {
           $attendance->ou = $ou[$i];
           $attendance->fuku = $fuku[$i];
       }

       if($attendanceTable->save($attendance)) {
           $flag = 1;
       } else {
           $flag = 0;
       }    
   }
       $host = $_SERVER['HTTP_HOST'];
       if($flag == 1){
           if($host == "[::1]:8765") {
               $this->Flash->success(__('保存されました'));
               return $this->redirect(['controller' => 'Tests','action' => 'edit']);
           } else {
               $this->Flash->success(__('保存されました'));
               return $this->redirect(['controller' => 'TimeCards','action' => 'editn']);
           }
       } else {
           if($host == "[::1]:8765") {
               $this->Flash->error(__('エラーが発生しました'));
               return $this->redirect(['controller' => 'Tests','action' => 'edit']);
           } else {
               $this->Flash->error(__('エラーが発生しました'));
               return $this->redirect(['controller' => 'TimeCards','action' => 'editn']);
           }
       }
   }

   //利用者が出勤簿の登録・更新、スタッフが利用者の出勤簿の登録・更新
    public function register2()
    {
    $user = $this->Auth->user();
    if(is_null($user)){
        $this->Flash->error('ログインしていません');
        return $this->redirect(['action' => 'editn']);
    }

    // 基本的なデータを取得
    $usersTable = TableRegistry::get('Users');
    $workplacesTable = TableRegistry::get('Workplaces');
    $attendanceTable = TableRegistry::get('Attendances');
    $query = $this->request->getQuery();
    $year = $query["year"];
    $month = $query["month"];
    $user = $query["staff_id"];
    $userData = $usersTable->find()->where(['Users.id'=>$user])->first();
    //$mainPlace = $workplacesTable->find('list',['valueField'=>'id'])->where(['Workplaces.sub'=>0])->first();

    // 配列化
    $data = $this->request->getData();
    $intime = $data["intime"];
    $outtime = $data["outtime"];
    $resttime = $data["resttime"];
    $ou = $data["ou"];
    $fuku = $data["fuku"];
    $meshi = $data["meshi"];
    //$medical = $data["medical"];
    $support = $data["support"];
    $koukyu = $data["koukyu"];
    $paid = $data["paid"];
    $kekkin = $data["kekkin"];
    $remote = $data["remote"];

    if($this->request->getSession()->read('Auth.User.adminfrag') == 1) {
        $bikou = $data["bikou"];
        $user_staffid = $data["user_staffid"];
    }

    $tsuitachi = mktime(0,0,0,$month,1,$year);
    $yokugetsu = mktime(0,0,0,$month+1,1,$year);
    $shiniri = 0;
    $joinstamp = strtotime($userData["joined"]);
    if($tsuitachi <= $joinstamp && $joinstamp < $yokugetsu) {
        $shiniri = 1;
        $iribi = $userData["joined"]->i18nFormat('d');
    } else {
        $sumkk = array_sum($koukyu) + array_sum($paid) + array_sum($kekkin);
        if($sumkk < 8) {
            $this->Flash->error('月の勤務可能日数を超えています');
            return $this->redirect(['controller' => 'TimeCards','action' => 'editn']);
        }
    }

    for($i = 1; $i <= date("t",$tsuitachi); $i++) {
        $timestamp = mktime(0,0,0,$month,$i,$year);
        $attResult = $attendanceTable
        ->find()
        ->select(['Attendances.id','Attendances.user_id','Attendances.date', 'Attendances.bikou','Attendances.user_staffid'])
        ->where(['Attendances.user_id' => $user,'Attendances.date' => date("Y-m-d",$timestamp)])
        ->first();
        
        if($shiniri == 1 && $i < $iribi) {
            $intime[$i] = NULL;
            $outtime[$i] = NULL;
            $resttime[$i] = NULL;
            $ou[$i] = 0;
            $fuku[$i] = 0;
            $meshi[$i] = 0;
            $medical[$i] = 0;
            $support[$i] = 0;
            $koukyu[$i] = 0;
            $paid[$i] = 0;
            $kekkin[$i] = 0;
            $remote[$i] = 0;
        } else {    
            if($intime[$i] == "00:00" || empty($intime[$i])){
                $intime[$i] = null;
            }
            if($outtime[$i] == "00:00" || empty($outtime[$i])){
                $outtime[$i] = null;
            }
            if($resttime[$i] == "00:00" || empty($resttime[$i])){
                $resttime[$i] = null;
            }

            if(!empty($intime[$i]) && empty($intime[$i])){
                $this->Flash->error('退勤時間のみ空欄になっている箇所があります');
                return $this->redirect(['controller' => 'TimeCards','action' => 'editn']);
            } elseif(empty($intime[$i]) && !empty($intime[$i])) {
                $this->Flash->error('出勤時間のみ空欄になっている箇所があります');
                return $this->redirect(['controller' => 'TimeCards','action' => 'editn']);
            }
            if(!empty($intime[$i]) && !empty($outtime[$i]) && $koukyu[$i]+$paid[$i]+$kekkin[$i] >= 1) {
                $this->Flash->error('出勤日に欠勤情報が設定されています');
                return $this->redirect(['controller' => 'TimeCards','action' => 'editn']);
            }
            if($koukyu[$i]+$paid[$i]+$kekkin[$i] >= 2) {
                $this->Flash->error('欠勤情報に重複があります');
                return $this->redirect(['controller' => 'TimeCards','action' => 'editn']);
            }
            //if($userData["adminfrag"] == 0) {
            //    if($userData["sapporo"] == 1 && $userData["workplace"] != $mainPlace &&
            //        $remote[$i] == 0 && $koukyu[$i] == 0 && $paid[$i] == 0 && $kekkin[$i] == 0) {
            //        $support[$i] = 1;
            //    }
            //}
        }
        if(!empty($attResult) && $attResult->date->i18nFormat("yyyy-MM-dd") == date("Y-m-d",$timestamp) && $attResult->user_id == $user){
            $attendance = $attendanceTable->get($attResult->id);
            if($this->request->getSession()->read('Auth.User.adminfrag') == 1) {
                $attendance->bikou = $bikou[$i];
                $attendance->user_staffid = $user_staffid[$i];
            } else {
                $attendance->bikou = $attResult->bikou;
                $attendance->user_staffid = $attResult->user_staffid;
            }
            
            if ( $support[$i] != 0) {
                $work = $workplacesTable->find('list',['valueField'=>'name'])->where(['Workplaces.id'=>$support[$i]])->first();
                $attendance->bikou = $work;
            } 
        } else {
            $attendance = $attendanceTable->newEntity();            
            if($this->request->getSession()->read('Auth.User.adminfrag') == 1) {
                $attendance->user_staffid = $user_staffid[$i];
            }
        }

        $attendance->user_id = $user;
        $attendance->intime = $intime[$i];
        $attendance->outtime = $outtime[$i];
        $attendance->resttime = $resttime[$i];
        $attendance->date = date("Y-m-d",$timestamp);
        $attendance->ou = $ou[$i];
        $attendance->fuku = $fuku[$i];
        $attendance->meshi = $meshi[$i];
        //$attendance->medical = $medical[$i];
        $attendance->support = $support[$i];
        $attendance->koukyu = $koukyu[$i];
        $attendance->paid = $paid[$i];
        $attendance->kekkin = $kekkin[$i];
        $attendance->remote = $remote[$i];
        $attendanceTable->save($attendance);

        // 送迎記録の追加または削除
        $transportsTable = TableRegistry::get('transports');

        // 迎えの処理
        if ((int)$ou[$i] === 1) {
            // 既存の迎えの送迎記録を確認
            $existingTransport = $transportsTable
                ->find()
                ->where([
                    'date' => date("Y-m-d", $timestamp),
                    'user_id' => $user,
                    'kind' => 1
                ])
                ->first();

            log::debug("existingTransport:".$existingTransport);

            if (empty($existingTransport)) {
                $transport = $transportsTable->newEntity();
            } else {
                $transport = $existingTransport;
            }

            $transport->date = date("Y-m-d", $timestamp);
            $transport->user_id = $user;
            $transport->kind = 1; // 迎え

            // 出発時間を15分前に設定
            if (!empty($intime[$i])) {
                $timeParts = explode(':', $intime[$i]);
                $hour = (int)$timeParts[0];
                $minute = (int)$timeParts[1];

                // 15分前の時間を計算
                if ($minute >= 15) {
                    $minute -= 15;
                    log::debug("minute1:".$minute);
                } else {
                    $hour -= 1;
                    $minute = 60 - (15 - $minute);
                }

                $transport->hatsutime = sprintf('%02d:%02d', $hour, $minute);
            } else {
                // ユーザー情報から出勤時間を取得
                $userInfo = $usersTable->get($user);
                if (!empty($userInfo->dintime)) {
                    // 時刻部分だけを抽出
                    $rawTime = str_replace("\u{202F}", ' ', $userInfo->dintime);

                    // 期待される形式：4/16/25, 10:45 AM
                    $dt = DateTime::createFromFormat('n/j/y, h:i A', $rawTime);
                    $timeStr = $dt->format('H:i'); // ← これが「10:45」になる
                    $timeParts = explode(':', $timeStr);
                    $hour = (int)$timeParts[0];
                    $minute = (int)$timeParts[1];

                    // 15分前の時間を計算
                    if ($minute >= 15) {
                        $minute -= 15;
                        log::debug("minute2:".$minute);
                    } else {
                        $hour -= 1;
                        $minute = 60 - (15 - $minute);
                    }
                    
                    $transport->hatsutime = sprintf('%02d:%02d:00', $hour, $minute);
                } else {
                    $transport->hatsutime = null;
                }
            }

            // ユーザー情報から送迎場所を取得
            $userInfo = $usersTable->get($user);
            $transport->hatsuplace = $userInfo->oufuku_place;
            $transport->tyakuplace = "事業所";

            // createdとmodifiedを設定
            if (empty($existingTransport)) {
                $transport->created = date('Y-m-d H:i:s');
            }
            $transport->modified = date('Y-m-d H:i:s');

            log::debug("transport:".$transport);
            $transportsTable->save($transport);

        } else {
            // 迎えが無くなった場合、既存の迎えの送迎記録を削除
            $existingTransport = $transportsTable
                ->find()
                ->where([
                    'date' => date("Y-m-d", $timestamp),
                    'user_id' => $user,
                    'kind' => 1
                ])
                ->first();
            
            if (!empty($existingTransport)) {
                $transportsTable->delete($existingTransport);
            }
        }
        
        // 送りの処理
        if ((int)$fuku[$i] === 1) {

            // 送迎記録の取得
            $existingTransport = $transportsTable
                ->find()
                ->where([
                    'date' => date("Y-m-d", $timestamp),
                    'user_id' => $user,
                    'kind' => 2
                ])
                ->first();
            
            if (empty($existingTransport)) {
                $transport = $transportsTable->newEntity();
            } else {
                $transport = $existingTransport;
            }
            
            $transport->date = date("Y-m-d", $timestamp);
            $transport->user_id = $user;
            $transport->kind = 2; // 送り

            // 出発時間を設定
            if (empty($outtime[$i])) {
                // ユーザー情報から出発時間を取得
                $userInfo = $usersTable->get($user);
                if (!empty($userInfo->douttime)) {
                    $rawTime = str_replace("\u{202F}", ' ', $userInfo->douttime);
                    $dt = DateTime::createFromFormat('n/j/y, h:i A', $rawTime);
                    $timeStr = $dt->format('H:i'); // ← これが「10:45」になる
                    $transport->hatsutime = $timeStr . ':00';
                } else {
                    $transport->hatsutime = null;
                }
            } else {
                $transport->hatsutime = $outtime[$i];
            }

            // ユーザー情報から送迎場所を取得
            $userInfo = $usersTable->get($user);
            $transport->hatsuplace = "事業所";
            $transport->tyakuplace = $userInfo->oufuku_place;

            // createdとmodifiedを設定
            if (empty($existingTransport)) {
                $transport->created = date('Y-m-d H:i:s');
            }
            $transport->modified = date('Y-m-d H:i:s');
            
            $transportsTable->save($transport);
        } else {
            // 送りが無くなった場合、既存の送りの送迎記録を削除
            $existingTransport = $transportsTable
                ->find()
                ->where([
                    'date' => date("Y-m-d", $timestamp),
                    'user_id' => $user,
                    'kind' => 2
                ])
                ->first();
                
            if (!empty($existingTransport)) {
                $transportsTable->delete($existingTransport);
            }
        }
    }
        $host = $_SERVER['HTTP_HOST'];
        if($attendanceTable->save($attendance)){
            if($host == "[::1]:8765") {
                // テストサイトの場合は違うページにリダイレクト
                $this->Flash->success(__('保存されました'));
                return $this->redirect(['controller' => 'Tests','action' => 'edit']);
            } else {
                $this->Flash->success(__('保存されました'));
                return $this->redirect(['controller' => 'TimeCards','action' => 'editn']);
            }
        }
    }

    //社長の出勤簿を作る
    public function schedule() 
    {
        $attendanceTable = TableRegistry::get('Attendances');
        $query = $this->request->getQuery();
        if($query["id"] != 12) {
            $this->Flash->error(__('アクセスできません'));
            return $this->redirect(['controller' => 'TimeCards','action' => 'editn']);
        }
        if($query["year"] == 2021) {
            for($m=4; $m<=12; $m++) {
                $timestamp = mktime(0,0,0,$m,1,$query["year"]);
                for($d=1; $d<=date('t',$timestamp); $d++){
                    $timestamp = mktime(0,0,0,$m,$d,$query["year"]);
                    $att = $attendanceTable->find()
                    ->where(['Attendances.user_id' => 12,'Attendances.date' => date("Y-m-d",$timestamp)])
                    ->first();
                    if(date('w',$timestamp) != 0 && date('w',$timestamp) != 6) {
                        if(!empty($att)) {
                            $attendance = $attendanceTable->get($att->id);
                        } else {
                            $attendance = $attendanceTable->newEntity();
                        }
                        $attendance->user_id = 12;
                        $attendance->intime = "09:00";
                        $attendance->outtime = "17:00";
                        $attendance->resttime = "01:00";
                        $attendance->date = date("Y-m-d",$timestamp);
                        $attendanceTable->save($attendance); 
                    } else {
                        if(!empty($att)) {
                            $attendance = $attendanceTable->get($att->id);  
                        } else {
                            $attendance = $attendanceTable->newEntity();
                        }
                        $attendance->user_id = 12;
                        $attendance->koukyu = 1;
                        $attendance->date = date("Y-m-d",$timestamp);
                        $attendanceTable->save($attendance); 
                    }
                }
            }
        } elseif($query["year"] == 2022) {
            for($m=1; $m<=12; $m++) {
                $timestamp = mktime(0,0,0,$m,1,$query["year"]);
                for($d=1; $d<=date('t',$timestamp); $d++){
                    $timestamp = mktime(0,0,0,$m,$d,$query["year"]);
                    $att = $attendanceTable->find()
                    ->where(['Attendances.user_id' => 12,'Attendances.date' => date("Y-m-d",$timestamp)])
                    ->first();
                    if(date('w',$timestamp) != 0 && date('w',$timestamp) != 6) {
                        if(!empty($att)) {
                            $attendance = $attendanceTable->get($att->id);
                        } else {
                            $attendance = $attendanceTable->newEntity();
                        }
                        $attendance->user_id = 12;
                        $attendance->intime = "09:00";
                        $attendance->outtime = "17:00";
                        $attendance->resttime = "01:00";
                        $attendance->date = date("Y-m-d",$timestamp);
                        $attendanceTable->save($attendance); 
                    } else {
                        if(!empty($att)) {
                            $attendance = $attendanceTable->get($att->id);  
                        } else {
                            $attendance = $attendanceTable->newEntity();
                        }
                        $attendance->user_id = 12;
                        $attendance->koukyu = 1;
                        $attendance->date = date("Y-m-d",$timestamp);
                        $attendanceTable->save($attendance); 
                    }
                }
            }
        } elseif($query["year"] == date("Y")) {
            $timestamp = mktime(0,0,0,$query["month"],1,$query["year"]);
            for($d=1; $d<=date('t',$timestamp); $d++){
                $timestamp = mktime(0,0,0,$query["month"],$d,$query["year"]);
                $att = $attendanceTable->find()
                ->where(['Attendances.user_id' => 12,'Attendances.date' => date("Y-m-d",$timestamp)])
                ->first();
                if(date('w',$timestamp) != 0 && date('w',$timestamp) != 6) {
                    if(!empty($att)) {
                        $attendance = $attendanceTable->get($att->id);   
                    } else {
                        $attendance = $attendanceTable->newEntity();
                    }
                    $attendance->user_id = 12;
                    $attendance->intime = "09:00";
                    $attendance->outtime = "17:00";
                    $attendance->resttime = "01:00";
                    $attendance->date = date("Y-m-d",$timestamp);
                    $attendanceTable->save($attendance); 
                } else {
                    if(!empty($att)) {
                        $attendance = $attendanceTable->get($att->id);
                    } else {
                        $attendance = $attendanceTable->newEntity();
                    }
                    $attendance->user_id = 12;
                    $attendance->koukyu = 1;
                    $attendance->date = date("Y-m-d",$timestamp);
                    $attendanceTable->save($attendance); 
                }
            }
        }
        if($attendanceTable->save($attendance)){
            $this->Flash->success(__('保存されました'));
            return $this->redirect([
                'controller' => 'TimeCards',
                'action' => 'editn',
            ]);
        }
    }

    public function editn() 
    {
        $name = $this->request->getSession()->read('Auth.User.name');
        $weekList = ["日","月","火","水","木","金","土"];
        $usersTable = TableRegistry::get('Users');
        $sideusers = $usersTable
        ->find('list',['keyField'=>'id','valueField'=>'name'])
        ->where(['Users.display'=>0])
        ->order(['Users.narabi'=>'ASC','Users.id'=>'ASC'])
        ->toArray();
        for($i=2021;$i<=date('Y')+1;$i++) {
            $years["$i"] = $i;
        }
        for($i=1;$i<=12;$i++) {
            $months[sprintf('%02d',$i)] = $i;
        }
        $this->set(compact('name','weekList','sideusers','years','months'));
        
        $year = $this->request->getSession()->read('Qyear');
        $month = $this->request->getSession()->read('Qmonth');
        $user_id = $this->request->getSession()->read('Quser_id');
        if(empty($year)) {
            $year = date('Y');
        }
        if(empty($month)) {
            $month = date('m');
        }
        if(empty($user_id)) {
            $user_id = $this->request->getSession()->read('Auth.User.id');
        }
        $timestamp = mktime(0,0,0,$month,1,$year);

        $usersTable = TableRegistry::get('Users');
        $getFrag = $usersTable
        -> find('list',['valueField'=>'adminfrag'])
        -> where(['Users.id'=>$user_id]);
        $adminfrag = $getFrag->first();
        $astaffs = $usersTable
        ->find('list',['valueField' => 'lastname'])
        ->where(['Users.adminfrag'=>1])
        ->order(['Users.narabi'=>'ASC','Users.id'=>'ASC']);
        $admresults = $astaffs->toArray();
        $getname = $usersTable
        ->find('list',['valueField'=>'name'])
        ->where(['Users.id'=>$user_id])
        ->first();
        
        $username = $usersTable->find('list',['valueField' => 'name'])->toArray();

        $attendanceTable = TableRegistry::get('Attendances');
        $results = [null];
        for($i = 1; $i <= date("t",$timestamp); $i++) {
            $timestamp = mktime(0,0,0,$month,$i,$year);
            $getA = $attendanceTable
            ->find()
            ->where(['Attendances.user_id' => $user_id, 'Attendances.date' => date('Y-m-d',$timestamp)])
            ->first();
            array_push($results,$getA);
        }
        $holidays = \Yasumi\Yasumi::create('Japan', $year, 'ja_JP');
        $allh = 0; $allm = 0; $attenddays = 0;
        $allkoukyu = 0; $allpaid = 0; $allkekkin = 0;
        foreach($results as $result) {
            if($result == null) {
                continue;
            } elseif($result["koukyu"] == 0 && $result["kekkin"] == 0) {
                if(!empty($result["intime"]) && !empty($result["outtime"])){
                    $oneh = $result["outtime"]->i18nFormat("H") - $result["intime"]->i18nFormat("H");
                    $onem = $result["outtime"]->i18nFormat("m") - $result["intime"]->i18nFormat("m");
                    if(!empty($result["resttime"])) {
                        $oneh -= $result["resttime"]->i18nFormat("H");
                        $onem -= $result["resttime"]->i18nFormat("m");
                    }
                    if($onem >= -120 && $onem < -60) {
                        $oneh -= 2;
                        $onem += 120;
                    } elseif($onem < 0) {
                        $oneh--;
                        $onem += 60;
                    }
                    $allh += $oneh;
                    $allm += $onem;
                    $attenddays++;
                }
            }
            $allkoukyu += $result["koukyu"];
            $allpaid += $result["paid"];
            $allkekkin += $result["kekkin"];
        }
        if($allm >= 60) {
            $allfh = $allh + floor($allm / 60);
            $allfm = $allm % 60;
        } else {
            $allfh = $allh;
            $allfm = $allm;
        }
        $attenddays += $allpaid;
        $alltime = sprintf('%02d',$allfh).":".sprintf('%02d',$allfm);

        //施設外就労場所情報を渡す
        $workPlacesTable = TableRegistry::get("Workplaces");
        $workName = $workPlacesTable->find('list', ['keyField'=>'id','valueField'=>'name'])->select( ['id','name'])->where(['Workplaces.sub'=>1, 'Workplaces.del'=>0])->enableHydration(false)->all()->toArray();
        $workName = array_reverse($workName, true);
        $workName['0'] = '';
        $workName = array_reverse($workName, true);
    

        $allworkdays = date("t",$timestamp) - 8;
        $percent = round($attenddays/$allworkdays*100) ." %";
        $this->set(compact("year","month","user_id","getname","timestamp","adminfrag","admresults","username",
        "results","holidays","allkoukyu","allpaid","allkekkin","allworkdays","percent","alltime", "workName"));
    }

    public function getquery0()
    {
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            $this->request->getSession()->write([
                'Qyear' => $this->request->getData('year'),
                'Qmonth' => $this->request->getData('month'),
                'Quser_id' => $this->request->getData('id'),
            ]);
        } else {
            $this->request->getSession()->write([
                'Qyear' => $this->request->getData('year'),
                'Qmonth' => $this->request->getData('month'),
                'Quser_id' => $this->request->getSession()->read('Auth.User.id'),
            ]);
        }
        return $this->redirect(['action' => 'editn']);
    }

    public function getquery1($id = NULL)
    {
        $this->request->getSession()->write([
            'Qyear' => date('Y'),
            'Qmonth' => date('m'),
            'Quser_id' => $id,
        ]);
        return $this->redirect(['action' => 'editn']);
    }

    public function detailn() {
        $auser = $this->Auth->user();
        if(is_null($auser)){
            $this->Flash->error('ログインしていません');
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        } else {
        $year = $this -> request -> getQuery("year");
        $month = $this -> request -> getQuery("month");
        $user_id = $this -> request -> getQuery("id");
        $timestamp = mktime(0,0,0,$month,1,$year);
        $usersTable = TableRegistry::get('Users');
        $username = $usersTable
        ->find('list',['valueField'=>'name'])
        ->where(['Users.id'=>$user_id])->first();

        $AttendancesTable = TableRegistry::get('Attendances');
        $reps = $AttendancesTable
        ->find()
        ->where(['Attendances.user_id' => $user_id, 'Attendances.date >=' => date('Y-m',$timestamp).'-01', 'Attendances.date <=' => date('Y-m',$timestamp)."-".date("t",$timestamp)])
        ->toArray();

        $allh = 0; $allm = 0; $allrh = 0; $allrm = 0; $attenddays = 0;
        //$allkoukyu = 0; $allpaid = 0; $allkekkin = 0; $allmeshi = 0; $allmedical = 0; $allsupport = 0;
        $allkoukyu = 0; $allpaid = 0; $allkekkin = 0; $allmeshi = 0; $allsupport = 0;
        foreach($reps as $rep) {
            if($rep == null) {
                continue;
            } elseif($rep["koukyu"] == 0 && $rep["kekkin"] == 0) {
                if(!empty($rep["intime"]) && !empty($rep["outtime"])){
                    $oneh = $rep["outtime"]->i18nFormat("H") - $rep["intime"]->i18nFormat("H");
                    $onem = $rep["outtime"]->i18nFormat("m") - $rep["intime"]->i18nFormat("m");
                    if(!empty($rep["resttime"])) {
                        $oneh -= $rep["resttime"]->i18nFormat("H");
                        $onem -= $rep["resttime"]->i18nFormat("m");
                        $allrh += $rep["resttime"]->i18nFormat("H");
                        $allrm += $rep["resttime"]->i18nFormat("m");               
                    }
                    if($onem >= -120 && $onem < -60) {
                        $oneh -= 2;
                        $onem += 120;
                    } elseif($onem < 0) {
                        $oneh--;
                        $onem += 60;
                    }
                    $allh += $oneh;
                    $allm += $onem;
                    $attenddays++;
                }
            }
            $allkoukyu += $rep["koukyu"];
            $allpaid += $rep["paid"];
            $allkekkin += $rep["kekkin"];
            //if($this->request->getSession()->read('Auth.User.adminfrag') == 0) {
                $allmeshi += $rep["meshi"];
                //$allmedical += $rep["medical"];
                $allsupport += $rep["support"];
            //}
        }

        //if($this->request->getSession()->read('Auth.User.adminfrag') == 1) {
        //    $allmeshi = 0;
        //    $allmedical = 0;
        //    $allsupport = 0;
        //}

        if($allm >= 60) {
            $allfh = $allh + floor($allm / 60);
            $allfm = $allm % 60;
        } else {
            $allfh = $allh;
            $allfm = $allm;
        }
        $alltime = sprintf('%02d',$allfh).":".sprintf('%02d',$allfm);
        
        if($allrm >= 60) {
            $allfrh = $allrh + floor($allrm / 60);
            $allfrm = $allrm % 60;
        } else {
            $allfrh = $allrh;
            $allfrm = $allrm;
        }
        $allrest = sprintf('%02d',$allfrh).":".sprintf('%02d',$allfrm);
        $attenddays += $allpaid;

        $allworkdays = date("t",$timestamp) - 8;
        $percent = round($attenddays/$allworkdays*100) ." %";
        $this->set(compact("year","month","username","allrest","allkoukyu","allpaid","allkekkin","allmeshi",
                           "allmedical","allsupport","allworkdays","alltime","percent"));
        }
    }

    public function delete() 
    {
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            $attendanceTable = TableRegistry::get('Attendances');
            $query = $this->request->getQuery();
            $year = $query["year"];
            $month = $query["month"];
            $user_id = $query["user_id"];
            $timestamp = mktime(0,0,0,$month,1,$year);
            
            $datacheck = $attendanceTable
            ->find()
            ->where(['Attendances.user_id' => $user_id,
                    'Attendances.date >=' => date('Y-m',$timestamp).'-01',
                    'Attendances.date <=' => date('Y-m',$timestamp)."-".date("t",$timestamp)])
            ->toArray();
            
            if(empty($datacheck)) {
                $this->Flash->error('該当するデータが存在しません');
                return $this->redirect(['action' => 'editn']);
            }
            
            for($i=1; $i<=date('t',$timestamp); $i++) {
                $timestamp = mktime(0,0,0,$month,$i,$year);
                $data = $attendanceTable
                ->find()
                ->where(['Attendances.user_id' => $user_id, 'Attendances.date' => date('Y-m-d',$timestamp)])
                ->first();
                if(!empty($data["id"])) {
                    $attendanceTable->delete($data);
                }
            }

            $datacheck = $attendanceTable
            ->find('list',["valueField"=>"id"])
            ->where(['Attendances.user_id' => $user_id,
                    'Attendances.date >=' => date('Y-m',$timestamp).'-01',
                    'Attendances.date <=' => date('Y-m',$timestamp)."-".date("t",$timestamp)])
            ->toArray();

            if(empty($datacheck)) {
                $this->Flash->success(__('対象のデータはすべて削除されました'));
                return $this->redirect(['action' => 'editn']);
            } else {
                $this->Flash->error('データの削除に失敗しました');
                return $this->redirect(['action' => 'editn']);
            }
        } else {
            $this->Flash->error('アクセス権限がありません');
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }
    }
}