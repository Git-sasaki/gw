<?php $this->assign('title', '購入申請詳細'); ?>

<div class = "main1">
    <?php if($kaimono["type"]==0): ?>
        <h4 class="midashih4 mt30 mb30">
            <div class = "odakoku">
                <div>物品購入伺</div>
                <span style = "font-size: 16px; margin-left:10px;">
                    <?= $this->Form->create(__("View"), ['type'=>'post','url'=>['action' => 'editn']]) ?>
                        <?= $this->Form->control('id',['type'=>'hidden','value'=>$id]) ?>
                        <?= $this->Form->button("[編集]",["class"=>"ichibtn datalink"]) ?>
                    <?= $this->Form->end(); ?>
                </span>
            </div>
        </h4>
    <?php else: ?>
        <h4 class="midashih4 mt30 mb30">
            物品購入報告
            <span style = "font-size: 16px; margin-left:10px;">
                <?= $this->Form->create(__("View"), ['type'=>'post','url'=>['action' => 'editn']]) ?>
                    <?= $this->Form->control('id',['type'=>'hidden','value'=>$id]) ?>
                    <?= $this->Form->button("[編集]",["class"=>"ichibtn datalink"]) ?>
                <?= $this->Form->end(); ?>
            </span>
        </h4>
    <?php endif; ?>
    
    <table class="table01 table02">
        <tr>
            <th class="date bokkoht" scope="row"><?= __('日付') ?></th>
            <?php if(!empty($kaimono)): ?>
                <td class="bokkot"><?= $kaimono["date"]->i18nFormat('yyyy/MM/dd') ?></td>
            <?php endif; ?>
            <th class="date bokkoht bokkohb" scope="row"><?= __('購入者') ?></th>
            <td class="bokkot"><?= $users[$kaimono["user_id"]] ?></td>
            <th class="date bokkoht bokkohb" scope="row"><?= __('価格') ?></th>
            <td class="bokkot"><?= $kaimono["price"]."　円" ?></td>
        </tr>
    <?php for($i=0; $i<3; $i++): ?>
        <?php if(!empty($details[$i]["cinnamon"])): ?>
        <tr>
            <?php $number = $i+1; ?>
            <th class="date bokkoht" scope="row"><?= __('購入品'.$number) ?></th>
            <td class="bokkot"><?= $details[$i]["cinnamon"] ?></td>
            <th class="date bokkoht" scope="row"><?= __('詳細') ?></th>
            <td class="bokkot"><?= $details[$i]["detail"] ?></td>
            <th class="date bokkoht" scope="row"><?= __('購入先') ?></th>
            <td class="bokkot"><?= $details[$i]["shop"] ?></td>
        </tr>
            <?php if(!empty($details[$i]["url"])): ?>
                <tr>
                    <th class="date bokkoht" scope="row"><?= __('URL') ?></th>
                    <td class="bokkot" colspan = "5">
                        <a href = "<?= $details[$i]["url"] ?>" target = "_new" rel = "noopener noreferrer">
                            <?= $details[$i]["url"] ?>
                        </a>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endif; ?>
    <?php endfor; ?>
        <tr>
            <th class="date" scope="row"><?= __('状態') ?></th>
            <?php if($kaimono["status"]!=1): ?>
                <td class = "bokkot" colspan = "5"><?= $statusbun[$kaimono["status"]] ?></td>
            <?php else: ?>
                <td><?= $statusbun[$kaimono["status"]] ?></td>
            <?php endif; ?>
        <?php if($kaimono["status"]==1): ?>
            <th class="date" scope="row"><?= __('決裁日') ?></th>
            <td><?= $kaimono["kessaibi"]->i18nFormat('yyyy/MM/dd') ?></td>
            <th class="date" scope="row"><?= __('決裁者') ?></th>
            <td><?= $users[$kaimono["kessaisha"]] ?></td>
        <?php endif; ?>
    </table>