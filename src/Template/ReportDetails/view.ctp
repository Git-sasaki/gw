<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ReportDetail $reportDetail
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Report Detail'), ['action' => 'edit', $reportDetail->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Report Detail'), ['action' => 'delete', $reportDetail->id], ['confirm' => __('Are you sure you want to delete # {0}?', $reportDetail->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Report Details'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Report Detail'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Reports'), ['controller' => 'Reports', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Report'), ['controller' => 'Reports', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="reportDetails view large-9 medium-8 columns content">
    <h3><?= h($reportDetail->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th scope="row"><?= __('Report') ?></th>
            <td><?= $reportDetail->has('report') ? $this->Html->link($reportDetail->report->id, ['controller' => 'Reports', 'action' => 'view', $reportDetail->report->id]) : '' ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Id') ?></th>
            <td><?= $this->Number->format($reportDetail->id) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Linenumber') ?></th>
            <td><?= $this->Number->format($reportDetail->linenumber) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Starttime') ?></th>
            <td><?= h($reportDetail->starttime) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Endtime') ?></th>
            <td><?= h($reportDetail->endtime) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Created') ?></th>
            <td><?= h($reportDetail->created) ?></td>
        </tr>
        <tr>
            <th scope="row"><?= __('Modified') ?></th>
            <td><?= h($reportDetail->modified) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Item') ?></h4>
        <?= $this->Text->autoParagraph(h($reportDetail->item)); ?>
    </div>
    <div class="row">
        <h4><?= __('Content') ?></h4>
        <?= $this->Text->autoParagraph(h($reportDetail->content)); ?>
    </div>
</div>
