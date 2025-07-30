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
                intime: {
                    time: true,
                },
                outtime: {
                    time: true,
                },
                resttime: {
                    time: true,
                },
            },
        });
    });

    function sgChange(value) {
        const chkboxname = document.getElementById("medichk");
        const selectname = document.getElementById("shisetsugai_name");

        if (chkboxname.checked) {
            selectname.disabled = null;
            options = selectname.options;
            options[1].selected = true;
        } else {
            selectname.value = 0;
            selectname.disabled = "disabled";
        }
    }

    function OnEnter() {
        const chkboxname = document.getElementById("medichk");
        const selectname = document.getElementById("shisetsugai_name");

        if (chkboxname.checked) {
            if ( selectname.value == 0) {
                alert( "施設外の場所に空白は指定出来ません");
                options = selectname.options;
                options[1].selected = true;
           }
        }
    }
</script>

<?php $this->assign('title', '出退社打刻'); ?>

<?php $host = $_SERVER['HTTP_HOST']; ?>
<?php if($host == "[::1]:8765"): ?>
    <div class = "maxwide testfooter antisp">
        <?= "　".date('Y')."年".date('m')."月".date('d')."日 (".$weekList[date("w")].")　".$user["name"]."　さん" ?>
    </div>
<?php else: ?>
    <div class = "maxwide footer antisp">
        <?= "　".date('Y')."年".date('m')."月".date('d')."日 (".$weekList[date("w")].")　".$user["name"]."　さん" ?>
    </div>
<?php endif; ?>

