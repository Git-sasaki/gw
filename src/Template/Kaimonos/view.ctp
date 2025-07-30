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

<div class="users view large-9 medium-8 columns content">
    <?php if($kaimono["type"]==0): ?>
        <h3>物品購入伺</h3>
    <?php else: ?>
        <h3>1物品購入報告</h3>
    <?php endif; ?>
    <table class="vertical-table table01 table02" style="width:600px;margin-left:0;">
        <tr>
            <th class="date" scope="row"><?= __('日付') ?></th>
            <td><?= $kaimono["date"]->i18nFormat('yyyy/MM/dd') ?></td>
        </tr>
        <tr>
            <th class="date" scope="row"><?= __('購入者') ?></th>
            <td><?= $users[$kaimono["user_id"]] ?></td>
        </tr>
        <tr>
            <th class="date" scope="row"><?= __('商品名') ?></th>
            <td><?= $kaimono["cinnamon"] ?></td>
        </tr>
        <tr>
            <th class="date" scope="row"><?= __('購入先名') ?></th>
            <td><?= $kaimono["shop"] ?></td>
        </tr>
        <tr>
            <th class="date" scope="row"><?= __('価格') ?></th>
            <td><?= $kaimono["price"]."　円" ?></td>
        </tr>
        <?php if(!empty($kaimono["url1"])): ?>
            <th class="date" scope="row"><?= __('URL') ?></th>
            <td><a href = <?= $kaimono["url1"] ?> target="_blank" rel="noopener noreferrer"><?= $kaimono["url1"] ?></a></td>
        <?php endif; ?>
        <?php if(!empty($kaimono["url2"])): ?>
            <th class="date" scope="row"><?= __('URL') ?></th>
            <td><a href = <?= $kaimono["url2"] ?> target="_blank" rel="noopener noreferrer"><?= $kaimono["url2"] ?></a></td>
        <?php endif; ?>
        <?php if(!empty($kaimono["url3"])): ?>
            <th class="date" scope="row"><?= __('URL') ?></th>
            <td><a href = <?= $kaimono["url3"] ?> target="_blank" rel="noopener noreferrer"><?= $kaimono["url3"] ?></a></td>
        <?php endif; ?>
        <tr>
            <th class="date" scope="row"><?= __('備考') ?></th>
            <td><?= $kaimono["bikou"] ?></td>
        </tr>
        <tr>
            <th class="date" scope="row"><?= __('状態') ?></th>
            <td><?= $statusbun[$kaimono["status"]] ?></td>
        </tr>
        <?php if($kaimono["status"]==1): ?>
        <tr>
            <th class="date" scope="row"><?= __('決済日') ?></th>
            <td><?= $kaimono["kessaibi"]->i18nFormat('yyyy/MM/dd') ?></td>
        </tr>
        <tr>
            <th class="date" scope="row"><?= __('決済者') ?></th>
            <td><?= $users[$kaimono["kessaisha"]] ?></td>
        </tr>
        <?php endif; ?>