<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/additional-methods.min.js"></script>
<script type="text/javascript" src="/js/validate-config.js"></script>
<script type="text/javascript">
    $(function() {
        $.validator.addMethod(
            "time",
            function(value,element) {
                return this.optional(element) || /^([0-9]|[1-2][0-3])+:+([0-5][0-9])$/.test(value);
            },
            "「時間:分」で入力"
        );
        $("#editform").validate({
            rules: {
                <?php for($i = 1; $i <= date("t",$timestamp); $i++) : ?>
                <?= '"intime['.$i.']"'; ?>: {
                    time: true,
                },
                <?php endfor; ?>

                <?php for($i = 1; $i <= date("t",$timestamp); $i++) : ?>
                <?= '"outtime['.$i.']"'; ?>: {
                    time: true,
                },
                <?php endfor; ?>
                
                <?php for($i = 1; $i <= date("t",$timestamp); $i++) : ?>
                <?= '"resttime['.$i.']"'; ?>: {
                    time: true,
                },
                <?php endfor; ?>

                <?php for($i = 1; $i <= date("t",$timestamp); $i++) : ?>
                <?= '"bikou['.$i.']"'; ?>: {
                    maxlength: 11,
                },
                <?php endfor; ?>
            },
            messages: {
                <?php for($i = 1; $i <= date("t",$timestamp); $i++) : ?>
                <?= '"bikou['.$i.']"'; ?>: {
                    maxlength: "11文字以内で入力",
                },
                <?php endfor; ?>
            }
        });
        var btn = document.getElementById('confirm');
        btn.addEventListener('click', function() {
            var pass = window.prompt("誤操作防止のため、パスワードを入力してください", "");
            // 削除する際に使用するパスワードを変更したい場合はpassの値を修正する
            if(pass == "redmoon") {
                location.href = "http://[::1]:8765/time-cards/delete?year="+<?=$year?>+"&month="+<?=$month?>+"&user_id="+<?=$user_id?>;
            } else {
                alert("パスワードが違います");
            }
        })
    });
</script>

<?php $this->assign('title', '出勤簿テスト'); ?>

