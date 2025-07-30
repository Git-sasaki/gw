<?php $this->assign('title', '在宅勤務一覧'); ?>

<div class = "odakoku">
    <div class = "sidemenu mvh120">
    <?= $this -> Form -> create(__("View"),["type" => "post","url" => ["action" => "getquery0"]]); ?>
        <?= $this->Form->control('type',['type'=>'hidden','value'=>2]) ?>
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
                    <?= $this->Form->select('user_id',$remotes,['id'=>'staff_id','label'=>false,'empty'=>false, 'value'=>$user_id]); ?>
                </div>
            <?php endif; ?>
        <div class="ml10_button mt30 ml10">
            <?= $this->Form->button(__("表示")) ?>
        </div>
        <?= $this -> Form -> end(); ?>
    </div>

    <div class = "main1">
    <!-- 在宅就労記録の一覧を表示 -->
    <h4 class = "midashih4 mt30 mb30">　<?= $year ?>年 <?= $month ?>月 在宅就労記録一覧</h4>
    <table class="table01 table02 mb30">
        <thead>
            <tr>
                <th class = "type2" scope="col" style="text-align:center">
                    <?= $this->Paginator->sort('date', $title = '日付') ?>
                </th>
                <th class = "type2" scope="col">勤務時間</th>
                <th scope="col">業務内容</th>
                <th class = "type1" scope="col" class="actions">操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($getremotes as $getremote): ?>
            <tr>
                <td>
                    <?php 
                        $date = $getremote["date"]->i18nFormat("YYYY/MM/dd");
                        $exdate = explode("/",$date);
                        $datestamp = mktime(0,0,0,$exdate[1],$exdate[2],$exdate[0]);
                    ?>
                    <?= $date." (".$weekList[date('w',$datestamp)].")"; ?>
                </td>
                <td>
                    <?= $getremote["intime"]->i18nFormat("HH:mm")." ～ ".$getremote["outtime"]->i18nFormat("HH:mm") ?>
                </td>
                <td>
                    <?= $getremote["work"]; ?>
                </td>
                <td>
                    <div class = "odakoku center">
                        <?= $this -> Form -> create(__("View"),["type" => "post","url" => ["action" => "getquery0"]]); ?>
                        <?= $this->Form->control('type',['type'=>'hidden','value'=>0]) ?>
                        <?= $this->Form->control('year',['type'=>'hidden','value'=>$exdate[0]]) ?>
                        <?= $this->Form->control('month',['type'=>'hidden','value'=>$exdate[1]]) ?>
                        <?= $this->Form->control('date',['type'=>'hidden','value'=>$exdate[2]]) ?>
                        <?= $this->Form->control('user_id',['type'=>'hidden','value'=>$getremote["user_id"]]) ?>
                        <?= $this->Form->button("[編集]",["class"=>"ichibtn datalink"]) ?>
                        <?= $this->Form->end(); ?>

                        <?= $this->Form->postLink(__('[削除]'), ['action'=>'delete',"?"=>["type"=>0], $getremote["id"]],
                                ['confirm' => __('本当に削除しますか？ # {0}?', $getremote["id"])]) ?>
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

    <!-- 在宅週間記録の出力 -->
    <h4 class = "midashih4 mt30 mb30">　<?= $year ?>年 <?= $month ?>月 在宅週間記録一覧</h4>
    <table class="table01 table02 mb30">
        <thead>
            <tr>
                <th class = "type2">
                    <?= $this->Paginator->sort('date', $title = '評価実施日') ?>
                </th>
                <th scope="col" style="text-align:center">評価対象日</th>
                <th scope="col" style="text-align:center">記録者</th>
                <th class = "type1" scope="col" class="actions">操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($getweeklies as $getweekly): ?>
            <tr>
                <?php 
                    $jdate = $getweekly["jdate"];
                    $hdate = $getweekly["hdate"];
                    $odate = $getweekly["odate"];
                ?>
                <?php if(empty($jdate) || empty($hdate) || empty($odate)): ?>
                    <td>日付登録エラー</td>
                    <td>日付登録エラー</td>
                <?php else: ?>
                    <?php 
                        $jexdate = explode("-",$jdate->i18nFormat("YYYY-MM-dd"));
                        $jstamp = mktime(0,0,0,$jexdate[1],$jexdate[2],$jexdate[0]);    
                        $hexdate = explode("-",$jdate->i18nFormat("YYYY-MM-dd"));
                        $hstamp = mktime(0,0,0,$hexdate[1],$hexdate[2],$hexdate[0]);    
                        $oexdate = explode("-",$jdate->i18nFormat("YYYY-MM-dd"));
                        $ostamp = mktime(0,0,0,$oexdate[1],$oexdate[2],$oexdate[0]);    
                    ?>
                    <td><?= $jdate->i18nFormat("YYYY/MM/dd")." (".$weekList[date('w',$jstamp)].")"; ?></td>
                    <td><?= $hdate->i18nFormat("YYYY/MM/dd")." (".$weekList[date('w',$hstamp)].") ～ "
                           .$odate->i18nFormat("YYYY/MM/dd")." (".$weekList[date('w',$ostamp)].")"?></td>
                <?php endif; ?>
                <td><?= $staffs[$getweekly["user_staffid"]]; ?></td>
                <td>
                    <div class = "odakoku center">
                        <?php if(empty($jdate) || empty($hdate) || empty($odate)): ?>

                        <?php else: ?>
                            <?php $jexdate = explode("-",$getweekly["jdate"]->i18nFormat("YYYY-MM-dd")); ?>
                            <?= $this->Form->create(__("View"),["type" => "post","url" => ["action" => "getquery0"]]); ?>
                            <?= $this->Form->control('type',['type'=>'hidden','value'=>1]) ?>
                            <?= $this->Form->control('year',['type'=>'hidden','value'=>$jexdate[0]]) ?>
                            <?= $this->Form->control('month',['type'=>'hidden','value'=>$jexdate[1]]) ?>
                            <?= $this->Form->control('date',['type'=>'hidden','value'=>$jexdate[2]]) ?>
                            <?= $this->Form->control('user_id',['type'=>'hidden','value'=>$getremote["user_id"]]) ?>
                            <?= $this->Form->button("[編集]",["class"=>"ichibtn datalink"]) ?>
                            <?= $this->Form->end(); ?>
                        <?php endif; ?>
                        <?= $this->Form->postLink(__('[削除]'), ['action'=>'delete',"?"=>["type"=>1], $getweekly["id"]],
                                ['confirm' => __('本当に削除しますか？ # {0}?', $getweekly["id"])]) ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

