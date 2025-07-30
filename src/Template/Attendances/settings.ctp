<?php $this->assign('title', '基本設定'); ?>

<div class = "main1">
    <h4 class = "titleh4 mt30">基本情報設定</h4>

    <h4 class="midashih4 mt30 mb30">職員</h4>
        <?= $this -> Form -> create(
            __("View"),
            ["type" => "post","url" => ["action" => "default"]]); ?>
        <?= $this->Form->control('type',['type'=>'hidden','value'=>1]) ?>
        <table class="table01 table02">
            <thead>
                <tr>
                    <th scope="col" style = "width:75px">表示順</th>
                    <th scope="col" style = "width:220px">名前</th>
                    <th scope="col">出勤時間</th>
                    <th scope="col">退勤時間</th>
                    <th scope="col">休憩時間</th>
                    <th scope="col" style = "width:87px">メニュー非表示</th>
                </tr>
            </thead>
            <tbody>
                <?php $narabi = 1; ?>
                <?php foreach($staffs as $staff): ?>
                    <tr>
                        <?php $id = $staff["id"]; ?>
                        <td><?php if(!empty($staff["narabi"])): ?>
                                <?php echo $this->Form->control("narabi[$id]",["type" => "text","label" => false,"value" => $staff["narabi"]]);?>
                            <?php else: ?>
                                <?php echo $this->Form->control("narabi[$id]",["type" => "text","label" => false,"value" => $narabi]);?>
                            <?php endif; ?></td>
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
                                echo $this->Form->control("display[$id]",["type" => "checkbox","label" => false,"value" => 1]);
                            }else{
                                echo $this->Form->control("display[$id]",["type" => "checkbox","label" => false,"value" => 1,"checked" => true]);
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
            ["type" => "post","url" => ["action" => "default"]]); ?>
        <?= $this->Form->control('type',['type'=>'hidden','value'=>0]) ?>
        <table class="table01 table02">
            <thead>
                <tr>
                    <th scope="col" style = "width:75px">表示順</th>
                    <th scope="col" style = "width:170px">名前</th>
                    <th scope="col">出勤時間</th>
                    <th scope="col">退勤時間</th>
                    <th scope="col">休憩時間</th>
                    <th scope="col" style = "width:170px">勤務地</th>
                    <th scope="col" style = "width:87px">在宅勤務</th>
                    <th scope="col" style = "width:87px">メニュー非表示</th>
                </tr>
            </thead>
            <tbody>
                <?php $narabi = 10; ?>
                <?php foreach ($users as $user): ?>
                <tr>
                    <?php $id = $user["id"]; ?>
                    <td><?php if(!empty($user["narabi"])): ?>
                            <?php echo $this->Form->control("narabi[$id]",["type" => "text","label" => false,"value" => $user["narabi"]]);?>
                        <?php else: ?>
                            <?php echo $this->Form->control("narabi[$id]",["type" => "text","label" => false,"value" => $narabi]);?>
                        <?php endif; ?></td>
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
                    <td><?php if(empty($user["workplace"])) {
                            echo $this->Form->select("workplace[$id]",$getPlaces);
                        } else {
                            echo $this->Form->select("workplace[$id]",$getPlaces,['value'=>$user["workplace"]]);
                        } ?></td>
                    <td><?php if(empty($user["remote"]) || $user["remote"] == 0){
                            echo $this->Form->control("remote[$id]",["type" => "checkbox","label" => false,"value" => 1]);
                        }else{
                            echo $this->Form->control("remote[$id]",["type" => "checkbox","label" => false,"value" => 1,"checked" => true]);
                        }?></td>
                    <td><?php if(empty($user["display"]) || $user["display"] == 0){
                            echo $this->Form->control("display[$id]",["type" => "checkbox","label" => false,"value" => 1]);
                        }else{
                            echo $this->Form->control("display[$id]",["type" => "checkbox","label" => false,"value" => 1,"checked" => true]);
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

    <h4 class="midashih4 mt30">退職者</h4>
        <?= $this -> Form -> create(
            __("View"),
            ["type" => "post","url" => ["action" => "default"]]); ?>
        <?= $this->Form->control('type',['type'=>'hidden','value'=>2]) ?>       
        <div class = "odakoku" style = "padding:0 2.6vw">
            <table class="table01 table03">
                <thead>
                    <tr>
                        <th scope="col" style = "width:75px">表示順</th>
                        <th scope="col" style = "width:220px">名前</th>
                        <th scope="col" style = "width:87px">メニュー非表示</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for($i=0; $i<$half; $i++): ?>
                        <?php $id = $retires[$i]["id"]; ?>
                        <tr>
                            <td>
                                <?php if(!empty($retires[$i]["narabi"])) {
                                    echo $this->Form->control("narabi[$id]",["type" => "text","label" => false,"value" => $retires[$i]["narabi"]]);
                                } else {
                                    echo $this->Form->control("narabi[$id]",["type" => "text","label" => false,"value" => $narabi]);
                                } ?>
                            </td>
                            <td><?= h($retires[$i]["name"]) ?></td>
                            <td>
                                <?php if(empty($retires[$i]["display"])){
                                    echo $this->Form->control("display[$id]",["type" => "checkbox","label" => false,"value" => 1]);
                                }else{
                                    echo $this->Form->control("display[$id]",["type" => "checkbox","label" => false,"value" => 1,"checked" => true]);
                                }?>
                            </td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
            <table class="table01 table03">
                <thead>
                    <tr>
                        <th scope="col" style = "width:75px">表示順</th>
                        <th scope="col" style = "width:220px">名前</th>
                        <th scope="col" style = "width:87px">メニュー非表示</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for($i=$half; $i<=count($retires)-1; $i++): ?>
                        <?php $id = $retires[$i]["id"]; ?>
                        <tr>
                            <td>
                                <?php if(!empty($retires[$i]["narabi"])) {
                                    echo $this->Form->control("narabi[$id]",["type" => "text","label" => false,"value" => $retires[$i]["narabi"]]);
                                } else {
                                    echo $this->Form->control("narabi[$id]",["type" => "text","label" => false,"value" => $narabi]);
                                } ?>
                            </td>
                            <td><?= h($retires[$i]["name"]) ?></td>
                            <td>
                                <?php if(empty($retires[$i]["display"])){
                                    echo $this->Form->control("display[$id]",["type" => "checkbox","label" => false,"value" => 1]);
                                }else{
                                    echo $this->Form->control("display[$id]",["type" => "checkbox","label" => false,"value" => 1,"checked" => true]);
                                }?>
                            </td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>

        <div class = "mt30 mlv48">
            <?= $this->Form->button(__("登録")) ?>
        </div>
        <?= $this -> Form -> end(); ?>
        <br>