<div class = "odakoku">
    <div class = "sidemenu antisp">
    <?= $this -> Form -> create(__("View"),["type" => "post","url" => ["action" => "getquery0"]]); ?>
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
                    <?= $this->Form->select('id',$sideusers,['id'=>'staff_id','label' => false,'value'=>$user_id,'empty'=>false]);?> 
                </div>
            <?php endif; ?>
        <div class="ml10_button mt30 ml10">
            <?= $this->Form->button(__("表示")) ?>
        </div>
        <?= $this -> Form -> end(); ?>
    </div>

    <div class = "main2 pt15">
        <?php if($user_id == 12): ?>
            <h4 class = "midashih4 mlv25 mb10">
                <?= $year." 年 ".$month." 月 　".$getname." さん "?>
                <span style = "font-size: 16px; margin-left:10px;">
                    <a id = "confirm">[この月のデータを削除]</a>
                    <?= $this->Html->link('[門脇さんコマンド]', ['action'=>'schedule', "?" => array("id" => $user_id, "year" => $year, "month" => $month)]); ?>
                </span>
            </h4>
        <?php elseif($this->request-> getSession()->read('Auth.User.adminfrag') != 1): ?>
            <h4 class = "midashih4 mlv25 mb10"><?= $year." 年 ".$month." 月 "?></h4>
        <?php else: ?>
            <h4 class = "midashih4 mlv25 mb10"><?= $year." 年 ".$month." 月 　".$getname." さん "?>
            <?php if($this->request-> getSession()->read('Auth.User.adminfrag') == 1): ?>
                <span style = "font-size: 16px; margin-left:10px;">
                    <a id = "confirm">[この月のデータを削除]</a>
                </span>
            <?php endif; ?>
            </h4>
        <?php endif; ?>
    
    <div class = "spmenu">
        <?= $this -> Form -> create(__("View"),["type" => "post","url" => ["action" => "getquery0"]]); ?>
        <h4 class = "sideh4 ml10 pt15">　年月日選択</h4>  
            <div class = "odakoku mlv25" style = "align-items: center;">
                <div class = "sdakoku">
                    <?= $this->Form->control('year',['type'=>'select','label'=>false,'value'=>$year], $years) ?>
                </div>
                <div>年</div>
                <div class = "sdakoku ml10">
                    <?= $this->Form->control('month',['type'=>'select','label'=>false,'value'=>$month], $months) ?> 
                </div>
                <div>月</div>
            </div>
        <div class="spmenu_button mt10 ml10" style = "margin-bottom: 15px;">
            <?= $this->Form->button(__("表示")) ?>
        </div>
        <?= $this->Form->end(); ?>
    </div>
    
    <div class = "sticky2">
    <?php if($this->request-> getSession()->read('Auth.User.adminfrag') != 1): ?>
            <?= $this->Form->create(__("View"),
                ["type" => "post","id" => "editform","url" => ["controller" => "TimeCards","action" => "register2", "?" => array("staff_id" => $user_id, "year" => $year, "month" => $month)]],
                array('target' => '_blank')); ?>
        <?php elseif($adminfrag == 1): ?>
            <?= $this->Form->create(__("View"),
                ["type" => "post","id" => "editform","url" => ["controller" => "TimeCards","action" => "register", "?" => array("staff_id" => $user_id, "year" => $year, "month" => $month)]],
                array('target' => '_blank')); ?>
        <?php elseif($adminfrag == 0): ?>
            <?= $this->Form->create(__("View"),
                ["type" => "post","id" => "editform","url" => ["controller" => "TimeCards","action" => "register2", "?" => array("staff_id" => $user_id, "year" => $year, "month" => $month)]],
                array('target' => '_blank')); ?>
        <?php endif; ?>
    <table class = "attable">
    <tr>
        <th class = "sema2">日付</th>
        <th class = "sema">曜日</th>
        <th class = "futusp">時間</th>
        <th class = "futu antisp">開始時間</th>
        <th class = "futu antisp">終了時間</th>
        <th class = "futu antisp">休憩時間</th>
        <?php if($adminfrag==1): ?>
            <th class = "futu">残業時間</th>
        <?php else: ?>
            <th class = "sema2 center antisp">送迎(往)</th>
            <th class = "sema2 center antisp">送迎(複)</th>
            <th class = "sema antisp">食事</th>
            <th class = "sema antisp">医療</th>
            <?php endif; ?>
        <th class = "sema antisp">施設外</th>
        <th class = "sema">公休</th>
        <th class = "sema">有給</th>
        <th class = "sema antisp">欠勤</th>
        <th class = "hiro antisp">備考</th>
        <th class = "sema">在宅</th>
        <?php if($adminfrag==0): ?>
            <th class = "futu antisp">担当者</th>
        <?php endif; ?>
    </tr>

    <?php for($i = 1; $i <= date("t",$timestamp); $i++) : ?>
    <?php $timestamp = mktime(0,0,0,$month,$i,$year); ?>
    <tr class = "hvrow">
        <td class = "sema" id = "jump<?=$i?>"><?= $i; ?></td>
        <td class = "sema"><?= $weekList[date("w",$timestamp)]; ?></td>
        <td class = "futusp timesp">
            <div class = "odakoku" style = "align-items:center; justify-content:center;">
                <div>出勤：</div>
                <?php 
                if((empty($results[$i]["intime"]))){
                    echo $this->Form->text("intime[$i]",["type" => "time","class" => "time"]);
                }else{
                    echo $this->Form->text("intime[$i]",["type" => "time","class" => "time","value" => $results[$i]["intime"]->i18nFormat("HH:mm")]);
                }?>
            </div>
            <div class = "odakoku" style = "align-items:center; justify-content:center;">
                <div>退勤：</div>
                <?php 
                if((empty($results[$i]["outtime"]))){
                    echo $this->Form->text("outtime[$i]",["type" => "time","class" => "time"]);
                }else{
                    echo $this->Form->text("outtime[$i]",["type" => "time","class" => "time","value" => $results[$i]["outtime"]->i18nFormat("HH:mm")]);
                }?>
            </div>
            <div class = "odakoku" style = "align-items:center; justify-content:center;">
                <div>休憩：</div>
                <?php 
                if((empty($results[$i]["resttime"]))){
                    echo $this->Form->text("resttime[$i]",["type" => "time","class" => "time"]);
                }else{
                    echo $this->Form->text("resttime[$i]",["type" => "time","class" => "time","value" => $results[$i]["resttime"]->i18nFormat("HH:mm")]);
                }?>
            </div>
        </td>
        <td class = "futu antisp">
            <?php 
            if((empty($results[$i]["intime"]))){
                echo $this->Form->text("intime[$i]",["type" => "time","class" => "time"]);
            }else{
                echo $this->Form->text("intime[$i]",["type" => "time","class" => "time","value" => $results[$i]["intime"]->i18nFormat("HH:mm")]);
            }?>
        </td>
        <td class = "futu antisp">
            <?php 
            if((empty($results[$i]["outtime"]))){
                echo $this->Form->text("outtime[$i]",["type" => "time","class" => "time"]);
            }else{
                echo $this->Form->text("outtime[$i]",["type" => "time","class" => "time","value" => $results[$i]["outtime"]->i18nFormat("HH:mm")]);
            }?>
        </td>
        <td class = "futu antisp">
            <?php 
            if((empty($results[$i]["resttime"]))){
                echo $this->Form->text("resttime[$i]",["type" => "time","class" => "time"]);
            }else{
                echo $this->Form->text("resttime[$i]",["type" => "time","class" => "time","value" => $results[$i]["resttime"]->i18nFormat("HH:mm")]);
            }?>
        </td>
        <?php if($adminfrag==1): ?>
            <td class = "futu">
                <?php 
                if((empty($results[$i]["overtime"]))){
                    echo $this->Form->text("overtime[$i]",["type" => "time","class" => "time"]);
                }else{
                    echo $this->Form->text("overtime[$i]",["type" => "time","class" => "time","value" => $results[$i]["overtime"]->i18nFormat("HH:mm")]);
                }?>
            </td>
        <?php else: ?>
            <td class = "sema antisp">
        	<?php 
                if(empty($results[$i]["ou"])){
                    echo $this -> Form -> control("ou[$i]",array("type" => "checkbox","label" => false,"value" => 1));
                }else{
                    echo $this -> Form -> control("ou[$i]",array("type" => "checkbox","label" => false,"value" => 1,"checked" => true));
                }?>
            </td>
            <td class = "sema antisp">
                <?php 
                if(empty($results[$i]["fuku"])){
                    echo $this -> Form -> control("fuku[$i]",array("type" => "checkbox","label" => false,"value" => 1));
                }else{
                    echo $this -> Form -> control("fuku[$i]",array("type" => "checkbox","label" => false,"value" => 1,"checked" => true));
                }?>
            </td>
            <td class = "sema antisp">
                <?php 
                if(empty($results[$i]["meshi"])){
                    echo $this -> Form -> control("meshi[$i]",array("type" => "checkbox","label" => false,"value" => 1));
                }else{
                    echo $this -> Form -> control("meshi[$i]",array("type" => "checkbox","label" => false,"value" => 1,"checked" => true));
                }?>
            </td>
            <td class = "sema antisp">
                <?php 
                if(empty($results[$i]["medical"])){
                    echo $this -> Form -> control("medical[$i]",array("type" => "checkbox","label" => false,"value" => 1));
                }else{
                    echo $this -> Form -> control("medical[$i]",array("type" => "checkbox","label" => false,"value" => 1,"checked" => true));
                }?>
            </td>
            <?php endif; ?>
            <td class = "sema antisp">
                <?php 
                if(empty($results[$i]["support"])){
                    echo $this -> Form -> control("support[$i]",array("type" => "checkbox","label" => false,"value" => 1));
                }else{
                    echo $this -> Form -> control("support[$i]",array("type" => "checkbox","label" => false,"value" => 1,"checked" => true));
            }?>
            </td>
        <td class = "sema">
        <?php 
            if(empty($results[$i]["koukyu"])){
                echo $this -> Form -> control("koukyu[$i]",array("type" => "checkbox","label" => false,"value" => 1));
            }else{
                echo $this -> Form -> control("koukyu[$i]",array("type" => "checkbox","label" => false,"value" => 1,"checked" => true));
            }?>
        </td>
        <td class = "sema">
            <?php
            if(empty($results[$i]["paid"])){
                echo $this -> Form -> control("paid[$i]",array("type" => "checkbox","label" => false,"value" => 1));
            }else{
                echo $this -> Form -> control("paid[$i]",array("type" => "checkbox","label" => false,"value" => 1,"checked" => true));
            }?>
        </td>
        <td class = "sema antisp">
            <?php 
            if(empty($results[$i]["kekkin"])){
                echo $this -> Form -> control("kekkin[$i]",array("type" => "checkbox","label" => false,"value" => 1));
            }else{
                echo $this -> Form -> control("kekkin[$i]",array("type" => "checkbox","label" => false,"value" => 1,"checked" => true));
            }?>
        </td>
        <td class = "biko antisp">
        <?php if($this->request-> getSession()->read('Auth.User.adminfrag') == 1): ?>
            <datalist id = "bikolist">
                <option value = "体調不良のため">
                <option value = "メンタル不調のため">
                <option value = "通院のため">
            </datalist>
            <?php if((empty($results[$i]["bikou"]))): ?>
                <?= $this->Form->control("bikou[$i]",[
                    "type" => "text",
                    "label" => false,
                    "list" => "bikolist",
                    "placeholder" => "ダブルクリックでリスト",
                ]); ?>
            <?php else: ?>
                <?= $this->Form->control("bikou[$i]",[
                    "type" => "text",
                    "label" => false,
                    "list" => "bikolist",
                    "value"=> $results[$i]["bikou"],
                    "placeholder" => "ダブルクリックでリスト",
                ]); ?>
            <?php endif; ?>
        <?php else: ?>
            <?php if((empty($results[$i]["bikou"]))): ?>
                <div></div>
            <?php else: ?>
                <div class = "ari pt10">備考あり</div>
                <div class = "kakure pt10"><?= $results[$i]["bikou"] ?></div>
            <?php endif; ?>
        <?php endif; ?>
        </td>
        <td class = "sema">
            <?php 
            if(empty($results[$i]["remote"])){
                echo $this -> Form -> control("remote[$i]",array("type" => "checkbox","label" => false,"value" => 1));
            }else{
                echo $this -> Form -> control("remote[$i]",array("type" => "checkbox","label" => false,"value" => 1,"checked" => true));
            }?>
        </td>
        <?php if($this->request-> getSession()->read('Auth.User.adminfrag') != 1): ?>
            <td class = "futu antisp">
            <?php
                if((empty($results[$i]["user_staffid"]))){
                    echo "";
                } else {
                    echo $admresults[$results[$i]["user_staffid"]];
                }
            ?>
            </td>
        <?php elseif($adminfrag==0): ?>
            <td class = "futu antisp">
            <?php
                if((empty($results[$i]["user_staffid"]))){
                    echo $this->Form->select("user_staffid[$i]",$admresults,['empty'=>array('0'=>null)]);
                } else {
                    echo $this->Form->select("user_staffid[$i]",$admresults,['empty'=>false,'value'=>$results[$i]["user_staffid"]]);
                }
            ?>
            </td>
        <?php endif; ?>
    </tr>
    <?php endfor; ?>
</table>
</div>
<div class = "alltable2 antisp">
    <table>
        <tr>
            <th class = "allhead">合計勤務時間</th>
            <td class = "alldetail"><?= $alltime ?></td>
            <th class = "allhead">全出勤日</th>
            <td class = "alldetail"><?= $allworkdays."日"?></td>
            <th class = "allhead">出勤率</th>
            <td class = "alldetail"><?= $percent ?></td>
        </tr>
        <tr>
            <th class = "allhead">公休</th>
            <td class = "alldetail"><?= $allkoukyu."回" ?></td>
            <th class = "allhead">有休</th>
            <td class = "alldetail"><?= $allpaid."回" ?></td>
            <th class = "allhead">欠勤</th>
            <td class = "alldetail"><?= $allkekkin."回" ?></td>
        </tr>
    </table>
</div>
    <div class = "odakoku mt-5 spmargin">
        <div class="ml10_button mlv25">
            <?= $this->Form->button(__("登録")) ?>
        </div>
        <div class="newright3">
            <?= $this->Html->link(__('[勤務情報の詳細]'), ['action' => 'detailn', "?" => array("id" => $user_id, "year" => $year, "month" => $month)]) ?>
        </div>
    </div>
<?php $this -> Form -> end(); ?>
</div>