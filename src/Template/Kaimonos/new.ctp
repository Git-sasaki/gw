<?php
$this->assign('title', '新規作成');
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
    <h3><?= __('物品購入届') ?></h3>

    <?= $this->Form->create(__("View"),["type"=>"post","url"=>["action"=>"register"]]) ?>
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
                $attributes = ['name'=>'type','value'=>'0']; 
                echo $this->Form->radio('type', $options, $attributes);
            ?>
        </div>

        <div class = "pdakoku">
            <div class = "sdakoku">
                <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>date("y")], $years) ?>
            </div>
            <div class = "sdakoku">
                <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>date("n")], $months) ?>
            </div>
            <div class = "sdakoku">
                <?= $this->Form->control('date',['type'=>'text','label'=>"日",'value'=>date("d")]) ?>
            </div>
            <div class = "w150">
                <?= $this->Form->control('user_id',['type'=>'select','label'=>"購入者",'value'=>$session], $users) ?>
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
                $attributes = ['name'=>'payment','value'=>'0']; 
                echo $this->Form->radio('payment', $options, $attributes);
            ?>
        </div>

        <div class = "odakoku">
            <div class = "w200">
                <?= $this->Form->control('cinnamon',['type'=>'text','label'=>"購入品名"]) ?>
            </div>
            <div class = "w200">
                <?= $this->Form->control('shop',['type'=>'text','label'=>"購入先"]) ?>
            </div>
            <div class = "w250">
                <?= $this->Form->control('price',['type'=>'text','label'=>"金額 (半角数字のみで入力)"]) ?>
            </div>
        </div>

        <div class = "ml10">
            <div class = "w500">
                <?= $this->Form->control('url1', ['type' => 'text', 'label' => 'URL']); ?>
            </div>
            <div class = "w500">
                <?= $this->Form->control('url2', ['type' => 'text', 'label' => 'URL']); ?>
            </div>
            <div class = "w500">
                <?= $this->Form->control('url3', ['type' => 'text', 'label' => 'URL']); ?>
            </div>
        </div>

        <div class = "pdakoku">
            <div class = "w500">
                <?= $this->Form->control('bikou', ['type' => 'text', 'label' => '備考']); ?>
            </div>
            <div class="left_button">
                <?= $this->Form->button(__("登録")) ?>
            </div>
            <?= $this -> Form -> end(); ?>
        </div>
    </fieldset>
</div>