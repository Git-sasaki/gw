<?php $this->assign('title', '作業日報詳細'); ?>

<div class = "main1 mb30">
        <h4 class="midashih4 mt30 mb30">
        <div class = "odakoku">
        <?= $name ."さんの作業日報" ?>
            <span style = "font-size: 16px; margin-left:10px;">
                <!-- 編集画面 -->
                <?php $exdate = explode("-",$report["date"]->i18nFormat("YYYY-MM-dd")); ?>
                <?= $this -> Form -> create(__("View"),[
                        "type" => "post",
                        "url" => ["action" => "getquery0"]]); ?>
                <?= $this->Form->control('year',['type'=>'hidden','value'=>$exdate[0]]) ?>
                <?= $this->Form->control('month',['type'=>'hidden','value'=>$exdate[1]]) ?>
                <?= $this->Form->control('date',['type'=>'hidden','value'=>$exdate[2]]) ?>
                <?= $this->Form->control('id',['type'=>'hidden','value'=>$report["user_id"]]) ?>
                <?= $this->Form->button("[編集]",["class"=>"ichibtn aoao", "style"=>"width: 50px;"]) ?>
                <?= $this->Form->end(); ?>
            </span>
        </div>
    </h4>
    <table class="table01 table02">
        <tr>
            <th class="w175 bokkoht">日付</td>
            <?php 
                $datetime = new DateTime($report->date);
                $w = (int)$datetime->format('w');
            ?>
            <td class = "bokkot" colspan="2"><?= $report['date']->i18nFormat('yyyy/MM/dd (' . $weekList[$w] . ')') ?></td>
            <th class = "w175 bokkoht">勤務時間</td>
            <td class = "bokkot" colspan="2"><?= $report['intime']->i18nFormat("H:mm")." ～ ".$report['outtime']->i18nFormat("H:mm") ?></td>
            <th class = "w175 bokkoht">喫食データ</td>
            <?php $kisssyokulist = ["", "完食", "1/2", "1/3", "1/4"]; ?>
            <td class = "bokkot" colspan="1"><?= $kisssyokulist[$report['kissyoku']] ?></td>
        </tr>
        <tr>
            <th class="w175">業務内容</td>
            <td colspan="7"><?= $report['content'] ?></td>
        </tr>
        <?php for($i=0; $i<=2; $i++): ?>
            <tr>
            <?php if($i==0): ?>
                <th class="w175" rowspan="3">業務内容の詳細</th>
                <td colspan="3"><?= $red[$i]['item'] ?></td>
                <td colspan="4"><?= $red[$i]['reportcontent'] ?></td>    
            <?php else: ?>
                <?php if(!empty($red[$i]['item'])): ?>
                    <td colspan="3"><?= $red[$i]['item'] ?></td>
                    <td colspan="4"><?= $red[$i]['reportcontent'] ?></td>
                <?php endif; ?>
            <?php endif; ?>
            </tr>
        <?php endfor; ?>
        <tr>
            <th class="w175">反省・特記事項</td>
            <td class = "bokkot" colspan="7"><?= $report['notice'] ?></td>
        </tr>
        <tr>
            <th class="w175">次回の予定</td>
            <td colspan="7"><?= $report['plan'] ?></td>
        </tr>
<?php if($this->request->getSession()->read('Auth.User.adminfrag') == 1): ?>
        <tr>
            <th class="w175 bokkoht">業務内容・様子</td>
            <td class="bokkot" colspan="7"><?= $report['state'] ?></td>
        </tr>
        <tr>
            <th class="w175">体調・連絡事項など</td>
            <td colspan="7"><?= $report['information'] ?></td>
        </tr>
        <tr>
            <th class="w175">備考</td>
            <td colspan="7"><?= $report['bikou'] ?></td>
        </tr>
        <tr>
            <th class="w175">記録者</td>
            <?php if(!empty($staffs[$report['recorder']])): ?>
                <td colspan="7"><?= $staffs[$report['recorder']] ?></td>
            <?php else: ?>
                <td colspan="7"><?= $report['recorder'] ?></td>
            <?php endif; ?>
        </tr>
    </table>
<?php endif; ?>
<br>
</div>