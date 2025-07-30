<?php $this->assign('title', '購入申請一覧'); ?>

<div class = "main1">
<h4 class="midashih4 mt30 mb30"> 物品購入申請<span style = "font-size: 16px; margin-left:10px;"><?= $this->Html->link('[新規登録]', ['action' => 'newn']); ?></span></h4>
    
    <div class = "kensakusan">
        <?= $this->Form->create(__("View"), ['type'=>'post','url'=>['action'=>'kokodozo','?'=>['type'=>1]]]) ?>
        <div class = "odakoku mt10 center">
            <div class = "mlv25">商品名を検索： </div>
            <div style = "width: 500px;">
                <?php if(empty($kensakusan)): ?>
                    <?= $this->Form->control("kensakusan",['type'=>'text','label'=>false]) ?>
                <?php else: ?>
                    <?= $this->Form->control("kensakusan",['type'=>'text','label'=>false,'value'=>$kensakusan]) ?>
                <?php endif; ?>
            </div>
            <div style = "margin-left: 30px;">
                <?= $this->Form->button(__("検索"),["class"=>"kensakubutton"]) ?>
            </div>
        </div>
        <?= $this -> Form -> end(); ?>
    </div>

    <table class="table01 table02 mt30">
        <thead>
            <tr>
                <th scope="col" style = "width: 60px"><?= $this->Paginator->sort('date', $title = 'ID') ?></th>
                <th scope="col" style = "width: 90px"><?= $this->Paginator->sort('date', $title = '日付') ?></th>
                <th scope="col" style = "width: 200px">商品名</th>
                <th scope="col" style = "width: 100px">購入者</th>
                <th scope="col" style = "width: 125px">状態</th>
                <th scope="col" style = "width: 200px"><?= __('操作') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($kais as $kai): ?>
            <tr>
                <td><?= $kai["id"] ?></td>
                <?php if(!empty($kai["date"])): ?>
                    <td><?= $kai["date"]->i18nFormat('yyyy/MM/dd') ?></td>
                <?php else: ?>
                    <td></td>
                <?php endif; ?>
                <td><?= $kai["cinnamon"] ?></td>
                <td><?= $users[$kai["user_id"]] ?></td>
                <?php if($kai["status"]==0 || empty($kai["status"])): ?>
                    <td>未決裁</td>
                <?php elseif($kai["status"]==2): ?>
                    <td><?= $kai["kessaibi"]->i18nFormat('yyyy/MM/dd')." 否決" ?></td>
                <?php else: ?>
                    <td><?= $kai["kessaibi"]->i18nFormat('yyyy/MM/dd')." 決裁済" ?></td>
                <?php endif;?>
                    <td class="actions">
                    <div class = "odakoku center">
                        <div style = "margin-left:5px;">
                            <?= $this->Form->create(__("View"), ['type'=>'post','url'=>['action' => 'detailn']]) ?>
                                <?= $this->Form->control('id',['type'=>'hidden','value'=>$kai['id']]) ?>
                                <?= $this->Form->button("[詳細]",["class"=>"ichibtn datalink"]) ?>
                            <?= $this->Form->end(); ?>
                        </div>
                        <div>
                        <?= $this->Form->create(__("View"), ['type'=>'post','url'=>['action' => 'newn']]) ?>
                            <?= $this->Form->control('id',['type'=>'hidden','value'=>$kai['id']]) ?>
                            <?= $this->Form->button("[再購入]",["class"=>"ichibtn datalink"]) ?>
                        <?= $this->Form->end(); ?>
                        </div>
                        <?php if($this->request->getSession()->read('Auth.User.adminfrag') == 0 && $session == $kai["user_id"]): ?>
                            <div>
                            <?= $this->Form->create(__("View"), ['type'=>'post','url'=>['action' => 'editn']]) ?>
                                <?= $this->Form->control('id',['type'=>'hidden','value'=>$kai['id']]) ?>
                                <?= $this->Form->button("[編集]",["class"=>"ichibtn datalink"]) ?>
                            <?= $this->Form->end(); ?>
                            </div>
                            <div>
                                <?= $this->Html->link(__('[出力]'), ['action' => 'excelexport', $kai["id"]]) ?>
                            </div>
                        <?php endif; ?>
                        <?php if($this->request->getSession()->read('Auth.User.adminfrag') == 1): ?>
                            <div>
                            <?= $this->Form->create(__("View"), ['type'=>'post','url'=>['action' => 'editn']]) ?>
                                <?= $this->Form->control('id',['type'=>'hidden','value'=>$kai['id']]) ?>
                                <?= $this->Form->button("[編集]",["class"=>"ichibtn datalink"]) ?>
                            <?= $this->Form->end(); ?>
                            </div>
                            <div>
                            <?= $this->Html->link(__('[出力]'), ['action' => 'excelexport', $kai["id"]]) ?>
                            </div>
                            <div>
                            <?= $this->Form->postLink(__('[削除]'), ['action' => 'delete', $kai["id"]], ['confirm' => __('本当に削除しますか？ # {0}?', $kai["id"])]) ?>
                            </div>
                        <?php endif; ?>
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
<br>