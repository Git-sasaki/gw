<div class = "main3">
    <h4 class = "midashih4 mt30">　週間記録登録</h4>
    <?= $this->Form->create(__("View"),["type"=>"post","url"=>["action"=>"register2"]]); ?>

    <div class = "odakoku mt30 mlv25" style = "align-items:center">
        <div>ユーザー：　</div>
            <div style = "width: 125px;">
                <input type="text" name="name" value="<?= $user["name"]?>" readonly>
            </div>
        <div class = "mlv25">評価実施日：　</div>
            <div style = "width: 125px;">
                <input type="text" name="jdate" value="<?=$year?>-<?=$month?>-<?=$date?>" readonly>
            </div>
        <div class = "mlv25">手段：　</div>
            <div style = "width: 75px;">
                <!-- 手段の欄の登録 -->
                <?php if(empty($getthis["shudan"]) || $gyakushudan[$getthis["shudan"]]==1): ?>
                    <?= $this->Form->select('shudan',$shudan,['type'=>'select','value'=>1]);?>
                <?php elseif($gyakushudan[$getthis["shudan"]] == 0): ?>
                    <?= $this->Form->select('shudan',$shudan,['type'=>'select','value'=>0]);?>
                <?php else: ?>
                    <?= $this->Form->select('shudan',$shudan,['type'=>'select','value'=>2]);?>
                <?php endif; ?>
            </div>
            
            <!-- 手段の欄がその他になっている場合は横のテキストボックスに初期値として記入 -->
            <div style = "width:150px; margin-left:10px;">
                <?php if(!empty($getthis["shudan"]) && $getthis["shudan"]==2): ?>
                    <input type="text" name="shudan2" value = "<?= $getthis["shudan"] ?>">
                <?php else: ?>
                    <input type="text" name="shudan2">
                <?php endif; ?>
            </div>
    </div>
    <div class = "odakoku mt30 mlv25" style = "align-items:center">
        <div>前回記録日：　</div>
            <div style = "width: 275px;">
                <div class = "odakoku" style = "align-items:center">
                    <div class = "w50"><input type="text" name="lyear" value = <?= $maeda[0] ?>></div>
                    <div style = "margin: 0 10px;">年</div>
                    <div class = "w50"><input type="text" name="lmonth" value = <?= $maeda[1] ?>></div>
                    <div style = "margin: 0 10px;">月</div>
                    <div class = "w50"><input type="text" name="ldate" value = <?= $maeda[2] ?>></div>
                    <div style = "margin: 0 10px;">日</div>
                </div>
            </div>
            <div class = "mlv25">記録者：　</div>
                <div style = "width: 125px;">
                    <?php if(!empty($getthis["user_staffid"])): ?>
                        <?= $this->Form->select('staff',$getstaffs,['type'=>'select','value'=>$getthis["user_staffid"]]);?>
                    <?php else: ?>
                        <?= $this->Form->select('staff',$getstaffs,['type'=>'select','value'=>$staff]);?>
                    <?php endif; ?>
                </div>
            <div class = "mlv25">確認者：　</div>
                <div style = "width: 125px;">
                    <?php if(!empty($getthis["sabikan"])): ?>
                        <?= $this->Form->select('sabikan',$getstaffs,['type'=>'select','value'=>$getthis["sabikan"]]);?>
                    <?php else: ?>
                        <?= $this->Form->select('sabikan',$getstaffs,['type'=>'select','value'=>58]);?>
                    <?php endif; ?>
                </div>
    </div>
    <div class = "odakoku mt30 mlv25" style = "align-items:center">
        <div>評価対象日：　</div>
            <div class = "w50"><input type="text" name="hyear" value= <?=$hajime[0] ?>></div>
            <div style = "margin: 0 10px;">年</div>
            <div class = "w50"><input type="text" name="hmonth" value= <?=$hajime[1] ?>></div>
            <div style = "margin: 0 10px;">月</div>
            <div class = "w50"><input type="text" name="hdate" value= <?=$hajime[2] ?>></div>
            <div style = "margin: 0 10px;">日から　</div>
            <div class = "w50"><input type="text" name="oyear" value= <?=$owari[0] ?>></div>
            <div style = "margin: 0 10px;">年</div>
            <div class = "w50"><input type="text" name="omonth" value= <?=$owari[1] ?>></div>
            <div style = "margin: 0 10px;">月</div>
            <div class = "w50"><input type="text" name="odate" value= <?=$owari[2] ?>></div>
            <div style = "margin: 0 10px;">日まで　</div>
    </div>
    <div class = "odakoku">
        <div style = "width: 37vw" class = "mlv25 mt30">
            <div>評価内容：</div>
            <?php if(!empty($getthis["content"])): ?>
                <?= $this->Form->control('content', ['type'=>'textarea','label'=>false,'value'=>$getthis["content"]]); ?>
            <?php else: ?>
                <?= $this->Form->control('content', ['type'=>'textarea','label'=>false]); ?>
            <?php endif; ?>
            <div class="mt30">
                <?= $this->Form->button(__("登録")) ?>
            </div>
            <?= $this->Form->end(); ?>
        </div>
        <div style = "width: 37vw; margin-left: 4vw; margin-top: 30px">
            <div class = "mlv25 mt30 shutsuryoku" style = "width: 90%;">
                <h4 class = "exportdeka">評価期間の勤務内容一覧</h4>
                <?php if(!empty($getworks)): ?>
                    <?php foreach($getworks as $getwork): ?>
                        <div>・ <?= $getwork["work"] ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <br>
            </div>
        </div>
    </div>
    <br>