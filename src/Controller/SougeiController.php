<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

use Cake\Log\Log;


class SougeiController extends AppController
{
    public function index()
    {
        // ログイン判定
        $user = $this->Auth->user();
        if(is_null($user)){
            // ログインしていない場合
            $this->Flash->error('ログインしていません');
            return $this->redirect(['action' => 'index']);
        }

        //サイドバーリストボックス用データ
        for($i=2021;$i<=date('Y')+1;$i++) {
            $years[$i] = $i;
        }
        for($i=1;$i<=12;$i++) {
            $months[$i] = $i;
        }
        for($i=1;$i<=31;$i++) {
            $days[$i] = $i;  // キーを数値型に
        }

        // POSTリクエストを処理
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            
            // フォームからの値をセッションに保存
            if (isset($data['year'])) {
                $year = (int)$data['year'];
                //$this->request->getSession()->write('Qyear', $year);
            }
            
            if (isset($data['month'])) {
                $month = (int)$data['month'];
                //$this->request->getSession()->write('Qmonth', $month);
            }
            
            if (isset($data['day'])) {
                $day = (int)$data['day'];
                //$this->request->getSession()->write('Qday', $day);
            }
        } else {
            // 値がない場合は現在の日付を設定
            if(empty($year)) $year = date('Y');
            if(empty($month)) $month = date('n');
            if(empty($day)) $day = date('j');
        }
        
        //使用するテーブル
        $transportsTable = TableRegistry::get('Transports');
        $usersTable = TableRegistry::get('Users');
        $sougeicarTable = TableRegistry::get('sougeicar');

        //日付の設定
        $date = sprintf('%04d-%02d-%02d', $year, $month, $day);

        //当日の迎データを取得
        $arrivalData = $transportsTable->find()
            ->contain(['Users', 'StaffUsers', 'SubstaffUsers'])
            ->where([
                'Transports.date' => $date,
                'Transports.kind' => 1  // 1: 迎
            ])
            ->order(['Transports.hatsutime' => 'ASC'])
            ->all();

        //当日の送データを取得
        $departureData = $transportsTable->find()
            ->contain(['Users', 'StaffUsers', 'SubstaffUsers'])
            ->where([
                'Transports.date' => $date,
                'Transports.kind' => 2  // 2: 送
            ])
            ->order(['Transports.hatsutime' => 'ASC'])
            ->all();

        //配列に挿入
        $SougeiData = [];
        
        //迎データの整形
        foreach ($arrivalData as $data) {
            $SougeiData[] = [
                'id' => $data->id,
                'userid' => $data->user->id,
                'name' => $data->user->name,
                'type' => $data->kind == 1 ? '迎' : '送',
                'departure_time' => $data->hatsutime ? $data->hatsutime->format('H:i') : '',
                'departure_place' => $data->hatsuplace,
                'arrival_time' => $data->taykutime ? $data->taykutime->format('H:i') : '',
                'arrival_place' => $data->tyakuplace,
                'driver' => $data->staff_id ? $data->staff_user->id : '',
                'passenger' => $data->substaff_id ? $data->substaff_id : '',
                'car_no' => $data->car ? $data->car : '',
                'del' => ''
            ];
        }

        //送データの整形
        foreach ($departureData as $data) {
            $SougeiData[] = [
                'id' => $data->id,
                'userid' => $data->user->id,
                'name' => $data->user->name,
                'type' => $data->kind == 1 ? '迎' : '送',
                'departure_time' => $data->hatsutime ? $data->hatsutime->format('H:i') : '',
                'departure_place' => $data->hatsuplace,
                'arrival_time' => $data->taykutime ? $data->taykutime->format('H:i') : '',
                'arrival_place' => $data->tyakuplace,
                'driver' => $data->staff_id ? $data->staff_user->id : '',
                'passenger' => $data->substaff_id ? $data->substaff_id : '',
                'car_no' => $data->car ? $data->car : '',
                'del' => ''
            ];
        }

        //スタッフ一覧取得
        $staffs = $usersTable
        ->find('list',['valueField'=>'name'])
        ->where(['Users.adminfrag'=>1,'Users.display'=>0])
        ->order(['Users.narabi'=>'ASC','Users.id'=>'ASC'])
        ->toArray();

        //車両一覧取得
        $cars = $sougeicarTable
        ->find()
        ->where(['del' => 0])
        ->order(['id' => 'ASC'])
        ->all()
        ->combine(
            'id', // キーにしたいフィールド
            function ($car) {
                return $car->no . '   ' . $car->name;
            }
        )
        ->toArray();

        $this->set(compact('years','months','days','year','month','day','SougeiData','staffs','cars'));
    }
    
    public function register()
    {
        // ログイン判定
        $user = $this->Auth->user();
        if(is_null($user)){
            // ログインしていない場合
            $this->Flash->error('ログインしていません');
            return $this->redirect(['action' => 'index']);
        }

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            
            // トランザクション開始
            $connection = ConnectionManager::get('default');
            
            try {
                $connection->transactional(function ($connection) use ($data) {
                    $transportsTable = TableRegistry::getTableLocator()->get('Transports');
                    $attendanceTable = TableRegistry::getTableLocator()->get('Attendances');

                    // データの数だけ処理
                    for ($i = 0; $i < count($data['id']); $i++) {
                        $id = $data['id'][$i];
                        $transport = $transportsTable->get($id);
                        
                        // 削除フラグがONの場合
                        if (isset($data['del'][$i]) && $data['del'][$i] == 1) {
                            // 種別に応じてattendancesテーブルを更新
                            $attendance = $attendanceTable->find()
                                ->where([
                                    'user_id' => $transport->user_id,
                                    'date' => $transport->date
                                ])
                                ->first();
                                
                            if ($attendance) {
                                if ($transport->kind == 1) { // 迎の場合
                                    $attendance->ou = 0;
                                } else if ($transport->kind == 2) { // 送の場合
                                    $attendance->fuku = 0;
                                }
                                $attendanceTable->save($attendance);
                            }
                            
                            // 送迎レコードを削除
                            $transportsTable->delete($transport);
                            
                        } else {
                            // 更新処理
                            $transport->hatsutime = !empty($data['departure_time'][$i]) ? $data['departure_time'][$i] : null;
                            $transport->taykutime = !empty($data['arrival_time'][$i]) ? $data['arrival_time'][$i] : null;
                            $transport->hatsuplace = !empty($data['departure_place'][$i]) ? $data['departure_place'][$i] : null;
                            $transport->tyakuplace = !empty($data['arrival_place'][$i]) ? $data['arrival_place'][$i] : null;
                            $transport->staff_id = !empty($data['driver'][$i]) ? $data['driver'][$i] : null;
                            $transport->substaff_id = !empty($data['passenger'][$i]) ? $data['passenger'][$i] : null;
                            $transport->car = !empty($data['car'][$i]) ? $data['car'][$i] : null;
                            $transport->modified = date('Y-m-d H:i:s');
                            $transportsTable->save($transport);
                        }
                    }
                });
                
                $this->Flash->success(__('保存されました。'));
                
            } catch (\Exception $e) {
                $this->Flash->error(__('保存に失敗しました。もう一度お試しください。'));
            }
            
            // 同じ画面に戻る（年月日のパラメータ付き）
            return $this->redirect(['action' => 'index', 
                '?' => [
                    'year' => $this->request->getQuery('year'),
                    'month' => $this->request->getQuery('month'),
                    'day' => $this->request->getQuery('day')
                ]
            ]);
        }
        
        // POSTでない場合はindexにリダイレクト
        return $this->redirect(['action' => 'index']);
    }
}