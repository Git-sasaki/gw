<?php
namespace App\Controller;
use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

use Cake\Log\Log;

class UsersController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow(['logout', 'add', 'checkUsernameDuplicate']);
    }

    public function indexn()
    {
        if($this->request->getSession()->read('Auth.User.adminfrag') == 1) {
            $display = $this->request->getSession()->read(['displaykubun']);
            if(empty($display)) {
                $display = 0;
            }
            $this->paginate = [
                "limit" => 8,
                "order" => ['display'=>'DESC','id'=>'ASC']
            ];
            if($display == 0) {
                $users = $this->paginate($this->Users
                                ->find()
                                ->where(['Users.adminfrag' => 1,'Users.narabi' => 1]))
                                ->toArray();
            } elseif($display == 1) {
                $users = $this->paginate($this->Users
                                ->find()
                                ->where(['Users.adminfrag' => 0,'Users.narabi' => 10]))
                                ->toArray();
            } else {
                $users = $this->paginate($this->Users
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

    public function getquery0() 
    {
        $kubun = $this->request->getData('kubun');
        $this->request->getSession()->write(['displaykubun' => $kubun]);
        return $this->redirect(['action' => 'indexn']);
    }

    public function detailn()
    {
        $id = $this->request->getData('id');
        $user = $this->Users->get($id, [
            'contain' => [],
        ]);
        $this->set('user', $user);
    }

    public function newn()
    {
        $attendanceTable = TableRegistry::get('Attendances');
        $timestamp = mktime(0,0,0,date('m'),1,date('Y'));

        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            
            // ユーザー名重複チェック
            if (!empty($data["user"])) {
                $existingUser = $this->Users->find()
                    ->where(['Users.user' => $data["user"]])
                    ->first();
                
                if (!empty($existingUser)) {
                    $this->Flash->error(__('このユーザー名は既に使用されています。別のユーザー名を入力してください。'));
                    return $this->redirect(['action' => 'indexn']);
                }
            }
            
            $adminfrag = $data["adminfrag"];
            $name = $data["lastname"]."　".$data["firstname"];
            if(empty($data["jyear"]) || empty($data["jmonth"]) || empty($data["jdate"])) {
                $this->Flash->error(__('入社日に空欄があります'));
                return $this->redirect(['action' => 'indexn']);
            } else {
                $jstamp = mktime(0,0,0,$data["jmonth"],$data["jdate"],$data["jyear"]);
                $joined = date('Y-m-d',$jstamp);
            }
            if(!empty($data["sjhyear"]) && !empty($data["sjhmonth"]) && !empty($data["sjhdate"])) {
                $sjh = mktime(0,0,0,$data["sjhmonth"],$data["sjhdate"],$data["sjhyear"]);
                $sjhajime = date('Y-m-d',$sjh);
            }
            if(!empty($data["sjoyear"]) && !empty($data["sjomonth"]) && !empty($data["sjodate"])) {
                $sjo = mktime(0,0,0,$data["sjomonth"],$data["sjodate"],$data["sjoyear"]);
                $sjowari = date('Y-m-d',$sjo);
            }
            $user = $this->Users->patchEntity($user, $this->request->getData());
            $user->adminfrag = $adminfrag;
            $user->joined = $joined;
            $user->sjnumber = $data["sjnumber"];
            $user->name = $name;
            $user->lastname = $data["lastname"];
            $user->sapporo = $data["sapporo"];
            $user->wrkCase = ($adminfrag == 0) ? $data["wrkCase"] : null;
            $user->mail = ($adminfrag == 0) ? null : $data["mailaddress"];
            $user->kessai = ($adminfrag == 0) ? null : (array_key_exists('kessai', $data) ? $data["kessai"] : null);
            $user->oufuku_place =  ($adminfrag == 0) ? $data["oufuku_place"] : null;

            if($adminfrag == 1) {
                $user->narabi = 1;
            } else {
                $user->narabi = 10;
            }
            if(!empty($sjhajime) && !empty($sjowari)) {
                $user->sjhajime = $sjhajime;
                $user->sjowari = $sjowari;
            } elseif(!empty($sjhajime) && empty($sjowari)) {
                $this->Flash->error(__('受給者証期限のどちらかに空欄があります'));
                return $this->redirect(['action' => 'indexn']);
            } elseif(empty($sjhajime) && !empty($sjowari)) {
                $this->Flash->error(__('受給者証期限のどちらかに空欄があります'));
                return $this->redirect(['action' => 'indexn']);
            }
            if ($this->Users->save($user)) {
                $newdata = $this->Users
                ->find()
                ->where(['Users.name'=>$name])
                ->first();
                for($i=1; $i<=date('t',$timestamp); $i++) {
                    $timestamp = mktime(0,0,0,date('m'),$i,date('Y'));
                    $attResult = $attendanceTable
                    ->find()
                    ->select(['Attendances.id','Attendances.user_id','Attendances.date'])
                    ->where(['Attendances.user_id' => $newdata["id"],'Attendances.date' => date("Y-m-d",$timestamp)])
                    ->first();
                    $attendance = $attendanceTable->newEntity();
                    $attendance->user_id = $newdata["id"];
                    $attendance->intime = null;
                    $attendance->outtime = null;
                    $attendance->resttime = null;
                    $attendance->overtime = null;
                    $attendance->date = date("Y-m-d",$timestamp);
                    if(empty($newdata["adminfrag"]) || $newdata["adminfrag"] == 0) {
                        $attendance->ou = 0;
                        $attendance->fuku = 0;
                        $attendance->meshi = 0;
                        $attendance->medical = 0;
                        $attendance->support = 0;
                    }
                    $attendance->koukyu = 0;
                    $attendance->paid = 0;
                    $attendance->kekkin = 0;
                    $attendance->bikou = null;
                    $attendance->remote = 0;
                    $attendanceTable->save($attendance);                                
                }
                if($attendanceTable->save($attendance)){
                    $this->Flash->success(__('ユーザーが追加されました'));
                    return $this->redirect(['action' => 'indexn']);
                } else {
                    $this->Flash->error('出勤データの入力でエラーが発生しました');     
                    return $this->redirect(['action' => 'indexn']);               
                }
            } else {
                $this->Flash->error('ユーザーの登録に失敗しました');     
                return $this->redirect(['action' => 'indexn']);  
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        for($i=2019;$i<=date('Y')+3;$i++) {
            if($i == 2019) {
                $years["$i"] = "令和 元年";
            } else {
                $reiwaYear = $i - 2018;
                $years["$i"] = "令和 ".$reiwaYear."年";
            }
        }
        for($i=1;$i<=12;$i++) {
            $months[sprintf('%02d',$i)] = $i;
        }
        $this->set(compact('user','years','months'));
    }

    public function editn()
    {
        // 通常の編集処理（POST）とエラー時の編集処理（GET）の両方に対応
        $id = $this->request->getData('id') ?: $this->request->getQuery('id');
        $attendanceTable = TableRegistry::get('Attendances');
        $timestamp = mktime(0,0,0,date('m'),1,date('Y'));

        $user = $this->Users->find()->where(['Users.id'=>$id])->first();
        if (empty($user)) {
            throw new \Exception('ユーザーが見つかりません');
        }
        $firstname = explode("　",$user["name"]);
        if(!empty($user["joined"])) {
            $joined = explode("/",$user["joined"]->i18nFormat('MM/dd/yyyy'));
        } else {
            $joined[0] = date('m');
            $joined[1] = date('d');
            $joined[2] = date('Y');
        }
        if(!empty($user["sjhajime"])) {
            $sjhajime = explode("/",$user["sjhajime"]);
        } else {
            $sjhajime = null;
        }
        if(!empty($user["sjowari"])) {
            $sjowari = explode("/",$user["sjowari"]);
        } else {
            $sjowari = null;
        }
        if(!empty($user["retired"])) {
            $retired = explode("/",$user["retired"]);
        } else {
            $retired[0] = date('n');
            $retired[1] = date('d');
            $retired[2] = date('y');
        }

        // 和暦で年を登録する
        for($i=2019;$i<=date('Y')+3;$i++) {
            if($i == 2019) {
                $years["$i"] = "令和 元年";
            } else {
                $reiwaYear = $i - 2018;
                $years["$i"] = "令和 ".$reiwaYear."年";
            }
        }
        for($i=1;$i<=12;$i++) {
            $months[sprintf('%02d',$i)] = $i;
        }
        $this->set(compact('user','years','months','firstname','sjhajime','sjowari','joined','retired'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('該当のユーザーは削除されました'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'indexn']);
    }

    public function login()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();
            if ($user) {
                // 退職者チェック
                if (!empty($user['retired']) && strtotime($user['retired']) <= time()) {
                    $this->Flash->error('退職者はログインできません。');
                    return;
                }
                
                $attendanceTable = TableRegistry::get('Attendances');
                $zumi = $attendanceTable
                ->find('list',["valueField"=>"intime"])
                ->where(['Attendances.user_id'=>$user["id"],'Attendances.date'=>date('Y-m-d')])
                ->first();
                $this->Auth->setUser($user);

                // 利用者の受給者証切れ判定
                if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
                    $usersTable = TableRegistry::get('Users');
                    $owaris = $this->Users
                    ->find()
                    ->where(['Users.adminfrag'=>0])
                    ->select(['Users.id','Users.name','Users.sjowari','Users.retired'])
                    ->toArray();
                    $owariman = [];
                    foreach($owaris as $owari) {
                        if((!empty($owari["retired"]) && strtotime($owari["retired"]->i18nFormat('yyyy-MM-dd')) > time())
                            || empty($owari["retired"])) {
                            if(!empty($owari["sjowari"])) {
                                $diff = (strtotime($owari["sjowari"]->i18nFormat('yyyy-MM-dd')) - time()) / 86400;
                                if($diff <= 31) {
                                    array_push($owariman,$owari["name"]);
                                }
                            }
                        }
                    }
                    if(!empty($owariman)) {
                        $this->request->getSession()->write(['owarimen' => $owariman]);
                        $this->request->getSession()->write('hajimete',true);
                        $this->request->getSession()->write('owarifrag',true);
                    } else {
                        $this->request->getSession()->write('hajimete',true);
                        $this->request->getSession()->write('owarifrag',false);
                    }
                // 日報記入漏れ
                } else {
                    $reportsTable = TableRegistry::get('Reports');
                    $wasuremon = [];
                    for($i=0; $i<30; $i++){
                        $timestamp = time() - 86400 * (30 - $i);
                        $attendance = $attendanceTable
                        ->find('list',['valueField'=>'intime'])
                        ->where(['Attendances.user_id'=>$user["id"],'Attendances.date'=>date('Y-m-d',$timestamp)])
                        ->first();
                        if(!empty($attendance)) {
                            $rep = $reportsTable
                            ->find()
                            ->where(['Reports.user_id'=>$user["id"],'Reports.date'=>date('Y-m-d',$timestamp)])
                            ->first();
                            if(empty($rep) || empty($rep["content"])) {
                                array_push($wasuremon,date('Y年m月d日',$timestamp));
                            }
                        }
                    }
                    if(!empty($wasuremon)) {
                        $this->request->getSession()->write(['wasuremon' => $wasuremon]);
                        $this->request->getSession()->write('hajimete',true);
                        $this->request->getSession()->write('owasurefrag',true);
                    } else {
                        $this->request->getSession()->write('hajimete',true);
                        $this->request->getSession()->write('owasurefrag',false);
                    }
                }
                $this->Flash->success('ログインできました。');

                if($this->request->getSession()->read('Auth.User.adminfrag') == 1) {
                    if(empty($zumi)) {
                        return $this->redirect($this->Auth->redirectUrl('/users/stampn'));
                    } else {
                        return $this->redirect($this->Auth->redirectUrl('/calendars/indexn'));
                    }
                } else {
                    return $this->redirect($this->Auth->redirectUrl('/users/stampn'));
                }      
            }
           $this->Flash->error('ユーザー名またはパスワードが不正です。');
        }
    }

    public function logout()
    {
        $this->request->session()->destroy();
        $this->Flash->success('ログアウトしました。');
        $this->redirect($this->Auth->redirectUrl('/users/login'));
    }

    public function stampn()
    {
        $user = $this->Auth->user();
        if(is_null($user)){
            $this->Flash->error('ログインしていません');
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        } else {
            // 基本的なデータを取得
            $usersTable = TableRegistry::get('Users');
            $kangoTable = TableRegistry::get('Kangos');
            $reportsTable = TableRegistry::get('Reports');
            $calendarTable = TableRegistry::get('Calendars');
            $workPlacesTable = TableRegistry::get("Workplaces");
            $attendanceTable = TableRegistry::get('Attendances');
            $weekList = ["日","月","火","水","木","金","土"];
            $mainKana = $workPlacesTable->find()->where(['Workplaces.sub'=>0])->first();
            $workName = $workPlacesTable->find('list', ['keyField'=>'id','valueField'=>'name'])->select( ['id','name'])->where(['Workplaces.sub'=>1, 'Workplaces.del'=>0])->enableHydration(false)->all()->toArray();
            $results = $attendanceTable
            ->find()
            ->select(['Attendances.intime','Attendances.outtime','Attendances.resttime','Attendances.support'])
            ->where(['Attendances.user_id' => $user["id"], 'Attendances.date' => date('Y-m-d')])
            ->EnableHydration(false)
            ->first();

            // メッセージのフラグを立てるかどうかを決める
            $msg1 = 0; $msg2 = 0;
            for($i=1; $i<=date('t'); $i++) {
                $timestamp = mktime(0,0,0,date('n'),$i,date('Y'));
                $attendance = $attendanceTable
                ->find()
                ->where(['Attendances.user_id'=>$user["id"],'Attendances.date'=>date('Y-m-d',$timestamp)])
                ->first();
                if(empty($attendance)) {
                    $msg1 = 1;
                    break;
                }
            }
            if(date('j') >= 20) {
                $raigetsu = time() + 86400 * 12;
                $datacheck = $attendanceTable
                ->find()
                ->where(['Attendances.user_id' => $user["id"],
                         'Attendances.date >=' => date('Y-m',$raigetsu).'-01',
                         'Attendances.date <=' => date('Y-m',$raigetsu)."-".date("t",$raigetsu)])
                ->toArray();
                if(empty($datacheck)) {
                    $msg2 = 1;
                }
            } else {
                $raigetsu = NULL;
            }
            $wasuremons = [];
            if($this->request->getSession()->read('Auth.User.adminfrag') == 0) {
                $x = 0;
                for($i=0; $i<30; $i++){
                    $timestamp = time() - 86400 * (30 - $i);
                    $attendance = $attendanceTable
                    ->find('list',['valueField'=>'intime'])
                    ->where(['Attendances.user_id'=>$user["id"],'Attendances.date'=>date('Y-m-d',$timestamp)])
                    ->first();
                    if(!empty($attendance)) {
                        $rep = $reportsTable
                        ->find()
                        ->where(['Reports.user_id'=>$user["id"],'Reports.date'=>date('Y-m-d',$timestamp)])
                        ->first();
                        if(empty($rep) || empty($rep["content"])) {
                            $wasuremons[$x] = $timestamp;
                            $x++;
                        }
                    }
                }
            }

            //======================================//
            //         訪問看護チェック(機能のみ) 　   //
            //======================================//
            // $nurse = NULL;
            // $getkango = $calendarTable
            // ->find()
            // ->select(['Calendars.kango','Calendars.nurse'])
            // ->where(['Calendars.date'=>date('Y-m-d')])
            // ->EnableHydration(false)
            // ->first();
            // if(!empty($getkango["nurse"])) {
            //     $nurse = $kangoTable->find('list',["valueField"=>"name"])->where(["Kangos.id"=>$getkango["nurse"]])->first();
            // } else {
            //     $nurse = null;
            // }
            // $this->set(compact('nurse','getkango'));

            if(!empty($msg1) || !empty($msg2) || !empty($wasuremons) || !empty($nurse)) {
                $frag = 1;
            } else {
                $frag = 0;
            }

            //施設外名に空白を追加する
            $workName = array_reverse($workName, true);
            $workName['0'] = '';
            $workName = array_reverse($workName, true);

            $this->set(compact('user','results','weekList','raigetsu','mainKana','wasuremons','frag','msg1','msg2','workName'));
        }
    }

    public function ichiran()
    {
        $auser = $this->Auth->user("adminfrag");
        $myid = $this->Auth->user("id");
        if(is_null($auser)) {
            $this->Flash->error('ログインしていません');
           return $this->redirect(['controller' => 'users', 'action' => 'login']);
         } else {
            // 基本的なデータを取得
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
                    if(!empty($getStamp["afk"]) && $getStamp["afk"] == 1 && !empty($getStamp["outtime"]) &&
                       $getStamp["outtime"] != "" && $getStamp["outtime"]->i18nFormat("HH:mm") >= date("H:i")) {
                        $staffs[$x]["status"] = 7; 
                    } elseif(!empty($getStamp["afk"]) && $getStamp["afk"] == 1 && !empty($getStamp["outtime"]) &&
                       $getStamp["outtime"] != "" && $getStamp["outtime"]->i18nFormat("HH:mm") < date("H:i")) {
                        $staffs[$x]["status"] = 2; 
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

                //施設外就労場所を検索
                if ( empty($getStamp["support"]) or $getStamp["support"] == 0) {
                    $staffs[$x]["shisetsugai"] = "";
                } else {
                    $shisetsu = $workplacesTable
                    ->find()
                    ->where(['id' => $getStamp["support"]])
                    ->first();
                    //->toArray();
                    $staffs[$x]["shisetsugai"] = $shisetsu['name'];
                }

                if(!empty($allstaff["retired"]) && $allstaff["retired"]->i18nFormat('yyyy-mm-dd') <= date('Y-m-d')) {
                    continue;
                } else {
                    $staffs[$x]["user_id"] = $allstaff["id"];
                    $staffs[$x]["name"] = $allstaff["name"];
                    $x++;
                }
            }

            $getRep = '';
            $workPlaces = $workplacesTable->find()->EnableHydration(false)->toArray();
            for($i = 0; $i < count($workPlaces); $i++) {
                //削除された場所は飛ばす
                if ($workPlaces[$i]["del"]) continue;

                $zanUsers = $usersTable
                ->find()
                ->where(['Users.adminfrag' => 0])
                ->toArray();

                //施設外で働いた利用者さん抽出
                if ( $i == 0) {
                    $selwrkPlc = 0;
                } else {
                    $selwrkPlc = $workPlaces[$i]['id'];
                }
                //$workUsers = $attendanceTable
                //->find()
                //->where(['Attendances.date'=>date('Y-m-d'),'Attendances.support'=>$selwrkPlc])
                //->toArray();

				// 勤務場所に誰も在籍してない、スタッフは除く
                $workUsers = $attendanceTable->find()
                ->join( [
                    'table' => 'users',
                    'alias' => 'us',
                    'type' => 'Inner',
                    'conditions' => 'us.id = Attendances.user_id'])
                ->where(['Attendances.date'=>date('Y-m-d'),'Attendances.support'=>$selwrkPlc,'us.adminfrag' => 0])
                ->toArray();
                if (count($workUsers) == 0) continue;

                //在職中利用者を抽出
                $j = 0;
                foreach($zanUsers as $zanUser) {
                    if(empty($zanUser["retired"]) || 
                    strtotime($zanUser["retired"]->i18nFormat("yyyy-MM-dd")) > time()) {
                        foreach($workUsers as $workUser) {
                            if ( $zanUser["id"] == $workUser["user_id"]) {
                                $users[$i][$j] = $zanUser;
                                $j++;

                            }
                        }
                    }
                }


                for($j = 0; $j < count($users[$i]); $j++) {
                    $getStamp = $attendanceTable
                    ->find()
                    ->where(['Attendances.user_id'=>$users[$i][$j]["id"],'Attendances.date'=>date('Y-m-d'),'Attendances.support'=>$selwrkPlc])
                    ->EnableHydration(false)
                    ->first();
                    if ( empty($getStamp)) continue;

                    $getRep = $reportTable
                    ->find()
                    ->select(['Reports.id','Reports.recorder'])
                    ->where(['Reports.user_id'=>$users[$i][$j]["id"],'Reports.date'=>date('Y-m-d')])
                    ->first();

                    // 閲覧者で区分
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
                    if(!empty($getStamp["user_staffid"])) {
                        $users[$i][$j]["staffcheck"] = 2;
                    } elseif(empty($getStamp["user_staffid"]) && $users[$i][$j]["status"] != 0 && 
                      ($users[$i][$j]["status"] <= 4 || $users[$i][$j]["status"] >= 7)) {
                        $users[$i][$j]["staffcheck"] = 1;
                    } else {
                        $users[$i][$j]["staffcheck"] = 0;
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

    public function register()
    {
        if($this->request->getSession()->read('Auth.User.adminfrag') == 1) {      
            $type = $this->request->getQuery("type");
            $attendanceTable = TableRegistry::get('Attendances');

            if($type == 0) {
                $staff_id = $this->Auth->user("id");
                $user_id = $this->request->getQuery("id");

                // チェック
                $userchk = $this->Users->find()->where(['Users.id'=>$user_id])->first();
                $attchk = $attendanceTable
                ->find()
                ->where(['Attendances.date'=>date('Y-m-d'),'Attendances.user_id'=>$user_id])
                ->EnableHydration(false)
                ->first();

                if(empty($attchk['id'])) {
                    $this->Flash->error('出勤情報エラー');
                    return $this->redirect(['action' => 'ichiran']);
                } else {
                    $attendances = $attendanceTable->get($attchk['id']);
                    $attendances->user_staffid = $staff_id;
                    if($attchk["koukyu"] == 0 && $attchk["paid"] == 0 && $attchk["kekkin"] == 0) {
                        if($userchk["narabi"] == 70 && $userchk["sapporo"] == 1) {
                            $attendances->support = 1;
                        } elseif($userchk["narabi"] == 71 && $userchk["sapporo"] == 1 && $attchk["remote"] == 0) {
                            $attendances->support = 1;
                        }
                    }

                    if($attendanceTable->save($attendances)){
                        $this->Flash->success(__('担当者入力が登録されました'));
                        return $this->redirect(['action'=>'ichiran']);
                    }
                }
            } elseif($type == 1) {
                $user_id = $this->request->getQuery("id");

                // チェック
                $attchk = $attendanceTable
                ->find()
                ->where(['Attendances.date'=>date('Y-m-d'),'Attendances.user_id'=>$user_id])
                ->EnableHydration(false)
                ->first();

                if(empty($attchk['id'])) {
                    $this->Flash->error('出勤情報エラー');
                    return $this->redirect(['action' => 'ichiran']);
                } else {
                    $attendances = $attendanceTable->get($attchk['id']);
                    if($attchk["afk"] == 0 || empty($attchk["afk"])) {
                        $attendances->afk = 1;
                        if($attendanceTable->save($attendances)){
                            $this->Flash->success(__('離席中になりました'));
                            return $this->redirect(['action'=>'ichiran']);
                        } else {
                            $this->Flash->error(__('離席の処理にエラーが発生しました'));
                            return $this->redirect(['action'=>'ichiran']); 
                        }
                    } else {
                        $attendances->afk = 0;
                        if($attendanceTable->save($attendances)){
                            $this->Flash->success(__('離席中を解除しました'));
                            return $this->redirect(['action'=>'ichiran']);
                        } else {
                            $this->Flash->error(__('離席の処理にエラーが発生しました'));
                            return $this->redirect(['action'=>'ichiran']); 
                        }
                    }
                }
            //ユーザー情報編集登録処理
            } elseif($type == 2) {
                $data = $this->request->getData();
                $user_id = $this->request->getQuery('id');
                
                // CakePHPのトランザクション処理
                // エラーレベルを一時的に変更してUndefined indexを例外として扱う
                $old_error_reporting = error_reporting(E_ALL);
                set_error_handler(function($severity, $message, $file, $line) {
                    throw new \ErrorException($message, 0, $severity, $file, $line);
                });
                
                try {
                    $this->Users->getConnection()->transactional(function ($connection) use ($data, $user_id) {
                        $name = $data["lastname"]."　".$data["firstname"];
                        $user = $this->Users->get($user_id);
                        
                        $user->user = $data["user"];
                        $user->name = $name;
                        $user->adminfrag = $data["adminfrag"];
                        
                        $user->lastname = $data["lastname"];
                        $user->sapporo = ($data["adminfrag"] == 0) ? (array_key_exists('sapporo', $data) ? $data["sapporo"] : null) : null;
                        $user->wrkCase = ($data["adminfrag"] == 0) ? $data["wrkCase"] : null;
                        $user->mail = ($data["adminfrag"] == 0) ? null : $data["mailaddress"];
                        $user->kessai = ($data["adminfrag"] == 0) ? null : (array_key_exists('kessai', $data) ? $data["kessai"] : null);
                        
                        // 送迎場所が変更されたかチェック
                        $old_oufuku_place = $user->oufuku_place;
                        $user->oufuku_place = $data["oufuku_place"];
                        
                        if(empty($data["narabi"]) && empty($data["retired"])) {
                            if($data["adminfrag"] == 1) {
                                $user->narabi = 1;
                            } else {
                                $user->narabi = 10;
                            }
                        } elseif(strtotime($data["retired"]) < time()) {
                            $user->narabi = 999;
                        }
                        
                        if(empty($data["jyear"]) || empty($data["jmonth"]) || empty($data["jdate"])) {
                            throw new \Exception('入社日に空欄があります');
                        } else {
                            $jstamp = mktime(0,0,0,$data["jmonth"],$data["jdate"],$data["jyear"]);
                            $joined = date('Y-m-d',$jstamp);
                            $user->joined = $joined;
                        }
                        
                        if(!empty($data["sjnumber"])) {
                            $user->sjnumber = $data["sjnumber"];
                            if(!empty($data["sjhyear"]) && !empty($data["sjhmonth"]) && !empty($data["sjhdate"])) {
                                $sjh = mktime(0,0,0,$data["sjhmonth"],$data["sjhdate"],$data["sjhyear"]);
                                $sjhajime = date('Y-m-d',$sjh);
                            }
                            if(!empty($data["sjoyear"]) && !empty($data["sjomonth"]) && !empty($data["sjodate"])) {
                                $sjo = mktime(0,0,0,$data["sjomonth"],$data["sjodate"],$data["sjoyear"]);
                                $sjowari = date('Y-m-d',$sjo);
                            }
                            if(!empty($sjhajime) && !empty($sjowari)) {
                                $user->sjhajime = $sjhajime;
                                $user->sjowari = $sjowari;
                            } elseif(!empty($sjhajime) && empty($sjowari)) {
                                throw new \Exception('受給者証期限のどちらかに空欄があります');
                            } elseif(empty($sjhajime) && !empty($sjowari)) {
                                throw new \Exception('受給者証期限のどちらかに空欄があります');
                            }
                        }
            
                        if(!$this->Users->save($user)) {
                            throw new \Exception('ユーザーの保存に失敗しました');
                        }
                        
                        // 送迎場所が変更された場合、transportsテーブルを更新
                        if ($old_oufuku_place != $data["oufuku_place"]) {
                            $this->updateTransportsForUser($user_id, $data["oufuku_place"]);
                        }
                        
                        return true;
                    });
                    
                    // エラーハンドラーを元に戻す
                    restore_error_handler();
                    error_reporting($old_error_reporting);
                    
                    // トランザクションが成功した場合
                    $this->Flash->success(__('ユーザーが更新されました'));
                    return $this->redirect(['action' => 'indexn']);
                    
                } catch (\Exception $e) {
                    // エラーハンドラーを元に戻す
                    restore_error_handler();
                    error_reporting($old_error_reporting);
                    
                    // トランザクションが失敗した場合（自動的にロールバックされる）
                    $this->Flash->error(__($e->getMessage()));
                    return $this->redirect(['action' => 'editn', 'id' => $user_id]);
                }
            //退職処理
            } elseif($type == 3) {
                $id = $this->request->getQuery("id");
                $user = $this->Users->get($id);
                if ( empty($this->request->getData("year"))) {
                    $user->retired = null;
                    $user->narabi = ($user->adminfrag == 0) ? 10 : 1;
                } else {
                    $year = $this->request->getData("year");
                    $month = $this->request->getData("month");
                    $day = $this->request->getData("day");
                    $user->retired = $year."-".$month."-".$day;
                    $user->narabi = 999;
                }
                $this->Users->save($user);
                if ($this->Users->save($user)) {
                    $this->Flash->success(__('退職日を設定しました'));
                    return $this->redirect(['action' => 'indexn']);
                }
            }
        } else {
            $this->Flash->error('アクセス権限がありません');
            return $this->redirect(['action' => 'login']);
        }
    }

    /**
     * ユーザー名重複チェック用のAJAXアクション
     */
    public function checkUsernameDuplicate()
    {
        $this->autoRender = false;
        $this->response->type('json');
        if ($this->request->is('post') && $this->request->is('ajax')) {
            $username = $this->request->getData('username');
            $id = $this->request->getData('id');
            if (!empty($username)) {
                $query = $this->Users->find()->where(['Users.user' => $username]);
                if (!empty($id)) {
                    $query->where(['Users.id !=' => $id]);
                }
                $existingUser = $query->first();
                $response = [
                    'duplicate' => !empty($existingUser),
                    'username' => $username
                ];
            } else {
                $response = [
                    'duplicate' => false,
                    'username' => ''
                ];
            }
            echo json_encode($response);
        }
    }

    /**
     * 送迎場所が変更された場合、該当ユーザーの未完了送迎記録を更新
     */
    private function updateTransportsForUser($user_id, $new_place)
    {
        $transportsTable = $this->getTableLocator()->get('Transports');
                
        // 当日以降でまだ送迎が終わっていないレコードを取得
        $transports = $transportsTable->find()
            ->where([
                'user_id' => $user_id,
                'date >=' => date('Y-m-d'),
                'taykutime IS' => null  // 到着時間が未設定（送迎未完了）
            ])
            ->toArray();
        
        // 各送迎記録の場所を更新（kindに応じて適切なフィールドを更新）
        foreach ($transports as $transport) {
            if ($transport->kind == 1) {
                // kind=1（迎え）の場合：hatsuplace（出発場所）を更新
                $transport->hatsuplace = $new_place;
            } elseif ($transport->kind == 2) {
                // kind=2（送り）の場合：tyakuplace（到着場所）を更新
                $transport->tyakuplace = $new_place;
            }
            $transport->modified = date('Y-m-d H:i:s');
            $transportsTable->save($transport);
        }
    }
}