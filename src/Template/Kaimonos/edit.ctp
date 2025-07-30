<?php
$this->assign('title', '編集');
?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
<ul class="side-nav">
        <?php if($this->request-> getSession()->read('Auth.User.adminfrag') != 1): ?>
        <li class = "heading"><?= __('メニュー') ?></li>
            <ul class = "dotlist">
                <li>
                    <?= $this->Html->link('打刻', ['controller' => 'Users', 'action' => 'stamp']); ?>
                </li>
                <li>
                    <?= $this->Html->link('出勤簿', ['controller' => 'Edits', 'action' => 'index']); ?>
                </li>
                <li>
                    <?= $this->Html->link('物品購入申請', ['controller' => 'Kaimonos', 'action' => 'index']); ?>
                </li>
                <li>
                    <?= $this->Html->link('出勤情報一覧', ['controller' => 'Users', 'action' => 'stamp2']); ?>
                </li>
            </ul>
                <li class = "heading"><?= __('作業日報') ?></li>
                <ul class = "dotlist">
                <li>
                    <?= $this->Html->link('一覧', ['controller' => 'Reports', 'action' => 'list']); ?>
                </li>
                <li>
                    <?= $this->Html->link('新規登録・編集', ['controller' => 'Users', 'action' => 'index2']); ?>
                </li>         
            </ul>
                <li><?= $this->Html->link('ログアウト', ['controller' => 'Users', 'action' => 'logout']); ?></li>
        <?php else: ?>
            <li class = "heading"><?= __('メニュー') ?></li>
            <ul class = "dotlist">
                <li>
                    <?= $this->Html->link('打刻', ['controller' => 'Users', 'action' => 'stamp']); ?>
                </li>
                <li>
                    <?= $this->Html->link('出勤簿', ['controller' => 'Edits', 'action' => 'index']); ?>
                </li>
                <li>
                    <?= $this->Html->link("欠席連絡", ["controller" => "Absents", "action" => "index"]); ?>
                </li>
                <li>
                    <?= $this->Html->link('作業日報', ['controller' => 'Reports', 'action' => 'index']); ?>
                </li>
                <li>
                    <?= $this->Html->link('物品購入申請', ['controller' => 'Kaimonos', 'action' => 'index']); ?>
                </li>
                <li>
                    <?= $this->Html->link('スケジュール', ['controller' => 'Calendars', 'action' => 'index']); ?>
                </li>
                <li>
                    <?= $this->Html->link('出勤情報一覧', ['controller' => 'Users', 'action' => 'stamp2']); ?>
                </li>
            </ul>
            <li class = "heading"><?= __('帳票') ?></li>
                <ul class = "dotlist">
                    <li>
                        <?= $this->Html->link("出勤簿印刷", ["controller" => "Prints", "action" => "index"]); ?>
                    </li>
                    <li>
                    <?= $this->Html->link("業務日誌印刷", ["controller" => "Nisshis", "action" => "index"]); ?>
                    </li>
                    <li>
                    <?= $this->Html->link("欠勤情報出力", ["controller" => "Exports", "action" => "absent"]); ?>
                    </li>
                    <li>
                        <?= $this->Html->link("サービス記録出力", ["controller" => "Exports", "action" => "srecords"]); ?>
                    </li>
                    <li>
                        <?= $this->Html->link("CSV出力", ["controller" => "Exports", "action" => "csv"]); ?>
                    </li>
                </ul>
            <li class = "heading"><?= __('マスタ') ?></li>
                <ul class = "dotlist">
                    <li>
                        <?= $this->Html->link('ユーザー', ['controller' => 'Users', 'action' => 'index']); ?>
                    </li>
                    <li>
                    <?= $this->Html->link('デフォルト設定', ['controller' => 'Attendances', 'action' => 'default']); ?>
                    </li>
                </ul>
            <li><?= $this->Html->link('ログアウト', ['controller' => 'Users', 'action' => 'logout']); ?></li>
        <?php endif; ?>
    </ul>
</nav>

