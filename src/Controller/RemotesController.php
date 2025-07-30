<?php
namespace App\Controller;
use Cake\ORM\Table;
use cake\ORM\TableRegistry;
use App\Controller\AppController;

class RemotesController extends AppController
{    
    public function indexn()
    {
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            $usersTable = TableRegistry::get('Users');
            $year = date('Y');
            $month = date('m');
            $date = date('d');
            $timestamp = mktime(0,0,0,$month,$date,$year);

            for($i=2021;$i<=date('Y')+1;$i++) {
                $years["$i"] = $i;
            }
            for($i=1;$i<=12;$i++) {
                $months[sprintf('%02d',$i)] = $i;
            }
            for($i=1;$i<=31;$i++) {
                $dates[sprintf('%02d',$i)] = $i;
            }
            $remotes = $usersTable
            ->find('list',['keyField'=>'id','valueField'=>'name'])
            ->where(['Users.remote'=>1, 'Users.display'=>0])
            ->order(['Users.workplace'=>'ASC','Users.id'=>'ASC'])
            ->toArray();
            $this->set(compact("years","months","dates","year","month","date","remotes"));
        } else {
            $this->Flash->error(__('アクセス権限がありません'));
            return $this->redirect(['controller'=>'users','action'=>'login']);
        }
    }

    public function getquery0()
    {
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            if(!empty($this->request->getQuery())) {
                $query = $this->request->getQuery();
                $exdate = explode("-",$query["date"]);
                $data["type"] = $query["type"];
                $data["year"] = $exdate[0];
                $data["month"] = $exdate[1];
                $data["date"] = $exdate[2];
                $data["user_id"] = $query["user_id"];
            } else {
                $data = $this->request->getData();
                $timestamp = mktime(0,0,0,$data["month"],1,$data["year"]);
                if(!empty($data["date"]) && $data["date"] > date('t',$timestamp)) {
                    $this->Flash->error(__('存在しない日付です'));
                    return $this->redirect(['action'=>'indexn']);
                }
            }
            if($data["type"]==0) {
                $hinichi = $data["year"]."-".$data["month"]."-".$data["date"];
                $this->request->getSession()->write([
                    'Remyear' => $data["year"],
                    'Remmonth' => $data["month"],
                    'Remdate' => $data["date"],
                    'Remid' => $data["user_id"]
                ]);
                return $this->redirect(['action'=>'edit']);
            } elseif($data["type"]==1) {
                $hinichi = $data["year"]."-".$data["month"]."-".$data["date"];
                $this->request->getSession()->write([
                    'Weeyear' => $data["year"],
                    'Weemonth' => $data["month"],
                    'Weedate' => $data["date"],
                    'Weeid' => $data["user_id"]
                ]);
                return $this->redirect(['action'=>'editn']);
            } elseif($data["type"]==2) {
                $this->request->getSession()->write([
                    'Ichyear' => $data["year"],
                    'Ichmonth' => $data["month"],
                    'Ichid' => $data["user_id"]
                ]);
                return $this->redirect(['action'=>'ichiran']);
            } else {
                $this->Flash->error(__('フォーム入力エラー'));
                return $this->redirect(['action'=>'index']);
            }
        } else {
            $this->Flash->error(__('アクセス権限がありません'));
            return $this->redirect(['controller'=>'users','action'=>'login']);
        }
    }

    public function edit()
    {
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            // 基本的な情報を取得
            $year = $this->request->getSession()->read('Remyear');
            $month = $this->request->getSession()->read('Remmonth');
            $date = $this->request->getSession()->read('Remdate');
            $user_id = $this->request->getSession()->read('Remid');
            $timestamp = mktime(0,0,0,$month,$date,$year);
            $staff = $this->request-> getSession()->read('Auth.User.id');
            $usersTable = TableRegistry::get('Users');
            $reportTable = TableRegistry::get('Reports');
            $attendanceTable = TableRegistry::get('Attendances');
            $user = $usersTable->find()->where(['Users.id'=>$user_id])->first();
            $getstaffs = $usersTable
            ->find('list',['valueField'=>'name'])
            ->where(['Users.adminfrag'=>1,'Users.display'=>0])
            ->order(['Users.narabi'=>'ASC','Users.id'=>'ASC'])
            ->toArray();

            $getAtt = $attendanceTable
            ->find()
            ->where(['Attendances.user_id'=>$user_id,'Attendances.date'=>date('Y-m-d',$timestamp)])
            ->EnableHydration(false)
            ->first();

            $getrep = $reportTable
            ->find()
            ->where(["Reports.user_id"=>$user_id,"Reports.date"=>date('Y-m-d',$timestamp)])
            ->EnableHydration(false)
            ->first();

            $getRem = $this->Remotes
            ->find()
            ->where(['Remotes.user_id'=>$user_id,"Remotes.date"=>date('Y-m-d',$timestamp)])
            ->first();

            $shudan = ["訪問","電話","その他"];
            $gyakushudan["訪問"] = 0;
            $gyakushudan["電話"] = 1;
            $template1 = $user["lastname"]."さんからZoomにて作業開始の連絡を受け、体調などの様子を伺う";
            $template2 = $user["lastname"]."さんからZoomにて作業終了の連絡";

            $this->set(compact("user","user_id","year","month","date","staff","getstaffs","getAtt",
                               "shudan","template1","template2","getrep","getRem","gyakushudan"));
        } else {
            $this->Flash->error(__('アクセス権限がありません'));
            return $this->redirect(['controller'=>'users','action'=>'login']);
        }
    }

    public function editn()
    {
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            // 基本的な情報を取得
            $usersTable = TableRegistry::get('Users');
            $attendanceTable = TableRegistry::get('Attendances');
            $weeklyTable = TableRegistry::get('Weeklies');
            $year = $this->request->getSession()->read('Weeyear');
            $month = $this->request->getSession()->read('Weemonth');
            $date = $this->request->getSession()->read('Weedate');
            $user_id = $this->request->getSession()->read('Weeid');
            $timestamp = mktime(0,0,0,$month,$date,$year);
            $staff = $this->request-> getSession()->read('Auth.User.id');
            $user = $usersTable->find()->where(['Users.id'=>$user_id])->first();
            $getstaffs = $usersTable
            ->find('list',['valueField'=>'name'])
            ->where(['Users.adminfrag'=>1,'Users.display'=>0])
            ->order(['Users.narabi'=>'ASC','Users.id'=>'ASC'])
            ->toArray();
            
            $zen = $timestamp - 86400;
            $sengetsu = strtotime("first day of previous month",$timestamp);

            $getthis = $weeklyTable
            ->find()
            ->where(['Weeklies.user_id'=>$user_id,'Weeklies.jdate'=>date('Y-m-d',$timestamp)])
            ->EnableHydration(false)
            ->first();

            if(!empty($getthis)) {
                $getworks = $this->Remotes
                ->find()
                ->where(['Remotes.user_id' => $user_id,
                         'Remotes.date >=' => $getthis["hdate"]->i18nFormat('yyyy-MM-dd'),
                         'Remotes.date <=' => date('Y-m-d',$zen)])
                ->toArray();
                if(!empty($getthis["lasttime"])) {
                    $maeda = explode("-",$getthis["lasttime"]->i18nFormat("yyyy-MM-dd"));
                } else {
                    $maeda = ["","",""];
                }
                if(!empty($getthis["hdate"])) {
                    $hajime = explode("-",$getthis["hdate"]->i18nFormat("yyyy-MM-dd"));
                } else {
                    $hajime = ["","",""];
                }
                if(!empty($getthis["odate"])) {
                    $owari = explode("-",$getthis["odate"]->i18nFormat("yyyy-MM-dd"));
                } else {
                    $owari = ["","",""];
                }
            } else {
                $getlast = $weeklyTable
                ->find()
                ->where(['Weeklies.user_id'=>$user_id,
                        'Weeklies.jdate >='=>date('Y-m-d',$sengetsu),
                        'Weeklies.jdate <='=>date('Y-m-d',$zen)])
                ->order(['Weeklies.jdate'=>'DESC'])
                ->EnableHydration(false)
                ->first();
                $this->set(compact('getlast'));

                if(!empty($getlast["jdate"])) {
                    $getworks = $this->Remotes
                    ->find()
                    ->where(['Remotes.user_id' => $user_id,
                             'Remotes.date >=' => $getlast["jdate"]->i18nFormat('yyyy-MM-dd'),
                             'Remotes.date <=' => date('Y-m-d',$zen)])
                    ->toArray();
                    $maeda = explode("-",$getlast["jdate"]->i18nFormat("yyyy-MM-dd"));
                    $jstamp = mktime(0,0,0,$maeda[1],$maeda[2],$maeda[0]);
                    $hajime = explode("-",date('Y-m-d',$jstamp + 86400));
                    $owari = explode("-",date('Y-m-d',$zen));
                } else {
                    $getworks = $this->Remotes
                    ->find()
                    ->where(['Remotes.user_id' => $user_id,
                             'Remotes.date >=' => date('Y-m-d',$zen - 86400 * 7),
                             'Remotes.date <=' => date('Y-m-d',$zen)])
                    ->toArray();
                    if(empty($getworks)) {
                        $getworks = NULL;
                    }
                    $maeda = ["","",""];
                    $hajime = explode("-",date('Y-m-d',$timestamp - 86400 * 8));
                    $owari = explode("-",date('Y-m-d',$zen));
                }
            }
            $shudan = ["電話","通所","その他"];
            $gyakushudan[0] = "電話";
            $gyakushudan[1] = "通所";
            $this->set(compact("user","user_id","year","month","date","getstaffs","staff",
                               "shudan","getworks","zen","gyakushudan","getthis","hajime","owari","maeda"));
        } else {
            $this->Flash->error(__('アクセス権限がありません'));
            return $this->redirect(['controller'=>'users','action'=>'login']);
        }
    }

    public function register()
    {
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            $usersTable = TableRegistry::get('Users');

            $data = $this->request->getData();
            $exdate = explode("-",$data["date"]);
            $shudan = ["訪問","電話","その他"];

            // 送られてきたデータを判定
            if(empty($data["time1"]) || empty($data["time2"])) {
                $this->Flash->error(__('時刻入力に空欄があります'));
                return $this->redirect(['action'=>'indexn']);
            }

            $user_id = $usersTable
            ->find("list",["valueField"=>"id"])
            ->where(["Users.name"=>$data["name"]])
            ->first();
            $datachk = $this->Remotes
            ->find()
            ->where(['Remotes.date'=>$data["date"],'Remotes.user_id'=>$user_id])
            ->EnableHydration(false)
            ->first();

            if(empty($datachk)) {
                $remote = $this->Remotes->newEntity();
            } else {
                $remote = $this->Remotes->get($datachk["id"]);
            }
            $remote->user_id = $user_id;
            $remote->date = $data["date"];
            $remote->intime = $data["intime"];
            $remote->outtime = $data["outtime"];
            $remote->work = $data["work"];
            $remote->time1 = $data["time1"];
            $remote->content1 = $data["content1"];
            $remote->time2 = $data["time2"];
            $remote->content2 = $data["content2"];
            $remote->user_staffid = $data["staff"];
            $remote->health = $data["health"];
            if($data["shudan"]==2) {
                if(!empty($data["shudan2"])) {
                    $remote->shudan = $data["shudan2"];
                } else {
                    $this->Flash->error(__('手段を入力してください'));
                    return $this->redirect(['action'=>'indexn']);
                }
            } else {
                $remote->shudan = $shudan[$data["shudan"]];
            }
            if($this->Remotes->save($remote)){
                $this->Flash->success(__('在宅就労記録が登録されました'));
                return $this->redirect(['controller'=>'Users','action'=>'ichiran']);
            } else {
                $this->Flash->error(__('データの登録に失敗しました'));
                return $this->redirect(['action'=>'indexn']);
            }
        } else {
            $this->Flash->error(__('アクセス権限がありません'));
            return $this->redirect(['controller'=>'users','action'=>'login']);
        }
    }

    public function register2()
    {
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            // 基本的な情報を取得
            $data = $this->request->getData();
            $usersTable = TableRegistry::get('Users');
            $attendanceTable = TableRegistry::get('Attendances');
            $weeklyTable = TableRegistry::get('Weeklies');
            $user_id = $usersTable
            ->find("list",["valueField"=>"id"])
            ->where(["Users.name"=>$data["name"]])
            ->first();

            $datachk = $weeklyTable
            ->find()
            ->where(["Weeklies.jdate"=>$data["jdate"],"Weeklies.user_id"=>$user_id])
            ->EnableHydration(false)
            ->first();

            $lmonth = sprintf("%02d",$data["lmonth"]);
            $hmonth = sprintf("%02d",$data["hmonth"]);
            $omonth = sprintf("%02d",$data["omonth"]);

            if(empty($datachk)) {
                $weekly = $weeklyTable->newEntity();
            } else {
                $weekly = $weeklyTable->get($datachk["id"]);
            }
            $weekly->user_id = $user_id;
            $weekly->user_staffid = $data["staff"];
            $weekly->sabikan = $data["sabikan"];
            $weekly->shudan = $data["shudan"];
            $weekly->jdate = $data["jdate"];
            $weekly->lasttime = $data["lyear"]."-".$lmonth."-".$data["ldate"];
            $weekly->hdate = $data["hyear"]."-".$hmonth."-".$data["hdate"];
            $weekly->odate = $data["oyear"]."-".$omonth."-".$data["odate"];
            $weekly->content = $data["content"];

            if($weeklyTable->save($weekly)){
                $this->Flash->success(__('在宅就労記録が登録されました'));
                return $this->redirect(['controller'=>'Users','action'=>'ichiran']);
            } else {
                $this->Flash->error(__('データの登録に失敗しました'));
                return $this->redirect(['action'=>'indexn']);
            }
        } else {
            $this->Flash->error(__('アクセス権限がありません'));
            return $this->redirect(['controller'=>'users','action'=>'login']);
        }
    }

    public function ichiran()
    {
        // 基本的な情報を取得
        $year = $this->request->getSession()->read('Ichyear');
        $month = $this->request->getSession()->read('Ichmonth');
        $user_id = $this->request->getSession()->read('Ichid');
        $timestamp = mktime(0,0,0,$month,1,$year);
        $timestamp2 = mktime(0,0,0,$month,date('t',$timestamp),$year);
        $usersTable = TableRegistry::get('Users');
        $weeklyTable = TableRegistry::get('Weeklies');

        $remotes = $usersTable
        ->find('list',['valueField'=>'name'])
        ->where(['Users.remote'=>1])
        ->toArray();
        
        $weekList = ["日","月","火","水","木","金","土"];
        for($i=2021;$i<=date('Y')+1;$i++) {
            $years["$i"] = $i;
        }
        for($i=1;$i<=12;$i++) {
            $months[sprintf('%02d',$i)] = $i;
        }
        
        $users = $usersTable
        ->find('list',['valueField'=>'name'])
        ->where(['Users.adminfrag'=>0,'Users.display'=>0])
        ->toArray();
        $staffs = $usersTable
        ->find('list',['valueField'=>'name'])
        ->where(['Users.adminfrag'=>1,'Users.display'=>0])
        ->toArray();

        // 10件ずつ分割する
        $this->paginate = [
            'limit' => 10,
            "order" => ["date" => "ASC"]
        ];
        $getremotes = $this->paginate($this->Remotes
                           ->find()
                           ->where(['Remotes.user_id'=>$user_id,
                                    'Remotes.date >='=>date('Y-m-d',$timestamp),
                                    'Remotes.date <='=>date('Y-m-d',$timestamp2)]))
                           ->toArray();

        $getweeklies = $weeklyTable
        ->find()
        ->where(['Weeklies.user_id'=>$user_id,
                 'Weeklies.jdate >='=>date('Y-m-d',$timestamp),
                 'Weeklies.jdate <='=>date('Y-m-d',$timestamp2)])
        ->EnableHydration(false)
        ->toArray();
        $this->set(compact("years","months","year","month","user_id","users","staffs","weekList",
                           "getremotes","getweeklies","remotes"));
    }

    public function delete($id = null)
    {
        $query = $this->request->getQuery();
        
        if($query["type"] == 0) {
            $this->request->allowMethod(['post', 'delete']);
            $remotes = $this->Remotes->get($id);
            if ($this->Remotes->delete($remotes)) {
                $this->Flash->success(__('該当の在宅記録は削除されました'));
            } else {
                $this->Flash->error(__('削除に失敗しました。もう一度お試しください。'));
            }
            return $this->redirect(['action' => 'indexn']);
        } else {
            $weeklyTable = TableRegistry::get('Weeklies');
            $this->request->allowMethod(['post', 'delete']);
            $weeklies = $weeklyTable->get($id);
            if ($weeklyTable->delete($weeklies)) {
                $this->Flash->success(__('該当の在宅週間記録は削除されました'));
            } else {
                $this->Flash->error(__('削除に失敗しました。もう一度お試しください。'));
            }
            return $this->redirect(['action' => 'indexn']); 
        }
    }
}