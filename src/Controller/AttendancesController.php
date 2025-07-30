<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\I18n\FrozenTime;

use Cake\Log\Log;

class AttendancesController extends AppController
{
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $attendance = $this->Attendances->get($id);
        if ($this->Attendances->delete($attendance)) {
            $this->Flash->success(__('The attendance has been deleted.'));
        } else {
            $this->Flash->error(__('The attendance could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function register()
    {
        // ログイン判定
        $user = $this->Auth->user();
        $usersTable = TableRegistry::get("Users");
        $workPlacesTable = TableRegistry::get("Workplaces");

        if(is_null($user)){
            // ログインしていない場合
            $this->Flash->error('ログインしていません');
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }
        // ログインユーザーの取得
        $user_id = $this->request->getSession()->read('Auth.User.id');

        $user = $usersTable
        ->find()
        ->where(['Users.id' => $user_id])
        ->first();

        $results = $this->Attendances
        ->find()
        ->where(['Attendances.user_id' => $user_id, 'Attendances.date' => date('Y-m-d')])
        ->order(['Attendances.id' => 'DESC'])
        ->EnableHydration(false)
        ->first();

        $data = $this->request->getData();

        if(empty($data["intime"]) || empty($data["outtime"])) {
            $this->Flash->error('出勤もしくは退勤の時間に空欄があります');
            return $this->redirect(['controller'=>'users','action' => 'stampn']);
        } else {
            $intimeEx = explode(":",$data["intime"]);
            $outtimeEx = explode(":",$data["outtime"]);
            $inStamp = mktime($intimeEx[0],$intimeEx[1],0,1,1,1970);
            $outStamp = mktime($outtimeEx[0],$outtimeEx[1],0,1,1,1970);
            if($inStamp >= $outStamp) {
                $this->Flash->error('退勤時間に不正な値が入力されています');
                return $this->redirect(['controller'=>'users','action' => 'stampn']);
            }
        }

        // 15分区切りで登録する処理を追加する場合はmemo.txtを参照する
        if(!empty($results) && $results["date"]->i18nFormat("Y-MM-dd") == date("Y-m-d") && $results["user_id"] == $user_id){
            $attendance = $this->Attendances->get($results["id"]);
            $attendance->date = $results["date"];
        } else {
            $attendance = $this->Attendances->newEntity();
            $attendance->date = date("Y-m-d");
        }
            $attendance->user_id = $user_id;
            $attendance->intime = $data["intime"];
            $attendance->outtime = $data["outtime"];
            $attendance->resttime = $data["resttime"];
            $attendance->afk = 0;
            
            // 利用者のA型/B型情報を記録（0=A型、1=B型）
            if ($user->adminfrag == 0) {
                $attendance->user_type = $user->wrkCase;
            } else {
                $attendance->user_type = null; // 職員の場合はnull
            }
            
        if(!empty($results["medical"])) {
            $attendance->medical = $results["medical"];
        } else {
            $attendance->medical = 0;
        }
        if(!empty($data["support"])) {
            $attendance->support = $data["support"];

            //施設外の時に施設外の場所を備考に入れる
            if ($data["support"] == 0 ) {
                $attendance -> bikou = "";
            } else {
                $workPlacesName = $workPlacesTable
                ->find()
                ->where(['id' => $data["support"]])
                ->first();
        
                $attendance -> bikou = $workPlacesName["name"];
            }
        }
        if(!empty($overtime)) {
            $attendance->overtime = $data["overtime"];
        }

        $attendance -> koukyu = 0;
        $attendance -> paid = 0;
        $attendance -> kekkin = 0;

        if ($user->adminfrag == 0) {
            //送迎記録簿（迎）の更新・新規登録が必要か？
            if( $attendance->ou == 1) {
                // 送迎記録の追加または更新
                $transportsTable = TableRegistry::get('transports');

                // 送迎記録の取得
                $existingTransport = $transportsTable
                    ->find()
                    ->where([
                        'date' => date("Y-m-d"),
                        'user_id' => $user_id,
                        'kind' => 1
                    ])
                    ->first();

                $update = false;
                if (empty($existingTransport)) {
                    $update = true;
                    $transport = $transportsTable->newEntity();
                } else {
                    if ( $existingTransport->hatsutime != $data["intime"]) {
                        $update = true;
                        $transport = $existingTransport;
                    }
                }

                //出社時間が変更された、若しくは送迎記録簿データがない
                if ($update) {
                    $transport->date = date("Y-m-d");
                    $transport->user_id = $user_id;
                    $transport->kind = 1; // 迎え

                    // 出発時間を設定
                    if (empty($data["intime"])) {
                        // ユーザー情報から出発時間を取得
                        if (!empty($user->dintime)) {
                            // 時刻部分だけを抽出（15分前の時間を設定）
                            $timeStr = date('H:i', strtotime($user->dintime . ' -15 minutes'));
                            $transport->hatsutime = $timeStr . ':00';
                        } else {
                            $transport->hatsutime = null;
                        }
                    } else {
                        $timeStr = date('H:i', strtotime($data["intime"] . ' -15 minutes'));
                        $transport->hatsutime = $timeStr . ':00';
                    }

                    // ユーザー情報から送迎場所を取得
                    $transport->hatsuplace = $user->oufuku_place;
                    $transport->tyakuplace = "事業所";

                    // createdとmodifiedを設定
                    if (empty($existingTransport)) {
                        $transport->created = date('Y-m-d H:i:s');
                    }
                    $transport->modified = date('Y-m-d H:i:s');
                    
                    $transportsTable->save($transport);
                }
            } else {
                // 送迎記録の削除
                $transportsTable = TableRegistry::get('transports');
                $existingTransport = $transportsTable
                    ->find()
                    ->where([
                        'date' => date("Y-m-d"),
                        'user_id' => $user_id,
                        'kind' => 1
                    ])
                    ->first();
                
                if (!empty($existingTransport)) {
                    $transportsTable->delete($existingTransport);
                }
            }

            //送迎記録簿（送）の更新・新規登録が必要か？
            if( $attendance->fuku == 1) {
                // 送迎記録の追加または更新
                $transportsTable = TableRegistry::get('transports');

                // 送迎記録の取得
                $existingTransport = $transportsTable
                    ->find()
                    ->where([
                        'date' => date("Y-m-d"),
                        'user_id' => $user_id,
                        'kind' => 2
                    ])
                    ->first();

                $update = false;
                if (empty($existingTransport)) {
                    $update = true;
                    $transport = $transportsTable->newEntity();
                } else {
                    if ( $existingTransport->hatsutime != $data["outtime"]) {
                        $update = true;
                        $transport = $existingTransport;
                    }
                }

                //退社時間が変更された、若しくは送迎記録簿データがない
                if ($update) {
                    $transport->date = date("Y-m-d");
                    $transport->user_id = $user_id;
                    $transport->kind = 2; // 送り

                    // 出発時間を設定
                    if (empty($data["outtime"])) {
                        // ユーザー情報から出発時間を取得
                        if (!empty($user->douttime)) {
                            // 時刻部分だけを抽出
                            $timeStr = date('H:i', strtotime($user->douttime));
                            $transport->hatsutime = $timeStr . ':00';
                        } else {
                            $transport->hatsutime = null;
                        }
                    } else {
                        // 送データ（退勤時）は退勤時間と同じ時刻に設定
                        $timeStr = date('H:i', strtotime($data["outtime"]));
                        $transport->hatsutime = $timeStr . ':00';
                }

                    // ユーザー情報から送迎場所を取得
                    $transport->hatsuplace = "事業所";
                    $transport->tyakuplace = $user->oufuku_place;

                    // createdとmodifiedを設定
                    if (empty($existingTransport)) {
                        $transport->created = date('Y-m-d H:i:s');
                    }
                    $transport->modified = date('Y-m-d H:i:s');
                    
                    $transportsTable->save($transport);
                }
            } else {
                // 送迎記録の削除
                $transportsTable = TableRegistry::get('transports');
                $existingTransport = $transportsTable
                    ->find()
                    ->where([
                        'date' => date("Y-m-d"),
                        'user_id' => $user_id,
                        'kind' => 2
                    ])
                    ->first();
                
                if (!empty($existingTransport)) {
                    $transportsTable->delete($existingTransport);
                }
            }
        }

        if($this->Attendances->save($attendance)){
            $this->Flash->success(__('出勤情報が登録されました'));
            return $this->redirect(['controller' => 'users', 'action' => 'stampn']);
        }
    }

    public function settings() {
        $usersTable = TableRegistry::get("Users");
        $jigyoushasTable = TableRegistry::get("Jigyoushas");
        $workplacesTable = TableRegistry::get("Workplaces");
        $staffs = [];
        $users = [];
        $retires = [];

        $getCompany = $jigyoushasTable
        ->find()
        ->where(['Jigyoushas.id'=>1])
        ->first();
        $getPlaces = $workplacesTable
        ->find('list',['keyField'=>'id','valueField'=>'name'])
        ->toArray();
        $getstaffs = $usersTable
        ->find()
        ->where(['Users.adminfrag' => 1])
        ->order(['Users.narabi'=>'ASC'])
        ->toArray();

        foreach($getstaffs as $getstaff) {
            if(empty($getstaff["retired"])) {
                array_push($staffs,$getstaff);
            } elseif($getstaff["retired"]->i18nFormat("Y-MM-dd") >= date("Y-m-d")) {
                array_push($staffs,$getstaff);
            }
        }
        $stafforder = [null];
        for($i=1; $i<=count($staffs); $i++) {
            array_push($stafforder,$i);
        }

        $getusers = $usersTable
        ->find()
        ->where(['Users.adminfrag' => 0])
        ->order(['Users.narabi'=>'ASC','Users.workplace'=>'ASC','Users.remote'=>'ASC','Users.id'=>'ASC'])
        ->toArray();
        foreach($getusers as $getuser) {
            if(empty($getuser["retired"])) {
                array_push($users,$getuser);
            } elseif($getuser["retired"]->format("Y-m-d") >= date("Y-m-d")) {
                array_push($users,$getuser);
            }
        }
        $userorder = [null];
        for($i=1; $i<=count($users); $i++) {
            array_push($userorder,$i);
        }

        $getretires = $usersTable
        ->find()
        ->order(["Users.narabi"=>"ASC","Users.retired"=>"ASC",'Users.id'=>'ASC'])
        ->toArray();
        foreach($getretires as $getretire) {
            //if(!empty($getretire["retired"]) && $getretire["retired"]->i18nFormat("Y-MM-dd") < date("Y-m-d")) {
            if(!empty($getretire["retired"]) && $getretire["retired"]->format("Y-m-d") < date("Y-m-d")) {
                array_push($retires,$getretire);
            }
        }
        $retireorder = [null];
        for($i=1; $i<=count($retires); $i++) {
            array_push($retireorder,$i);
        }
        $narabi = 999; 
        $half = floor(count($retires) / 2);

        $this->set(compact("staffs","stafforder","users","userorder","retires","retireorder",
                           "getCompany","getPlaces","narabi","half"));
    }

    public function default()
    {
        $usersTable = TableRegistry::get("Users");
        $data = $this->request->getData();
        $ids = array_keys($data["narabi"]);

        if($data["type"] == 0) {
            foreach($ids as $id) {
                if($data["dintime"][$id] == "00:00" || empty($data["dintime"][$id])){
                    $data["dintime"][$id] = null;
                }
                if($data["douttime"][$id] == "00:00" || empty($data["douttime"][$id])){
                    $data["douttime"][$id] = null;
                }
                if($data["dresttime"][$id] == "00:00" || empty($data["dresttime"][$id])){
                    $data["dresttime"][$id] = null;
                }
                $user = $usersTable->get($id);
                $user->narabi = $data["narabi"][$id];
                $user->dintime = $data["dintime"][$id];
                $user->douttime = $data["douttime"][$id];
                $user->dresttime = $data["dresttime"][$id];
                $user->workplace = $data["workplace"][$id];
                $user->remote = $data["remote"][$id];
                $user->display = $data["display"][$id];
                $usersTable->save($user);
            }
        } elseif($data["type"] == 1) {
            foreach($ids as $id) {
                if($data["dintime"][$id] == "00:00" || empty($data["dintime"][$id])){
                    $data["dintime"][$id] = null;
                }
                if($data["douttime"][$id] == "00:00" || empty($data["douttime"][$id])){
                    $data["douttime"][$id] = null;
                }
                if($data["dresttime"][$id] == "00:00" || empty($data["dresttime"][$id])){
                    $data["dresttime"][$id] = null;
                }
                $user = $usersTable->get($id);
                $user->narabi = $data["narabi"][$id];
                $user->dintime = $data["dintime"][$id];
                $user->douttime = $data["douttime"][$id];
                $user->dresttime = $data["dresttime"][$id];
                $user->display = $data["display"][$id];
                $usersTable->save($user);
            }
        } else {
            foreach($ids as $id) {
                $user = $usersTable->get($id);
                $user->narabi = $data["narabi"][$id];
                $user->display = $data["display"][$id];
                $usersTable->save($user);
            }
        }
        if($usersTable->save($user)){
            $this->Flash->success(__('保存されました'));
            return $this->redirect(['action' => 'settings']);
        } else {
            $this->Flash->error('保存の際にエラーが発生しました');
            return $this->redirect(['action' => 'settings']);
        }
    }

    public function register2()
    {
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            $jigyoushasTable = TableRegistry::get("Jigyoushas");

            $getCompany = $jigyoushasTable
            ->find()
            ->where(['Jigyoushas.id'=>1])
            ->first();

            $data = $this->request->getData();    
            if(!empty($getCompany["id"])){
                $jigyoushas = $jigyoushasTable->get($getCompany["id"]);
            } else {
                $jigyoushas = $jigyoushasTable->newEntity();
            }    
            $jigyoushas->jname = $data["jname"];
            $jigyoushas->jnumber = $data["jnumber"];
            $jigyoushas->skubun = $data["skubun"];
            $jigyoushas->teiin = $data["teiin"];
            
            if ($jigyoushasTable->save($jigyoushas)) {
                $this->Flash->success(__('保存されました'));
                return $this->redirect(['action' => 'settings']);
            }
        } else {
            $this->Flash->error('アクセス権限がありません');
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }
    }

    public function attendance()
    {
        $usersTable = TableRegistry::get("Users");

        $year = 2022;
        $member = [];

        $userData = $usersTable
        ->find()
        ->select(['Users.id','Users.name','Users.joined','Users.retired'])
        ->where(['Users.adminfrag'=>0,'Users.workplace'=>1])
        ->EnableHydration(false)
        ->toArray();

        for($i=0;  $i<12; $i++) {
            $tsukinin = 0;

            if($i < 8) {
                $gessho = mktime(0,0,0,4+$i,1,$year);
                $raigetsu = mktime(0,0,0,5+$i,1,$year);
            } elseif($i == 8) {
                $gessho = mktime(0,0,0,4+$i,1,$year);
                $raigetsu = mktime(0,0,0,$i-7,1,$year+1);
            } else {
                $gessho = mktime(0,0,0,$i-8,1,$year+1);
                $raigetsu = mktime(0,0,0,$i-7,1,$year+1);
            }

            foreach($userData as $userDatum) {
                if(!empty($userDatum["joined"]) && strtotime($userDatum["joined"]) < $raigetsu) {
                    $joinFlag = 1;
                } else {
                    $joinFlag = 0;
                }
                if(empty($userDatum["retired"]) || strtotime($userDatum["retired"]) >= $gessho) {
                    $notRetireFlag = 1;
                } else {
                    $notRetireFlag = 0;
                }
                if($joinFlag + $notRetireFlag == 2) {
                    $tsukinin++;
                }
            }
            $member[$i] = $tsukinin;
        }
        pr($member);
        $sum = array_sum($member);
        pr($sum / 12);
        exit;
    }
}
