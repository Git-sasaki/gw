<?php
$this->assign('title', '作業日報詳細');
$weekList = array("日", "月", "火", "水", "木", "金", "土");
?>

<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <?php if($this->request-> getSession()->read('Auth.User.adminfrag') != 1): ?>
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
        <?php else: ?>
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
        <?php endif; ?>
    </ul>
</nav>
<tr>

<div class="users index columns content report">
    <h3><?= $name ."さんの作業日報" ?> </h3>
    <h4 class="legend"><?= __('作業日報') ?></h4>
    <table class="table01 table02">
        <tr>
            <th class="caption date">日付</td>
            <?php 
                $datetime = new DateTime($report->date);
                $w = (int)$datetime->format('w');
            ?>
            <td colspan="2"><?= $report['date']->i18nFormat('yyyy/MM/dd (' . $weekList[$w] . ')') ?></td>
        </tr>
            <th class="caption date">勤務時間</td>
            <td colspan="2"><?= $report['intime']->i18nFormat("H:mm")." ～ ".$report['outtime']->i18nFormat("H:mm") ?></td>
        </tr>
        <tr>
            <th class="caption date">業務内容</td>
            <td colspan="2"><?= $report['content'] ?></td>
        </tr>
        <?php for($i=0; $i<=2; $i++): ?>
            <tr>
            <?php if($i==0): ?>
                <th class="caption date" rowspan="3">業務内容の詳細</th>
                <td><?= $red[$i]['item'] ?></td>
                <td><?= $red[$i]['reportcontent'] ?></td>    
            <?php else:?>
                <?php if(!empty($red[$i]['item'])): ?>
                    <td><?= $red[$i]['item'] ?></td>
                    <td><?= $red[$i]['reportcontent'] ?></td>
                <?php endif; ?>
            <?php endif; ?>
            </tr>
        <?php endfor; ?>
        <tr>
            <th class="caption date">反省・特記事項</td>
            <td colspan="2"><?= $report['notice'] ?></td>
        </tr>
        <tr>
            <th class="caption date">次回の予定</td>
            <td colspan="2"><?= $report['plan'] ?></td>
        </tr>
    </table>

    <?php if($this->request-> getSession()->read('Auth.User.adminfrag') == 1): ?>

    <h4 class="legend"><?= __('業務日誌') ?></h4>
    <table class="table01 table02">
        <tr>
            <th class="caption date">業務内容・様子</td>
            <td><?= $report['state'] ?></td>
        </tr>
        <tr>
            <th class="caption date">体調・連絡事項など</td>
            <td><?= $report['information'] ?></td>
        </tr>
        <tr>
            <th class="caption date">記録者</td>
            <td><?= $report['recorder'] ?></td>
        </tr>
    </table>

    <?php endif; ?>
</div>