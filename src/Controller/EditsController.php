<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

class EditsController extends AppController
{
    public function index()
    {
        // ログイン判定
        $user = $this->Auth->user();
        if(is_null($user)){
            // ログインしていない場合
            $this->Flash->error('ログインしていません');
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        } else {
            $usersTable = TableRegistry::get('Users');
            //ユーザー一覧取得
            $staffs = $usersTable
            ->find('list',['keyField'=>'id','valueField'=>'name'])
            ->where(['Users.display'=>0])
            ->order(['Users.adminfrag'=>'DESC','Users.narabi'=>'ASC'])
            ->toArray();
            $this->set('staffs', $staffs);
            //日付の定義
            $this->set('years', array("2022"=>"2022","2023"=>"2023"));
            $this->set('months', array("1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"8","9"=>"9","10"=>"10","11"=>"11","12"=>"12"));
        }
    }

    public function edit2() {
        $year = $this -> request -> getData("year");
        $month = $this -> request -> getData("month");
        //管理者権限の場合
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            $user = $this->request->getData("id");
            return $this->redirect(['controller'=>'TimeCards','action' => 'edit',"?"=>array("id"=>$user,"year"=>$year,"month"=>$month)]);
        } elseif($this->request-> getSession()->read('Auth.User.adminfrag') == 0) {
            $user = $this->request-> getSession()->read('Auth.User.id');
            return $this->redirect(['controller'=>'TimeCards','action' => 'edit2',"?"=>array("id"=>$user,"year"=>$year,"month"=>$month)]);
        } else {
            $this->Flash->error('ログインしてください');
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }
    }                        
}