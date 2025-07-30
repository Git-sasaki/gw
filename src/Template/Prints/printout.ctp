<?php
$this -> assign("title","出勤簿印刷");
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
    </ul>
</nav>

<div class = "main">
<h3 class="attendance-header">出勤簿印刷</h3>

    <?= $this -> Form -> create(
        __("View"),
        ["type" => "get","url" => ["controller" => "TimeCards", "action" => "pdf", "?" => array("id" => $staff_id, "year" => $year, "month" => $month)]],
        array('target' => '_blank')); ?>
    
    <div class = "boxblock">
        <fieldset>
            <legend><?= __('対象年月') ?></legend>
            <div class = "datebox">
            <!-- セレクトボックス：$this->Form->select(1：fieldname,2：セレクトボックスの値,3：属性) -->
            <?= $this->Form->select('year',$years,array('id'=>'getYear','label' => false,'value'=>$year,'empty'=>true));?>　年　　
            <?= $this->Form->select('month',$months,array('id'=>'getMonth','label' => false,'value'=>$month,'empty'=>true));?>　月　　
        </div>
        </fieldset>
    </div>

    <div class = "boxblock">
        <fieldset>
        <legend><?= __('ユーザー') ?></legend>
        <div class = "staffbox">
            <?= $this->Form->select('staff_id',$staffs,array('id'=>'staff_id','label' => false,'value'=>$staff_id,'empty'=>array('0'=>'ALL')));?>
        </div>
        </fieldset>
    </div>

    <div class="print_button">
        <?= $this->Form->button(__("印刷")) ?>
    </div>
    <?= $this -> Form -> end(); ?>
</div>