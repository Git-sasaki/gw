<?php $this->assign('title', 'ユーザー一覧'); ?>

<div class = "odakoku">
    <div class = "sidemenu mvh170">
        <?= $this -> Form -> create(__("View"),["type" => "post","url" => ["action" => "getquery0"]]); ?>
            <h4 class = "sideh4 ml10 pt25"> 表示</h4>  
                <div class = "sdakoku ml20">
                    <?= $this->Form->control('kubun',['type'=>'select','label'=>"表示区分",'value'=>$display], $kubuns) ?>
                </div>
            <div class="ml10_button mt30 ml20">
                <?= $this->Form->button(__("表示")) ?>
            </div>
        <?= $this -> Form -> end(); ?>
        <br>
            <h4 class = "sideh4 ml10 pt15"> 新規登録</h4> 
            <div class = "ml10_button mt40 ml20">
                <?= $this->Html->link('登録',
                    ['controller' => 'users', 'action' => 'newn'],
                    ['class' => 'linkbutton']); ?>
            </div>
    </div>
    
    <div class = "main2 ml15 pl30">
    <h4 class="midashih4 mt30 mb30"> ユーザー一覧 </h4>
    <table class="table01 table02">
        <thead>
            <tr>
                <th class = "w50" scope="col" style="text-align:center"><?= $this->Paginator->sort('id', $title = 'ID') ?></th>
                <th class = "w80" scope="col">ユーザー名</th>
                <th class = "w100" scope="col">名前</th>
                <?php if($display == 1): ?>
                    <th class = "w175" scope="col">サービス受給者証期限</th>
                <?php elseif($display == 2): ?>
                    <th class = "w100" scope="col">退職日</th>
                <?php endif; ?>
                <?php if($display == 1): ?>
                    <th class = "w150" scope="col">修正日時</th>
                <?php else: ?>
                    <th class = "w175" scope="col">修正日時</th>
                <?php endif; ?>
                <th class = "w100" scope="col" class="actions"><?= __('操作') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <th class="date" style = "background:#89babe"><?= $this->Number->format($user->id) ?></td>
                    <td><?= h($user->user) ?></td>
                    <td><?= h($user->name) ?></td>
                    <?php if($display == 1): ?>
                        <?php if(!empty($user->sjhajime) && !empty($user->sjowari)): ?>
                            <?php 
                                $today = date('Y-m-d');
                                $owaribi = $user->sjowari->i18nFormat('yyyy/MM/dd');
                                $diff = (strtotime($owaribi) - strtotime($today)) / 86400;
                            ?>
                            <?php if($diff <= 31): ?>
                                <td style = "background: red; color: white;">
                            <?php else: ?>
                                <td>
                            <?php endif; ?>
                                <?= $user->sjhajime->i18nFormat('yyyy/MM/dd') ?> ～ <?= $user->sjowari->i18nFormat('yyyy/MM/dd') ?></td>
                        <?php else: ?>
                            <td>未入力</td>
                        <?php endif; ?>
                    <?php elseif($display == 2): ?>
                        <td><?= $user->userd->i18nFormat('yyyy/MM/dd') ?></td>
                    <?php endif; ?>
                    <td>
                        <?= $user->modified->i18nFormat('yyyy/MM/dd HH:mm:ss') ?>
                    </td>
                    <td class="actions">
                    <div class = "odakoku center">
                        <div style = "margin-left:3px;">
                        <?= $this->Form->create(__("View"), ['type'=>'post','url'=>['action' => 'detailn']]) ?>
                            <?= $this->Form->control('id',['type'=>'hidden','value'=>$user->id]) ?>
                            <?= $this->Form->button("[詳細]",["class"=>"ichibtn datalink"]) ?>
                        <?= $this->Form->end(); ?>
                        </div>
                        <div>
                        <?= $this->Form->create(__("View"), ['type'=>'post','url'=>['action' => 'editn']]) ?>
                            <?= $this->Form->control('id',['type'=>'hidden','value'=>$user->id]) ?>
                            <?= $this->Form->button("[編集]",["class"=>"ichibtn datalink"]) ?>
                        <?= $this->Form->end(); ?>
                        </div>
                        <div>
                        <?= $this->Form->postLink(__('[削除]'), ['action' => 'delete', $user->id], ['confirm' => __('本当に削除しますか？ # {0}?', $user->id)]) ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        </table>
    </div>
</div>
