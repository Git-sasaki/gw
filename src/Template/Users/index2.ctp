<?php
$this -> assign("title","作業日報編集");
echo $this -> Html -> css("sytle.css");
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
        </li>
    </ul>
</nav>

<div class = "main">
<h3 class="attendance-header">作業日報編集</h3>

    <?= $this -> Form -> create(
        __("View"),
        ["type" => "get","url" => ["controller" => "Users", "action" => "report", "?" => array("id" => $user_id, "year" => $year, "month" => $month, "date" => $date)],
        array('target' => '_blank')]); ?>

    <fieldset>
        <legend><?= __('対象年月') ?></legend>
        <div class = "odakoku ml10">
            <div class = "sdakoku">
                <?= $this->Form->control('year', ['type' => 'select', 'label' => "年", 'value'=> date("Y")], $years) ?>
            </div>
            <div class = "sdakoku">
                <?= $this->Form->control('month', ['type' => 'select', 'label' => "月", 'value'=> date("m")], $months) ?>
            </div>
            <div class = "sdakoku">
                <?= $this->Form->control('date', ['type' => 'text', 'label' => '日', 'value'=> date("d")]) ?>
            </div>
        </div>
        
        <div class="ml10_button">
            <?= $this->Form->button(__("表示")) ?>
        </div>
        <?= $this -> Form -> end(); ?>
    </fieldset>