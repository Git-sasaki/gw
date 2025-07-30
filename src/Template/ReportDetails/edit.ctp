<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\ReportDetail $reportDetail
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $reportDetail->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $reportDetail->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Report Details'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Reports'), ['controller' => 'Reports', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Report'), ['controller' => 'Reports', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="reportDetails form large-9 medium-8 columns content">
    <?= $this->Form->create($reportDetail) ?>
    <fieldset>
        <legend><?= __('Edit Report Detail') ?></legend>
        <?php
            echo $this->Form->control('report_id', ['options' => $reports]);
            echo $this->Form->control('linenumber');
            echo $this->Form->control('starttime');
            echo $this->Form->control('endtime');
            echo $this->Form->control('item');
            echo $this->Form->control('content');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
