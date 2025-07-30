<?php
$this->assign('title', '作業日報一覧');
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

<div class="users index columns content report">
    <h3><?php
        if($user == 0) {
            echo "作業日報一覧";
        } else {
            echo $names[$user] . "さんの作業日報一覧";
        }
    ?> </h3>

    <?php if($this->request-> getSession()->read('Auth.User.adminfrag') == 1): ?>
        <table class="table01 table02">
            <thead>
                <tr>
                    <th class = "type2" scope="col" style="text-align:center"><?= $this->Paginator->sort('date', $title = '日付') ?></th>
                    <th class = "type2" scope="col"><?= $this->Paginator->sort('user', $title = 'ユーザー') ?></th>
                    <th class = "type2" scope="col"><?= $this->Paginator->sort('time', $title = '勤務時間') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('content', $title = '業務内容') ?></th>
                    <th class = "type1" scope="col" class="actions"><?= __('操作') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                <tr>
                    <?php 
                        $datetime = new DateTime($report->date);
                        $w = (int)$datetime->format('w');
                    ?>
                    <th class="type2"><?= $report->date->i18nFormat('yyyy/MM/dd')."(".$weekList[$w].")" ?></th>
                    <td><?= $names[$report->user_id] ?></td>
                    <td><?= $report["intime"]->i18nFormat("H:mm")." ～ ".$report["outtime"]->i18nFormat("H:mm") ?></td>
                    <td><?= h($report->content) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('[編集]'), ['action' => 'edit', $report->id]) ?>
                        <?= $this->Html->link(__('[詳細]'), ['action' => 'view', $report->id]) ?>
                        </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif($this->request-> getSession()->read('Auth.User.adminfrag') == 0): ?>
        <table class="table01 table02">
            <thead>
                <tr>
                    <th class = "type2" scope="col" style="text-align:center"><?= $this->Paginator->sort('date', $title = '日付') ?></th>
                    <th class = "type2" scope="col"><?= $this->Paginator->sort('time', $title = '勤務時間') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('content', $title = '業務内容') ?></th>
                    <th class = "type1" scope="col" class="actions"><?= __('操作') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                <tr>
                    <?php 
                        $datetime = new DateTime($report->date);
                        $w = (int)$datetime->format('w');
                    ?>
                    <th class="type2"><?= $report->date->i18nFormat('yyyy/MM/dd')."(".$weekList[$w].")" ?></th>
                    <td><?= $report["intime"]->i18nFormat("H:mm")." ～ ".$report["outtime"]->i18nFormat("H:mm") ?></td>
                    <td><?= h($report->content) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('[詳細を見る]'), ['action' => 'view', $report->id]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
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
