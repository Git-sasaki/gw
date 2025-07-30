<?php
$this->assign('title', '出勤情報一覧');
$weekList = array("日","月","火","水","木","金","土");
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
    <h3>出勤情報一覧</h3>
    <div class = "odakoku">
    <table class="table01 table04">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('name', $title = '名前') ?></th>
                <th scope="col"><?= $this->Paginator->sort('status', $title = '状態') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($staffs as $staff): ?>
            <tr>
                <td><?= $staff["name"] ?></td>
                <?php if($staff["status"]==0): ?>
                    <td><?= $statustext[$staff["status"]] ?></td>
                <?php elseif($staff["status"]==1): ?>
                    <td style = "background: rgb(68, 240, 68);"><?= $statustext[$staff["status"]] ?></td>
                <?php else: ?>
                    <td style = "background-color: rgb(245, 116, 116);"><?= $statustext[$staff["status"]] ?></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class = "space"></div>

    <table class="table01 table03">
        <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('name', $title = '名前') ?></th>
                <th scope="col"><?= $this->Paginator->sort('status', $title = '状態') ?></th>
                <th scope="col"><?= $this->Paginator->sort('status', $title = '日報') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <!-- 状態表示 -->
                <td><?= $user["name"] ?></td>
                <?php if($user["status"]==0): ?>
                    <td><?= $statustext[$user["status"]] ?></td>
                <?php elseif($user["status"]==1): ?>
                    <td style = "background: rgb(68, 240, 68);"><?= $statustext[$user["status"]] ?></td>
                <?php else: ?>
                    <td style = "background-color: rgb(245, 116, 116);"><?= $statustext[$user["status"]] ?></td>
                <?php endif; ?>

                <!-- 日報の状態表示             
                <?php if($user["report"]==0): ?>
                    <td></td>
                <?php else: ?>
                    <td><?= $reptext[$user["report"]] ?></td>
                <?php endif; ?>     -->

                <?php if($this->request-> getSession()->read('Auth.User.adminfrag') == 0): ?>
                    <?php if($user["report"]==0): ?>
                        <td></td>
                    <?php else: ?>
                        <td>済</td>
                    <?php endif; ?>
                <?php elseif($this->request-> getSession()->read('Auth.User.adminfrag') == 1): ?>
                    <?php if($user["report"]==0): ?>
                        <td></td>
                    <?php elseif(($user["report"]==1)): ?>
                        <td>
                            <?= $this->Html->link(
                                $reptext[$user["report"]],
                                ['controller' => 'reports','action'=>'edit', $user["rep_id"]],
                                ['class' => 'bla','target' => '_blank'],
                            ); ?>
                        </td>
                    <?php elseif(($user["report"]==2)): ?>
                        <td>
                            <?= $this->Html->link(
                                $reptext[$user["report"]],
                                ['controller' => 'reports','action'=>'edit', $user["rep_id"]],
                                ['class' => 'bla','target' => '_blank'],
                            ); ?>
                        </td>
                    <?php endif; ?>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>
