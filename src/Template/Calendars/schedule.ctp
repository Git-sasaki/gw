<?php
$this->assign('title', 'カレンダー');
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

<div class="users index large-9 medium-8 columns content">
    <div class = "odakoku">
        <h3><?= __($year.' 年 '.$month.' 月 '.$date.' 日 ('.$weekList[date('w',$timestamp)].')') ?></h3>
        <!-- <?= $this->Form->postLink(__('[削除]'), 
                        ['action' => 'delete', $result["id"]], 
                        ['class' => 'vw60'],
                        ['confirm' => __('本当に削除しますか？ # {0}?', $result["id"])]
                        ) ?> -->
    </div>
        <?= $this->Form->create(__("View"),["type"=>"post","url"=>["action"=>"register",'?'=>["year"=>$year,"month"=>$month,"date"=>$date]]]) ?>
        <fieldset>
            <legend><?= __('予定を入力') ?></legend>
        <div class = "ml10">
            <div class = "w500">
                <?php if(empty($result["plana"])): ?>
                    <?= $this->Form->control('plana', ['type' => 'text', 'label' => '予定1']); ?>
                <?php else: ?>
                    <?= $this->Form->control('plana', ['type' => 'text', 'label' => '予定1', 'value'=>$result["plana"]]); ?>
                <?php endif; ?>
            </div>
            <div class = "w500">
                <?php if(empty($result["planb"])): ?>
                    <?= $this->Form->control('planb', ['type' => 'text', 'label' => '予定2']); ?>
                <?php else: ?>
                    <?= $this->Form->control('planb', ['type' => 'text', 'label' => '予定2', 'value'=>$result["planb"]]); ?>
                <?php endif; ?>
            </div>
            <div class = "w500">
                <?php if(empty($result["planc"])): ?>
                    <?= $this->Form->control('planc', ['type' => 'text', 'label' => '予定3']); ?>
                <?php else: ?>
                    <?= $this->Form->control('planc', ['type' => 'text', 'label' => '予定3', 'value'=>$result["planc"]]); ?>
                <?php endif; ?>
            </div>
        </div>
        <div class = "ml10">
            <div class = "w500">
                <?php if(empty($result["memo"])): ?>
                    <?= $this->Form->control('memo', ['type' => 'textarea', 'label' => 'その他メモなど']); ?>
                <?php else: ?>
                    <?= $this->Form->control('memo', ['type' => 'textarea', 'label' => 'その他メモなど', 'value'=>$result["memo"]]); ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="absent_button">
                <?= $this->Form->button(__("登録")) ?>
            </div>
            <?= $this -> Form -> end(); ?>
        </div>
</div>
