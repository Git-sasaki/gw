<?php
    $this->assign('title', '出退社打刻');
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

<div class = "main">
    <h3 class="attendance-header">出勤情報 詳細</h3>
        <table class="alltable">
            <tr>
                <th class = "allhead">合計勤務時間</td>
                <td class = "alldetail"><?= $alltime ?></td>
                <th class = "allhead">合計休憩時間</td>
                <td class = "alldetail"><?= $allrest ?></td>
            </tr>
            <tr>
                <th class = "allhead">全出勤日</th>
                <td class = "alldetail"><?= $allworkdays."日"?></td>
                <th class = "allhead">出勤率</th>
                <td class = "alldetail"><?= $percent ?></td>
            </tr>
            <tr>
                <th class = "allhead">公休</th>
                <td class = "alldetail"><?= $allkoukyu."回" ?></td>
                <th class = "allhead">有休</th>
                <td class = "alldetail"><?= $allpaid."回" ?></td>
            </tr>
            <tr>
                <th class = "allhead">欠勤</th>
                <td class = "alldetail"><?= $allkekkin."回" ?></td>
                <th class = "allhead">食事提供</th>
                <td class = "alldetail"><?= $allmeshi."回" ?></td>
            </tr>
            <tr>
                <th class = "allhead">医療連携</th>
                <td class = "alldetail"><?= $allmedical."回" ?></td>
                <th class = "allhead">施設外支援</th>
                <td class = "alldetail"><?= $allsupport."回" ?></td>
            </tr>
        </table>
    <p class = "bun">
        ※1 全出勤日：月の日数から土日・祝日を差し引いたもの<br>
        ※2 (出勤率) = (出勤した日) ÷ (全出勤日) × 100
    </p>