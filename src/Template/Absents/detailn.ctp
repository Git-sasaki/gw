<?php $this->assign('title', '欠席情報詳細'); ?>

<div class = "main1">
<h4 class="midashih4 mt30 mb30">
    <div class = "odakoku">
        <div><?= $name."さんの欠席情報" ?></div>
        <span style = "font-size: 16px; margin-left:10px;">
            <!--<?= $this->Html->link('[編集]', ['action' => 'editn', $mitai['id']]);?>-->
            <?= $this->Form->create(__("View"), ['type'=>'post','url'=>['action' => 'editn']]) ?>
            <?= $this->Form->control('id',['type'=>'hidden','value'=>$mitai['id']]) ?>
            <?= $this->Form->button("[編集]",["class"=>"ichibtn datalink"]) ?>
            <?= $this->Form->end(); ?>
        </span>
    </div>
</h4>
<table class="table01 table02">
    <tr>
        <th class="caption bokkoht">受付日付</td>
        <td class="bokkot"><?= $mitai['date']->i18nFormat('yyyy/MM/dd') ?></td>
        <th class="caption bokkoht" style="border-bottom:#FFF 1px solid">受付時間</td>
        <td class="bokkot"><?= $mitai['time']->i18nFormat('H:mm'); ?></td>
        <th class="caption bokkoht" style="border-bottom:#FFF 1px solid">連絡手段</td>
        <td class="bokkot"><?= $mitai['shudan'] ?></td>
    </tr>
    <tr>
        <th class="caption bokkoht">欠勤日付</td>
        <td class="bokkot"><?= $mitai['kekkindate']->i18nFormat('yyyy/MM/dd') ?></td>
        <th class="caption bokkoht" style="border-bottom:#FFF 1px solid">欠勤加算対象</td>
        <td class="bokkot"><?= ($mitai['kekkinkasan'] == 1) ? '✔' : '' ?></td>
    </tr>
    <tr>
        <th class="caption date">受けた人</td>
        <td><?= $staffname ?></td>
        <th class="caption date">連絡者</td>
        <td><?= $mitai['relation'] ?></td>
        <th class="caption date">次回利用の促し</td>
        <td><?= $okona[$mitai["next"]] ?></td>
    </tr>
    <tr>
        <th class="caption date">内容</td>
        <td colspan="5"><?= $mitai['naiyou'] ?></td>
    </tr>
    <tr>
        <th class="caption date">相手の回答1</td>
        <?php if(empty($mitai['answer1'])){
            $answer1 = "---";
        } else {
            $answer1 = $mitai['answer1']; 
        }
        ?>
        <td colspan="5"><?= $answer1 ?></td>
        </tr>
    <tr>
        <th class="caption date">相手の回答2</td>
        <?php if(empty($mitai['answer2'])){
            $answer2 = "---";
        } else {
            $answer2 = $mitai['answer2']; 
        }
        ?>
        <td colspan="5"><?= $answer2 ?></td>
    </tr>
    <tr>
        <th class="caption date">相手の回答3</td>
        <?php if(empty($mitai['answer3'])){
            $answer3 = "---";
        } else {
            $answer3 = $mitai['answer3']; 
        }
        ?>
        <td colspan="5"><?= $answer3 ?></td>
    </tr>
    <tr>
        <th class="caption date">相手の回答4</td>
        <?php if(empty($mitai['answer4'])){
            $answer4 = "---";
        } else {
            $answer4 = $mitai['answer4']; 
        }
        ?>
        <td colspan="5"><?= $answer4 ?></td>
    </tr>
    <tr>
        <th class="caption date">備考</td>
        <td colspan="5"><?= $mitai['bikou'] ?></td>
    </tr>       
</table>
