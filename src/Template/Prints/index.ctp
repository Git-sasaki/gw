<?php
$this -> assign("title","出勤簿印刷");
echo $this -> Html -> css("style.css");
$weekList = array("日","月","火","水","木","金","土");
?>

<!-- 本番環境にアップロードする場合はurlに注意する -->
<!-- ローカル環境：http://[::1]:8765/prints/updf　または　http://[::1]:8765/prints/spdf -->
<!-- 本番環境：https://www.nbg-rd.com/gw/prints/updf　または　https://www.nbg-rd.com/gw/prints/spdf -->

<?php if($updf == 1): ?>
    <script language = javascript> 
        window.open("https://www.nbg-rd.com/gw/prints/updf"); 
    </script>
<?php endif; ?>

<?php if($spdf == 1): ?>
    <script language = javascript> 
        window.open("https://www.nbg-rd.com/gw/prints/spdf"); 
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

<div class="main">
<h3 class="attendance-header2">出勤簿印刷</h3>
    <fieldset class = "field1">
        <?= $this -> Form -> create(__("View"),["type" => "post","url" => ["action" => "getquery0"]]); ?>
        <legend><?= __('利用者') ?></legend>
        <div class = "odakoku mt10 ml10">
            <div class = "sdakoku">
                <?php if(!empty($uyear)): ?>
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>$uyear], $years) ?>
                <?php else: ?>
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>date("Y")], $years) ?>
                <?php endif; ?>
            </div>
            <div class = "sdakoku">
                <?php if(!empty($umonth)): ?>
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>$umonth], $months) ?>
                <?php else: ?>
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>date("m")], $months) ?>
                <?php endif; ?>
            </div>
            <div class = "staffbox2">
                <?php if(!empty($suser)): ?>
                    <?php echo $this->Form->label("名前")?>
                    <?= $this->Form->select('user',$users,array('id'=>'user','type'=>'select','empty'=>array('0'=>'ALL'),'value'=>$suser));?>
                <?php else: ?>
                    <?php echo $this->Form->label("名前")?>
                    <?= $this->Form->select('user',$users,array('id'=>'user','type'=>'select','empty'=>array('0'=>'ALL')));?>
                <?php endif; ?>
            </div>
        </div>
        <div class="ml10_button">
            <?= $this->Form->button(__("送信")) ?>
        </div>
        <?= $this -> Form -> end(); ?>
    </fieldset>

    <fieldset class = "field1">
        <?= $this -> Form -> create(__("View"),["type" => "post","url" => ["action" => "getquery1"]]); ?>
        <legend><?= __('職員') ?></legend>
        <div class = "odakoku mt10 ml10">
            <div class = "sdakoku">
            <?php if(!empty($syear)): ?>
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>$syear], $years) ?>
                <?php else: ?>
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>date("Y")], $years) ?>
                <?php endif; ?>
            </div>
            <div class = "sdakoku">
                <?php if(!empty($smonth)): ?>
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>$smonth], $months) ?>
                <?php else: ?>
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>date("m")], $months) ?>
                <?php endif; ?>
            </div>
            <div class = "staffbox2">
            <?php if(!empty($sstaff)): ?>
                    <?php echo $this->Form->label("名前")?>
                    <?= $this->Form->select('staff',$staffs,array('id'=>'user','type'=>'select','empty'=>array('0'=>'ALL'),'value'=>$sstaff));?>
                <?php else: ?>
                    <?php echo $this->Form->label("名前")?>
                    <?= $this->Form->select('staff',$staffs,array('id'=>'user','type'=>'select','empty'=>array('0'=>'ALL')));?>
                <?php endif; ?>
            </div>
        </div>
        <div class="ml10_button">
            <?= $this->Form->button(__("送信")) ?>
        </div>
        <?= $this -> Form -> end(); ?>
    </fieldset>
</div>
