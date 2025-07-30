<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Report[]|\Cake\Collection\CollectionInterface $reports
 */
$this->assign('title', '作業日報一覧');
$weekList = array("日", "月", "火", "水", "木", "金", "土");
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('メニュー') ?></li>
        <li>
            <?= $this->Html->link('出退社打刻', ['controller' => 'Users', 'action' => 'stamp']); ?>
        </li>
        <li>
            <?= $this->Html->link('新規作業日報登録', ['controller' => 'Users', 'action' => 'index2']); ?>
        </li>
        <li>
            <?= $this->Html->link('作業日報一覧', ['controller' => 'Reports', 'action' => 'userList']); ?>
        </li>
        <li>
            <?= $this->Html->link('ログアウト', ['controller' => 'Users', 'action' => 'logout']); ?>
    </ul>
</nav>
<div class="users index columns content report">
    <h3><?= $name . "(" . $user . ")さんの作業日報一覧" ?> </h3>
    <table class="table01 table02">
        <thead>
            <tr>
                <!-- <th scope="col"><?= $this->Paginator->sort('id', $title = 'ID') ?></th> -->
                <th scope="col" style="text-align: center;width:200px;"><?= $this->Paginator->sort('date', $title = '日付') ?></th>
                <th scope="col"><?= $this->Paginator->sort('content', $title = '業務内容') ?></th>
                <th scope="col" class="actions"><?= __('操作') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reports as $report): ?>
            <tr>
                <!-- <td><?= $this->Number->format($report->id) ?></td> -->
                <?php 
                    $datetime = new DateTime($report->date);
                    $w = (int)$datetime->format('w');
                ?>
                <th class="date"><?= $report->date->i18nFormat('yyyy/MM/dd (' . $weekList[$w] . ')') ?></th>
                <td><?= h($report->content) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('[詳細を見る]'), ['action' => 'view', $report->id]) ?>
                    <?= $this->Html->link(__('[編集]'), ['action' => 'edit', $report->id]) ?>
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
