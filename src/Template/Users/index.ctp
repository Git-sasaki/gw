<?php
$this->assign('title', 'ユーザー一覧');
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
    <h3><?= __('ユーザー一覧') ?></h3>
    <div class = "newright"><?= $this->Html->link('新規ユーザー登録', ['action' => 'add']); ?></div>
    <table class="table01 table02">
        <thead>
            <tr>
                <th scope="col" style="text-align:center"><?= $this->Paginator->sort('id', $title = 'ID') ?></th>
                <th scope="col"><?= $this->Paginator->sort('user', $title = 'ユーザー名') ?></th>
                <th scope="col"><?= $this->Paginator->sort('name', $title = '名前') ?></th>
                <th scope="col"><?= $this->Paginator->sort('password', $title = 'パスワード') ?></th>
                <th scope="col"><?= $this->Paginator->sort('adminfrag', $title = '管理者権限') ?></th>
                <th scope="col"><?= $this->Paginator->sort('retired', $title = '退職日') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified', $title = '修正日時') ?></th>
                <th scope="col" class="actions"><?= __('操作') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <th class="date"><?= $this->Number->format($user->id) ?></td>
                <td><?= h($user->user) ?></td>
                <td><?= h($user->name) ?></td>
                <td><?= h($user->password) ?></td>
                <td><?= h($user->adminfrag) ?></td>
                <?php if(!empty($user->retired)): ?>
                    <td><?= $user->retired->i18nFormat('yyyy/MM/dd') ?></td>
                <?php else: ?>
                    <td>―</td>
                <?php endif; ?>
                <td><?= $user->modified->i18nFormat('yyyy/MM/dd　　HH:mm:ss') ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('[詳細]'), ['action' => 'view', $user->id]) ?>
                    <?= $this->Html->link(__('[編集]'), ['action' => 'edit', $user->id]) ?>
                    <?= $this->Form->postLink(__('[削除]'), ['action' => 'delete', $user->id], ['confirm' => __('本当に削除しますか？ # {0}?', $user->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('最初')) ?>
            <?= $this->Paginator->prev('< ' . __('前')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('次') . ' >') ?>
            <?= $this->Paginator->last(__('最後') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
    </div>
</div>
