<?php $this->assign('title', '欠席情報一覧'); ?>

<div class = "odakoku">
    <div class = "sidemenu mvh120">
    <?= $this -> Form -> create(__("View"),["type" => "post","url" => ["action" => "getquery0"]]); ?>
        <h4 class = "sideh4 ml10 pt15">年月日選択</h4>  
            <div class = "odakoku ml10">
                <div class = "sdakoku">
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>$year], $years) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>$month], $months) ?> 
                </div>
            </div>
        <h4 class = "sideh4 ml10 pt15">ユーザー</h4>  
                <div class = "staffbox mt30 ml10">
                    <?= $this->Form->select('id',$sideusers,['id'=>'staff_id','label' => false,'value'=>$user_id,'empty'=>array('0'=>'ALL')]);?>
                </div>
        <div class="ml10_button mt30 ml10">
            <?= $this->Form->button(__("表示")) ?>
        </div>
        <?= $this -> Form -> end(); ?>
    </div>

<div class = "main1">
<h4 class="midashih4 mt30 mb30"> 欠席連絡<span style = "font-size: 16px; margin-left:10px;"><?= $this->Html->link('[新規登録]', ['action' => 'editn']); ?></span></h4>
    <table class="table01 table02 mb30">
        <thead>
            <tr>
                <th scope="col" style = "text-align:center" class = "thsema"><?= $this->Paginator->sort('date', $title = '日付') ?></th>
                <th scope="col" class = "thsema"><?= $this->Paginator->sort('user_id', $title = '欠席者') ?></th>
                <th scope="col" class = "thhiro">内容</th>
                <th scope="col" style="width: 7%;">欠勤加算</th>
                <th scope="col" class = "thsema">操作</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($absents as $absent): ?>
            <?php 
                $staff = $users[$absent['user_id']];
                $datetime = new DateTime($absent['kekkindate']);
                $w = (int)$datetime->format('w');
            ?>
        <tr>                
            <th style="background:#89babe"><?= $absent['kekkindate']->i18nFormat('yyyy/MM/dd')."(".$weekList[$w].")" ?></th>
            <td><?= $staff ?></td>
            <td><?= h($absent['naiyou']) ?></td>
            <td><?= ($absent['kekkinkasan'] == 0) ? '' : '✔' ?></td>
            <td class="actions">
                <div class = "odakoku center">
                    <div>
                    <?= $this->Form->create(__("View"), ['type'=>'post','url'=>['action' => 'detailn']]) ?>
                        <?= $this->Form->control('id',['type'=>'hidden','value'=>$absent['id']]) ?>
                        <?= $this->Form->button("[詳細]",["class"=>"ichibtn datalink"]) ?>
                    <?= $this->Form->end(); ?>
                    </div>
                    <div class = "ml10">
                    <?= $this->Form->create(__("View"), ['type'=>'post','url'=>['action' => 'editn']]) ?>
                        <?= $this->Form->control('id',['type'=>'hidden','value'=>$absent['id']]) ?>
                        <?= $this->Form->button("[編集]",["class"=>"ichibtn datalink"]) ?>
                    <?= $this->Form->end(); ?>
                    </div>
                    <div class = "ml10">
                    <?= $this->Html->link(__('[削除]'), ['action' => 'delete', $absent['id']], ['confirm' => __('本当に削除しますか？ # {0}?', $absent['id'])]) ?>
                    </div>
                </div>
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
    </div>
