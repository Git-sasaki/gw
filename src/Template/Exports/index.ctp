<?php
$this -> assign("title","各種出力");
echo $this -> Html -> css("style.css");
$weekList = array("日","月","火","水","木","金","土");
?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
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
                    <?= $this->Html->link("各種出力", ["controller" => "Exports", "action" => "index"]); ?>
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
    </ul>
</nav>

<div class = "main">
    <div class="users index large-9 medium-8 columns content">
    <h3><?= __('各種出力') ?></h3>

        <fieldset class = "field1">    
            <legend><?= __('欠勤情報出力') ?></legend>
            <?= $this -> Form -> create(
            __("View"),
            ["type" => "post","url" => ["controller" => "Absents","action" => "excelexport"]]); ?>    
            <div class = "odakoku">
                <div class = "sdakoku">
                    <?= $this->Form->control('year', ['type' => 'select', 'label' => "年", 'value'=> date("Y")], $years) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('month', ['type' => 'select', 'label' => "月", 'value'=> date("m")], $months) ?>
                </div>
                <div class = "staffbox2">
                    <?php echo $this->Form->label("欠席者")?>
                    <?php echo $this->Form->select('user_id',$users,array('id'=>'user','label' => "対象者",'type'=> 'select','empty'=>array('0'=>'ALL')));?>
                </div>
            </div>
                <div class="absent_button">
                    <?= $this->Form->button(__("送信")) ?>
                </div>
            <?= $this -> Form -> end(); ?>
        </fieldset>

        <fieldset class = "field1">
            <?= $this -> Form -> create(
            __("View"),
            ["type" => "post","url" => ["action" => "service"]]); ?>    
                <legend><?= __('サービス提供記録出力') ?></legend>
                <div class = "odakoku">
                    <div class = "sdakoku">
                        <?= $this->Form->control('year', ['type' => 'select', 'label' => "年", 'value'=> date("Y")], $years) ?>
                    </div>
                    <div class = "sdakoku">
                        <?= $this->Form->control('month', ['type' => 'select', 'label' => "月", 'value'=> date("m")], $months) ?>
                    </div>
                    <div class = "staffbox2">
                        <?php echo $this->Form->label("対象者")?>
                        <?php echo $this->Form->select('user_id',$users,array('id'=>'user','label' => "対象者",'type'=> 'select'));?>
                    </div>
                </div>
                    <div class="absent_button">
                        <?= $this->Form->button(__("送信")) ?>
                    </div>
            <?= $this -> Form -> end(); ?>
        </fieldset>

        <fieldset class = "field1">
            <?= $this -> Form -> create(
            __("View"),
            ["type" => "post","url" => ["action" => "export"]]); ?>    
                <legend><?= __('CSV出力') ?></legend>
                <div class = "odakoku">
                    <div class = "sdakoku">
                        <?= $this->Form->control('year', ['type' => 'select', 'label' => "年", 'value'=> date("Y")], $years) ?>
                    </div>
                    <div class = "sdakoku">
                        <?= $this->Form->control('month', ['type' => 'select', 'label' => "月", 'value'=> date("m")], $months) ?>
                    </div>
                    <div class = "staffbox2">
                        <?php echo $this->Form->label("対象者")?>
                        <?php echo $this->Form->select('user_id',$users,array('id'=>'user','label' => "欠席者",'type'=> 'select'));?>
                    </div>
                </div>
                    <div class="absent_button">
                        <?= $this->Form->button(__("送信")) ?>
                    </div>
            <?= $this -> Form -> end(); ?>
        </fieldset>
</div>