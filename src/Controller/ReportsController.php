<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

use Cake\Log\Log;

class ReportsController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        // $this->Auth->allow(['logout', 'add']);
    }

    public function delete($id = null)
    {
        $usersTable = TableRegistry::get('Users');
        $staffs = $usersTable->find('list',['keyField' => 'id','valueField' => 'name'])->toArray();
        $this->set('staffs', $staffs);
        $report = $this->Reports->get($id);

        if ($this->Reports->delete($report)) {
            $Items = TableRegistry::get('reportDetails');
            $result = $Items
            ->find()
            ->where(['reportDetails.report_id' => $id])
            ->order(['reportDetails.linenumber' => 'ASC'])
            ->first();
            for($i = 0; $i <= 2; $i++){
                $id2 = $result['id']+$i;
                $reportdetail = $Items->get($id2);
                $Items->delete($reportdetail);             
            }
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'ichiran']);
    }

    public function kokodozo()
    {
        $type = $this->request->getQuery("type");
        if($type == 0) {
            $timestamp = time();
            $Reps = $this->Reports
            ->find('list',['valueField'=>'date'])
            ->where(['Reports.date >=' => date('Y-m',$timestamp)."-1", 
                     'Reports.date <=' => date('Y-m',$timestamp)."-".date('t',$timestamp)])
            ->toArray();
            $RepCount = count($Reps);
            $LastPage = ceil($RepCount / 12);
            return $this->redirect(['action' => 'ichiran','?'=>['page'=>$LastPage]]);
        } else {
            return $this->redirect(['action' => 'ichiran']);
        }
    }

    public function editn() 
    {
        // 基本的な情報を取得
        $name = $this->request-> getSession()->read('Auth.User.name');
        $auser_id = $this->request-> getSession()->read('Auth.User.id');
        $weekList = ["日","月","火","水","木","金","土"];
        $usersTable = TableRegistry::get('Users');
        $reportsTable = TableRegistry::get('Reports');
        $attendanceTable = TableRegistry::get('Attendances');
        $year = $this->request->getSession()->read('Ryear');
        $month = $this->request->getSession()->read('Rmonth');
        $date = $this->request->getSession()->read('Rdate');
        $user_id = $this->request->getSession()->read('Ruser_id');
        $staffs = $usersTable
        ->find('list',['keyField'=>'id','valueField'=>'name'])
        ->where(['Users.adminfrag'=>0,'Users.display'=>0])
        ->toArray();

        if(empty($year)) {
            $year = date('Y');
        }
        if(empty($month)) {
            $month = date('m');
        }
        if(empty($date)) {
            $date = sprintf('%02d',date('d'));
        }
        if(empty($user_id)) {
            $user_id = $this->request->getSession()->read('Auth.User.id');
        }
        
        $getname = $usersTable
        ->find('list',["valueField"=>"name"])
        ->where(['Users.id'=>$user_id])
        ->first();
        $defaults = $usersTable
        ->find()
        ->select(['Users.user','Users.dintime','Users.douttime','Users.dresttime'])
        ->where(['Users.id' => $user_id])
        ->first();
        
        $todayStamp = mktime(0,0,0,date('n'),date('j'),date('Y'));
        $repStamp = mktime(0,0,0,$month,$date,$year);
        
        if($this->request-> getSession()->read('Auth.User.adminfrag') != 1) {
            $attchk = $attendanceTable
            ->find('list',['valueField'=>'intime'])
            ->where(['Attendances.user_id'=>$user_id,'Attendances.date'=>date('Y-m-d',$repStamp)])
            ->first();

            if(empty($attchk)) {
                if($repStamp < $todayStamp) {
                    $this->request->getSession()->write([
                        'Qyear' => $year,
                        'Qmonth' => $month,
                        'Quser_id' => $user_id,
                    ]);
                    $this->request->getSession()->delete('Ryear');
                    $this->request->getSession()->delete('Rmonth');
                    $this->request->getSession()->delete('Rdate');
                    $this->Flash->error(__('その日の出勤情報がありません。確認してください。'));

                    if($date <= 3) {
                        if($_SERVER['HTTP_HOST'] == "[::1]:8765") {
                            return $this->redirect(['controller'=>'tests','action'=>'edit']);
                        } else {
                            return $this->redirect(['controller'=>'TimeCards','action'=>'editn']);
                        }
                    } else {
                        $hi = $date - 3;
                        if($_SERVER['HTTP_HOST'] == "[::1]:8765") {
                            return $this->redirect([
                                'controller' => 'Tests',
                                'action' => 'edit',
                                '#' => 'jump'.$hi
                            ]);
                        } else {
                            return $this->redirect([
                                'controller' => 'TimeCards',
                                'action' => 'editn',
                                '#' => 'jump'.$hi
                            ]);
                        }
                    }
                } elseif($repStamp > $todayStamp) {
                    $this->request->getSession()->delete('Ryear');
                    $this->request->getSession()->delete('Rmonth');
                    $this->request->getSession()->delete('Rdate');
                    $this->Flash->error(__('本日より後のデータが選択されています。'));
                    return $this->redirect(['controller'=>'reports','action'=>'editn']);
                } else {
                    $this->Flash->error(__('打刻されていません。確認してください。'));
                    return $this->redirect(['controller'=>'users','action'=>'stampn']);
                }
            }
        }

        $rep = $reportsTable
        ->find()
        ->where(['Reports.user' => $defaults["user"], 'Reports.date' => date('Y-m-d',$repStamp)])
        ->first();

        $attendances = $attendanceTable
        ->find()
        ->select(['Attendances.intime','Attendances.outtime','Attendances.resttime','Attendances.meshi'])
        ->where(['Attendances.user_id' => $user_id,'Attendances.date' => date('Y-m-d',$repStamp)])
        ->EnableHydration(false)
        ->first();
        
        $timestamp = mktime(0,0,0,$month,1,$year);
        if(empty($date)) {
            $date = date('d');
        }
        if(empty($user_id)) {
            $user_id = $this->request->getSession()->read('Auth.User.id');
        }
        $postdate = $year."-".$month."-".$date;
        $postdate2 = mktime(0,0,0,$month,$date,$year);

        $reportdetailsTable = TableRegistry::get('ReportDetails');
        if(!empty($rep)) {
            $red = $reportdetailsTable
            ->find()
            ->where(['ReportDetails.report_id' => $rep["id"]])
            ->toArray();
        } else {
            $red = null;
        }

        $admresults = $usersTable
        ->find('list',["valueField"=>"name"])
        ->where(['Users.adminfrag'=>1])
        ->order(['Users.narabi'=>'ASC','Users.id'=>'ASC'])
        ->toArray();
        
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            if(!empty($rep)) {
                $staff_id = $usersTable
                ->find("list",["valueField"=>"id"])
                ->where(['Users.name' => $rep["recorder"]])
                ->first();
            } else {
                $staff_id = null;
            }
        } else {
            $staff_id = null;
        }

        for($i=2021;$i<=date('Y')+1;$i++) {
            $years["$i"] = $i;
        }
        for($i=1;$i<=12;$i++) {
            $months[sprintf('%02d',$i)] = $i;
        }
        for($i=1;$i<=31;$i++) {
            $dates[sprintf('%02d',$i)] = $i;
        }
        $this->set(compact('name','auser_id','staff_id','admresults','getname','weekList','staffs','rep','red','year',
        'month','date','dates','postdate2','user_id','defaults','years','months','attendances'));
    }

    public function getquery0()
    {
        $year = $this->request->getData('year');
        $month = $this->request->getData('month');
        $date = sprintf('%02d',$this->request->getData('date'));
        $ym = mktime(0,0,0,$month,1,$year);
        if($date > date('t',$ym)) {
            $this->Flash->error('存在しない日付です');
            return $this->redirect(['action' => 'ichiran']);
        }
        if($this->request->getSession()->read('Auth.User.adminfrag') == 1){
            $this->request->getSession()->write([
                'Ryear' => $year,
                'Rmonth' => $month,
                'Rdate' => sprintf('%02d',$date),
                'Ruser_id' => $this->request->getData('id'),
            ]);
            return $this->redirect(['action' => 'editn']);
        } else {
            $this->request->getSession()->write([
                'Ryear' => $year,
                'Rmonth' => $month,
                'Rdate' => sprintf('%02d',$date),
                'Ruser_id' => $this->request->getSession()->read('Auth.User.id'),
            ]);
            return $this->redirect(['action' => 'editn']);
        }
    }

    public function ichiran()
    {
        $weekList = ["日","月","火","水","木","金","土"];
        $usersTable = TableRegistry::get('Users');
        $staffs = $usersTable
        ->find('list',['keyField'=>'id','valueField'=>'name'])
        ->where(['Users.adminfrag'=>0,'Users.display'=>0])
        ->toArray();
        
        $year = $this->request->getSession()->read('Iyear');
        $month = $this->request->getSession()->read('Imonth');
        $user_id = $this->request->getSession()->read('Iuser_id');
        if(empty($year)) {
            $year = date('Y');
        }
        if(empty($month)) {
            $month = date("m");
        }
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            if(empty($user_id) || $user_id == 0) {
                $user_id = 0;
                $user = null;
                $name = null;
            } else {
                $getUser = $usersTable
                ->find()
                ->where(['Users.id'=>$user_id])
                ->first();
                $user = $getUser["user"];
                $name = $getUser["name"];
            }
        } else {
            if(empty($user_id)) {
                $user_id = $this->request->getSession()->read('Auth.User.id');
                $user = $this->request-> getSession()->read('Auth.User.user');
                $name = $this->request-> getSession()->read('Auth.User.name');
            } else {
                $user = $usersTable->find('list',['valueField'=>'user'])->where(['Users.id'=>$user_id])->first();
                $name = $this->request-> getSession()->read('Auth.User.name');
            }
        }
        $timestamp = mktime(0,0,0,$month,1,$year);
        
        if($this->request->getSession()->read('Auth.User.adminfrag') == 1 && empty($user)) {
            // 12個ずつに分割
            $this->paginate = [
                'limit' => 12,
                "order" => ["date" => "ASC"]
            ];
            $reports = $this->paginate($this->Reports
                            ->find()
                            ->where(['Reports.date >=' => date('Y-m',$timestamp)."-1", 
                                     'Reports.date <=' => date('Y-m',$timestamp)."-".date('t',$timestamp)]))
                            ->toArray();
        } else {
            // 8個ずつに分割
            $this->paginate = [
                'limit' => 8,
                "order" => ["date" => "ASC"]
            ];
            $reports = $this->paginate($this->Reports
                            ->find()
                            ->where(['Reports.user_id'=>$user_id, 
                                     'Reports.date >=' => date('Y-m',$timestamp)."-1",
                                     'Reports.date <=' => date('Y-m',$timestamp)."-".date('t',$timestamp)]))
                            ->toArray();
        }
        for($i=2021;$i<=date('Y')+1;$i++) {
            $years["$i"] = $i;
        }
        for($i=1;$i<=12;$i++) {
            $months[sprintf('%02d',$i)] = $i;
        }
        $this->set(compact('years','months','year','month','name','weekList','staffs','user','user_id','name','reports'));
    }

    public function getquery1()
    {
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            $this->request->getSession()->write([
                'Iyear' => $this->request->getData('year'),
                'Imonth' => $this->request->getData('month'),
                'Iuser_id' => $this->request->getData('id'),
            ]);
            return $this->redirect(['action' => 'ichiran']);
        } else {
            $this->request->getSession()->write([
                'Iyear' => $this->request->getData('year'),
                'Imonth' => $this->request->getData('month'),
                'Iuser_id' => $this->request->getSession()->read('Auth.User.id'),
            ]);
            return $this->redirect(['action' => 'ichiran']);
        }
    }

    public function detailn($id=null)
    {
        // ログイン判定
        $user = $this->Auth->user();

        if(is_null($user)){
            $this->Flash->error('ログインしていません');
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        } else {
            $data = $this->request->getData();
            $usersTable = TableRegistry::get('Users');
            $weekList = ["日","月","火","水","木","金","土"];
            $staffs = $usersTable->find('list',['keyField' => 'id','valueField' => 'name'])->toArray();
            $this->set('staffs', $staffs);
            $user = $this->request-> getSession()->read('Auth.User.user');
            $name = $this->request-> getSession()->read('Auth.User.name');

            $report = $this->Reports->get($id, [
                'contain' => [],
            ]);
            $this->set("report",$report);

            $Items = TableRegistry::get('reportDetails');
            $getQuery = $Items
            ->find()
            ->select(['reportDetails.id','reportDetails.report_id','reportDetails.linenumber','reportDetails.starttime','reportDetails.endtime','reportDetails.item','reportDetails.reportcontent'])
            ->where(['reportDetails.report_id' => $id])
            ->order(['reportDetails.linenumber' => 'ASC'])
            ->EnableHydration(false);
            $result = $getQuery->toArray();
            $this->set("red",$result);

            if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
                $usersTable = TableRegistry::get('Users');
                $aaa = $this->Reports
                    ->find('list',['keyField'=>'id','valueField'=>'user_id'])
                    ->where(['Reports.id' => $data["id"]]);
                $bbb = $aaa -> first();
                $ccc = $usersTable
                    ->find('list',['keyField'=>'id','valueField'=>'name'])
                    ->where(['Users.id' => $bbb]);
                $ddd = $ccc -> first();
                $user = $report['user'];
                $name = $ddd;
                $this->set(compact('user','name','report','weekList'));
            }else{
                if ($user !== $report['user']) {
                    return $this->redirect(['action' => 'list']);
                }else{
                $this->set(compact('user','name','report','weekList'));
                }
            }
        }
    }

    public function registern()
    {
        $user = $this->Auth->user();
        if(is_null($user)){
            $this->Flash->error('ログインしていません');
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }else{
            $usersTable = TableRegistry::get('Users');
            $remotesTable = TableRegistry::get('Remotes');
            $weekliesTable = TableRegistry::get('Weeklies');
            $attendanceTable = TableRegistry::get('Attendances');
            $reportDetailsTable = TableRegistry::get('ReportDetails');
            $data = $this->request->getData();
            $query = $this->request->getQuery();
            $date = $query["year"]."-".$query["month"]."-".$query["date"];
            $reportcontents = [$data['reportcontent0'],$data['reportcontent1'],$data['reportcontent2']];
            $items = [$data['item0'],$data['item1'],$data['item2']];

            $getRep = $this->Reports
            ->find()
            ->where(['Reports.user_id'=>$query["id"],'Reports.date'=>$date])
            ->first();
            $getUser = $usersTable
            ->find()
            ->where(['Users.id'=>$query["id"]])
            ->first();
            $remCheck = $attendanceTable
            ->find('list',['valueField'=>'remote'])
            ->where(['Attendances.user_id'=>$query["id"],'Attendances.date'=>$date])
            ->first();

            if(empty($getRep)) {
                $reports = $this->Reports->newEntity();
            } else {
                $reports = $this->Reports->get($getRep["id"]);
            }
            $reports->user = $getUser["user"];
            $reports->date = $date;
            $reports->intime = $data["intime"];
            $reports->outtime = $data["outtime"];
            $reports->resttime = $data["resttime"];
            $reports->content = $data["content"];
            $reports->notice = $data["notice"];
            $reports->plan = $data["plan"];
            $reports->user_id = $query["id"];
            $reports->kissyoku = (array_key_exists("kissyoku", $data)) ? $data["kissyoku"] : 0;
            if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
                $reports->recorder = $data["recorder"];
                $reports->state = $data["state"];
                $reports->information = $data["information"];
                $reports->bikou = $data["bikou"];
            }
            $this->Reports->save($reports);

            $getRep2 = $this->Reports
            ->find()
            ->where(['Reports.user_id'=>$query["id"],'Reports.date'=>$date])
            ->first();

            $articlesTable = TableRegistry::get('ReportDetails');
            for($i = 0; $i <= 2; $i++){
                $getArticle = $articlesTable
                ->find('list',["valueField"=>"id"])
                ->where(['ReportDetails.report_id'=>$getRep2["id"],'ReportDetails.linenumber'=>$i])
                ->first();

                if(empty($getArticle)){
                    $article = $articlesTable->newEntity();
                    $article->report_id = $getRep2["id"];
                    $article->linenumber = $i;
                } else {
                    $article = $articlesTable->get($getArticle);
                }
                $article->item = $items[$i];
                $article->reportcontent = $reportcontents[$i];
                $articlesTable->save($article); 
            }
            $this->Flash->success(__('作業日報が保存されました'));
            if($this->request-> getSession()->read('Auth.User.adminfrag') == 1) {
                if($getUser["remote"] == 1) {
                    if($remCheck == 1) {
                        return $this->redirect(['controller'=>'Remotes','action'=>'getquery0',
                                                '?'=>["date"=>$date,"user_id"=>$query["id"],"type"=>0]]);
                    } else {
                        $getLast = $weekliesTable
                        ->find('list',["valueField"=>"jdate"])
                        ->where(['Weeklies.user_id'=>$query["id"],
                                 'Weeklies.jdate >='=>date('Y-m-d', strtotime('last month')),
                                 'Weeklies.jdate <='=>date('Y-m-d', strtotime('yesterday'))])
                        ->order(['Weeklies.jdate'=>'DESC'])
                        ->EnableHydration(false)
                        ->first();
                        if(!empty($getLast)) {
                            $weeklyCheck = $remotesTable
                            ->find('list',["valueField"=>"id"])
                            ->where(['Remotes.user_id'=>$query["id"],
                                     'Remotes.date >='=>$getLast->i18nFormat('yyyy-MM-dd'),
                                     'Remotes.date <='=>date('Y-m-d', strtotime('yesterday'))])
                            ->toArray();
                            if(!empty($weeklyCheck)) {
                                return $this->redirect(['controller'=>'Remotes','action'=>'getquery0',
                                '?'=>["date"=>$date,"user_id"=>$query["id"],"type"=>1]]);
                            }
                        }
                    }
                }
                return $this->redirect(['controller' => 'Users', 'action' => 'ichiran']);
            } else {
                return $this->redirect(['controller' => 'Reports', 'action' => 'editn']);
            }
        }
    }

    public function report()
    {
        $watakushi = $this->Auth->user("id");
        $usersTable = TableRegistry::get('Users');
        $attendanceTable = TableRegistry::get('Attendances');
        $users = $usersTable
        ->find()
        ->select(['Users.id','Users.name','Users.retired'])
        ->where(['Users.adminfrag'=>0])
        ->EnableHydration(false)
        ->toArray();

        $x = 0; $y = 0; $mikans = []; $wasuremons = [];
        foreach($users as $user) {
            if(!empty($user["retired"]) && strtotime($user["retired"]->i18nFormat("YYYY-MM-dd")) < time() - 86400 * 30) {
                continue;
            } else {
                for($i=0; $i<30; $i++){
                    $timestamp = time() - 86400 * (30 - $i);
                    $attendance = $attendanceTable
                    ->find('list',['valueField'=>'intime'])
                    ->where(['Attendances.user_id'=>$user["id"],'Attendances.date'=>date('Y-m-d',$timestamp)])
                    ->first();
    
                    if(!empty($attendance)) {
                        $rep = $this->Reports
                        ->find()
                        ->where(['Reports.user_id'=>$user["id"],'Reports.date'=>date('Y-m-d',$timestamp)])
                        ->first();
                        if(!empty($rep) && empty($rep["recorder"])) {
                            $tantouchk = $attendanceTable
                            ->find('list',['valueField'=>'intime'])
                            ->where(['Attendances.user_id'=>$watakushi,'Attendances.date'=>date('Y-m-d',$timestamp)])
                            ->first();
    
                            $mikans[$x]["user_id"] = $user["id"];
                            $mikans[$x]["user_name"] = $user["name"];
                            $mikans[$x]["date"] = $timestamp;
                            if(!empty($tantouchk)) {
                                $mikans[$x]["status"] = 1;
                            } else {
                                $mikans[$x]["status"] = 2;
                            }
                            $x++;
                        } elseif(empty($rep) || empty($rep["content"])) {
                            $wasuremons[$y]["user_id"] = $user["id"];
                            $wasuremons[$y]["user_name"] = $user["name"];
                            $wasuremons[$y]["date"] = $timestamp;
                            $wasuremons[$y]["status"] = 0;
                            $y++;
                        }
                    }
                }
            }
        }
        $statustext = ['未入力','日誌入力可','日誌入力不可'];
        $this->set(compact('mikans','wasuremons','statustext'));
    }
}