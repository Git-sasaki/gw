<?php
$this->assign('title', '欠席情報一覧');
$weekList = array("日", "月", "火", "水", "木", "金", "土");
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
    <h3><?= $name."さんの欠席情報" ?> </h3>

    <table class="table01 table02">
        <tr>
            <th class="caption date">日付</td>
            <td colspan="2"><?= $mitai['date']->i18nFormat('yyyy/MM/dd') ?></td>
        </tr>
        <tr>
            <th class="caption date">時間</td>
            <td colspan="2"><?= $mitai['time']->i18nFormat('H:mm'); ?></td>
        </tr>
        <tr>
            <th class="caption date">受けた人</td>
            <td colspan="2"><?= $staffname ?></td>
        </tr>
        <tr>
            <th class="caption date">連絡者</td>
            <td colspan="2"><?= $mitai['relation'] ?></td>
        </tr>
        <tr>
            <th class="caption date">連絡手段</td>
            <td colspan="2"><?= $mitai['shudan'] ?></td>
        </tr>
        <tr>
            <th class="caption date">内容</td>
            <td colspan="2"><?= $mitai['naiyou'] ?></td>
        </tr>
        <tr>
            <th class="caption date">次回利用の促し</td>
            <td colspan="2"><?= $okona[$mitai["next"]] ?></td>
        </tr>
        <tr>
            <th class="caption date">相手の回答1</td>
            <td colspan="2"><?= $mitai['answer1'] ?></td>
        </tr>
        <tr>
            <th class="caption date">相手の回答2</td>
            <?php if(empty($mitai['answer2'])){
                $answer2 = "---";
            } else {
                $answer2 = $mitai['answer2']; 
            }
            ?>
            <td colspan="2"><?= $answer2 ?></td>
        </tr>
        <tr>
            <th class="caption date">相手の回答3</td>
            <?php if(empty($mitai['answer3'])){
                $answer3 = "---";
            } else {
                $answer3 = $mitai['answer3']; 
            }
            ?>
            <td colspan="2"><?= $answer3 ?></td>
        </tr>
        <tr>
            <th class="caption date">相手の回答4</td>
            <?php if(empty($mitai['answer4'])){
                $answer4 = "---";
            } else {
                $answer4 = $mitai['answer4']; 
            }
            ?>
            <td colspan="2"><?= $answer4 ?></td>
        </tr>
        
        <tr>
            <th class="caption date">備考</td>
            <td colspan="2"><?= $mitai['bikou'] ?></td>
        </tr>       
    </table>