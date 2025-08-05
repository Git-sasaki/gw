<?php
namespace App\Controller;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use App\Controller\AppController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Cake\Log\Log;

class AbsentsController extends AppController
{
    public function indexn()
    {
        // ログイン判定
        $auser = $this->Auth->user();
        if(is_null($auser)){
            // ログインしていない場合
            $this->Flash->error('ログインしていません');
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            $usersTable = TableRegistry::get('Users');
            $users = $usersTable->find('list',['keyField'=>'id','valueField'=>'name'])->toArray();
            $sideusers = $usersTable
            ->find('list',['keyField'=>'id','valueField'=>'name'])
            ->where(['Users.adminfrag'=>0, 'Users.display'=>0])
            ->order(['Users.narabi'=>'ASC','Users.id'=>'ASC'])
            ->toArray();

            //日付の定義
            $weekList = ["日","月","火","水","木","金","土"];
            for($i=2021;$i<=date('Y')+1;$i++) {
                $years["$i"] = $i;
            }
            for($i=1;$i<=12;$i++) {
                $months[sprintf('%02d',$i)] = $i;
            }
            $year = $this->request->getSession()->read('Ayear');
            $month = $this->request->getSession()->read('Amonth');
            $user_id = $this->request->getSession()->read('Auser_id');

            if(empty($year)) {
                $year = date('Y');
            }
            if(empty($month)) {
                $month = date('m');
            }
            if(empty($user_id)) {
                $user_id = 0;
            }

            $timestamp = mktime(0,0,0,$month,1,$year);

            // 10件ずつ分割
            $this->paginate = [
                'limit' => 10,
                "order" => ["date" => "ASC"]
            ];

            if($user_id == 0) {
                $absents = $this->paginate(
                $this->Absents->find()
                ->where(['Absents.kekkindate >=' => date('Y-m',$timestamp)."-1",
                         'Absents.kekkindate <=' => date('Y-m',$timestamp)."-".date('t',$timestamp)])
                ->order(['Absents.kekkindate' => 'ASC']))
                ->toArray();
            } else {
                $absents = $this->paginate(
                $this->Absents->find()
                ->where(['Absents.user_id'=>$user_id, 
                         'Absents.kekkindate >=' => date('Y-m',$timestamp)."-1", 
                         'Absents.kekkindate <=' => date('Y-m',$timestamp)."-".date('t',$timestamp)])
                ->order(['Absents.kekkindate' => 'ASC']))
                ->toArray();
            }

            $this->set(compact('users','sideusers','years','months','year','month','user_id','absents','weekList'));
        } else {
            $this->Flash->error('アクセス権限がありません');
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }
    }

    public function getquery0()
    {
        $this->request->getSession()->write([
            'Ayear' => $this->request->getData('year'),
            'Amonth' => $this->request->getData('month'),
            'Auser_id' => $this->request->getData('id'),
        ]);
        return $this->redirect(['action' => 'indexn']);
    }

