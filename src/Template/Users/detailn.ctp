<?php $this->assign('title', 'ユーザー詳細'); ?>

<div class = "main1">
<h4 class="midashih4 mt30 mb30">
    <div class = "odakoku">
    <?= h($user->name)."さんのユーザー詳細" ?>
    <span style = "font-size: 16px; margin-left:10px;">
        <?= $this->Form->create(__("View"), ['type'=>'post','url'=>['action' => 'editn']]) ?>
        <?= $this->Form->control('id',['type'=>'hidden','value'=>$user->id]) ?>
        <?= $this->Form->button("[編集]",["class"=>"ichibtn datalink"]) ?>
    </span>
    </div>
</h4>
    <table class="table01 table02">
        <tr>
            <th class="date bokkoht" scope="row"><?= __('ID') ?></th>
            <td class="bokkot"><?= $this->Number->format($user->id) ?></td>
            <th class="date bokkoht" scope="row"><?= __('ユーザー名') ?></th>
            <td class="bokkot"><?= h($user->user) ?></td>
        </tr>
        <tr>
            <th class="date" scope="row"><?= __('名前') ?></th>
            <td><?= h($user->name) ?></td>
            <th class="date wbokko" scope="row"><?= __('パスワード') ?></th>
            <td><?= h($user->password) ?></td>
        </tr>
        <tr>
            <th class="date" scope="row"><?= __('区分') ?></th>
            <!--<td><?= h($user->adminfrag) ?></td>-->
            <td><?= h(($user->adminfrag == "0") ? "利用者" : "職員") ?></td>
            <th class="date wbokko" scope="row"><?= __('退職日') ?></th>
            <td>
                <?php if(!empty($user->retired)) {
                    echo h($user->retired->i18nFormat('yyyy/MM/dd')); 
                } else {
                    echo "---";
                }
                ?>
            </td>
        </tr>
        <?php if($user->adminfrag == 0): ?>
            <tr>
                <th class="date" scope="row"><?= __('受給者証番号') ?></th>
                <?php if(!empty($user->sjnumber)): ?>
                    <td><?= $user->sjnumber ?></td>
                <?php else: ?>
                    <td>未入力</td>
                <?php endif; ?>
                <th class="date" scope="row"><?= __('受給者証期限') ?></th>
                <?php if(!empty($user->sjhajime) && !empty($user->sjowari)): ?>
                    <td><?= h($user->sjhajime->i18nFormat('yyyy/MM/dd')) ?>　～　<?= h($user->sjowari->i18nFormat('yyyy/MM/dd')) ?></td>
                <?php else: ?>
                    <td>未入力</td>
                <?php endif; ?>
            </tr>
            <tr>
                <th class="date" scope="row"><?= __('就労タイプ') ?></th>
                <td><?= h(($user->wrkCase == 0) ? "Ａ型" : "Ｂ型") ?></td>
                <th class="date" scope="row"><?= __('送迎場所') ?></th>
                <td><?= $user->oufuku_place ?></td>
            </tr>
        <?php else: ?>
            <tr>
                <th class="date" scope="row"><?= __('メールアドレス') ?></th>
                <td><?= $user->mail ?></td>
                <th class="date" scope="row"><?= __('決済資格') ?></th>
                <td>
                <?php
                    echo ((empty($user->kessai)) ? "なし" : "あり"); 
                ?>    
                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <th class="date" scope="row"><?= __('作成日') ?></th>
            <td><?= h($user->created->i18nFormat('yyyy/MM/dd HH:mm:ss')) ?></td>
            <th class="date" scope="row"><?= __('修正日') ?></th>
            <td><?= h($user->modified->i18nFormat('yyyy/MM/dd HH:mm:ss')) ?></td>
        </tr>
    </table>
</div>
