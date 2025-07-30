<?php $this->assign('title', '訪問看護情報'); ?>

<div class = "main3">
    <h4 class = "midashih4 mt30">　<?=$year?> 年 <?=$month?> 月 <?=$date?> 日</h4>
    <?= $this->Form->create(__("View"),[
        "type"=>"post",
        "url"=>["action"=>"register2","?"=>["year"=>$year,"month"=>$month,"date"=>$date]]
    ]); ?>

        <div class = "odakoku" style = "margin-left: 4.5vw;">
        <?php $count = 0; ?>
        <?php $i = 0; ?>
        <?php foreach($attendances as $attendance): ?>
            <?= $this->Form->control("id[$i]",['type'=>'hidden','value'=>$attendance["id"]]); ?>
            <div style = "width: 8vw; margin-right: 1vw">
                <?php if($attendance["medical"] == 1): ?>
                    <?= $this->Form->control("medical[$i]",[
                        "type" => "checkbox",
                        "label"=>"　".$attendance["lastname"],
                        "value"=>1,
                        "checked"=>true
                    ]); ?>
                <?php else: ?>
                    <?= $this->Form->control("medical[$i]",[
                        "type" => "checkbox",
                        "label"=>"　".$attendance["lastname"],
                        "value"=>1
                    ]); ?>
                <?php endif; ?>
            </div>
            <?php $i++; ?>
            <?php $count++; ?>
            <?php if($count==9): ?>
                </div>
                <div class = "odakoku" style = "margin-left: 4.5vw;">
                <?php $count = 0; ?>
            <?php endif; ?>
        <?php endforeach; ?>
        </div>

        <div class="mt10_button mt20" style = "margin-left: 4.5vw;">
            <?= $this->Form->button(__("登録")) ?>
        </div>

    <?= $this->Form->end(); ?>
</div>
