<?php
namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use App\Controller\AppController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use setasign\Fpdi;

use Cake\Mailer\Email;

use Cake\Log\Log;

class KaimonosController extends AppController
{
    public function indexn()
    {
        $kensakusan = $this->request->getSession()->read('kensakusan');

        // 10件ずつ分割
        $this->paginate = [
            'limit' => 10,
            "order" => ["date" => "ASC"]
        ];

        if(empty($kensakusan)) {
            $kais = $this->paginate($this->Kaimonos->find())->toArray();
        } else {
            $kais = $this->paginate($this->Kaimonos
            ->find('all',['conditions'=>['cinnamon like' => '%'.$kensakusan.'%']]))->toArray();
        }

        $this->request->getSession()->delete('kensakusan');
        $session = $this->request->getSession()->read('Auth.User.id');
        
        $usersTable = TableRegistry::get('Users');
        $users = $usersTable->find('list',['keyField'=>'id','valueField'=>'name'])->toArray();

        $this->set(compact('kais','users','session','kensakusan'));
    }

    public function kokodozo()
    {
        $type = $this->request->getQuery("type");
        if($type == 0) {
            $KaiCount = count($this->Kaimonos->find()->toArray());
            $LastPage = ceil($KaiCount / 10);
            return $this->redirect(['action' => 'indexn','?'=>['page'=>$LastPage]]);
        } elseif($type == 1) {
            $data = $this->request->getData("kensakusan");
            if(empty($data)) {
                $this->request->getSession()->delete('kensakusan');
            } else {
                $this->request->getSession()->write([
                    "kensakusan" => $data
                ]);
            }
            return $this->redirect(['action' => 'indexn']);
        } else {
            return $this->redirect(['action' => 'indexn']);
        }
    }

    public function delete($id = null)
    {
        $kaimonoDetailsTable = TableRegistry::get('KaimonoDetails');
        $this->request->allowMethod(['post', 'delete']);
        $kaimono = $this->Kaimonos->get($id, ['contain' => []]);
        $details = $kaimonoDetailsTable
        ->find()
        ->where(["KaimonoDetails.kaimono_id" => $id])
        ->toArray();

        $delekaimono = $this->Kaimonos->get($kaimono['id']);
        $this->Kaimonos->delete($delekaimono);
        for($i=0; $i<3; $i++) {
            $deledetails = $kaimonoDetailsTable->get($details[$i]['id']);
            $kaimonoDetailsTable->delete($deledetails);
        }
        if(empty($deledetails["cinnamon"])) {
            $this->Flash->success(__('削除されました'));
        } else {
            $this->Flash->error(__('エラーが発生しました'));
        }
        return $this->redirect(['action' => 'indexn']);
    }

