<?php
$this->assign('title', '業務日誌印刷');
?>

<!-- 本番環境にアップロードする場合はurlに注意する -->
<!-- ローカル環境：http://[::1]:8765/nisshis/pdf -->
<!-- 本番環境：https://www.nbg-rd.com/gw/nisshis/pdf -->

<?php if($pdf == 1): ?>
    <script language = javascript> 
        window.open("https://www.nbg-rd.com/gw/nisshis/pdf"); 
    </script>
<?php endif; ?>

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
    <h3><?= __('業務日誌印刷') ?></h3>
        <fieldset class = "field1">
            <?= $this -> Form -> create(__("View"),["type" => "post","url" => ["action" => "printout"]]); ?>
            <legend><?= __('対象日') ?></legend>    
                <div class = "odakoku mt10 ml10">
                    <div class = "sdakoku">
                        <?= $this->Form->control('year', ['type' => 'select', 'label' => "年", 'value' => date("y")], $years) ?>
                    </div>
                    <div class = "sdakoku">
                        <?= $this->Form->control('month', ['type' => 'select', 'label' => "月", 'value' => date("m")], $months) ?>
                    </div>
                    <div class = "sdakoku">
                        <?= $this->Form->control('sdate', ['type' => 'text', 'label' => '最初の日','value' => 1]) ?>
                    </div>
                    <div class = "sdakoku">
                        <?= $this->Form->control('edate', ['type' => 'text', 'label' => '最後の日','value' => date("t")]) ?>
                    </div>
                </div>
            <legend class = "mt10"><?= __('ユーザー') ?></legend>
                    <div class = "staffbox2 mt10 ml10">
                        <?php echo $this->Form->select('user_id',$users,array('id'=>'user','type'=> 'select'));?>
                    </div>
                    <div class="ml10_button">
                        <?= $this->Form->button(__("送信")) ?>
                    </div>
            <?= $this -> Form -> end(); ?>
            </div>
        </fieldset>