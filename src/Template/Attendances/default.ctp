<?php
$this->assign('title', 'デフォルト値の設定');
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
    <h3><?= __('デフォルト値の設定') ?></h3>

    <fieldset>
        <legend><?= __('職員') ?></legend>
    <?= $this -> Form -> create(
        __("View"),
        ["type" => "post","url" => ["action" => "dregister"]]); ?>
    <table class="table01 table02">
        <thead>
            <tr>
                <th scope="col" class = "w100"><?= $this->Paginator->sort('narabi', $title = '表示順') ?></th>
                <th scope="col"><?= $this->Paginator->sort('name', $title = '名前') ?></th>
                <th scope="col"><?= $this->Paginator->sort('dintime', $title = '出勤時間') ?></th>
                <th scope="col"><?= $this->Paginator->sort('douttime', $title = '退勤時間') ?></th>
                <th scope="col"><?= $this->Paginator->sort('dresttime', $title = '休憩時間') ?></th>
                <th scope="col" class = "w100"><?= $this->Paginator->sort('display', $title = 'メニュー非表示') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $narabi = 1; ?>
            <?php foreach ($staffs as $staff): ?>
            <tr>
                <?php $id = $staff["id"]; ?>
                <td><?php if(!empty($staff["narabi"])): ?>
                        <?php echo $this -> Form -> control("narabi[$id]",array("type" => "text","label" => false,"value" => $staff["narabi"]));?>
                    <?php else: ?>
                        <?php echo $this -> Form -> control("narabi[$id]",array("type" => "text","label" => false,"value" => $narabi));?>
                    <? endif; ?></td>
                <td><?= h($staff["name"]) ?></td>
                <td><?php if(empty($staff["dintime"])) {
                        echo $this->Form->text("dintime[$id]",["type" => "time","class" => "time"]);
                    } else {
                        echo $this->Form->text("dintime[$id]",["type" => "time","class" => "time","value" => $staff["dintime"]->i18nFormat("HH:mm")]);
                    } ?></td>
                <td><?php if(empty($staff["douttime"])) {
                        echo $this->Form->text("douttime[$id]",["type" => "time","class" => "time"]);
                    } else {
                        echo $this->Form->text("douttime[$id]",["type" => "time","class" => "time","value" => $staff["douttime"]->i18nFormat("HH:mm")]);                      
                    } ?></td>
                <td><?php if(empty($staff["dresttime"])) {
                        echo $this->Form->text("dresttime[$id]",["type" => "time","class" => "time"]);
                    } else {
                        echo $this->Form->text("dresttime[$id]",["type" => "time","class" => "time","value" => $staff["dresttime"]->i18nFormat("HH:mm")]);
                    } ?></td>
                <td><?php if(empty($staff["display"])){
                        echo $this -> Form -> control("display[$id]",array("type" => "checkbox","label" => false,"value" => 1));
                    }else{
                        echo $this -> Form -> control("display[$id]",array("type" => "checkbox","label" => false,"value" => 1,"checked" => true));
                    }?></td>
            </tr>
            <?php $narabi++ ?>
            <?php endforeach; ?>
        </tbody>
    </table>
<br>
    <div class="vw62">
        <?= $this->Form->button(__("登録")) ?>
    </div>
    <?= $this -> Form -> end(); ?>
    </fieldset>

    <fieldset>
        <legend><?= __('利用者') ?></legend>
    <?= $this -> Form -> create(
        __("View"),
        ["type" => "post","url" => ["action" => "dregister"]]); ?>
    <table class="table01 table02">
        <thead>
            <tr>
                <th scope="col" class = "w100"><?= $this->Paginator->sort('narabi', $title = '表示順') ?></th>
                <th scope="col"><?= $this->Paginator->sort('name', $title = '名前') ?></th>
                <th scope="col"><?= $this->Paginator->sort('dintime', $title = '出勤時間') ?></th>
                <th scope="col"><?= $this->Paginator->sort('douttime', $title = '退勤時間') ?></th>
                <th scope="col"><?= $this->Paginator->sort('dresttime', $title = '休憩時間') ?></th>
                <th scope="col" class = "w100"><?= $this->Paginator->sort('display', $title = 'メニュー非表示') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $narabi = 1; ?>
            <?php foreach ($users as $user): ?>
            <tr>
                <?php $id = $user["id"]; ?>
                <td><?php if(!empty($user["narabi"])): ?>
                        <?php echo $this -> Form -> control("narabi[$id]",array("type" => "text","label" => false,"value" => $user["narabi"]));?>
                    <?php else: ?>
                        <?php echo $this -> Form -> control("narabi[$id]",array("type" => "text","label" => false,"value" => $narabi));?>
                    <? endif; ?></td>
                <td><?= h($user["name"]) ?></td>
                <td><?php if(empty($user["dintime"])) {
                        echo $this->Form->text("dintime[$id]",["type" => "time","class" => "time"]);
                    } else {
                        echo $this->Form->text("dintime[$id]",["type" => "time","class" => "time","value" => $user["dintime"]->i18nFormat("HH:mm")]);
                    } ?></td>
                <td><?php if(empty($user["douttime"])) {
                        echo $this->Form->text("douttime[$id]",["type" => "time","class" => "time"]);
                    } else {
                        echo $this->Form->text("douttime[$id]",["type" => "time","class" => "time","value" => $user["douttime"]->i18nFormat("HH:mm")]);                      
                    } ?></td>
                <td><?php if(empty($user["dresttime"])) {
                        echo $this->Form->text("dresttime[$id]",["type" => "time","class" => "time"]);
                    } else {
                        echo $this->Form->text("dresttime[$id]",["type" => "time","class" => "time","value" => $user["dresttime"]->i18nFormat("HH:mm")]);
                    } ?></td>
                <td><?php if(empty($user["display"])){
                        echo $this -> Form -> control("display[$id]",array("type" => "checkbox","label" => false,"value" => 1));
                    }else{
                        echo $this -> Form -> control("display[$id]",array("type" => "checkbox","label" => false,"value" => 1,"checked" => true));
                    }?></td>
            </tr>
            <?php $narabi++ ?>
            <?php endforeach; ?>
        </tbody>
    </table>
