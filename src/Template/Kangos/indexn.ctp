<?php $this->assign('title', '訪問看護情報'); ?>

<div class = "main1">
    <h4 class = "midashih4 mt30">　訪問看護情報</h4>
    <div class = "odakoku mt30">
        <div style = "width: 37vw">
            <div class = "kangos">
                <p style = "margin-bottom: 10px;">本日の訪問看護は</p>
                <?php if(!empty($getkango["kango"]) && !empty($nurse)): ?>
                    <h4 class = "kangomoji">
                        <?=$getkango["kango"]->i18nFormat("HH:mm")?>～　<?=$nurse?>さん
                    </h4>
                <?php else: ?>
                    <h4 class = "kangomoji">ありません</h4>
                <?php endif; ?>
                <br>
            </div>
            <br>
            <div class = "kangos">
                <h4 class = "exportdeka">スケジュール登録</h4>
                <?= $this->Form->create(__("View"),["type"=>"post","url"=>["action"=>"getquery0"]]); ?>
                <?= $this->Form->control('type',['type'=>'hidden','value'=>0]) ?>
                <div class = "odakoku mlv25">
                    <div class = "sdakoku">
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>date("Y")], $years) ?>
                </div>
                    <div class = "sdakoku">
                        <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>date("m")], $months) ?> 
                    </div>
                </div>
                <div class="mt10_button mt20" style = "margin-left: 70%">
                    <?= $this->Form->button(__("表示")) ?>
                </div>
                <br>
            </div>
            <br>
        </div>
        <div style = "width: 37vw; margin-left: 4vw; margin-top: 10px;">
            <?php if(!empty($deterusers)): ?>
                <h4 class = "exportdeka">　本日の訪問看護受診予定者</h4>
                <table class = "table01 table03 mb30">
                    <tr>
                        <th style = "width:6vw;">利用者名</th>
                        <th style = "width:6vw;">状況</th>
                    </tr>
                    <?php foreach($deterusers as $deteruser): ?>
                    <tr>
                        <td><?= $deteruser["name"] ?></td>
                        <?php if($deteruser["flag"] == 1): ?>
                            <td style = "background: rgb(68, 240, 68);">出　勤</td>
                        <?php else: ?>
                            <td style = "background-color: rgb(245, 116, 116);">未出勤</td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        <br>
    </div>
</div>
        
    