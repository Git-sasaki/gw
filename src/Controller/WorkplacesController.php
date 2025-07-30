<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

use Cake\Log\Log;


class WorkPlacesController extends AppController
{
    public function indexn()
    {
        // ログイン判定
        $user = $this->Auth->user();
        if(is_null($user)){
            // ログインしていない場合
            $this->Flash->error('ログインしていません');
            return $this->redirect(['action' => 'editn']);
        }
        $usersTable = TableRegistry::get('Users');
        $jigyoushasTable = TableRegistry::get('Jigyoushas');

        // 事業者情報の取得
        $getCompany = $jigyoushasTable
        ->find()
        ->where(['Jigyoushas.id'=>1])
        ->first();

        $staffs = $usersTable
        ->find('list',["valueField"=>"name"])
        ->where(['Users.adminfrag' => 1,'Users.display' => 0])
        ->toArray();

        // 施設一覧の取得
        $getPlaces = $this->Workplaces->find()->toArray();

        //送迎車種データの取得
        $sougeicarTable = TableRegistry::get('sougeicar');
        $getsougeicar = $sougeicarTable
        ->find()
        ->enableHydration(false)
        ->toArray();

        //年 select box
        for($i=2019;$i<=date('Y')+3;$i++) {
            if($i == 2019) {
                $years["$i"] = "令和 元年";
            } else {
                $reiwaYear = $i - 2018;
                $years["$i"] = "令和 ".$reiwaYear."年";
            }
        }


        //月 select box
        for($i=1;$i<=12;$i++) {
            $months[sprintf('%02d',$i)] = $i;
        }

        $this->set(compact("getCompany","getPlaces","staffs","getsougeicar","years","months"));
    }
    
    public function register()
    {
        $data = $this->request->getData();
        $jigyoushasTable = TableRegistry::get("Jigyoushas");

        // $data["type"] == 0は事業所一覧からのデータの編集
        if($data["type"] == 0) {
            for($i=1; $i<=count($data["id"]); $i++) {
                $stdate =  ((!empty($data["styear"][$i])) && (!empty($data["stmonth"][$i])) && (!empty($data["stdate"][$i]))) 
                                ? ($data["styear"][$i] . '-' . $data["stmonth"][$i] . '-' . $data["stdate"][$i]) : null;
                $eddate =  ((!empty($data["edyear"][$i])) && (!empty($data["edmonth"][$i])) && (!empty($data["eddate"][$i]))) 
                                ? ($data["edyear"][$i] . '-' . $data["edmonth"][$i] . '-' . $data["eddate"][$i]) : null;
                $workplace = $this->Workplaces->get($data["id"][$i]);
                $workplace->name = $data["name"][$i];
                $workplace->address = $data["address"][$i];
                if ( $i != 1) {
                    $workplace->company = $data["company"][$i];
                    $workplace->mokuhyo = $data["mokuhyo"][$i];
                    $workplace->wrkcontentsu = $data["wrkcontentsu"][$i];
                    $workplace->sub = $data["sub"][$i];
                    $workplace->del = $data["del"][$i];
                    $workplace->stdate = $stdate;
                    $workplace->eddate = $eddate;
                }
                if(!$this->Workplaces->save($workplace)) {
                    $this->Flash->error(__('保存にエラーが発生しました'));
                    return $this->redirect(['action' => 'indexn']);
                }
            }
            $this->Flash->success(__('保存されました'));
            return $this->redirect(['action' => 'indexn']);
 
        // $data["type"] == 1は新規事業所登録
        } elseif($data["type"] == 1) {
            $stdate =  ((!empty($data["styear"])) && (!empty($data["stmonth"])) && (!empty($data["stdate"]))) 
                                        ? ($data["styear"] . '-' . $data["stmonth"] . '-' . $data["stdate"]) : null;
            $eddate =  ((!empty($data["edyear"])) && (!empty($data["edmonth"])) && (!empty($data["eddate"]))) 
                                        ? ($data["edyear"] . '-' . $data["edmonth"] . '-' . $data["eddate"]) : null;
            $workplace = $this->Workplaces->newEntity();
            $workplace->name = $data["name"];
            $workplace->address = $data["address"];
            $workplace->company = $data["company"];
            $workplace->mokuhyo = $data["mokuhyo"];
            $workplace->wrkcontentsu = $data["wrkcontentsu"];
            $workplace->sub = $data["sub"];
            $workplace->stdate = $stdate;
            $workplace->eddate = $eddate;
            if($this->Workplaces->save($workplace)) {
                $this->Flash->success(__('保存されました'));
                return $this->redirect(['action' => 'indexn']);
            } else {
                $this->Flash->error(__('保存できませんでした'));
                return $this->redirect(['action' => 'indexn']);
            }
            
        // $data["type"] == 2は事業者情報の登録
        } elseif($data["type"] == 2) {
            // 事業者情報の取得
            $getCompany = $jigyoushasTable
            ->find()
            ->where(['Jigyoushas.id'=>1])
            ->first();

            if(!empty($getCompany["id"])){
                $jigyoushas = $jigyoushasTable->get($getCompany["id"]);
            } else {
                $jigyoushas = $jigyoushasTable->newEntity();
            }    
            $jigyoushas->jname = $data["jname"];
            $jigyoushas->jnumber = $data["jnumber"];
            $jigyoushas->skubun = $data["skubun"];
            $jigyoushas->teiin = $data["teiin"];
            $jigyoushas->jinkubun = $data["jinkubun"];
            if ($jigyoushasTable->save($jigyoushas)) {
                $this->Flash->success(__('保存されました'));
                return $this->redirect(['action' => 'indexn']);
            } else {
                $this->Flash->error(__('保存できませんでした'));
                return $this->redirect(['action' => 'indexn']);
            }
        }
    }