<br>
    <div class="vw62">
        <?= $this->Form->button(__("登録")) ?>
    </div>
    <?= $this -> Form -> end(); ?>
    </fieldset>

    <fieldset>
        <legend><?= __('退職者') ?></legend>
    <?= $this -> Form -> create(
        __("View"),
        ["type" => "post","url" => ["action" => "dregister"]]); ?>
    <table class="table01 table02">
        <thead>
            <tr>
                <th scope="col" class = "w100"><?= $this->Paginator->sort('narabi', $title = '表示順') ?></th>
                <th scope="col"><?= $this->Paginator->sort('name', $title = '名前') ?></th>
                <th scope="col"><?= $this->Paginator->sort('dintime', $title = '出勤時間') ?></th>
                <th scope="col"><?= $this->Paginator->sort('douttime', $title = '退勤時間') ?></th>
                <th scope="col"><?= $this->Paginator->sort('dresttime', $title = '休憩時間') ?></th>
                <th scope="col" class = "w100"><?= $this->Paginator->sort('display', $title = 'メニュー非表示') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $narabi = 1; ?>
            <?php foreach ($retires as $retire): ?>
            <tr>
                <?php $id = $retire["id"]; ?>
                <td><?php if(!empty($retire["narabi"])): ?>
                        <?php echo $this -> Form -> control("narabi[$id]",array("type" => "text","label" => false,"value" => $retire["narabi"]));?>
                    <?php else: ?>
                        <?php echo $this -> Form -> control("narabi[$id]",array("type" => "text","label" => false,"value" => $narabi));?>
                    <? endif; ?></td>
                <td><?= h($retire["name"]) ?></td>
                <td><?php if(empty($retire["dintime"])) {
                        echo $this->Form->text("dintime[$id]",["type" => "time","class" => "time"]);
                    } else {
                        echo $this->Form->text("dintime[$id]",["type" => "time","class" => "time","value" => $retire["dintime"]->i18nFormat("HH:mm")]);
                    } ?></td>
                <td><?php if(empty($retire["douttime"])) {
                        echo $this->Form->text("douttime[$id]",["type" => "time","class" => "time"]);
                    } else {
                        echo $this->Form->text("douttime[$id]",["type" => "time","class" => "time","value" => $retire["douttime"]->i18nFormat("HH:mm")]);                      
                    } ?></td>
                <td><?php if(empty($retire["dresttime"])) {
                        echo $this->Form->text("dresttime[$id]",["type" => "time","class" => "time"]);
                    } else {
                        echo $this->Form->text("dresttime[$id]",["type" => "time","class" => "time","value" => $retire["dresttime"]->i18nFormat("HH:mm")]);
                    } ?></td>
                <td><?php if(empty($retire["display"])){
                        echo $this -> Form -> control("display[$id]",array("type" => "checkbox","label" => false,"value" => 1,"checked" => true));
                    }else{
                        echo $this -> Form -> control("display[$id]",array("type" => "checkbox","label" => false,"value" => 1,"checked" => true));
                    }?></td>
            </tr>
            <?php $narabi++ ?>
            <?php endforeach; ?>
        </tbody>
    </table>
<br>
    <div class="vw62">
        <?= $this->Form->button(__("登録")) ?>
    </div>
    <?= $this -> Form -> end(); ?>
    </fieldset>