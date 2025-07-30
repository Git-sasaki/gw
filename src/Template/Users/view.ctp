<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
$this->assign('title', 'ユーザー詳細');
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
<div class="users view large-9 medium-8 columns content">
    <h3><?= h($user->name)."さんのユーザー詳細" ?></h3>
    <table class="vertical-table table01 table02" style="width:600px;margin-left:0;">
        <tr>
            <th class="date" scope="row"><?= __('ID') ?></th>
            <td><?= $this->Number->format($user->id) ?></td>
        </tr>
        <tr>
            <th class="date" scope="row"><?= __('ユーザー名') ?></th>
            <td><?= h($user->user) ?></td>
        </tr>
        <tr>
            <th class="date" scope="row"><?= __('名前') ?></th>
            <td><?= h($user->name) ?></td>
        </tr>
        <tr>
            <th class="date" scope="row"><?= __('パスワード') ?></th>
            <td><?= h($user->password) ?></td>
        </tr>
        <tr>
            <th class="date" scope="row"><?= __('管理者権限') ?></th>
            <td><?= h($user->adminfrag) ?></td>
        </tr>
        <tr>
            <th class="date" scope="row"><?= __('作成日') ?></th>
            <td><?= h($user->created->i18nFormat('yyyy/MM/dd HH:mm:ss')) ?></td>
        </tr>
        <tr>
            <th class="date" scope="row"><?= __('修正日') ?></th>
            <td><?= h($user->modified->i18nFormat('yyyy/MM/dd HH:mm:ss')) ?></td>
        </tr>
    </table>
</div>
