<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ReportDetail[]|\Cake\Collection\CollectionInterface $reportDetails
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Report Detail'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Reports'), ['controller' => 'Reports', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Report'), ['controller' => 'Reports', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="reportDetails index large-9 medium-8 columns content">
    <h3><?= __('Report Details') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('report_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('linenumber') ?></th>
                <th scope="col"><?= $this->Paginator->sort('starttime') ?></th>
                <th scope="col"><?= $this->Paginator->sort('endtime') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
                <th scope="col"><?= $this->Paginator->sort('modified') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reportDetails as $reportDetail): ?>
            <tr>
                <td><?= $this->Number->format($reportDetail->id) ?></td>
                <td><?= $reportDetail->has('report') ? $this->Html->link($reportDetail->report->id, ['controller' => 'Reports', 'action' => 'view', $reportDetail->report->id]) : '' ?></td>
                <td><?= $this->Number->format($reportDetail->linenumber) ?></td>
                <td><?= h($reportDetail->starttime) ?></td>
                <td><?= h($reportDetail->endtime) ?></td>
                <td><?= h($reportDetail->created) ?></td>
                <td><?= h($reportDetail->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $reportDetail->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $reportDetail->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $reportDetail->id], ['confirm' => __('Are you sure you want to delete # {0}?', $reportDetail->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
    </div>
</div>