    public function register() 
    {
        $data = $this->request->getData();
        $postdate = $data["year"].'-'.$data["month"].'-'.$data["date"];
        $kaimonoDetailsTable = TableRegistry::get('KaimonoDetails');
        $session = $this->request->getSession()->read('Auth.User.id');

        //購入者とログインしている人が異なる場合購入申請を出来ない
        if(($data["user_id"] != $session) and ($this->request->getSession()->read('Auth.User.kessai') == 0)) {
            $this->Flash->error(__('購入申請は購入者自身で行ってください'));
            return $this->redirect(['action' => 'newn']);
        }

        if(empty($data["price"])) {
            $this->Flash->error(__('価格が設定されていません'));
            return $this->redirect(['action' => 'newn']);
        }
        if(empty($data["user_id"])) {
            $data["user_id"] = $session;
        }

        $kaimonos = $this->Kaimonos->newentity();
        $kaimonos->type = $data["type"];
        $kaimonos->date = $postdate;
        $kaimonos->cinnamon = $data["cinnamon"][0];
        $kaimonos->user_id = $data["user_id"];
        $kaimonos->price = $data["price"];
        $kaimonos->payment = $data["payment"];
        $kaimonos->bikou = $data["bikou"];
        $kaimonos->status = 0;
        $this->Kaimonos->save($kaimonos);

        $getid = $this->Kaimonos
        ->find("list",["valueField"=>"id"])
        ->order(["Kaimonos.id"=>"DESC"])
        ->first();

        for($i=0; $i<3; $i++) {
            $kaimonoDetails = $kaimonoDetailsTable->newentity();
            $kaimonoDetails->kaimono_id = $getid;
            $kaimonoDetails->cinnamon = $data["cinnamon"][$i];
            $kaimonoDetails->detail = $data["detail"][$i];
            $kaimonoDetails->shop = $data["shop"][$i];
            $kaimonoDetails->url = $data["url"][$i];
            $kaimonoDetailsTable->save($kaimonoDetails);
        }

        if($kaimonoDetailsTable->save($kaimonoDetails)){

            //決済者にメールを送信
            //購入申請したのが決済者自身だった場合にはメールを送らない
            if ($this->request->getSession()->read('Auth.User.kessai') != 1) {
                //決済者メールアドレス取得
                $UsersTable = TableRegistry::get('Users');
                $mailads = $UsersTable
                ->find('all')
                ->where(['kessai' => 1])
                ->extract('mail')
                ->toList();

                //タイトル作成
                $title = "【グループウェア】 決済申請 " . "ID：" . $getid;

                //本文作成
                $contents = "ＩＤ：" . $getid . "\r\n" . 
                            "日付：" . $postdate . "\r\n" . 
                            "購入者：" . $this->request->getSession()->read('Auth.User.name') . "\r\n" .
                            "価格：" . $data["price"] . "円\r\n" .
                            "商品名１：" . $data["cinnamon"][0] . "\r\n" .
                            "商品名２：" . $data["cinnamon"][1] . "\r\n" .
                            "商品名３：" . $data["cinnamon"][2];
                

                $email = new Email();
                $email->setProfile('default');
                $email->setFrom(['info@nbg-rd.com' => 'グループウェア'])
                    ->setTo($mailads)
                   ->setSubject($title)
                    ->send($contents);

            }

            $this->Flash->success(__('保存されました'));
            return $this->redirect([
                'action' => 'kokodozo',
                '?' => ['type' => 0],                   
            ]);
        }
    }

