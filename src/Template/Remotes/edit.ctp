<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/additional-methods.min.js"></script>
<script type="text/javascript" src="/js/validate-config.js"></script>
<script type="text/javascript">
    $(function() {
        $("#editform").validate({
            rules: {
                time1: {
                    required: true;
                }
                time2: {
                    required: true;
                }
            }
            messages: {
                time1: {
                    '必須項目です',
                }
                time2: {
                    '必須項目です',
                }
            }
        });
    });
</script>

<div class = "main3">
    <h4 class = "midashih4 mt30">　在宅就労記録登録</h4>
    <?= $this->Form->create(__("View"),["type"=>"post","id"=>"editform","url"=>["action"=>"register"]]); ?>
    <div class = "odakoku mt30 mlv25" style = "align-items:center">
        <div>ユーザー：　</div>
        <div style = "width: 125px;">
            <input type="text" name="name" value="<?= $user["name"]?>" readonly>
        </div>
        <div class = "mlv25">実施年月日：　</div>
        <div style = "width: 125px;">
            <input type="text" name="date" value="<?=$year?>-<?=$month?>-<?=$date?>" readonly>
        </div>
        <div class = "odakoku mlv25" style = "align-items:center">
            <div style = "margin-right: 15px;">実施時間</div>
            <div style = "width:86px; margin-left:15px;">
                <?php if(!empty($getAtt["intime"])): ?>
                    <?= $this->Form->text("intime",["type" => "time","value"=>$getAtt["intime"]->i18nFormat("HH:mm")]); ?>
                <?php elseif(!empty($user["dintime"])): ?>
                    <?= $this->Form->text("intime",["type" => "time","value"=>$user["dintime"]->i18nFormat("HH:mm")]); ?>
                <?php else: ?>
                    <?= $this->Form->text("intime",["type" => "time"]); ?>
                <?php endif; ?>
            </div>
            <div style = "margin: 0 10px;">～</div>
            <div style = "width:86px;">
                <?php if(!empty($getAtt["outtime"])): ?>
                    <?= $this->Form->text("outtime",["type" => "time","value"=>$getAtt["outtime"]->i18nFormat("HH:mm")]); ?>
                <?php elseif(!empty($user["douttime"])): ?>
                    <?= $this->Form->text("outtime",["type" => "time","value"=>$user["douttime"]->i18nFormat("HH:mm")]); ?>
                <?php else: ?>
                    <?= $this->Form->text("outtime",["type" => "time"]); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class = "odakoku mlv25 mt10" style = "align-items:center">
        <div>手段：</div>
        <div style = "width:100px; margin-left:15px;">
            <?php if(empty($getRem["shudan"]) || empty($gyakushudan[$getRem["shudan"]])): ?>
                <?= $this->Form->select('shudan',$shudan,['id'=>'user_id','type'=> 'select','value'=>2]);?>
            <?php elseif($gyakushudan[$getRem["shudan"]] == 0 || $gyakushudan[$getRem["shudan"]] == 1): ?>
                <?= $this->Form->select('shudan',$shudan,['id'=>'user_id','type'=> 'select','value'=>$gyakushudan[$getRem["shudan"]]]);?>  
            <?php endif; ?>          
        </div>
        <div style = "width:150px; margin-left:15px;">
            <?php if(empty($getRem["shudan"]) || empty($gyakushudan[$getRem["shudan"]])): ?>
                <?= $this->Form->control('shudan2', ['type'=>'text','label'=>false,'value'=>"Zoom"]); ?>
            <?php elseif($gyakushudan[$getRem["shudan"]] == 0 || $gyakushudan[$getRem["shudan"]] == 1): ?>
                <?= $this->Form->control('shudan2', ['type'=>'text','label'=>false]); ?>
            <?php endif; ?>
        </div>
        <div class = "mlv25">対応職員：</div>
        <div style = "width:150px; margin-left:15px;">
            <?php if(empty($getRem["user_staffid"])): ?>
                <?= $this->Form->select('staff',$getstaffs,['type'=>'select','value'=>$staff]);?>
            <?php else: ?>
                <?= $this->Form->select('staff',$getstaffs,['type'=>'select','value'=>$getRem["user_staffid"]]);?>
            <?php endif; ?>
        </div>
    </div>
    <div class = "mlv25 mt20">
        <div>作業・訓練内容</div>
        <?php if(!empty($getRem["work"])): ?>
            <?= $this->Form->control('work', ['type'=>'textarea','label'=>false,'value'=>$getRem["work"]]); ?>
        <?php elseif(!empty($getrep["state"])): ?>
            <?= $this->Form->control('work', ['type'=>'textarea','label'=>false,'value'=>$getrep["state"]]); ?>
        <?php else: ?>
            <?= $this->Form->control('work', ['type'=>'textarea','label'=>false]); ?>
        <?php endif; ?>
    </div>
    <div class = "mlv25 mt20">
        <div>内容</div>
        <div class = "odakoku" style = "align-items:center; margin-top:10px;">
            <div style = "width:86px;">
                <?php if(!empty($getRem["time1"])): ?>
                    <?= $this->Form->text("time1",["type" => "time","value"=>$getRem["time1"]->i18nFormat("HH:mm"),"id"=>"times"]); ?>
                <?php else: ?>
                    <?= $this->Form->text("time1",["type" => "time","id"=>"times"]); ?>
                <?php endif; ?>
            </div>
            <div style = "width:850px; margin-left:15px;">
                <?php if(!empty($getRem["content1"])): ?>
                    <?= $this->Form->control('content1', ['type'=>'text','label'=>false,'value'=>$getRem["content1"]]); ?>
                <?php else: ?>
                    <?= $this->Form->control('content1', ['type'=>'text','label'=>false,'value'=>$template1]); ?>
                <?php endif; ?> 
            </div>
        </div>
        <div class = "odakoku" style = "align-items:center; margin-top:10px;">
            <div style = "width:86px;">
                <?php if(!empty($getRem["time2"])): ?>
                    <?= $this->Form->text("time2",["type" => "time","value"=>$getRem["time2"]->i18nFormat("HH:mm"),"id"=>"times"]); ?>
                <?php else: ?>
                    <?= $this->Form->text("time2",["type" => "time","id"=>"times"]); ?>
                <?php endif; ?>
            </div>
            <div style = "width:850px; margin-left:15px;">
                <?php if(!empty($getRem["content2"])): ?>
                    <?= $this->Form->control('content2', ['type'=>'text','label'=>false,'value'=>$getRem["content2"]]); ?>
                <?php else: ?>
                    <?= $this->Form->control('content2', ['type'=>'text','label'=>false,'value'=>$template2]); ?>
                <?php endif; ?> 
            </div>
        </div>
    </div>
    <div class = "mlv25 mt20">
        <div>心身の状況</div>
        <?php if(!empty($getRem["health"])): ?>
            <?= $this->Form->control('health', ['type'=>'textarea','label'=>false,'value'=>$getRem["health"]]); ?>
        <?php elseif(!empty($getrep["information"])): ?>
            <?= $this->Form->control('health', ['type'=>'textarea','label'=>false,'value'=>$getrep["information"]]); ?>
        <?php else: ?>
            <?= $this->Form->control('health', ['type'=>'textarea','label'=>false]); ?>
        <?php endif; ?>
    </div>
    <div class="ml10_button mt30" style = "margin-left: 75%;">
        <?= $this->Form->button(__("登録")) ?>
    </div>
    <?= $this->Form->end(); ?>
<br>