<div class = "main1">
    <h4 class="midashih4 mt30">　出退勤時間打刻</h4>
    <div class = "odakoku";>
        <div style = "width: 37vw">
            <?= $this->Form->create(null, ['type' => 'post',"id" => "editform",
                                        'url' => ['controller' => 'Attendances', 'action' => 'register']]) ?>
            <!--<div class = "pdakoku">-->
            <div style="margin-left: 10px;"> 
                <div class = "dakoku5">
                    <div class = "dakoku2">
                        <div class = "dakoku">
                            <?php 
                                if(!empty($results["intime"])) {
                                    echo $this->Form->label("出勤時間");
                                    echo $this->Form->text("intime",["type" => "time","value" => $results["intime"]->i18nFormat("HH:mm")]);
                                } elseif (!empty($user["dintime"])) {       
                                    if(strtotime($user["dintime"]->i18nFormat('HH:mm')) <= time()) {
                                        echo $this->Form->label("出勤時間");
                                        echo $this->Form->text("intime",["type" => "time","value" => date('H:i')]);
                                    } else {
                                        echo $this->Form->label("出勤時間");
                                        echo $this->Form->text("intime",["type" => "time","value" => $user["dintime"]->i18nFormat("HH:mm")]);
                                    }
                                } else {
                                    echo $this->Form->label("出勤時間");
                                    echo $this->Form->text("intime",["type" => "time"]);
                                } ?>
                        </div>
                        <div class = "dakoku">
                            <?php 
                                if(!empty($results["outtime"])) {
                                    echo $this->Form->label("退勤時間");
                                    echo $this->Form->text("outtime",["type" => "time","value" => $results["outtime"]->i18nFormat("HH:mm")]);
                                } elseif (!empty($user["douttime"])) {
                                    echo $this->Form->label("退勤時間");
                                    echo $this->Form->text("outtime",["type" => "time","value" => $user["douttime"]->i18nFormat("HH:mm")]);
                                } else {
                                    echo $this->Form->label("退勤時間");
                                    echo $this->Form->text("outtime",["type" => "time"]);
                                } ?>
                        </div>
                        <div class = "dakoku">
                            <?php 
                                if(!empty($results["resttime"])) {
                                    echo $this->Form->label("休憩時間");
                                    echo $this->Form->text("resttime",["type" => "time","value" => $results["resttime"]->i18nFormat("HH:mm")]);
                                } elseif (!empty($user["dresttime"])) {
                                    echo $this->Form->label("休憩時間");
                                    echo $this->Form->text("resttime",["type" => "time","value" => $user["dresttime"]->i18nFormat("HH:mm")]);
                                } else {
                                    echo $this->Form->label("休憩時間");
                                    echo $this->Form->text("resttime",["type" => "time"]);
                                } ?>
                        </div>
                        <?php if($this->request-> getSession()->read('Auth.User.adminfrag') == 1): ?>
                        <div class = "dakoku">
                        <?php 
                        if((empty($results["overtime"]))){
                            echo $this->Form->label("残業時間");
                            echo $this->Form->text("overtime",["type" => "time"]);
                        }else{
                            echo $this->Form->label("残業時間");
                            echo $this->Form->text("overtime",["type" => "time","value" => $results["overtime"]->i18nFormat("HH:mm")]);
                        }?>
                        </div>
                    </div>
                    <div class = "dakoku3">
                        <div class = "dakoku " style="text-align:center; padding: 0 auto;">
                            <?php 
                                if((!empty($results["support"]) && ($results["support"] >= 1))) {
                                    //($user["workplace"] != $mainKana)) {
                                    echo $this->Form->label("施設外");
                                    echo $this->Form->control("support",[
                                                            "type" => "checkbox",
                                                            "id"=>"medichk",
                                                            "label"=>false,
                                                            "value" => 1,
                                                            "checked" => true,
                                                            "onchange" =>"sgChange(value)"]);
                                // それ以外の場合はチェックがない状態にしておく
                                } else {
                                    echo $this->Form->label("施設外");
                                    echo $this->Form->control("support",[
                                                            "type" => "checkbox",
                                                            "id"=>"medichk",
                                                            "label"=>false,
                                                            "value" => 1,
                                                            "onchange" =>"sgChange(value)"]);
                                }
                            ?>
                        </div>
                        <div class = "dakoku" style="text-align:center; padding: 0 auto;">
                        <?php 
                                //施設外選択リスト
                                echo $this->Form->label("施設外場所");
                                if(empty($results["support"])) {
                                    echo $this->form->input( 'support',array('id'=>'shisetsugai_name','disabled' => 'disabled','label'=>false,'type' => 'select', 'options' => $workName, "onchange" =>"OnEnter()")); 
                                } else {
                                    echo $this->form->input( 'support',array('id'=>'shisetsugai_name','label'=>false,'type' => 'select', 'options' => $workName, 'default' => $results["support"],"onchange" =>"OnEnter()")); 
                                }
                            ?>
                        </div>
                    </div>
                    <?php else: ?>
                    </div>
                    <div class = "dakoku3">
                        <div class = "dakoku" style="text-align:center; padding: 0 auto;">
                            <?php 
                                // (施設外に既にチェックが入っている)　または　(在宅勤務にチェックがない　かつ　職場が主たる事業所でない)
                                if((!empty($results["support"]) && $results["support"] >= 1) ||
                                    (!empty($results["remote"]) && $results["remote"] == 0 && $user["workplace"] != $mainKana)) {
                                    echo $this->Form->label("施設外");
                                    echo $this->Form->control("support",[
                                                                "type" => "checkbox",
                                                                "id"=>"medichk",
                                                                "label"=>false,
                                                                "value" => 1,
                                                                "checked" => true,
                                                                "onchange" =>"sgChange(value)"]);
                                } else {
                                    echo $this->Form->label("施設外");
                                    echo $this->Form->control("support",[
                                                                "type" => "checkbox",
                                                                "id"=>"medichk",
                                                                "label"=>false,
                                                                "value" => 1,
                                                                "onchange" =>"sgChange(value)"]);
                                }
                            ?>
                        </div>
                        <div class = "dakoku" style="text-align:center; padding: 0 auto;">
                        <?php 
                            //施設外選択リスト
                            echo $this->Form->label("施設外場所");
                            if(empty($results) || empty($results["support"])) {
                                echo $this->form->input( 'support',array('id'=>'shisetsugai_name','disabled' => 'disabled','label'=>false,'type' => 'select', 'options' => $workName,"onchange" =>"OnEnter()")); 
                            } else {
                                echo $this->form->input( 'support',array('id'=>'shisetsugai_name','label'=>false,'type' => 'select', 'options' => $workName, 'default' => $results["support"],"onchange" =>"OnEnter()")); 
                            }
                        ?>
                        </div>
                    </div>

                    <?php endif; ?>
                    <?php if($frag == 1): ?>
                    <div class = "antisp" style = "width: 480px; margin-left: 0; margin-top: 10px;">
                        <div class = "informations">
                            <h4 class = "exportdeka">　お知らせ</h4>

                            <!-- 出勤簿入力のお知らせ -->
                            <?php if($msg1 == 1 || $msg2 == 1): ?>
                                <h5 style = "font-size: 24px; margin-top: 20px;">・ 出勤簿の入力</h5>
                                <?php if($msg1 == 1): ?>
                                    <div class = "odakoku">
                                        <p class = "oshirase">　今月の出勤簿が未入力です。入力してください。</p>
                                        <div>
                                            <?= $this->Form->create(__("View"), [
                                                'type'=>'post',
                                                'url'=>['controller'=>'timecards','action' => 'getquery0']]) ?>
                                                <?= $this->Form->control('year',['type'=>'hidden','value'=>date('Y')]) ?>
                                                <?= $this->Form->control('month',['type'=>'hidden','value'=>date('n')]) ?>
                                                <?= $this->Form->button("[編集]",["class"=>"ichibtn datalink"]) ?>
                                            <?= $this->Form->end(); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if($msg2 == 1 && $this->request-> getSession()->read('Auth.User.adminfrag') == 1): ?>
                                    <div class = "odakoku">
                                        <p class = "oshirase">　来月の休み希望管理表と出勤簿の入力をしてください。</p>
                                        <div>
                                            <?= $this->Form->create(__("View"), [
                                                'type'=>'post',
                                                'url'=>['controller'=>'TimeCards','action' => 'getquery0']]) ?>
                                                <?= $this->Form->control('year',['type'=>'hidden','value'=>date('Y',$raigetsu)]) ?>
                                                <?= $this->Form->control('month',['type'=>'hidden','value'=>date('n',$raigetsu)]) ?>
                                                <?= $this->Form->control('id',['type'=>'hidden','value'=>$user["id"]]) ?>
                                                <?= $this->Form->button("[編集]",["class"=>"ichibtn datalink"]) ?>
                                            <?= $this->Form->end(); ?>
                                        </div>
                                    </div>
                                <?php elseif($msg2 == 1 && $this->request-> getSession()->read('Auth.User.adminfrag') == 0): ?>
                                    <div class = "odakoku">
                                        <p class = "oshirase">　来月の出勤簿を入力してください。</p>
                                        <div>
                                            <?= $this->Form->create(__("View"), [
                                                'type'=>'post',
                                                'url'=>['controller'=>'TimeCards','action' => 'getquery0']]) ?>
                                                <?= $this->Form->control('year',['type'=>'hidden','value'=>date('Y',$raigetsu)]) ?>
                                                <?= $this->Form->control('month',['type'=>'hidden','value'=>date('n',$raigetsu)]) ?>
                                                <?= $this->Form->button("[編集]",["class"=>"ichibtn datalink"]) ?>
                                            <?= $this->Form->end(); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if(!empty($wasuremons)): ?>
                                <h5 style = "font-size: 24px; margin-top: 20px;">・ 記入されていない作業日報があります</h5>
                                <?php foreach($wasuremons as $wasuremon): ?>
                                    <div class = "odakoku">
                                        <p class = "oshirase">　<?= date('Y年m月d日',$wasuremon); ?></p>
                                        <div>
                                            <?= $this->Form->create(__("View"), [
                                                'type'=>'post',
                                                'url'=>['controller'=>'reports','action' => 'getquery0']]) ?>
                                                <?= $this->Form->control('year',['type'=>'hidden','value'=>date('Y',$wasuremon)]) ?>
                                                <?= $this->Form->control('month',['type'=>'hidden','value'=>date('n',$wasuremon)]) ?>
                                                <?= $this->Form->control('date',['type'=>'hidden','value'=>date('j',$wasuremon)]) ?>
                                                <?= $this->Form->button("[編集]",["class"=>"ichibtn datalink"]) ?>
                                            <?= $this->Form->end(); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
            <?php endif; ?>
            </div>
            <div class="ml10_button mt20">
                <?php 
                //施設外がチェックされいるが施設外場所が選択されていないかチェック
                if(empty($results)) {
                    echo $this->Form->button(__("登録"));
                } elseif(empty($results["intime"] && $results["outtime"] && $results["resttime"])) {
                    echo $this->Form->button(__("登録"));
                } else {
                    echo $this->Form->button(__("上書き"));
                } ?>
            </div>
            <?= $this->Form->end() ?>
        </div>
 

    </div>
