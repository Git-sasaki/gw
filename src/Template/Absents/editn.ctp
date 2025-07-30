<?php
    use Cake\Log\Log;
?>

<script type="text/javascript">
    function tourokuCheck() {
        //欠席者の確認
        userid = document.getElementsByName("user_id")[0].value;
        if ( userid == "") {
            alert("欠席者が指定されていません。");
            return false;
        }

        //報告内容の確認
        naiyou = document.getElementsByName("naiyou")[0].value;
        if ( naiyou == "") {
            alert("報告内容が入力されていません。");
            return false;
        }

        //受付日の妥当性チェック
        const receptionYear = document.getElementsByName("year")[0].value;
        const receptionMonth = document.getElementsByName("month")[0].value;
        const receptionDate = document.getElementsByName("date")[0].value;
        const receptionDateObj = new Date(receptionYear, receptionMonth - 1, receptionDate);
        
        if (receptionDateObj.getFullYear() != receptionYear || 
            receptionDateObj.getMonth() != receptionMonth - 1 || 
            receptionDateObj.getDate() != receptionDate) {
            alert("受付日に正しい日付を入力してください。");
            document.getElementsByName("date")[0].focus();
            return false;
        }

        //欠勤日の妥当性チェック
        const absenceYear = document.getElementsByName("kekkinyear")[0].value;
        const absenceMonth = document.getElementsByName("kekkinmonth")[0].value;
        const absenceDate = document.getElementsByName("kekkindate")[0].value;
        const absenceDateObj = new Date(absenceYear, absenceMonth - 1, absenceDate);
        
        if (absenceDateObj.getFullYear() != absenceYear || 
            absenceDateObj.getMonth() != absenceMonth - 1 || 
            absenceDateObj.getDate() != absenceDate) {
            alert("欠勤日に正しい日付を入力してください。");
            document.getElementsByName("kekkindate")[0].focus();
            return false;
        }

        // 欠勤日が受付日より前の場合
        if (absenceDateObj < receptionDateObj) {
            alert("欠勤日は受付日より前の日付を指定できません。");
            document.getElementsByName("kekkindate")[0].focus();
            return false;
        }

            //欠勤加算対象の日付チェック
        const kekkinkasanElement = document.querySelector('input[type="checkbox"][name="kekkinkasan"]');
        if (kekkinkasanElement && kekkinkasanElement.checked) {

            const diffTime = Math.abs(absenceDateObj - receptionDateObj);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays > 2) {
                alert("欠勤加算対象の場合、欠勤日は受付日から2日以内である必要があります。");
                document.getElementsByName("kekkindate")[0].focus();
                return false;
            }
        }

        //次回利用の促し確認
        nexts = document.getElementsByName("next");
        if ( nexts.item(1).checked) {
            ans1 = document.getElementsByName("answer1")[0].value;
            ans2 = document.getElementsByName("answer2")[0].value;
            ans3 = document.getElementsByName("answer3")[0].value;
            ans4 = document.getElementsByName("answer4")[0].value;
            if ( (ans1 == "") && (ans2 == "") && (ans3 == "") && (ans4 == "")) {
                alert("次回利用の促しの相手の回答が入力されていません。");
                document.getElementsByName("answer1")[0].focus();
                return false;
            }
        }

        return true;
    }

    function hensyutourokuCheck() {
        //報告内容の確認
        naiyou = document.getElementsByName("naiyou")[0].value;
        if ( naiyou == "") {
            alert("報告内容が入力されていません。");
            return false;
        }

        //受付日の妥当性チェック
        const receptionYear = document.getElementsByName("year")[0].value;
        const receptionMonth = document.getElementsByName("month")[0].value;
        const receptionDate = document.getElementsByName("date")[0].value;
        const receptionDateObj = new Date(receptionYear, receptionMonth - 1, receptionDate);
        
        if (receptionDateObj.getFullYear() != receptionYear || 
            receptionDateObj.getMonth() != receptionMonth - 1 || 
            receptionDateObj.getDate() != receptionDate) {
            alert("受付日に正しい日付を入力してください。");
            document.getElementsByName("date")[0].focus();
            return false;
        }

        //欠勤日の妥当性チェック
        const absenceYear = document.getElementsByName("kekkinyear")[0].value;
        const absenceMonth = document.getElementsByName("kekkinmonth")[0].value;
        const absenceDate = document.getElementsByName("kekkindate")[0].value;
        const absenceDateObj = new Date(absenceYear, absenceMonth - 1, absenceDate);
        
        if (absenceDateObj.getFullYear() != absenceYear || 
            absenceDateObj.getMonth() != absenceMonth - 1 || 
            absenceDateObj.getDate() != absenceDate) {
            alert("欠勤日に正しい日付を入力してください。");
            document.getElementsByName("kekkindate")[0].focus();
            return false;
        }

        // 欠勤日が受付日より前の場合
        if (absenceDateObj < receptionDateObj) {
            alert("欠勤日は受付日より前の日付を指定できません。");
            document.getElementsByName("kekkindate")[0].focus();
            return false;
        }

            //欠勤加算対象の日付チェック
        const kekkinkasanElement = document.querySelector('input[type="checkbox"][name="kekkinkasan"]');
        if (kekkinkasanElement && kekkinkasanElement.checked) {
            const diffTime = Math.abs(absenceDateObj - receptionDateObj);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays > 2) {
                alert("欠勤加算対象の場合、欠勤日は受付日から2日以内である必要があります。");
                document.getElementsByName("kekkindate")[0].focus();
                return false;
            }
        }

        //次回利用の促し確認
        nexts = document.getElementsByName("next");
        if ( nexts.item(1).checked) {
            ans1 = document.getElementsByName("answer1")[0].value;
            ans2 = document.getElementsByName("answer2")[0].value;
            ans3 = document.getElementsByName("answer3")[0].value;
            ans4 = document.getElementsByName("answer4")[0].value;
            if ( (ans1 == "") && (ans2 == "") && (ans3 == "") && (ans4 == "")) {
                alert("次回利用の促しの相手の回答が入力されていません。");
                document.getElementsByName("answer1")[0].focus();
                return false;
            }
        }

        return true;
    }
