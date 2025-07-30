<?php
namespace App\Controller;

use Cake\ORM\Table;
use cake\ORM\TableRegistry;
use App\Controller\AppController;

class TestsController extends AppController
{
    public function index() 
    {
        if($_SERVER['HTTP_HOST'] == "[::1]:8765") {
            $usersTable = TableRegistry::get('Users');
            for($i=2021;$i<=date('Y')+1;$i++) {
                $years["$i"] = $i;
            }
            for($i=1;$i<=12;$i++) {
                $months[sprintf('%02d',$i)] = $i;
            }
            $users = $usersTable
            ->find('list',['keyField'=>'id','valueField'=>'name'])
            ->where(['Users.narabi <=' => 100])
            ->order(['Users.narabi'=>'ASC','Users.id'=>'ASC'])
            ->toArray();

            $this->set(compact('years','months','users'));
        } else {
            $this->Flash->error('アクセス権限がありません');
            return $this->redirect(['controller'=>'users','action' => 'stampn']);
        }
    }

    public function edit() 
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
        
        $admresults = $usersTable
        ->find('list',['valueField' => 'lastname'])
        ->where(['Users.adminfrag'=>1])
        ->order(['Users.narabi'=>'ASC','Users.id'=>'ASC'])
        ->toArray();

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
        $alltime = sprintf('%02d',$allfh).":".sprintf('%02d',$allfm);