    public function register2($id = null) 
    {
        $data = $this->request->getData();
        $kaimonoDetailsTable = TableRegistry::get('KaimonoDetails');

        //購入者とログインしている人が異なる場合購入申請を出来ない
        $session = $this->request->getSession()->read('Auth.User.id');
        if(($data["user_id"] != $session) and ($this->request->getSession()->read('Auth.User.kessai') == 0)) {
            $this->Flash->error(__('購入申請は購入者自身で行ってください'));
            return $this->redirect(['action' => 'indexn']);
        }

        //メール送信チェック（$emailflag=1:決済者へ、$emailflag=2:申請者へ）
        if ($this->request->getSession()->read('Auth.User.kessai') == 1) {
            //申請者が決済者か？
            $UsersTable = TableRegistry::get('Users');
            $iskessai = $UsersTable
            ->find('all')
            ->select(['kessai'])
            ->where(['id' => $data["user_id"]])
            ->first();

            $emailflag = ($iskessai->kessai == 1) ? -1 : 2;
        } else {
            $emailflag = 1;
        }


        if(empty($data["price"])) {
            $this->Flash->error(__('価格が設定されていません'));
            return $this->redirect(['action' => 'indexn']);
        }

        $kai = $this->Kaimonos
        ->get($id, ['contain' => []]);
        $postdate = $data["year"].'-'.$data["month"].'-'.$data["date"];
        if($this->request-> getSession()->read('Auth.User.kessai') == 1){
            $postdate2 = $data["kyear"].'-'.$data["kmonth"].'-'.$data["kdate"];
        }

        $kaimonos = $this->Kaimonos->get($kai['id']);
        $kaimonos->type = $data["type"];
        $kaimonos->date = $postdate;
        if(!empty($data["cinnamon"][0])) {
            $kaimonos->cinnamon = $data["cinnamon"][0];
        }
        $kaimonos->user_id = $data["user_id"];
        $kaimonos->price = $data["price"];
        $kaimonos->payment = $data["payment"];
        $kaimonos->bikou = $data["bikou"];
        $kaimonos->status = 0;
        if($this->request-> getSession()->read('Auth.User.kessai') == 1){
            $kaimonos->status = $data["status"];
            if($data["status"] == 1 || $data["status"] == 2){
                $kaimonos->kessaibi = $postdate2;
                $kaimonos->kessaisha = $data["kessaisha"];
            }
        }
        $this->Kaimonos->save($kaimonos);
        
        $success = false;
        if(!empty($data["cinnamon"][0])) {
            $detailid = $kaimonoDetailsTable
            ->find()
            ->where(["KaimonoDetails.kaimono_id" => $id])
            ->select(["KaimonoDetails.id"])
            ->toArray();
            for($i=0; $i<3; $i++) {
                $kaimonoDetails = $kaimonoDetailsTable->get($detailid[$i]["id"]);
                $kaimonoDetails->kaimono_id = $id;
                $kaimonoDetails->cinnamon = $data["cinnamon"][$i];
                $kaimonoDetails->detail = $data["detail"][$i];
                $kaimonoDetails->shop = $data["shop"][$i];
                $kaimonoDetails->url = $data["url"][$i];
                $kaimonoDetailsTable->save($kaimonoDetails);
            }
            if($this->Kaimonos->save($kaimonoDetails)){
                $success = true;
            }
        } else {
            if($this->Kaimonos->save($kaimonos)){
                $success = true;
            }
        }

        //保存成功・メール処理・ページ遷移
        if ($success) {
            if ($emailflag == 2) {  //申請者にメールを送信
                //申請者メールアドレス取得
                $UsersTable = TableRegistry::get('Users');
                $mailads = $UsersTable
                ->find('all')
                ->select(['name', 'mail'])
                ->where(['id' => $data["user_id"]])
                ->first();

                //タイトル作成
                $title = "【グループウェア】 " . (($data["status"] == 1) ? "決済済み " : "決済否決 ") . "ID：" . $kai['id'];

                //本文作成
                $contents = "ＩＤ：" . $kai['id'] . "\r\n" . 
                            "日付：" . $postdate2 . "\r\n" . 
                            "決済者：" . $this->request->getSession()->read('Auth.User.name') . "\r\n" .
                            "申請者：" . $mailads->name  . "\r\n" .
                            "価格：" . $data["price"] . "円\r\n" .
                            "商品名１：" . $data["cinnamon"][0] . "\r\n" .
                            "商品名２：" . $data["cinnamon"][1] . "\r\n" .
                            "商品名３：" . $data["cinnamon"][2];

                //否決時には備考を追加
                if ($data["status"] == 2) {
                    $contents = $contents . "\r\n" . "備考：" . $data["bikou"];
                }
                
                $email = new Email();
                $email->setProfile('default');
                $email->setFrom(['info@nbg-rd.com' => 'グループウェア'])
                    ->setTo($mailads->mail)
                    ->setSubject($title)
                    ->send($contents);

            } else if ($emailflag == 1) {    //決済者にメールを送信
                //決済者メールアドレス取得
                $UsersTable = TableRegistry::get('Users');
                $mailads = $UsersTable
                ->find('all')
                ->where(['kessai' => 1])
                ->extract('mail')
                ->toList();

                //タイトル作成
                $title = "【グループウェア】 決済申請 " . "ID：" . $kai['id'];

                //本文作成
                $contents = "ＩＤ：" . $kai['id'] . "\r\n" . 
                            "日付：" . $postdate . "\r\n" . 
                            "購入者：" . $this->request->getSession()->read('Auth.User.name') . "\r\n" .
                            "価格：" . $data["price"] . "円\r\n" .
                            "商品名１：" . $data["cinnamon"][0] . "\r\n" .
                            "商品名２：" . $data["cinnamon"][1] . "\r\n" .
                            "商品名３：" . $data["cinnamon"][2];
                

                $email = new Email();
                $email->setProfile('default');
                $email->setFrom(['info@nbg-rd.com' => 'グループウェア'])
                    ->setTo($mailads)
                   ->setSubject($title)
                    ->send($contents);
            }

            $this->Flash->success(__('保存されました'));
            return $this->redirect([
                'action' => 'kokodozo',
                '?'=>['type'=>0],            
            ]);
        }
    }