    //送迎車種データ新規登録・更新
    public function register2()
    {
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $sougeicarTable = TableRegistry::get('sougeicar');
            
            // 送迎車種データの更新
            foreach ($data['getsougeicar'] as $index => $sougeicarData) {
                // 更新データの作成
                $sougeicar = $sougeicarTable->get($sougeicarData['id']);
                $sougeicar->no = $sougeicarData['no'];
                $sougeicar->name = $sougeicarData['name'];
                
                // 削除フラグの設定（チェックボックスがチェックされていない場合は0）
                $sougeicar->del = isset($sougeicarData['del']) ? 1 : 0;
                
                // データの保存
                if (!$sougeicarTable->save($sougeicar)) {
                    $this->Flash->error(__('送迎車種データの更新に失敗しました'));
                    return $this->redirect(['action' => 'indexn', '#' => 'sougeicar-section']);
                }
            }
            
            $this->Flash->success(__('送迎車種データが更新されました'));
        }
        
        return $this->redirect(['action' => 'indexn', '#' => 'sougeicar-section']);
    }

    public function delete($id = null)
    {
        $workplace = $this->Workplaces->get($id);
        if($this->Workplaces->delete($workplace)) {
            $this->Flash->success(__('事業所情報が削除されました'));
        } else {
            $this->Flash->success(__('削除できませんでした'));
        }
        return $this->redirect(['action' => 'indexn']);
    }

    public function sougeiregister()
    {
        $data = $this->request->getData();
        $sougeicarTable = TableRegistry::get('sougeicar');

        // 新規送迎車種データの登録
        $sougeicar = $sougeicarTable->newEntity();
        $sougeicar->no = $data['carNo'];  // 前後の空白を削除
        $sougeicar->name = $data['carName'];  // 前後の空白を削除
        $sougeicar->del = 0;

        if ($sougeicarTable->save($sougeicar)) {
            $this->Flash->success(__('送迎車種データが登録されました'));
        } else {
            $this->Flash->error(__('送迎車種データの登録に失敗しました'));
        }

        return $this->redirect(['action' => 'indexn', '#' => 'sougeicar-section']);
    }
}