<?php $this->assign('title', '基本設定'); ?>

<div class = "main1">
<h4 class = "titleh4 mt30">基本情報設定</h4>

<h4 class="midashih4 mt30 mb30">職員</h4>
<?= $this -> Form -> create(
    __("View"),
    ["type" => "post","url" => ["action" => "dregister"]]); ?>
<table class="table01 table02">
    <thead>
        <tr>
            <th scope="col" class = "w100">表示順</th>
            <th scope="col">名前</th>
            <th scope="col">出勤時間</th>
            <th scope="col">退勤時間</th>
            <th scope="col">休憩時間</th>
            <th scope="col" class = "w100">メニュー非表示</th>
        </tr>
    </thead>
    <tbody>
        <?php $narabi = 1; ?>
        <?php foreach($staffs as $staff): ?>
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
        <?php $narabi++; ?>
        <?php endforeach; ?>
    </tbody>
</table>
<div class="mt30 mlv48">
    <?= $this->Form->button(__("登録")) ?>
</div>
<?= $this -> Form -> end(); ?>

<h4 class="midashih4 mt30 mb30">利用者</h4>
<?= $this -> Form -> create(
    __("View"),
    ["type" => "post","url" => ["action" => "dregister"]]); ?>
<table class="table01 table02">
    <thead>
        <tr>
            <th scope="col" class = "w100">表示順</th>
            <th scope="col">名前</th>
            <th scope="col">出勤時間</th>
            <th scope="col">退勤時間</th>
            <th scope="col">休憩時間</th>
            <th scope="col" class = "w100">メニュー非表示</th>
        </tr>
    </thead>
    <tbody>
        <?php $narabi = 10; ?>
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
<div class = "odakoku" style = "justify-conent:center;">
    <div class="mt30 mlv48">
        <?= $this->Form->button(__("登録")) ?>
    </div>
    <div style = "margin-left:30px; padding-top:25px">10：1階出勤者　11：1階在宅勤務者　70：7階勤務者　71：7階在宅勤務者</div>
</div>
<?= $this -> Form -> end(); ?>

<h4 class="midashih4 mt30 mb30">退職者</h4>
<?= $this -> Form -> create(
    __("View"),
    ["type" => "post","url" => ["action" => "dregister"]]); ?>
<table class="table01 table02">
    <thead>
        <tr>
            <th scope="col" class = "w100">表示順</th>
            <th scope="col">名前</th>
            <th scope="col">出勤時間</th>
            <th scope="col">退勤時間</th>
            <th scope="col">休憩時間</th>
            <th scope="col" class = "w100">メニュー非表示</th>
        </tr>
    </thead>
    <tbody>
        <?php $narabi = 999; ?>
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
                    echo $this -> Form -> control("display[$id]",array("type" => "checkbox","label" => false,"value" => 1));
                }else{
                    echo $this -> Form -> control("display[$id]",array("type" => "checkbox","label" => false,"value" => 1,"checked" => true));
                }?></td>
        </tr>
        <?php $narabi++ ?>
        <?php endforeach; ?>
    </tbody>
</table>
<div class="mt30 mlv48">
    <?= $this->Form->button(__("登録")) ?>
</div>
<?= $this -> Form -> end(); ?>
<br>