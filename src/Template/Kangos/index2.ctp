<?php $this->assign('title', '訪問看護情報'); ?>

    <div class = "main3">
        <h4 class = "midashih4 mt30"> <?=$year?> 年 <?=$month?> 月 </h4>
    <?= $this -> Form -> create(__("View"),["type" => "post","url" => ["action" => "register"]]); ?>
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
            <?php foreach($maes as $mae): ?>
                <?php if($count==0): ?>
                    <tr>
                <?php endif; ?>
                <td><div></div></td>
                <?php $count++ ?>
            <?php endforeach; ?>
            <?php foreach($dates as $date): ?>
                <?php $d = $date["hidake"] ?>
                <?php if($count==0): ?>
                    <tr>
                <?php endif; ?>
                <?php if($holidays->isHoliday(new \DateTime(date('Y-m-d',mktime(0,0,0,$month,$date["hidake"],$year)))) == 1): ?>
                    <?php if(date('Y-m-d') == date('Y-m-d',mktime(0,0,0,$month,$date["hidake"],$year))): ?>
                        <td class = "kangow sunday" style = "background-color: #a3f1ff;">
                    <?php else: ?>
                        <td class = "kangow sunday">
                    <?php endif; ?>
                <?php elseif(date('w',mktime(0,0,0,$month,$date["hidake"],$year)) == 0): ?>
                    <?php if(date('Y-m-d') == date('Y-m-d',mktime(0,0,0,$month,$date["hidake"],$year))): ?>
                        <td class = "kangow sunday" style = "background-color: #a3f1ff;">
                    <?php else: ?>
                        <td class = "kangow sunday">
                    <?php endif; ?>
                <?php elseif(date('w',mktime(0,0,0,$month,$date["hidake"],$year)) == 6): ?>
                    <?php if(date('Y-m-d') == date('Y-m-d',mktime(0,0,0,$month,$date["hidake"],$year))): ?>
                        <td class = "kangow saturday" style = "background-color: #a3f1ff;">
                    <?php else: ?>
                        <td class = "kangow saturday">
                    <?php endif; ?>
                <?php else: ?>
                    <?php if(date('Y-m-d') == date('Y-m-d',mktime(0,0,0,$month,$date["hidake"],$year))): ?>
                        <td class = "kangow heijitsu" style = "background-color: #a3f1ff;">
                    <?php else: ?>
                        <td class = "kangow heijitsu">
                    <?php endif; ?>
                <?php endif; ?>

                <div class = "centermoji" style = "margin-top: 1px; width: 100%; align-items: center;">
                    <div style = "margin-bottom: 15px;"><?= $date["hidake"] ?></div>
                    <?php if(!empty($date["kango"] && !empty($date["nurse"]))): ?>
                        <div style = "color:black; margin-bottom: 5px;">
                            <?php echo $date["kango"]->i18nFormat("HH:mm")."～ ".$nurses[$date["nurse"]-1]["name"]?>
                        </div>
                    <?php endif; ?>                     
                </div>
                </td>
                <?php $count++ ?>
                <?php if($count==7): ?>
                    </tr>
                    <?php $count=0?>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php foreach($atos as $ato): ?>
                <td><div></div></td>
                <?php $count++ ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</div>
<br>