    public function newn() 
    {
        $session = $this->request->getSession()->read('Auth.User.id');
        $usersTable = TableRegistry::get('Users');
        $kaimonoDetailsTable = TableRegistry::get('KaimonoDetails');
        $users = $usersTable
        ->find('list',['keyField' => 'id','valueField' => 'name'])
        ->where(['Users.display'=>0])
        ->order(['Users.narabi'=>'ASC','Users.id'=>'ASC'])
        ->toArray();
        $saikoflag = 0;

        $data = $this->request->getData();
        if(!empty($data["id"])) {
            $saikoflag = 1;
            $saiko = $this->Kaimonos
            ->find()
            ->where(['Kaimonos.id'=>$data["id"]])
            ->first();
            $saikodetails = $kaimonoDetailsTable
            ->find()
            ->where(['KaimonoDetails.kaimono_id'=>$data["id"]])
            ->toArray();
        } else {
            $saiko = NULL;
            $saikodetails = NULL;
        }

        for($i=2021;$i<=date('Y')+1;$i++) {
            $years["$i"] = $i;
        }
        for($i=1;$i<=12;$i++) {
            $months[sprintf('%02d',$i)] = $i;
        }
        $this->set(compact('years','months','users','session','saikoflag','saiko','saikodetails'));
    }

    public function detailn() 
    {
        $id = $this->request->getData('id');
        $kaimono = $this->Kaimonos
        ->get($id, ['contain' => []]);
        
        $kaimonoDetailsTable = TableRegistry::get('KaimonoDetails');
        $details = $kaimonoDetailsTable
        ->find()
        ->where(["KaimonoDetails.kaimono_id"=>$id])
        ->toArray();

        $usersTable = TableRegistry::get('Users');
        $users = $usersTable->find('list',['keyField'=>'id','valueField'=>'name'])->toArray();
        
        $statusbun = ["0"=>"未決裁","1"=>"決裁済","2"=>"否決"];
        $this->set(compact('kaimono','users','details','statusbun','id'));
    }

    public function editn()
    {
        $id = $this->request->getData('id');
        $session = $this->request->getSession()->read('Auth.User.id');
        $kaimono = $this->Kaimonos->get($id, ['contain'=>[]]);
        if(!empty($kaimono["date"])) {
            $bunkatsu = explode("/",$kaimono["date"]->i18nFormat("MM/dd/yyyy"));
        } else {
            $bunkatsu = null;
        }
        if(!empty($kaimono["kessaibi"])) {
            $bunkatsu2 = explode("/",$kaimono["kessaibi"]->i18nFormat("MM/dd/yyyy"));
        } else {
            $bunkatsu2 = null;
        }
        $usersTable = TableRegistry::get('Users');
        $kaimonoDetailsTable = TableRegistry::get('KaimonoDetails');
        $details = $kaimonoDetailsTable
        ->find()
        ->where(['KaimonoDetails.kaimono_id'=>$id])
        ->toArray();
        $users = $usersTable
        ->find('list',['keyField'=>'id','valueField'=>'name'])
        ->where(['Users.narabi <=' => 100])
        ->order(['Users.narabi'=>'ASC','Users.id'=>'ASC'])
        ->toArray();
        $users2 = $usersTable
        ->find('list',['keyField'=>'id','valueField'=>'name'])
        ->where(['Users.adminfrag'=>1, 'Users.kessai' => 1])
        ->order(['Users.narabi'=>'ASC','Users.id'=>'ASC'])
        ->toArray();

        for($i=2021;$i<=date('Y')+1;$i++) {
            $years[$i] = $i;
            $kyears[$i] = $i;
        }
        for($i=1;$i<=12;$i++) {
            $months[sprintf('%02d',$i)] = $i;
            $kmonths[sprintf('%02d',$i)] = $i;
        }
        $this->set(compact('years','months','kyears','kmonths','details','session','kaimono',
                           'users','users2','bunkatsu','bunkatsu2'));
    }

