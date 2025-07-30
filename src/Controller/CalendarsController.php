<?php
namespace App\Controller;
use Yasumi\Yasumi;
use setasign\Fpdi;
use Cake\ORM\Table;
use cake\ORM\TableRegistry;
use App\Controller\AppController;

use Cake\Log\Log;

class CalendarsController extends AppController
{    

    public function indexn() 
    {
        //$usersTable = TableRegistry::get('Users');
        $getY = $this->request->getSession()->read('Cyear');
        $getM = $this->request->getSession()->read('Cmonth');
        if(!empty($getY)) {
            $year = $getY;
        } else {
            $year = date('Y');
        }
        if(!empty($getM)) {
            $month = $getM;
        } else {
            $month = date('m');
        }
        
        $timestamp = mktime(0,0,0,$month,1,$year);
        $holidays = \Yasumi\Yasumi::create('Japan', $year, 'ja_JP');

        $maes = []; $dates = []; $jsch = []; $atos = [];
        for($i=1; $i<=date('w',$timestamp); $i++) {
            array_push($maes,"");
        }
        for($i=1; $i<=date('t',$timestamp); $i++) {
            $timestamp = mktime(0,0,0,$month,$i,$year);
            $cal = $this->Calendars
            ->find()
            ->select(['Calendars.id','Calendars.date','Calendars.plana','Calendars.planb','Calendars.planc','Calendars.memo'])
            ->where(['Calendars.date' => date("Y-m-d",$timestamp)])
            ->EnableHydration(false)
            ->first();
            $cal["hidake"] = $i;
            if(empty($cal["plana"]) && empty($cal["planb"]) && empty($cal["planc"]) && empty($cal["memo"])) {
                $cal["flag"] = 0;
            } else {
                $cal["flag"] = 1;
            }
            if(empty($cal)) {
                $cal["id"] = "";
                $cal["date"] = $year."-".$month."-".$i;
                $cal["plana"] = "";
                $cal["planb"] = "";
                $cal["planc"] = "";
                $cal["memo"] = "";
                $cal["flag"] = 0;
            }
            array_push($dates,$cal);
        }
        for($i=1; $i<=6-date('w',$timestamp); $i++) {
            array_push($atos,"");
        }
        for($i=2021;$i<=date('Y')+1;$i++) {
            $years["$i"] = $i;
        }
        for($i=1;$i<=12;$i++) {
            $months[sprintf('%02d',$i)] = $i;
        }

        //食事提供者情報ゲット
        $sdate = date('Y-m-d',mktime(0,0,0,$getM,1,$getY));
        $edate = date('Y-m-d',mktime(0,0,0,$getM+1,0,$getY));

        $attendancesTable = TableRegistry::get('attendances');
        $syokuji = $attendancesTable
        ->find()
        ->select( ['day' => 'RIGHT(date, 2)', 'name' => 'users.name'])
        ->join(['type' => 'inner', 'table' => 'users', 'conditions' => 'attendances.user_id = users.id'])
        ->where(['attendances.meshi' => 1, 'date >=' => $sdate,'date <=' => $edate])
        ->order(['attendances.date' => 'ASC'])
        ->toArray();

        $this->set(compact("years","months","year","month","maes","dates","jsch","atos","timestamp","holidays"));
    }

    public function index2() 
    {
        $user_id = $this->Auth->user("id");
        $userName = $this->Auth->user("name");
        $usersTable = TableRegistry::get('Users');
        $schedulesTable = TableRegistry::get('Schedules');
        $host = $_SERVER['HTTP_HOST'];
        $getY = $this->request->getSession()->read('Schyear');
        $getM = $this->request->getSession()->read('Schmonth');
        if(!empty($getY)) {
            $year = $getY;
        } else {
            $year = date('Y');
        }
        if(!empty($getM)) {
            $month = $getM;
        } else {
            $month = date('m');
        }
        
        $timestamp = mktime(0,0,0,$month,1,$year);
        if($host != "[::1]:8765") {
            $holidays = \Yasumi\Yasumi::create('Japan', $year, 'ja_JP');
        } else {
            $holidays = NULL;
        }

        $maes = []; $dates = []; $jsch = []; $atos = [];
        for($i=1; $i<=date('w',$timestamp); $i++) {
            array_push($maes,"");
        }
        for($i=1; $i<=date('t',$timestamp); $i++) {
            $timestamp = mktime(0,0,0,$month,$i,$year);
            $getSchedules = $schedulesTable
            ->find()
            ->where(['Schedules.user_id'=>$user_id,'Schedules.date' => date("Y-m-d",$timestamp)])
            ->EnableHydration(false)
            ->first();
            $getSchedules["hidake"] = $i;
            if(empty($getSchedules["plana"]) && empty($getSchedules["planb"]) && empty($getSchedules["planc"])) {
                $getSchedules["flag"] = 0;
            } else {
                $getSchedules["flag"] = 1;
            }
            if(empty($getSchedules)) {
                $getSchedules["id"] = "";
                $getSchedules["date"] = $year."-".$month."-".$i;
                $getSchedules["plana"] = "";
                $getSchedules["planb"] = "";
                $getSchedules["flag"] = 0;
            }
            array_push($dates,$getSchedules);
        }
        for($i=1; $i<=6-date('w',$timestamp); $i++) {
            array_push($atos,"");
        }
        for($i=2021;$i<=date('Y')+1;$i++) {
            $years["$i"] = $i;
        }
        for($i=1;$i<=12;$i++) {
            $months[sprintf('%02d',$i)] = $i;
        }
        $this->set(compact("years","months","year","month","maes","dates","jsch","atos","timestamp","holidays",
        "userName","host"));
    }

