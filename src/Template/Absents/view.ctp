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

<div class="users index large-9 medium-8 columns content">
    <h3><?= __('欠席連絡一覧') ?></h3>
        <table class="abstable">
            <thead>
                <tr>
                    <th scope="col" style = "text-align:center" class = "thsema"><?= $this->Paginator->sort('date', $title = '日付') ?></th>
                    <th scope="col" class = "thsema"><?= $this->Paginator->sort('user', $title = '欠席者') ?></th>
                    <th scope="col" class = "thhiro"><?= $this->Paginator->sort('naiyou', $title = '内容') ?></th>
                    <th scope="col" class = "thsema"><?= __('操作') ?></th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($absents as $absent): ?>
                <?php 
                    $staff = $staffs[$absent['user_id']];
                ?>
            <tr>                
                <th class="date"><?= $absent['date']->i18nFormat("yyyy-MM-dd") ?></th>
                <td><?= $staff ?></td>
                <td><?= h($absent['naiyou']) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('[詳細]'), ['action' => 'viewdetail', $absent['id']]) ?>
                    <?= $this->Html->link(__('[削除]'), ['action' => 'delete', $absent['id']], ['confirm' => __('本当に削除しますか？ # {0}?', $absent['id'])]) ?>
                </td>
            </tr>                
            <?php endforeach; ?>
            </tbody>
        </table>
</div>

