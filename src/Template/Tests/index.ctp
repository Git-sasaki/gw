<?php $this->assign('title', 'カレンダー'); ?>

<div class = "main1">
<h4 class="titleh4 mt20">　自動データ作成</h4>

<div class = "odakoku">
    <div class = "shutsuryoku">
        <h4 class = "exportdeka">出勤簿データ自動作成</h4>
        <?= $this->Form->create(__("View"),["type"=>"post","url"=>["action" => "register"]]); ?>
            <div class = "odakoku mlv25" style = "align-items:center;">
                <div class = "sdakoku">
                    <?= $this->Form->control('year',['type'=>'select','label'=>false,'value'=>date("Y")], $years) ?>
                </div>
                <div>年　</div>
                <div class = "sdakoku">
                    <?= $this->Form->control('month',['type'=>'select','label'=>false,'value'=>date("m")], $months) ?>
                </div>
                <div>月　</div>
            </div>
            <div class = "odakoku mlv25 mt10" style = "align-items:center;">
                <div>対象ユーザー </div>
                <div class = "staffbox" style = "margin-left:11px;">
                    <?= $this->Form->select('id',$users,['id'=>'staff_id','label' => false,'empty'=>false]);?>
                </div>
            </div>
        <div class="mt10_button mt20 mlv25">
            <?= $this->Form->button(__("表示")) ?>
        </div>
        <?= $this -> Form -> end(); ?>
    </div>