</script>

<?php $this->assign('title', '欠席情報登録'); ?>

<?php if(empty($notnew)): ?>
    <div class = "main1">
    <h4 class="midashih4 mt30 mb30"> 欠席情報登録</h4>
    <?= $this->Form->create(null, ['type' => 'post',"id" => "editform", 'url' => ['action' => 'register']]) ?>
        <div class = "shinsei">
        <h4 class = "exportdeka">　基本情報</h4>
            <div class = "odakoku ml10">
                受付日時
            </div>
            <div class = "odakoku ml10" style="margin-left: 30px;">
                <div class = "sdakoku">
                    <?= $this->Form->control('year', ['type' => 'select', 'label' => "年", 'value'=> date('Y')], $years) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('month', ['type' => 'select', 'label' => "月", 'value'=> date('m')], $months) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('date', ['type' => 'text', 'label' => '日', 'value'=> date('d')]) ?>
                </div>
                <div class = "futu3">
                    <?php
                        echo $this->Form->label("時間");
                        echo $this->Form->text('time', ['id' => 'reporttime','type' => 'time','label' => '時間', 'value' => date("H:i")]);
                    ?>
                </div>
                <div class = "futu3">
                    <?= $this->Form->control('shudan', ['type' => 'text', 'label' => '手段','value' => '電話']); ?>
                </div>
            </div>
            <div class = "odakoku ml10" style="margin-left: 30px;">
                <div class = "staffbox2">
                    <?= $this->Form->label("受けた人"); ?>
                    <?= $this->Form->select('user_staffid',$admresults,array('id'=>'user_staffid','label' => "受けた人",'type'=> 'select','value' => $staff_id));?>
                </div>
                <div class = "staffbox2">
                        <?= $this->Form->label("欠席者")?>
                        <?= $this->Form->select('user_id',$users,array('id'=>'user_id','label' => "欠席者",'type'=> 'select','empty' => true));?>
                    </div>
                <div class = "futu2">
                    <?= $this->Form->control('relation', ['type' => 'text', 'label' => '関係','value' => '本人']); ?>
                </div>
            </div>
            <div class = "odakoku ml10" style="margin-top: 15px;">
                欠勤日
            </div>
            <div class = "odakoku ml10" style="margin-left: 30px;">
            <div class = "sdakoku">
                    <?= $this->Form->control('kekkinyear', ['type' => 'select', 'label' => "年", 'value'=> date('Y'), 'options' => $years]) ?>
                </div>  
                <div class = "sdakoku">
                    <?= $this->Form->control('kekkinmonth', ['type' => 'select', 'label' => "月", 'value'=> date('m'), 'options' => $months]) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('kekkindate', ['type' => 'text', 'label' => '日', 'value'=> date('d')]) ?>
                </div>
                <div style="width: 200px; margin-left: 20px; margin-top: 8px;">
                    欠勤加算対象
                    <?= $this->Form->control('kekkinkasan', [
                        'type' => 'checkbox',
                        'label' => false,
                        'style' => "margin-top: 15px; margin-left: 40px;",
                        'value' => '1',
                        'hiddenField' => true
                    ]) ?>
                </div>
            </div>
        </div>
        <div class = "shinsei">
        <h4 class = "exportdeka">　報告内容</h4>
            <div class = "w900 ml10 mb10">
                <?= $this->Form->control('naiyou', ['type' => 'textarea', 'label' => '内容']); ?>
            </div>
            <div class = "odakoku ml10 mb10">
                <?php 
                    echo $this->Form->label('次回利用の促し：');
                    echo '<br>';
                    
                    $options = [
                                '1' => ' 行った　　',
                                '0' => ' 行えなかった　　'
                                ];
                    $attributes = array('value' => '1');    
                    echo $this->Form->radio('next', $options, $attributes);
                ?>
            </div>
            <div class = "w900 ml10 mb10">
                <?= $this->Form->control('answer1', ['type' => 'text', 'label' => '相手の回答：']); ?>
            </div>
            <div class = "w900 ml10 mb10">
                <?= $this->Form->control('answer2', ['type' => 'text', 'label' => false]);?>
            </div>
            <div class = "w900 ml10 mb10">
                <?= $this->Form->control('answer3', ['type' => 'text', 'label' => false]);?>
            </div>
            <div class = "w900 ml10 mb10">
                <?= $this->Form->control('answer4', ['type' => 'text', 'label' => false]);?>
            </div>
            <div class = "w900 ml10 mb10">
                <?= $this->Form->control('bikou', ['type' => 'textarea', 'label' => '備考']); ?>
            </div>
        </div> 
        <div class="mlv27 mb30">
                <?= $this->Form->button("送信",array('onClick' => 'return tourokuCheck()')) ?>
                <!--<?= $this->Form->button(__("送信")) ?>-->
        </div>
        <?php $this -> Form -> end(); ?>
