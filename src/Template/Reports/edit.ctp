<?php
$this->assign('title', '編集');
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

<div class="users index columns content report">
    <h3 class="edit-header">作業日報 - 編集</h3>
        <div class="report">
        <?= $this->Form->create(null, ['type' => 'post', 'url' => ['controller' => 'Reports', 'action' => 'update', "?" => array("id" => $user_id, "year" => $year, "month" => $month, "date" => $date)]]) ?>
        <fieldset>
            <legend><?= $defaults["name"] . "さん　".$year."年".$month."月".$date."日(".$weekList[date("w",$postdate2)].")"?></legend>
            <div class = "odakoku">
                <div class = "dakoku">
                <?php 
                    if(!empty($rep["intime"])) {
                        echo $this->Form->label("出勤時間");
                        echo $this->Form->text("intime",["type" => "time","value" => $rep["intime"]->i18nFormat("HH:mm")]);
                    } elseif (!empty($defaults["dintime"])) {
                        echo $this->Form->label("出勤時間");
                        echo $this->Form->text("intime",["type" => "time","value" => $defaults["dintime"]->i18nFormat("HH:mm")]);
                    } else {
                        echo $this->Form->label("出勤時間");
                        echo $this->Form->text("intime",["type" => "time"]);
                    } ?>
                </div>
                <div class = "dakoku">
                <?php 
                    if(!empty($rep["outtime"])) {
                        echo $this->Form->label("退勤時間");
                        echo $this->Form->text("outtime",["type" => "time","value" => $rep["outtime"]->i18nFormat("HH:mm")]);
                    } elseif (!empty($defaults["douttime"])) {
                        echo $this->Form->label("退勤時間");
                        echo $this->Form->text("outtime",["type" => "time","value" => $defaults["douttime"]->i18nFormat("HH:mm")]);
                    } else {
                        echo $this->Form->label("退勤時間");
                        echo $this->Form->text("outtime",["type" => "time"]);
                    } ?>
                </div>
                <div class = "dakoku">
                <?php 
                    if(!empty($rep["resttime"])) {
                        echo $this->Form->label("休憩時間");
                        echo $this->Form->text("resttime",["type" => "time","value" => $rep["resttime"]->i18nFormat("HH:mm")]);
                    } elseif (!empty($defaults["dresttime"])) {
                        echo $this->Form->label("休憩時間");
                        echo $this->Form->text("resttime",["type" => "time","value" => $defaults["dresttime"]->i18nFormat("HH:mm")]);
                    } else {
                        echo $this->Form->label("休憩時間");
                        echo $this->Form->text("resttime",["type" => "time"]);
                    } ?>
                </div>
            </div>

            <?php if(empty($rep["content"])) {
                echo $this->Form->control('content', ['type' => 'textarea', 'label' => '業務内容']);  
            } else {
                echo $this->Form->control('content', ['type' => 'textarea', 'label' => '業務内容', 'value' => $rep["content"]]);
            }?>

            <div class = "odakoku">
                <div class = "yayahiro">
                    <?php if(empty($red[0]["item"])) {
                        echo $this->Form->control('item0', ['type' => 'text', 'label' => '項目']);  
                    } else {
                        echo $this->Form->control('item0', ['type' => 'text', 'label' => '項目', 'value' => $red[0]["item"]]);
                    }?>
                </div>
                <div class = "waritohiro">
                    <?php if(empty($red[0]["reportcontent"])) {
                        echo $this->Form->control('reportcontent0', ['type' => 'text', 'label' => '内容']);  
                    } else {
                        echo $this->Form->control('reportcontent0', ['type' => 'text', 'label' => '内容', 'value' => $red[0]["reportcontent"]]);
                    }?>
                </div>               
            </div>
            <div class = "odakoku">
                <div class = "yayahiro">
                    <?php if(empty($red[1]["item"])) {
                        echo $this->Form->control('item1', ['type' => 'text', 'label' => '項目']);  
                    } else {
                        echo $this->Form->control('item1', ['type' => 'text', 'label' => '項目', 'value' => $red[1]["item"]]);
                    }?>
                </div>
                <div class = "waritohiro">
                    <?php if(empty($red[1]["reportcontent"])) {
                        echo $this->Form->control('reportcontent1', ['type' => 'text', 'label' => '内容']);  
                    } else {
                        echo $this->Form->control('reportcontent1', ['type' => 'text', 'label' => '内容', 'value' => $red[1]["reportcontent"]]);
                    }?>
                </div>               
            </div>
            <div class = "odakoku">
                <div class = "yayahiro">
                    <?php if(empty($red[2]["item"])) {
                        echo $this->Form->control('item2', ['type' => 'text', 'label' => '項目']);  
                    } else {
                        echo $this->Form->control('item2', ['type' => 'text', 'label' => '項目', 'value' => $red[2]["item"]]);
                    }?>
                </div>
                <div class = "waritohiro">
                    <?php if(empty($red[2]["reportcontent"])) {
                        echo $this->Form->control('reportcontent2', ['type' => 'text', 'label' => '内容']);  
                    } else {
                        echo $this->Form->control('reportcontent2', ['type' => 'text', 'label' => '内容', 'value' => $red[2]["reportcontent"]]);
                    }?>
                </div>               
            </div>

            <?php if(empty($rep["notice"])) {
                echo $this->Form->control('notice', ['type' => 'textarea', 'label' => '反省・特記事項']);  
            } else {
                echo $this->Form->control('notice', ['type' => 'textarea', 'label' => '反省・特記事項', 'value' => $rep["notice"]]);
            }?>
            <?php if(empty($rep["plan"])) {
                echo $this->Form->control('plan', ['type' => 'textarea', 'label' => '次回の予定']);  
            } else {
                echo $this->Form->control('plan', ['type' => 'textarea', 'label' => '次回の予定', 'value' => $rep["plan"]]);
            }?>

            <?php if($this->request-> getSession()->read('Auth.User.adminfrag') == 1): ?>
                <h4 class="legend"><?= __('業務日誌') ?></h4>
                <?php if(empty($rep["recorder"])) {
                    echo $this->Form->label("記録者");
                    echo $this->Form->select('recorder',$admresults,array('label' => false,'value' => $auser_id)); 
                } else {
                    echo $this->Form->label("記録者");
                    echo $this->Form->select('recorder',$admresults,array('label' => false,'value' => $staff_id)); 
                }?>
                <?php if(empty($rep["state"])) {
                    echo $this->Form->control('state', ['type' => 'textarea', 'label' => '業務内容・様子']);  
                } else {
                    echo $this->Form->control('state', ['type' => 'textarea', 'label' => '業務内容・様子', 'value' => $rep['state']]);
                }?>
                <?php if(empty($rep["information"])) {
                    echo $this->Form->control('information', ['type' => 'textarea', 'label' => '体調・連絡事項など']);  
                } else {
                    echo $this->Form->control('information', ['type' => 'textarea', 'label' => '体調・連絡事項など', 'value' => $rep['information']]);
                }?>
                <?php if(empty($rep["bikou"])) {
                    echo $this->Form->control('bikou', ['type' => 'textarea', 'label' => '備考']);  
                } else {
                    echo $this->Form->control('bikou', ['type' => 'textarea', 'label' => '備考', 'value' => $rep['bikou']]);
                }?>
            <?php endif; ?>
            </fieldset>

        <div class="mlv647_button">
            <?= $this->Form->button(__("送信")) ?>
        </div>
        <?php $this->Form->end(); ?>
    </div>
</div>