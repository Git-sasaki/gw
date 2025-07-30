<?php $this->assign('title', '作業日報一覧'); ?>

<div class = "odakoku">
    <div class = "sidemenu mvh120">
    <?= $this -> Form -> create(__("View"),["type" => "post","url" => ["action" => "getquery1"]]); ?>
        <h4 class = "sideh4 ml10 pt15">年月日選択</h4>  
            <div class = "odakoku ml10">
                <div class = "sdakoku">
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>$year], $years) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>$month], $months) ?> 
                </div>
            </div>
            <?php if($this->request-> getSession()->read('Auth.User.adminfrag') == 1): ?>
        <h4 class = "sideh4 ml10 pt15">ユーザー</h4>  
                <div class = "staffbox mt30 ml10">
                    <?= $this->Form->select('id',$staffs,array('id'=>'staff_id','label'=>false,'empty'=>array('0'=>'ALL')));?>
                </div>
            <?php endif; ?>
        <div class="ml10_button mt30 ml10">
            <?= $this->Form->button(__("表示")) ?>
        </div>
        <?= $this -> Form -> end(); ?>
    </div>

    <div class = "main1">
    <h4 class = "midashih4 mt30 mb30"><?php
        if($user_id == 0) {
            echo "作業日報一覧";
        } else {
            echo $name. "さんの作業日報一覧";
        }
    ?> </h4>
    <?php if($this->request-> getSession()->read('Auth.User.adminfrag') == 1): ?>
        <table class="table01 table02 mb30">
            <thead>
                <tr>
                    <th class = "type2" scope="col" style="text-align:center"><?= $this->Paginator->sort('date', $title = '日付') ?></th>
                    <th class = "type2" scope="col"><?= $this->Paginator->sort('user', $title = 'ユーザー') ?></th>
                    <th class = "type2" scope="col">勤務時間</th>
                    <th scope="col">業務内容</th>
                    <th class = "type1" scope="col" class="actions">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                <tr>
                    <?php 
                        $datetime = new DateTime($report->date);
                        $w = (int)$datetime->format('w');
                    ?>
                    <th class="type2" style="background:#89babe"><?= $report->date->i18nFormat('yyyy/MM/dd')."(".$weekList[$w].")" ?></th>
                    <td><?= $staffs[$report->user_id] ?></td>
                    <td><?= $report["intime"]->i18nFormat("H:mm")." ～ ".$report["outtime"]->i18nFormat("H:mm") ?></td>
                    <td><?= h($report->content) ?></td>
                    <td class="actions">
                        <?php $exdate = explode("-",$report["date"]->i18nFormat("YYYY-MM-dd")); ?>
                        <div class = "odakoku" style = "margin: 0 3.5%;">
                            <!-- 編集画面 -->
                            <div>
                                <?= $this -> Form -> create(__("View"),[
                                        "type" => "post",
                                        "url" => ["action" => "getquery0"]]); ?>
                                <?= $this->Form->control('year',['type'=>'hidden','value'=>$exdate[0]]) ?>
                                <?= $this->Form->control('month',['type'=>'hidden','value'=>$exdate[1]]) ?>
                                <?= $this->Form->control('date',['type'=>'hidden','value'=>$exdate[2]]) ?>
                                <?= $this->Form->control('id',['type'=>'hidden','value'=>$report["user_id"]]) ?>
                                <?= $this->Form->button("[編集]",["class"=>"ichibtn datalink"]) ?>
                                <?= $this->Form->end(); ?>
                            </div>

                            <!-- 詳細画面 -->
                            <div class = "ml10"> 
                                <?= $this -> Form -> create(__("View"),[
                                        "type" => "post",
                                        "url" => ["action" => "detailn",$report["id"]]]); ?>
                                <?= $this->Form->control('id',['type'=>'hidden','value'=>$report["id"]]) ?>
                                <?= $this->Form->button("[詳細]",["class"=>"ichibtn datalink"]) ?>
                                <?= $this->Form->end(); ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif($this->request-> getSession()->read('Auth.User.adminfrag') == 0): ?>
        <table class="table01 table02 mb30">
            <thead>
                <tr>
                    <th class = "type2" scope="col" style="text-align:center"><?= $this->Paginator->sort('date', $title = '日付') ?></th>
                    <th class = "type2" scope="col"><?= $this->Paginator->sort('time', $title = '勤務時間') ?></th>
                    <th scope="col"><?= $this->Paginator->sort('content', $title = '業務内容') ?></th>
                    <th class = "type1" scope="col" class="actions"><?= __('操作') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                <tr>
                    <?php 
                        $datetime = new DateTime($report["date"]);
                        $w = (int)$datetime->format('w');
                    ?>
                    <th style="background:#89babe"><?= $report["date"]->i18nFormat('yyyy/MM/dd')."(".$weekList[$w].")" ?></th>
                    <td><?= $report["intime"]->i18nFormat("H:mm")." ～ ".$report["outtime"]->i18nFormat("H:mm") ?></td>
                    <td class><?= h($report["content"]) ?></td>
                    <td class="actions">
                        <?= $this->Html->link(__('[詳細を見る]'), ['action'=>'detailn',$report["id"]]) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('最初')) ?>
            <?= $this->Paginator->prev('< ' . __('前')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('次') . ' >') ?>
            <?= $this->Paginator->last(__('最後') . ' >>') ?>
        </ul>
    </div>