    public function excelexport($id=null)
    {
        $kaimono = $this->Kaimonos
        ->get($id, ['contain' => []]);
        $bunkatsu = explode("/",$kaimono["date"]);
        $bunkatsuc = explode("/",$kaimono["created"]->i18nFormat('MM/dd/YY'));
        $timestamp = mktime(0,0,0,$bunkatsu[0],$bunkatsu[1],$bunkatsu[2]);
        $timestampc = mktime(0,0,0,$bunkatsuc[0],$bunkatsuc[1],$bunkatsuc[2]);
        $usersTable = TableRegistry::get('Users');
        $users = $usersTable->find('list',['keyField'=>'id','valueField'=>'name'])->toArray();
        $kaimonoDetailsTable = TableRegistry::get('KaimonoDetails');
        $details = $kaimonoDetailsTable
        ->find()
        ->where(["KaimonoDetails.kaimono_id"=>$id])
        ->toArray();
        if(!empty($details)) {
            $shop = $details[0]["shop"]." ".$details[1]["shop"]." ".$details[2]["shop"];
            $cinnamon = $details[0]["cinnamon"]." ".$details[1]["cinnamon"]." ".$details[2]["cinnamon"];
        } else {
            $shop = $kaimono["shop"];
            $cinnamon = $kaimono["cinnamon"];
        }

        $filePath = './template/buppin.xlsx';
        $reader = new XlsxReader();
        $spreadsheet = $reader->load($filePath);
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();

        if($kaimono["type"]==0){
            $sheet->setTitle("物品購入伺書");
            $sheet->setCellValue('D2',"物品購入伺書");
            $sheet->setCellValue('B15',"購入予定日");
        } else {
            $sheet->setTitle("物品購入報告書");
            $sheet->setCellValue('D2',"物品購入報告書");
            $sheet->setCellValue('B15',"購入日");
        }
        $sheet->setCellValue('K4',$kaimono["id"]);
        $sheet->setCellValue('B7',date("Y年n月j日",$timestampc));
        $sheet->setCellValue('C9',$users[$kaimono["user_id"]]);
        $sheet->setCellValue('D15',date("n月j日",$timestamp));
        $sheet->setCellValue('H15',$shop);
        $sheet->setCellValue('F16',$cinnamon);
        if($kaimono["payment"]==0){
            $sheet->setCellValue('F17',$kaimono["price"]);
        } elseif($kaimono["payment"]==1) {
            $sheet->setCellValue('F19',$kaimono["price"]);
        } else {
            $sheet->setCellValue('F18',$kaimono["price"]);
        }
        $sheet->setCellValue('F20',$kaimono["bikou"]);

        if($kaimono["type"]==0){
            $title = date("Y-m-d",$timestamp)." ".$users[$kaimono["user_id"]]." 物品購入伺書";
        } else {
            $title = date("Y-m-d",$timestamp)." ".$users[$kaimono["user_id"]]." 物品購入報告書";
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename='.$title.'.xlsx');
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
    
    public function absent()
    {
        $data = $this->Kaimonos
        ->find()
        ->select(['Kaimonos.date','Kaimonos.cinnamon','Kaimonos.price'])
        ->where(['Kaimonos.date >='=>'2022-04-01','Kaimonos.date <='=>'2023-03-31'])
        ->EnableHydration(false)
        ->toArray();
        pr($data);
        exit;
    }
}