<?php else: ?>
    <div class = "main1">
    <h4 class="midashih4 mt30 mb30"> 欠席情報登録</h4>
    <?= $this->Form->create(null, ['type' => 'post',"id" => "editform", 'url' => ['action' => 'register']]) ?>
        <?= $this->Form->control('id',['type'=>'hidden','value'=>$notnew['id']]) ?>
        <div class = "shinsei">
        <h4 class = "exportdeka">　基本情報</h4>
            <div class = "odakoku ml10">
                受付日時
            </div>
            <div class = "odakoku ml10" style="margin-left: 30px;">
                <div class = "sdakoku">
                    <?= $this->Form->control('year', ['type' => 'select', 'label' => "年", 'value'=> $date[0]], $years) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('month', ['type' => 'select', 'label' => "月", 'value'=> $date[1]], $months) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('date', ['type' => 'text', 'label' => '日', 'value'=> $date[2]]) ?>
                </div>
                <div class = "futu3">
                    <?php
                        echo $this->Form->label("時間");
                        if(!empty($notnew["time"])) {
                            echo $this->Form->text('time', ['id'=>'reporttime','type'=>'time','label'=>'時間','value'=>$notnew["time"]->i18nFormat("HH:mm")]);
                        } else {
                            echo $this->Form->text('time', ['id'=>'reporttime','type'=>'time','label'=>'時間','value'=>date("H:i")]);
                        }
                    ?>
                </div>
                <div class = "futu3">
                    <?= $this->Form->control('shudan', ['type' => 'text', 'label' => '手段','value' => $notnew["shudan"]]); ?>
                </div>
            </div>
            <div class = "odakoku ml10" style="margin-left: 30px;">
                <div class = "staffbox2">
                    <?= $this->Form->label("受けた人"); ?>
                    <?= $this->Form->select('user_staffid',$admresults,array('id'=>'user_staffid','label' => "受けた人",'type'=> 'select','value'=>$notnew["user_staffid"]));?>
                </div>
                <div class = "staffbox2">
                        <?= $this->Form->label("欠席者")?>
                        <?= $this->Form->select('user_id',$users,array('id'=>'user_id','label' => "欠席者",'type'=> 'select','value'=>$notnew["user_id"]));?>
                    </div>
                <div class = "futu2">
                    <?= $this->Form->control('relation', ['type' => 'text', 'label' => '関係','value' => $notnew["relation"]]); ?>
                </div>
            </div>
            <div class = "odakoku ml10" style="margin-top: 15px;">
                欠勤日
            </div>
            <div class = "odakoku ml10" style="margin-left: 30px;">
                <div class = "sdakoku">
                    <?= $this->Form->control('kekkinyear', ['type' => 'select', 'label' => "年", 'value'=> $kekkindate[0], 'options' => $years]) ?>
                </div>  
                <div class = "sdakoku">
                    <?= $this->Form->control('kekkinmonth', ['type' => 'select', 'label' => "月", 'value'=> $kekkindate[1], 'options' => $months]) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('kekkindate', ['type' => 'text', 'label' => '日', 'value'=> $kekkindate[2]]) ?>
                </div>
                <div style="width: 200px; margin-left: 20px; margin-top: 8px;">
                    欠勤加算対象
                    <?= $this->Form->control('kekkinkasan', [
                        'type' => 'checkbox',
                        'label' => false,
                        'style' => "margin-top: 15px; margin-left: 40px;",
                        'checked' => ($notnew["kekkinkasan"] == 1),
                        'value' => '1',
                        'hiddenField' => true
                    ]) ?>
                </div>
            </div>
        </div>
        <div class = "shinsei">
        <h4 class = "exportdeka">　報告内容</h4>
            <div class = "w900 ml10 mb10">
                <?= $this->Form->control('naiyou', ['type' => 'textarea', 'label' => '内容','value'=>$notnew["naiyou"]]); ?>
            </div>
            <div class = "odakoku ml10 mb10">
                <?php 
                    echo $this->Form->label('次回利用の促し：');
                    echo '<br>';
                    
                    $options = [
                                '1' => ' 行った　　',
                                '0' => ' 行えなかった　　'
                                ];
                    $attributes = array('value' => $notnew["next"]);    
                    echo $this->Form->radio('next', $options, $attributes);
                ?>
            </div>
            <div class = "w900 ml10 mb10">
                <?= $this->Form->control('answer1', ['type' => 'text', 'label' => '相手の回答：','value' => $notnew["answer1"]]); ?>
            </div>
            <div class = "w900 ml10 mb10">
                <?= $this->Form->control('answer2', ['type' => 'text', 'label' => false,'value' => $notnew["answer2"]]);?>
            </div>
            <div class = "w900 ml10 mb10">
                <?= $this->Form->control('answer3', ['type' => 'text', 'label' => false,'value' => $notnew["answer3"]]);?>
            </div>
            <div class = "w900 ml10 mb10">
                <?= $this->Form->control('answer4', ['type' => 'text', 'label' => false,'value' => $notnew["answer4"]]);?>
            </div>
            <div class = "w900 ml10 mb10">
                <?= $this->Form->control('bikou', ['type' => 'textarea', 'label' => '備考','value' => $notnew["bikou"]]); ?>
            </div>
        </div> 
        <div class="mlv27 mb30"> 
            <?= $this->Form->button("送信",array('onClick' => 'return hensyutourokuCheck()')) ?>
            <!--<?= $this->Form->button(__("送信")) ?>-->
        </div>
        <?php $this -> Form -> end(); ?>
<?php endif; ?>