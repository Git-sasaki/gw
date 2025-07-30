<?php $this->assign('title', '訪問看護情報'); ?>

    <div class = "main3">
        <h4 class = "midashih4 mt30"> <?=$year?> 年 <?=$month?> 月 </h4>
    <?= $this -> Form -> create(__("View"),["type" => "post","url" => ["controller"=>"Kangos","action" => "register"]]); ?>
        <?= $this->Form->control('type',['type'=>'hidden','value'=>0]) ?>
        <?= $this->Form->control('year',['type'=>'hidden','value'=>$year]) ?>
        <?= $this->Form->control('month',['type'=>'hidden','value'=>$month]) ?>
    <table class="table01 table02 mt30 mb30">
        <thead>
            <tr>
                <th scope="col" class = "kangow">日</th>
                <th scope="col" class = "kangow">月</th>
                <th scope="col" class = "kangow">火</th>
                <th scope="col" class = "kangow">水</th>
                <th scope="col" class = "kangow">木</th>
                <th scope="col" class = "kangow">金</th>
                <th scope="col" class = "kangow">土</th>
            </tr>
        </thead>
        <tbody>
            <?php $count=0;?>
            <!-- 一日までの空欄の処理 -->
            <?php foreach($maes as $mae): ?>
                <?php if($count==0): ?>
                    <tr>
                <?php endif; ?>
                <td><div></div></td>
                <?php $count++ ?>
            <?php endforeach; ?>
            <!-- 日にちの処理 -->
            <?php foreach($dates as $date): ?>
                <?php $d = $date["hidake"] ?>
                <?php if($count==0): ?>
                    <tr>
                <?php endif; ?>
                <?php if(date('w',mktime(0,0,0,$month,$date["hidake"],$year)) == 0): ?>
                    <?php if(date('Y-m-d') == date('Y-m-d',mktime(0,0,0,$month,$date["hidake"],$year))): ?>
                        <td class = "kanpad" style = "background-color: #a3f1ff;">
                    <?php else: ?>
                        <td class = "kanpad">
                    <?php endif; ?>
                    <?php $yobi = "sunday fs20"?>
                <?php elseif(date('w',mktime(0,0,0,$month,$date["hidake"],$year)) == 6): ?>
                    <?php if(date('Y-m-d') == date('Y-m-d',mktime(0,0,0,$month,$date["hidake"],$year))): ?>
                        <td class = "kanpad" style = "background-color: #a3f1ff;">
                    <?php else: ?>
                        <td class = "kanpad">
                    <?php endif; ?>
                    <?php $yobi = "saturday fs20"?>  
                <?php else: ?>
                    <?php if(date('Y-m-d') == date('Y-m-d',mktime(0,0,0,$month,$date["hidake"],$year))): ?>
                        <td class = "kanpad" style = "background-color: #a3f1ff;">
                    <?php else: ?>
                        <td class = "kanpad">
                    <?php endif; ?>
                    <?php $yobi = "heijitsu fs20"?>
                <?php endif; ?>

                <!-- 日付を表示 -->
                <?= $this->Html->link(__($date["hidake"]),
                                ['action'=>'editn','?'=>["year"=>$year,"month"=>$month,"date"=>$date["hidake"]]],
                                ['class'=>$yobi]
                                ) ?>
                <div class = "odakoku mt10 center">
                    <div>
                        <?php if(empty($date["kango"])): ?>
                            <?= $this->Form->text("kango[$d]",["type" => "time","class"=>"kantime"]); ?>
                        <?php else: ?>
                            <?= $this->Form->text("kango[$d]",["type" => "time","class"=>"kantime",
                                                               "value"=>$date["kango"]->i18nFormat("HH:mm")]); ?>                           
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php if(empty($date["nurse"])): ?>
                            <?= $this->Form->select("nurse[$d]",$nurses,['empty'=>['0'=>null]]); ?>
                        <?php else: ?>
                            <?= $this->Form->select("nurse[$d]",$nurses,['empty'=>['0'=>null],"class"=>"nerabi",
                                                                         "value"=>$date["nurse"]]); ?>
                        <?php endif; ?>
                    </div>
                </div>
                </td>
                <?php $count++ ?>
                <?php if($count==7): ?>
                    </tr>
                    <?php $count=0?>
                <?php endif; ?>
            <?php endforeach; ?>
            <!-- 月末の後の空欄の処理 -->
            <?php foreach($atos as $ato): ?>
                <td><div></div></td>
                <?php $count++ ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="ml10_button mt30 ml10">
        <?= $this->Form->button(__("登録")) ?>
    </div>
    <br>
    </div>
</div>