    public function editn()
    {
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            // 基本的な情報を取得
            $id = $this->request->getData('id');
            $staff_id = $this->request-> getSession()->read('Auth.User.id');
            $usersTable = TableRegistry::get('Users');
            
            $admresults = $usersTable
            ->find('list',['valueField' => 'name'])
            ->where(['Users.adminfrag' => 1])
            ->order(['Users.narabi' => 'ASC'])
            ->toArray();

            $users = $usersTable
            ->find('list',['valueField' => 'name'])
            ->where(['Users.adminfrag' => 0,'Users.display'=> 0])
            ->order(['Users.narabi' => 'ASC'])
            ->toArray();

            if(!empty($id)) {
                $notnew = $this->Absents
                ->find()
                ->where(['Absents.id'=>$id])
                ->first();
                $date = explode("-",$notnew["date"]->i18nFormat('yyyy-MM-dd'));
                $kekkindate = explode("-",$notnew["kekkindate"]->i18nFormat('yyyy-MM-dd'));
            } else {
                $notnew = null;
                $date = null;
                $kekkindate = null;
            }
            
            //日付の定義
            for($i=2021;$i<=date('Y')+1;$i++) {
                $years["$i"] = $i;
            }
            for($i=1;$i<=12;$i++) {
                $months[sprintf('%02d',$i)] = $i;
            }
            $this->set(compact("years","months","staff_id","admresults","users","notnew","date","kekkindate"));
        } else {
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }
    }

    public function register()
    {
        // ログイン判定
        $auser = $this->Auth->user();
        $attendanceTable = TableRegistry::get('Attendances');
        $usersTable = TableRegistry::get('Users');  
        if(is_null($auser)) {    
            // ログインしていない場合
            $this->Flash->error('ログインしていません');
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        } else {
            $data = $this->request->getData();
            $postdate = $data["kekkinyear"]."-".$data["kekkinmonth"]."-".$data["kekkindate"];
            $uketsuke = $data["year"]."-".$data["month"]."-".$data["date"];
            $abs = $this->Absents
            ->find()
            ->where(["Absents.date" => $postdate, "Absents.user_id" => $data["user_id"]])
            ->first();

            $att = $attendanceTable
            ->find()
            ->where(["Attendances.date" => $postdate, "Attendances.user_id" => $data["user_id"]])
            ->first();

            if(!empty($abs)) {
                $absents = $this->Absents->get($abs['id']);
            } else {
                $absents = $this->Absents->newentity();
            }

            $absents->date = $uketsuke;
            $absents->time = $data["time"];
            $absents->kekkindate = $postdate;
            $absents->kekkinkasan = $data["kekkinkasan"];
            $absents->shudan = $data["shudan"];
            $absents->user_staffid = $data["user_staffid"];
            $absents->user_id = $data["user_id"];
            $absents->relation = $data["relation"];
            $absents->naiyou = $data["naiyou"];
            $absents->next = $data["next"];
            $absents->answer1 = $data["answer1"];
            $absents->answer2 = $data["answer2"];
            $absents->answer3 = $data["answer3"];
            $absents->answer4 = $data["answer4"];
            $absents->bikou = $data["bikou"];

            if($this->Absents->save($absents)) {
                if(!empty($att)) {
                    $attendances = $attendanceTable->get($att['id']);
                } else {
                    $attendances = $attendanceTable->newentity();
                }
                $attendances->user_id = $data["user_id"]; // user_idを設定
                $attendances->date = $postdate;
                $attendances->remote = 0;
                $attendances->medical = 0;
                $attendances->support = 0;
                $attendances->kekkin = 1;
                if(empty($att["user_staffid"])) {
                    $attendances->user_staffid = $auser["id"];
                }
                
                // 利用者のA型/B型情報を記録（0=A型、1=B型）
                $userData = $usersTable->find()->where(['Users.id' => $data["user_id"]])->first();
                if ($userData && $userData->adminfrag == 0) {
                    $attendances->user_type = $userData->wrkCase;
                } else {
                    $attendances->user_type = null; // 職員の場合はnull
                }
                if($attendanceTable->save($attendances)) {
                    $this->Flash->success(__('保存されました'));
                    $this->request->getSession()->write([
                        'Qyear' => $data["kekkinyear"],
                        'Qmonth' => $data["kekkinmonth"],
                        'Quser_id' => $data["user_id"],
                    ]);
                    if($data["kekkindate"]<=3) {
                        return $this->redirect([
                            'controller' => 'TimeCards',
                            'action' => 'editn',
                        ]);
                    } else {
                        $hi = $data["kekkindate"]-3;
                        return $this->redirect([
                            'controller' => 'TimeCards',
                            'action' => 'editn',
                            '#' => 'jump'.$hi
                        ]);
                    }
                } else {
                    $this->Flash->error(__('出勤簿への登録に失敗しました'));
                    return $this->redirect(['action' => 'indexn']);
                }
            } else {
                $this->Flash->error(__('欠勤情報の登録に失敗しました'));
                return $this->redirect(['action' => 'indexn']);
            }
        }
    }

    public function delete($id = null)
    {
        $usersTable = TableRegistry::get('Users');        
        //ユーザー一覧取得
        $staffs = $usersTable
        ->find('list',['keyField' => 'id','valueField' => 'name'])
        ->where(['Users.display' => 0])
        ->toArray();
        $this->set('staffs', $staffs);

        $absent = $this->Absents->get($id);
        
        // 削除前に欠勤データの情報を保存
        $user_id = $absent->user_id;
        $kekkindate = $absent->kekkindate;
        
        // トランザクション開始
        $connection = ConnectionManager::get('default');
        $connection->begin();
        
        try {
            // 欠勤データを削除
            if ($this->Absents->delete($absent)) {
                // 関連する出勤データを更新
                $updateResult = $this->updateAttendanceAfterAbsentDelete($user_id, $kekkindate);
                
                if ($updateResult) {
                    // 更新が成功した場合はコミット
                    $connection->commit();
                    $this->Flash->success(__('欠席情報が削除されました'));
                } else {
                    // 更新が失敗した場合はロールバック
                    $connection->rollback();
                    $this->Flash->error(__('関連する出勤データが見つからないか、更新に失敗しました'));
                }
            } else {
                // 削除に失敗した場合はロールバック
                $connection->rollback();
                $this->Flash->error(__('欠席情報は削除されませんでした'));
            }
        } catch (\Exception $e) {
            // エラーが発生した場合はロールバック
            $connection->rollback();
            $this->Flash->error(__('エラーが発生しました: ') . $e->getMessage());
        }
        
        return $this->redirect(['action' => 'indexn']);
    }
    
    /**
     * 欠勤データ削除後に関連する出勤データを更新する
     * 
     * @param int $user_id ユーザーID
     * @param string $kekkindate 欠勤日
     * @return bool 更新が成功した場合はtrue、失敗した場合はfalse
     */
    private function updateAttendanceAfterAbsentDelete($user_id, $kekkindate)
    {
        $attendancesTable = TableRegistry::get('Attendances');
        
        // 該当する出勤データを検索
        $attendance = $attendancesTable->find()
            ->where([
                'Attendances.user_id' => $user_id,
                'Attendances.date' => $kekkindate
            ])
            ->first();
        
        if ($attendance) {
            // kekkinを0に、bikouを初期化、user_staffidを0に設定
            $attendance->kekkin = 0;
            $attendance->bikou = null;
            $attendance->user_staffid = 0;
            
            // データベースに保存して結果を返す
            return $attendancesTable->save($attendance) !== false;
        }
        
        // 出勤データが見つからない場合はエラー
        return false;
    }

    public function detailn()
    {
        if($this->request-> getSession()->read('Auth.User.adminfrag') == 1){
            
            $id = $this->request->getData('id');
            $usersTable = TableRegistry::get('Users');
            $staffs = $usersTable->find('list',['keyField'=>'id','valueField'=>'name'])->toArray();
            $mitai = $this->Absents
            ->find()
            ->where(['Absents.id' => $id])
            ->EnableHydration(false)
            ->first();
            $this->set("mitai",$mitai);
            
            $okona = array("行えなかった","行った");
            $this->set("okona",$okona);
            $this->set("name",$staffs[$mitai["user_id"]]);
            $this->set("staffname",$staffs[$mitai["user_staffid"]]);

        } else {
            return $this->redirect(['controller' => 'users', 'action' => 'login']);
        }
    }

    public function excelexport() 
    {
        // 基本的な情報を取得
        $usersTable = TableRegistry::get('Users');
        $year = $this->request->getData('year');
        $month = $this->request->getData('month');
        $user_id = $this->request->getData('user_id');
        $user_staffid = $this->request->getData('user_staffid');
        $timestamp = mktime(0,0,0,$month,1,$year);

        $users = $usersTable
        ->find('list',['keyField' => 'id','valueField' => 'lastname']);
        $results = $users->toArray();

        $staffs = $usersTable
        ->find('list',['keyField' => 'id','valueField' => 'name']);
        $resultsf = $staffs->toArray();

        if($user_id == 0) {
            $absents = $this->Absents
            ->find()
            ->where(['Absents.kekkindate >=' => date('Y-m',$timestamp).'-01', 
                     'Absents.kekkindate <=' => date('Y-m',$timestamp)."-".date("t",$timestamp)])
            ->order(['Absents.kekkindate'=>'ASC'])
            ->toArray();
        } else {
            $absents = $this->Absents
            ->find()
            ->where(['Absents.user_id' => $user_id,
                     'Absents.kekkindate >=' => date('Y-m',$timestamp).'-01', 
                     'Absents.kekkindate <=' => date('Y-m',$timestamp)."-".date("t",$timestamp)])
            ->order(['Absents.kekkindate'=>'ASC'])
            ->toArray();
        }

        $torf1 = array("行えなかった","行った");
        $torf2 = array("なし","あり");

        $filePath = './template/absent.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($filePath);
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($year."年".$month."月");

        $pages = floor(count($absents) / 22);
        for($j=1; $j<=$pages; $j++) {
            $cloneSheet = clone $spreadsheet->getSheet(0);
            $cloneSheet->setTitle($year."年".$month."月 (".$j.")");
            $spreadsheet->addSheet($cloneSheet);
        }

        $i = 0;
        $j = 0;
        foreach($absents as $absent) {
            $sheet->setCellValue('A'.($i + 3),$absent["date"]->i18nFormat('yyyy-MM-dd'));
            $sheet->setCellValue('B'.($i + 3),$absent["time"]->i18nFormat('H:mm'));
            $sheet->setCellValue('C'.($i + 3),$absent["kekkindate"]->i18nFormat('yyyy-MM-dd'));
            $sheet->setCellValue('D'.($i + 3),$absent["shudan"]);
            $sheet->setCellValue('E'.($i + 3),$results[$absent["user_staffid"]]);
            $sheet->setCellValue('F'.($i + 3),$results[$absent["user_id"]]);
            $sheet->setCellValue('G'.($i + 3),$absent["naiyou"]);
            $sheet->setCellValue('H'.($i + 3),$torf1[$absent["next"]]);
            $sheet->setCellValue('I'.($i + 3),$absent["answer1"]);
            if(empty($absent["bikou"])) {
                $bvalue = 0;
            } else {
                $bvalue = 1;
            }
            $sheet->setCellValue('J'.($i + 3),$torf2[$bvalue]);
            $i++;
            if($i == 22) {
                $j++;
                $sheet = $spreadsheet->getSheet($j);
                $i = 0;
            }
        }

        if($user_id == 0) {
            $title = date("Y-m",$timestamp);
        } else {
            $title = date("Y-m",$timestamp)."　".$resultsf[$user_id];
        }

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename='.$title.'　欠席連絡記録.xlsx');
		header('Cache-Control: max-age=0');
		header('Cache-Control: max-age=1');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: cache, must-revalidate');
		header('Pragma: public');

		$writer = new XlsxWriter($spreadsheet);
		$writer->save('php://output');
		exit;
    }
}