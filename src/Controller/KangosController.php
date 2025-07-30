<?php
namespace App\Controller;
use Yasumi\Yasumi;
use Cake\ORM\Table;
use cake\ORM\TableRegistry;
use App\Controller\AppController;

class KangosController extends AppController
{
    public function indexn()
    {
        $usersTable = TableRegistry::get('Users');
        $calendarTable = TableRegistry::get('Calendars');
        $attendanceTable = TableRegistry::get('Attendances');

        // 日付の定義
        for($i=2021;$i<=date('Y')+1;$i++) {
            $years["$i"] = $i;
        }
        for($i=1;$i<=12;$i++) {
            $months[sprintf('%02d',$i)] = $i;
        }

        // 訪問看護の予定を出力
        $getkango = $calendarTable
        ->find()
        ->select(['Calendars.kango','Calendars.nurse'])
        ->where(['Calendars.date'=>date('Y-m-d')])
        ->EnableHydration(false)
        ->first();

        if(!empty($getkango["nurse"])) {
            $nurse = $this->Kangos->find('list',["valueField"=>"name"])->where(["Kangos.id"=>$getkango["nurse"]])->first();
        } else {
            $nurse = null;
        }

        // ユーザーの一覧を出力
        $getusers = $usersTable
        ->find()
        ->select(['Users.id','Users.name','Users.dintime','Users.douttime','Users.retired'])
        ->where(['Users.adminfrag'=>0])
        ->order(['Users.narabi'=>'ASC','Users.id'=>'ASC'])
        ->EnableHydration(false)
        ->toArray();
        
        // 出勤者を出力
        $deterusers = [];
        foreach($getusers as $getuser) {
            $getid = $attendanceTable
            ->find()
            ->where(["Attendances.date"=>date('Y-m-d'),"Attendances.medical"=>1,"Attendances.user_id"=>$getuser["id"]])
            ->first();
            if(!empty($getid)) {
                $att["name"]= $usersTable
                ->find('list',["valueField"=>"name"])
                ->where(["Users.id"=>$getid["user_id"]])
                ->first();
                if(!empty($getid["intime"])) {
                    $att["flag"] = 1;
                } else {
                    $att["flag"] = 0;
                }
                array_push($deterusers,$att);
            }
        }
        $this->set(compact("years","months","deterusers","getkango","nurse"));
    }
                
    public function edit()
    {
        $calendarTable = TableRegistry::get('Calendars');
        $query = $this->request->getQuery();
        $year = $query["year"];
        $month = $query["month"];
        $timestamp = mktime(0,0,0,$month,1,$year);
        $holidays = \Yasumi\Yasumi::create('Japan', $year, 'ja_JP');

        // 看護師の一覧を表示
        $nurses = $this->Kangos->find('list',["valueField"=>"name"])->where(["Kangos.display"=>1])->toArray();

        $maes = []; $dates = []; $jsch = []; $atos = [];
        // 1日の前にある隙間を表示
        for($i=1; $i<=date('w',$timestamp); $i++) {
            array_push($maes,"");
        }
        // 日付を表示
        for($i=1; $i<=date('t',$timestamp); $i++) {
            $timestamp = mktime(0,0,0,$query["month"],$i,$query["year"]);
            $calchk = $calendarTable
            ->find()
            ->where(['Calendars.date' => date('Y-m-d',$timestamp)])
            ->first();
            $dates[$i-1]["hidake"] = $i;
            if(!empty($calchk)) {
                $dates[$i-1]["kango"] = $calchk["kango"];
                $dates[$i-1]["nurse"] = $calchk["nurse"];
            } else {
                $dates[$i-1]["kango"] = "";
                $dates[$i-1]["nurse"] = "";
            }
        }
        // 最終日の後の隙間を表示
        for($i=1; $i<=6-date('w',$timestamp); $i++) {
            array_push($atos,"");
        }

        $this->set(compact("year","month","maes","dates","jsch","atos","timestamp","holidays","nurses"));
    }

    public function editn()
    {
        $usersTable = TableRegistry::get('Users');
        $calendarTable = TableRegistry::get('Calendars');
        $attendanceTable = TableRegistry::get('Attendances');

        $query = $this->request->getQuery();
        $year = $query["year"];
        $month = sprintf("%02d",$query["month"]);
        $date = sprintf("%02d",$query["date"]);
        $dates = $year."-".$month."-".$date;
        $timestamp = mktime(0,0,0,$month,$date,$year);
        $timestamp2 = mktime(0,0,0,$month,date('t',$timestamp),$year);

        $users = [];
        $attendances = [];
        $an = 0;
        $getusers = $usersTable
        ->find()
        ->where(["Users.adminfrag"=>0])
        ->order(["Users.narabi"=>"ASC","Users.id"=>"ASC"])
        ->toArray();
        foreach($getusers as $getuser) {
            // narabi99：退職者ではないものの出勤がない者
            if($getuser["narabi"] != 99 && strtotime($getuser["created"]) < $timestamp2) {
                if(empty($getuser["retired"]) || strtotime($getuser["retired"]) >= $timestamp) {
                    array_push($users,$getuser);
                }
            }
        }
        foreach($users as $user) {
            $att = $attendanceTable
            ->find("list",["valueField"=>"medical"])
            ->where(["Attendances.date"=>$dates,"Attendances.user_id"=>$user["id"]])
            ->first();
            if(empty($att)) {
                $att = 0;
            }
            $attendances[$an]["id"] = $user["id"];
            $attendances[$an]["lastname"] = $user["lastname"];
            $attendances[$an]["medical"] = $att;
            $an++;
        }

        $this->set(compact("year","month","date","dates","attendances"));
    }