<div class="users index large-9 medium-8 columns content">
    <h3><?= __('物品購入届 編集') ?></h3>

    <?= $this->Form->create(__("View"),["type"=>"post","url"=>["action"=>"register2",$kaimono["id"]]]) ?>
    <fieldset>
        <legend><?= __('購入者情報') ?></legend>
        
        <div class = "pdakoku">
            <?php 
                echo $this->Form->label('分類：　');
                echo '<br>';
                
                //radioボタンの作成
                $options = [
                            '0' => ' 購入伺　',
                            '1' => ' 購入報告　'
                            ];
                $attributes = ['name'=>'type','value' => $kaimono["type"]]; 
                echo $this->Form->radio('type', $options, $attributes);
            ?>
        </div>

        <div class = "pdakoku">
            <div class = "sdakoku">
                <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>$bunkatsu[2]], $years) ?>
            </div>
            <div class = "sdakoku">
                <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>$bunkatsu[0]], $months) ?>
            </div>
            <div class = "sdakoku">
                <?= $this->Form->control('date',['type'=>'text','label'=>"日",'value'=>$bunkatsu[1]]) ?>
            </div>
            <div class = "w150">
                <?= $this->Form->control('user_id',['type'=>'select','label'=>"購入者",'value'=>$kaimono["user_id"]], $users) ?>
            </div>
        </div>
        <p class = "messe">※ 購入伺の場合は、年月日に購入予定日を入力してください。</p>
    </fieldset>

    <fieldset>
        <legend><?= __('購入品目情報') ?></legend>
        <div class = "pdakoku">
            <?php 
                echo $this->Form->label('支払方法：　');
                echo '<br>';
                
                //radioボタンの作成
                $options = [
                            '0' => ' 現金　',
                            '1' => ' 請求書・会社カード　'
                            ];
                $attributes = ['name'=>'payment','value' => $kaimono["payment"]]; 
                echo $this->Form->radio('payment', $options, $attributes);
            ?>
        </div>

        <div class = "odakoku">
            <div class = "w200">
                <?= $this->Form->control('cinnamon',['type'=>'text','label'=>"購入品名","value"=>$kaimono["cinnamon"]]) ?>
            </div>
            <div class = "w200">
                <?= $this->Form->control('shop',['type'=>'text','label'=>"購入先","value"=>$kaimono["shop"]]) ?>
            </div>
            <div class = "w250">
                <?= $this->Form->control('price',['type'=>'text','label'=>"金額 (半角数字のみで入力)","value"=>$kaimono["price"]]) ?>
            </div>
        </div>

        <div class = "ml10">
            <div class = "w500">
                <?= $this->Form->control('url1', ['type' => 'text', 'label' => 'URL',"value"=>$kaimono["url1"]]); ?>
            </div>
            <div class = "w500">
                <?= $this->Form->control('url2', ['type' => 'text', 'label' => 'URL',"value"=>$kaimono["url2"]]); ?>
            </div>
            <div class = "w500">
                <?= $this->Form->control('url3', ['type' => 'text', 'label' => 'URL',"value"=>$kaimono["url3"]]); ?>
            </div>
        </div>

        <?php if($this->request-> getSession()->read('Auth.User.adminfrag') != 1): ?>
        <div class = "pdakoku">
            <div class = "w500">
                <?= $this->Form->control('bikou', ['type' => 'text', 'label' => '備考',"value"=>$kaimono["bikou"]]); ?>
            </div>
            <div class="left_button">
                <?= $this->Form->button(__("登録")) ?>
            </div>
            <?= $this -> Form -> end(); ?>
            </div>
    </fieldset>
        <?php else: ?>
            <div class = "pdakoku">
            <div class = "w500">
                <?= $this->Form->control('bikou', ['type' => 'text', 'label' => '備考',"value"=>$kaimono["bikou"]]); ?>
            </div>
            </div>
    </fieldset>
    <fieldset>
        <legend><?= __('決裁状況') ?></legend>
            <div class = "pdakoku">
                <?php 
                    echo $this->Form->label('状況：　');
                    echo '<br>';
                    
                    //radioボタンの作成
                    $options = [
                                '0' => ' 未決裁　',
                                '1' => ' 決裁済み　',
                                '2' => ' 否決　'
                                ];            
                    $attributes = ['name'=>'status','value' => $kaimono["status"]]; 
                    echo $this->Form->radio('status', $options, $attributes);
                ?>
            </div>
            <div class = "pdakoku">
                <div class = "sdakoku">
                    <?php if($kaimono["status"] == 1 && !empty($kaimono["kessaibi"])): ?>
                        <?= $this->Form->control('kyear',['type'=>'select','label'=>"年",'value'=>$bunkatsu2[2]], $years) ?>
                    <?php else: ?>
                        <?= $this->Form->control('kyear',['type'=>'select','label'=>"年",'value'=>date('Y')], $years) ?>
                    <?php endif; ?>
                </div>
                <div class = "sdakoku">
                    <?php if($kaimono["status"]  == 1 && !empty($kaimono["kessaibi"])): ?>
                        <?= $this->Form->control('kmonth',['type'=>'select','label'=>"月",'value'=>$bunkatsu2[0]], $months) ?>
                    <?php else: ?>
                        <?= $this->Form->control('kmonth',['type'=>'select','label'=>"月",'value'=>date('m')], $months) ?>
                    <?php endif; ?>
                </div>
                <div class = "sdakoku">
                    <?php if($kaimono["status"]  == 1 && !empty($kaimono["kessaibi"])): ?>
                        <?= $this->Form->control('kdate',['type'=>'text','label'=>"日",'value'=>$bunkatsu2[1]]) ?>
                    <?php else: ?>
                        <?= $this->Form->control('kdate',['type'=>'text','label'=>"日",'value'=>date('d')]) ?>
                    <?php endif; ?>
                </div>
                <div class = "w150">
                    <?php if(!empty($kaimono["kessaisha"])): ?>
                        <?php echo $this->Form->label("決裁者")?>
                        <?php echo $this->Form->select('kessaisha',$users2,['label' => "対象者",'type'=> 'select','value'=> $kaimono["kessaisha"]]);?>
                    <?php else: ?> 
                        <?php echo $this->Form->label("決裁者")?>
                        <?php echo $this->Form->select('kessaisha',$users2,['label' => "対象者",'type'=> 'select','value'=> $session]);?>
                    <?php endif; ?>
                </div>
                <div class="left_button2">
                    <?= $this->Form->button(__("登録")) ?>
                </div>
            </div>
    </fieldset>
        <?php endif; ?>
</div>