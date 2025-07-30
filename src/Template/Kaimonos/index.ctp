<?php
$this->assign('title', '編集');
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

<div class="users index large-9 medium-8 columns content">
    <h3><?= __('物品購入申請一覧') ?></h3>
    <div class = "vw65"><?= $this->Html->link('新規登録', ['action' => 'new']); ?></div>
    <table class="table01 table02">
        <thead>
            <tr>
                <th scope="col" class = "w50"><?= $this->Paginator->sort('date', $title = 'ID') ?></th>
                <th scope="col" class = "w150"><?= $this->Paginator->sort('date', $title = '日付') ?></th>
                <th scope="col" class = "w150"><?= $this->Paginator->sort('cinnamon', $title = '商品名') ?></th>
                <th scope="col" class = "w150"><?= $this->Paginator->sort('password', $title = '購入者') ?></th>
                <th scope="col" class = "w200"><?= $this->Paginator->sort('status', $title = '状態') ?></th>
                <th scope="col" class = "w200"><?= __('操作') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($kais as $kai): ?>
            <tr>
                <td><?= $kai["id"] ?></td>
                <td><?= $kai["date"]->i18nFormat('yyyy/MM/dd') ?></td>
                <td><?= $kai["cinnamon"] ?></td>
                <td><?= $users[$kai["user_id"]] ?></td>
                <?php if($kai["status"]==0 || empty($kai["status"])): ?>
                    <td>未決裁</td>
                <?php elseif($kai["status"]==2): ?>
                    <td><?= $kai["kessaibi"]->i18nFormat('yyyy/MM/dd')." 否決" ?></td>
                <?php else: ?>
                    <td><?= $kai["kessaibi"]->i18nFormat('yyyy/MM/dd')." 決裁済" ?></td>
                <?php endif;?>
                <td class="actions">
                    <?= $this->Html->link(__('[詳細]'), ['action' => 'view', $kai["id"]]) ?>
                    <?php if($this->request-> getSession()->read('Auth.User.adminfrag') == 0 && $session == $kai["user_id"]): ?>
                        <?= $this->Html->link(__('[編集]'), ['action' => 'edit', $kai["id"]]) ?>
                    <?php endif; ?>
                    <?php if($this->request-> getSession()->read('Auth.User.adminfrag') == 1): ?>
                        <?= $this->Html->link(__('[編集]'), ['action' => 'edit', $kai["id"]]) ?>
                        <?= $this->Html->link(__('[出力]'), ['action' => 'excelexport', $kai["id"]]) ?>
                        <?= $this->Form->postLink(__('[削除]'), ['action' => 'delete', $kai["id"]], ['confirm' => __('本当に削除しますか？ # {0}?', $kai["id"])]) ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>