    public function index2()
    {
        $calendarTable = TableRegistry::get('Calendars');
        $year = date('Y');
        $month = date('m');
        $timestamp = mktime(0,0,0,$month,1,$year);
        $holidays = \Yasumi\Yasumi::create('Japan', $year, 'ja_JP');

        $nurses = $this->Kangos->find()->where(["Kangos.display"=>1])->toArray();

        $maes = []; $dates = []; $jsch = []; $atos = [];
        // 1日の前にある隙間を表示
        for($i=1; $i<=date('w',$timestamp); $i++) {
            array_push($maes,"");
        }
        // 日付を表示
        for($i=1; $i<=date('t',$timestamp); $i++) {
            $timestamp = mktime(0,0,0,$month,$i,$year);
            $calchk = $calendarTable
            ->find()
            ->where(['Calendars.date' => date('Y-m-d',$timestamp)])
            ->first();
            $dates[$i-1]["hidake"] = $i;
            $dates[$i-1]["date"] = date('Y-m-d',$timestamp);
            if(!empty($calchk["kango"])) {
                $dates[$i-1]["kango"] = $calchk["kango"];
            } else {
                $dates[$i-1]["kango"] = null;
            }
            if(!empty($calchk["nurse"])) {
                $dates[$i-1]["nurse"] = $calchk["nurse"];
            } else {
                $dates[$i-1]["nurse"] = null;
            }
        }

        // 最終日の後の隙間を表示
        for($i=1; $i<=6-date('w',$timestamp); $i++) {
            array_push($atos,"");
        }
        $this->set(compact("year","month","maes","dates","jsch","atos","timestamp","holidays","nurses"));
    }

    public function register()
    {
        // ログイン判定
        $user = $this->Auth->user();
        if(is_null($user)){
            // ログインしていない場合
            $this->Flash->error('ログインしていません');
            return $this->redirect(['action' => 'index']);
        } else {
            $calendarTable = TableRegistry::get('Calendars');
            $data = $this->request->getData();
            $year = $data["year"];
            $month = $data["month"];
            $kango = $data["kango"];
            $nurse = $data["nurse"];
            $timestamp = mktime(0,0,0,$month,1,$year);
            for($i=1; $i<=date('t',$timestamp); $i++) {
                $timestamp = mktime(0,0,0,$month,$i,$year);
                $calchk = $calendarTable
                ->find()
                ->where(['Calendars.date' => date('Y-m-d',$timestamp)])
                ->first();
                if($kango[$i] == "00:00" || empty($kango[$i])) {
                    $kango[$i] = null;
                }
                if(!empty($calchk)){
                    $calendar = $calendarTable->get($calchk->id);     
                } else {
                    $calendar = $calendarTable->newEntity();
                }
                $calendar->date = date("Y-m-d",$timestamp);
                $calendar->kango = $kango[$i];
                $calendar->nurse = $nurse[$i];
                $calendarTable->save($calendar);
            }
            if($calendarTable->save($calendar)){
                $this->Flash->success(__('保存されました'));
                return $this->redirect(['action' => 'indexn']);
            }
        }
    }

    public function register2()
    {
        $usersTable = TableRegistry::get('Users');
        $calendarTable = TableRegistry::get('Calendars');
        $attendanceTable = TableRegistry::get('Attendances');

        $data = $this->request->getData();
        $query = $this->request->getQuery();
        $timestamp = mktime(0,0,0,$query["month"],$query["date"],$query["year"]);

        for($i=0; $i<count($data["id"]); $i++) {
            $getatt = $attendanceTable
            ->find("list",["valueField"=>"id"])
            ->where(["Attendances.date"=>date('Y-m-d',$timestamp),"Attendances.user_id"=>$data["id"][$i]])
            ->first();

            if(!empty($getatt)) {
                $attendance = $attendanceTable->get($getatt);
            } else {
                $attendance = $attendanceTable->newEntity();
            }
                $attendance->medical = $data["medical"][$i];

            if($i != count($data["id"])-1) {
                $attendanceTable->save($attendance);
            } else {
                if($attendanceTable->save($attendance)) {
                    $this->Flash->success(__('医療支援情報が保存されました'));
                    return $this->redirect(['action' => 'indexn']);
                }
            }
            
        }
        $this->Flash->error(__('保存を完了できませんでした'));
        return $this->redirect(['action' => 'editn',"?"=>["year"=>$query["year"],"month"=>$query["month"],"date"=>$query["date"]]]);
    }

    public function getquery0()
    {
        $data = $this->request->getData();
        if($data["type"] == 0) {
            if($_SERVER['HTTP_HOST'] == "[::1]:8765") {
                return $this->redirect(['controller'=>'tests','action'=>'calendar',"?"=>["year"=>$data["year"],"month"=>$data["month"]]]);
            } else {
                return $this->redirect(['action'=>'edit',"?"=>["year"=>$data["year"],"month"=>$data["month"]]]);
            }
        }
    }
}