        $allworkdays = date("t",$timestamp) - 8;
        $percent = round($attenddays/$allworkdays*100) ." %";
        $this->set(compact("year","month","user_id","getname","timestamp","adminfrag","admresults","username",
        "results","allkoukyu","allpaid","allkekkin","allworkdays","percent","alltime"));
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
        return $this->redirect(['action' => 'edit']);
    }

    public function logout()
    {
        $this->request->session()->destroy();
        $this->Flash->success('ログアウトしました。');
        $this->redirect($this->Auth->redirectUrl('/users/login'));
    }

    public function calendar()
    {
        $calendarTable = TableRegistry::get('Calendars');
        $kangosTable = TableRegistry::get('Kangos');
        $query = $this->request->getQuery();
        $year = $query["year"];
        $month = $query["month"];
        $timestamp = mktime(0,0,0,$month,1,$year);

        $nurses = $kangosTable->find('list',["valueField"=>"name"])->where(["Kangos.display"=>1])->toArray();

        $maes = []; $dates = []; $jsch = []; $atos = [];
        for($i=1; $i<=date('w',$timestamp); $i++) {
            array_push($maes,"");
        }
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
        for($i=1; $i<=6-date('w',$timestamp); $i++) {
            array_push($atos,"");
        }

        $this->set(compact("year","month","maes","dates","jsch","atos","timestamp","nurses"));
    }

    public function register()
    {
        $usersTable = TableRegistry::get('Users');
        $attendancesTable = TableRegistry::get('Attendances');

        $data = $this->request->getData();
        $userData = $usersTable->find()->where(['Users.id'=>$data["id"]])->first();
        $timestamp = mktime(0,0,0,$data["month"],1,$data["year"]);

        for($i=1; $i<=date('t',$timestamp); $i++) {
            $rand = mt_rand(1,20);
            $timestamp = mktime(0,0,0,$data["month"],$i,$data["year"]);
            $attCheck = $attendancesTable
            ->find()
            ->where(['Attendances.user_id'=>$data["id"],'Attendances.date'=>date('Y-m-d',$timestamp)])
            ->first();
            if(empty($attCheck)) {
                $attendance = $attendancesTable->newEntity();
            } else {
                $attendance = $attendancesTable->get($attCheck["id"]);
            }
            $attendance->user_id = $data["id"];
            $attendance->date = date("Y-m-d",$timestamp);
            $attendance->ou = 0;
            $attendance->fuku = 0;
            $attendance->meshi = 0;
            $attendance->medical = 0;
            $attendance->support = 0;

            if(date('w',$timestamp) == 0 || date('w',$timestamp) == 6) {
                $attendance->intime = null;
                $attendance->outtime = null;
                $attendance->resttime = null;
                $attendance->overtime = null;
                $attendance->koukyu = 1;
                $attendance->paid = 0;
                $attendance->kekkin = 0;
                $attendance->bikou = null;
                $attendance->remote = 0;
            } else {
                $attendance->koukyu = 0;
                if($rand == 20) {
                    $attendance->intime = null;
                    $attendance->outtime = null;
                    $attendance->resttime = null;
                    $attendance->overtime = null;
                    $attendance->paid = 1;
                    $attendance->kekkin = 0;
                    $attendance->bikou = NULL;
                    $attendance->remote = 0;
                } elseif($rand == 1) {
                    $attendance->intime = null;
                    $attendance->outtime = null;
                    $attendance->resttime = null;
                    $attendance->overtime = null;
                    $attendance->paid = 0;
                    $attendance->kekkin = 1;
                    $attendance->bikou = "体調不良のため";
                    $attendance->remote = 0;
                } else {
                    $attendance->intime = $userData["dintime"]->i18nFormat("HH:mm");
                    $attendance->outtime = $userData["douttime"]->i18nFormat("HH:mm");
                    $attendance->resttime = $userData["dresttime"]->i18nFormat("HH:mm");
                    $attendance->overtime = null;
                    $attendance->paid = 0;
                    $attendance->kekkin = 0;
                    $attendance->bikou = NULL;
                    if($userData["remote"] == 1) {
                        $attendance->remote = 1;
                    }
                }
            }
            $attendancesTable->save($attendance);
        }
        if($attendancesTable->save($attendance)) {
            $this->Flash->success(__('出勤情報が保存されました'));
            return $this->redirect(['action' => 'index']);
        } else {
            $this->Flash->error(__('保存に失敗しました'));
            return $this->redirect(['action' => 'index']);
        }           
    }

    public function ichiran()
    {
        $usersTable = TableRegistry::get('Users');
        if($this->request->getSession()->read('Auth.User.adminfrag') == 1) {
            // 表示するデータを区別
            $display = $this->request->getSession()->read(['displaykubun']);
            if(empty($display)) {
                $display = 0;
            }
      
            $this->paginate = [
                "limit" => 8,
                "order" => ['display'=>'DESC','id'=>'ASC']
            ];
            if($display == 0) {
                $users = $this->paginate($usersTable
                                ->find()
                                ->where(['Users.adminfrag' => 1,'Users.narabi' => 1]))
                                ->toArray();
            } elseif($display == 1) {
                $users = $this->paginate($usersTable
                                ->find()
                                ->where(['Users.adminfrag' => 0,'Users.narabi' => 10]))
                                ->toArray();
            } else {
                $users = $this->paginate($usersTable
                                ->find()
                                ->where(['Users.retired <=' => date('Y-m-d')]))
                                ->toArray();
            }
            $kubuns = ["職員","利用者","退職者"];
            $this->set(compact("users","kubuns","display"));
        } else {
            return $this->redirect(['action' => 'login']);           
        }
    }

    public function attendance()
    {
        // ログイン判定
        $auser = $this->Auth->user("adminfrag");
        $myid = $this->Auth->user("id");
        if(is_null($auser)) {
            $this->Flash->error('ログインしていません');
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        } else {
            $users = [];
            $staffs = [];
            $usersTable = TableRegistry::get('Users');
            $reportTable = TableRegistry::get('Reports');
            $attendanceTable = TableRegistry::get('Attendances');
            $workplacesTable = TableRegistry::get('Workplaces');

            $allstaffs = $usersTable
            ->find()
            ->where(['Users.adminfrag'=>1,'Users.narabi'=>1])
            ->order(['Users.narabi'=>'ASC','Users.id'=>'ASC'])
            ->EnableHydration(false)
            ->toArray();

            $x = 0;
            foreach($allstaffs as $allstaff) {
                $getStamp = $attendanceTable
                ->find()
                ->where(['Attendances.user_id'=>$allstaff["id"],'Attendances.date'=>date('Y-m-d')])
                ->EnableHydration(false)
                ->first();
                if($this->request->getSession()->read('Auth.User.adminfrag') == 1) { 
                    if(!empty($getStamp["afk"]) && $getStamp["afk"] == 1) {
                        $staffs[$x]["status"] = 7; 
                    } elseif(!empty($getStamp["intime"]) && $getStamp["outtime"] && 
                        $getStamp["outtime"] != "" && $getStamp["outtime"]->i18nFormat("HH:mm") >= date("H:i") && 
                        $getStamp["intime"] != "" && $getStamp["intime"]->i18nFormat("HH:mm") <= date("H:i")) {
                        $staffs[$x]["status"] = 1;
                    } elseif(!empty($getStamp["intime"]) && $getStamp["intime"]->i18nFormat("HH:mm") > date("H:i")) {
                        $staffs[$x]["status"] = 8;
                    } elseif(!empty($getStamp["outtime"]) && $getStamp["outtime"]->i18nFormat("HH:mm") < date("H:i")) {
                        $staffs[$x]["status"] = 2;
                    } elseif(!empty($getStamp["koukyu"]) && $getStamp["koukyu"] == 1) {
                        $staffs[$x]["status"] = 3;
                    } elseif(!empty($getStamp["paid"]) && $getStamp["paid"] == 1) {
                        $staffs[$x]["status"] = 4;
                    } elseif(!empty($getStamp["kekkin"]) && $getStamp["kekkin"] == 1) {
                        $staffs[$x]["status"] = 5;
                    } else {
                        $staffs[$x]["status"] = 0;
                    }
                } else {
                    if(!empty($getStamp["afk"]) && $getStamp["afk"] == 1) {
                        $staffs[$x]["status"] = 7;
                    } elseif(!empty($getStamp["intime"]) && $getStamp["outtime"] && 
                    $getStamp["outtime"] != "" && $getStamp["outtime"]->i18nFormat("HH:mm") >= date("H:i") && 
                    $getStamp["intime"] != "" && $getStamp["intime"]->i18nFormat("HH:mm") <= date("H:i")) {
                        $staffs[$x]["status"] = 1;
                    } elseif(!empty($getStamp["intime"]) && $getStamp["intime"]->i18nFormat("HH:mm") > date("H:i")) {
                        $staffs[$x]["status"] = 8;
                    } elseif(!empty($getStamp["outtime"]) && $getStamp["outtime"]->i18nFormat("HH:mm") < date("H:i")) {
                        $staffs[$x]["status"] = 2;
                    } elseif(!empty($getStamp["koukyu"]) && $getStamp["koukyu"] == 1) {
                        $staffs[$x]["status"] = 6;
                    } elseif(!empty($getStamp["paid"]) && $getStamp["paid"] == 1) {
                        $staffs[$x]["status"] = 6;
                    } elseif(!empty($getStamp["kekkin"]) && $getStamp["kekkin"] == 1) {
                        $staffs[$x]["status"] = 6;
                    } else {
                        $staffs[$x]["status"] = 0;
                    }
                }
                if(!empty($allstaff["retired"]) && $allstaff["retired"]->i18nFormat('yyyy-mm-dd') <= date('Y-m-d')) {
                    continue;
                } else {
                    $staffs[$x]["user_id"] = $allstaff["id"];
                    $staffs[$x]["name"] = $allstaff["name"];
                    $x++;
                }
            }
            $workPlaces = $workplacesTable->find()->EnableHydration(false)->toArray();
            for($i = 0; $i < count($workPlaces); $i++) {
                $zanUsers = $usersTable
                ->find()
                ->where(['Users.adminfrag' => 0,'Users.workplace' => $workPlaces[$i]["id"]])
                ->toArray();
                $j = 0;
                foreach($zanUsers as $zanUser) {
                    if(empty($zanUser["retired"]) || 
                      strtotime($zanUser["retired"]->i18nFormat("yyyy-MM-dd")) > time()) {
                        $users[$i][$j] = $zanUser;
                        $j++;
                    }
                }

                for($j = 0; $j < count($users[$i]); $j++) {

                    $getStamp = $attendanceTable
                    ->find()
                    ->where(['Attendances.user_id'=>$users[$i][$j]["id"],'Attendances.date'=>date('Y-m-d')])
                    ->EnableHydration(false)
                    ->first();
                    $getRep = $reportTable
                    ->find()
                    ->select(['Reports.id','Reports.recorder'])
                    ->where(['Reports.user_id'=>$users[$i][$j]["id"],'Reports.date'=>date('Y-m-d')])
                    ->first();

                    if($this->request->getSession()->read('Auth.User.adminfrag') == 1) {   
                        if(!empty($getStamp["outtime"]) 
                            && $getStamp["outtime"] != "" && $getStamp["outtime"]->i18nFormat("HH:mm") >= date("H:i")
                            && $getStamp["intime"] != "" && $getStamp["intime"]->i18nFormat("HH:mm") <= date("H:i")) {
                                if($getStamp["remote"] == 1) {
                                    $users[$i][$j]["status"] = 9;
                                } else {
                                    $users[$i][$j]["status"] = 1;
                                }
                        } elseif(!empty($getStamp["intime"]) && $getStamp["intime"]->i18nFormat("HH:mm") > date("H:i")) {
                            $users[$i][$j]["status"] = 8;
                        } elseif(!empty($getStamp["outtime"]) && $getStamp["outtime"]->i18nFormat("HH:mm") < date("H:i")) {
                            $users[$i][$j]["status"] = 2;
                        } elseif(!empty($getStamp["koukyu"]) && $getStamp["koukyu"] == 1) {
                            $users[$i][$j]["status"] = 3;
                        } elseif(!empty($getStamp["paid"]) && $getStamp["paid"] == 1) {
                            $users[$i][$j]["status"] = 4;
                        } elseif(!empty($getStamp["kekkin"]) && $getStamp["kekkin"] == 1) {
                            $users[$i][$j]["status"] = 5;
                        } else {
                            $users[$i][$j]["status"] = 0;
                        }
                    } else {
                        if(!empty($getStamp["outtime"])
                            && $getStamp["outtime"] != "" && $getStamp["outtime"]->i18nFormat("HH:mm") >= date("H:i")
                            && $getStamp["intime"] != "" && $getStamp["intime"]->i18nFormat("HH:mm") <= date("H:i")) {
                                if($getStamp["remote"] == 1) {
                                    $users[$i][$j]["status"] = 9;
                                } else {
                                    $users[$i][$j]["status"] = 1;
                                }
                        } elseif(!empty($getStamp["intime"]) && $getStamp["intime"]->i18nFormat("HH:mm") > date("H:i")) {
                            $users[$i][$j]["status"] = 8;
                        } elseif(!empty($getStamp["outtime"]) && $getStamp["outtime"]->i18nFormat("HH:mm") < date("H:i")) {
                            $users[$i][$j]["status"] = 2;
                        } elseif(!empty($getStamp["koukyu"]) && $getStamp["koukyu"] == 1) {
                            $users[$i][$j]["status"] = 6;
                        } elseif(!empty($getStamp["paid"]) && $getStamp["paid"] == 1) {
                            $users[$i][$j]["status"] = 6;
                        } elseif(!empty($getStamp["kekkin"]) && $getStamp["kekkin"] == 1) {
                            $users[$i][$j]["status"] = 6;
                        } else {
                            $users[$i][$j]["status"] = 0;
                        }
                    }

                    if(empty($getRep)) {
                        $users[$i][$j]["reportcheck"] = 0;
                    } elseif(empty($getRep["recorder"])) {
                        $users[$i][$j]["reportcheck"] = 1;
                    } else {
                        $users[$i][$j]["reportcheck"] = 2;
                    }
                    if(empty($getStamp) || empty($getStamp["intime"])) {
                        $users[$i][$j]["staffcheck"] = 0;
                    } elseif(empty($getStamp["user_staffid"])) {
                        $users[$i][$j]["staffcheck"] = 1;
                    } else {
                        $users[$i][$j]["staffcheck"] = 2;
                    }
                }
            }

            $url = "https://www.nbg-rd.com/gw/reports/edit/".$getRep;
            $statustext = ['','出勤中','退　社','公　休','有　休','欠　勤','お休み','離席中','打刻済','在宅出勤中'];
            $reptext = ['','済','業務日誌済'];
            $tantoutext = ['','未入力','入力済'];
            $this->set(compact("users","staffs","statustext","reptext","tantoutext","myid","workPlaces"));
        }
    }

    public function schedule()
    {
        $usersTable = TableRegistry::get('Users');
        $calendarsTable = TableRegistry::get('Calendars');
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

        $maes = []; $dates = []; $jsch = []; $atos = [];
        for($i=1; $i<=date('w',$timestamp); $i++) {
            array_push($maes,"");
        }
        for($i=1; $i<=date('t',$timestamp); $i++) {
            $timestamp = mktime(0,0,0,$month,$i,$year);
            $cal = $calendarsTable
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
        $this->set(compact("years","months","year","month","maes","dates","jsch","atos","timestamp"));
    }

    public function hoken()
    {
        $usersTable = TableRegistry::get('Users');
    }

    public function stamp()
    {
        $a = NULL;
        $a -> i18nFormat("HH:mm");
    }

    public function akan()
    {
        $usersTable = TableRegistry::get('Users');
    }

    public function stamp2()
    {
        $year = NULL;
        $holidays = \Yasumi\Yasumi::create('Japan', $year, 'ja_JP');
    }
}