</div>

<?php
    if($this->request->getSession()->read('Auth.User.adminfrag') == 1) {
        $hajimete = $this->request->getSession()->read('hajimete');
        $owarifrag = $this->request->getSession()->read('owarifrag');
        if($hajimete == true && $owarifrag == true) {
            $owaries = "";
            $owarimen = $this->request->getSession()->read(['owarimen']);
            for($i=0; $i<count($owarimen); $i++) {
                $owaries .= $owarimen[$i].'\n';
            }
            $moji = 'サービス受給者証期限が近づいています：\n';
            $alert = "<script type='text/javascript'>alert('".$moji.$owaries."');</script>";
            echo $alert;
            $this->request->getSession()->delete('owarifrag');
            $this->request->getSession()->delete('owarimen');
        }
        $this->request->getSession()->write('hajimete',false);
    } else {
        $hajimete = $this->request->getSession()->read('hajimete');
        $owasurefrag = $this->request->getSession()->read('owasurefrag');
        if($hajimete == true && $owasurefrag == true) {
            $wasuremon = "";
            $wasurebi = $this->request->getSession()->read(['wasuremon']);
            for($i=0; $i<count($wasurebi); $i++) {
                $wasuremon .= $wasurebi[$i].'\n';
            }
            $moji = '以下の日の日報が記入されていません。：\n';
            $alert = "<script type='text/javascript'>alert('".$moji.$wasuremon."');</script>";
            echo $alert;
            $this->request->getSession()->delete('owasurefrag');
            $this->request->getSession()->delete('wasuremon');
        }
    }
?>
