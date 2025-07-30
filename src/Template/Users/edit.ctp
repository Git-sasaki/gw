<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
$this->assign('title', '編集');
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
                    <?= $this->Html->link('作業日報', ['controller' => 'Reports', 'action' => 'register']); ?>
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
<div class="users form large-9 medium-8 columns content">
    <?= $this->Form->create($user) ?>
    <fieldset>
        <legend><?= __('ユーザー編集') ?></legend>
        <?php
            echo $this->Form->control('user', ['label' => 'ユーザー名']);
            echo $this->Form->control('name', ['label' => '名前']);
            echo $this->Form->control('password', ['label' => 'パスワード']);
            echo $this->Form->control('adminfrag', ['label' => '管理者権限']);
        ?>
        <br>
        <div class = "l89">
            <?= $this->Form->button(__('送信')) ?>
        </div>
        <?= $this->Form->end() ?>

        <?= $this->Form->create(__("View"),
        ["type" => "post","url" => ["action" => "edit2","?"=>["id"=>$user["id"]]]]) ?>

        <legend><?= __('退職した場合は日時を選択') ?></legend>
        <div class = "odakoku">
            <div class = "sdakoku">
                <?= $this->Form->control('year', ['label' => '年','value'=>date('Y')]); ?>
            </div>
            <div class = "sdakoku">
                <?= $this->Form->control('month', ['label' => '月','value'=>date('m')]); ?>
            </div>
            <div class = "sdakoku">
                <?= $this->Form->control('day', ['label' => '日','value'=>date('d')]); ?>
            </div>
        </div>
        <div class = "l89">
            <?= $this->Form->button(__('送信')) ?>
        </div>
        <?= $this->Form->end() ?>
    </fieldset>
</div>