    public function getquery0()
    {
        $data = $this->request->getData();
        if($data["hidden"] == 0) {
            $this->request->getSession()->write([
                'Cyear' => $data["year"],
                'Cmonth' => $data["month"],
            ]);
            return $this->redirect(['action' => 'indexn']);
        } elseif($data["hidden"] == 1) {
            return $this->redirect(['controller'=>'prints','action' => 'srecords',
                                    '?'=>['year'=>$data["year"],'month'=>$data["month"]]]);
        }
        if($data["hidden"] == 2) {
            $this->request->getSession()->write([
                'Schyear' => $data["year"],
                'Schmonth' => $data["month"],
            ]);
            return $this->redirect(['action' => 'index2']);
        } elseif($data["hidden"] == 3) {
            return $this->redirect(['controller'=>'prints','action' => 'srecords',
                                    '?'=>['year'=>$data["year"],'month'=>$data["month"]]]);
        }
    }

    public function scheduleAjax() 
    {
        $jsondata = $this->request->getData();
		$this->viewBuilder()->setClassName('Json');
		$this->set([
            'data' => $jsondata,
            '_serialize' => ['data'],
		]);

        //食事提供者リスト取得
        $date = $jsondata["date"];
        $attendancesTable = TableRegistry::get('attendances');
        $syokuji = $attendancesTable
        ->find()
        ->select( ['name' => 'users.name'])
        ->join(['type' => 'inner', 'table' => 'users', 'conditions' => 'attendances.user_id = users.id'])
        ->where(['attendances.meshi' => 1, 'date ' => $date])
        ->order(['users.adminfrag' => 'DESC'])
        ->enableHydration(false)
        ->toArray();

        //送迎者リスト
        //当日の迎データを取得
        $AttendancesTable = TableRegistry::get('attendances');
        $SougeiList = $AttendancesTable
        ->find()
        ->select( ['time' => $AttendancesTable->query()->newExpr("DATE_FORMAT(SUBTIME(dintime, '00:15'), '%H:%i')"),'name' => 'Users.name', 'oufuku_place' => 'Users.oufuku_place'])
        ->innerJoinWith('Users')
        ->where(['ou' => 1,'date' => $date])
        ->order(['Users.dintime' => 'ASC','Users.id' => 'ASC'])
        ->enableHydration(false)
        ->toArray(); 

        //配列に挿入
        $SougeiData = [];
        foreach ($SougeiList as $Sougei) {
            $SougeiData[] = [
                'time' => $Sougei['time'],
                'sougei_type' => "迎",
                'name' => $Sougei['name'],
                'place' => $Sougei['oufuku_place']
            ];
        }

        //当日の送データを取得
        $SougeiList = $AttendancesTable
        ->find()
        ->select( ['time' => $AttendancesTable->query()->newExpr("DATE_FORMAT(douttime, '%H:%i')"),'name' => 'Users.name', 'oufuku_place' => 'Users.oufuku_place'])
        ->innerJoinWith('Users')
        ->where(['fuku' => 1,'date' => $date])
        ->order(['Users.dintime' => 'ASC','Users.id' => 'ASC'])
        ->enableHydration(false)
        ->toArray(); 

        //配列に挿入
        foreach ($SougeiList as $Sougei) {
            $SougeiData[] = [
                'time' => $Sougei['time'],
                'sougei_type' => "送",
                'name' => $Sougei['name'],
                'place' => $Sougei['oufuku_place']
            ];
        }

        $response = [
            'syokuji' => $syokuji,
            'sougei' => $SougeiData
        ];

        return $this->getResponse()->withStringBody(json_encode($response));
   }


   public function register()
    {
        $data = $this->request->getData();
        $calendarsTable = TableRegistry::get('Calendars');
        $cal = $calendarsTable
        ->find()
        ->where(['Calendars.date' => $data["date"]])
        ->first();
        if(empty($cal)) {
            $calendar = $calendarsTable->newEntity();
        } else {
            $calendar = $calendarsTable->get($cal["id"]);
        }
        $calendar->date = $data["date"];
        $calendar->plana = $data["plana"];
        $calendar->planb = $data["planb"];
        $calendar->planc = $data["planc"];
        $calendar->memo = $data["memo"];
        if($calendarsTable->save($calendar)) {
            $this->Flash->success(__('保存されました'));
        } else {
            $this->Flash->error(__('エラーが発生しました'));
        }
        return $this->redirect(['action' => 'indexn']);
    }

    public function register2()
    {
        $user_id = $this->Auth->user("id");
        $data = $this->request->getData();
        $schedulesTable = TableRegistry::get('Schedules');
        $sch = $schedulesTable
        ->find()
        ->where(['Schedules.date' => $data["date"]])
        ->first();
        if(empty($sch)) {
            $schedule = $schedulesTable->newEntity();
        } else {
            $schedule = $schedulesTable->get($sch["id"]);
        }
        $schedule->date = $data["date"];
        $schedule->user_id = $user_id;

        // 予定2や予定3だけ入力されている場合、予定1から登録されるように変更する
        if(empty($data["plana"]) && empty($data["planb"]) && !empty($data["planc"])) {
            $schedule->plana = $data["planc"];
        } elseif(empty($data["plana"]) && !empty($data["planb"])) {
            $schedule->plana = $data["planb"];
            if(!empty($data["planc"])) {
                $schedule->planb = $data["planc"];
            }
        } else {
            $schedule->plana = $data["plana"];
            $schedule->planb = $data["planb"];
            $schedule->planc = $data["planc"];
        }

        if($schedulesTable->save($schedule)) {
            $this->Flash->success(__('保存されました'));
        } else {
            $this->Flash->error(__('エラーが発生しました'));
        }
        return $this->redirect(['action' => 'index2']);
    }
}