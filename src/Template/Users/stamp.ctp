<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/additional-methods.min.js"></script>
<script type="text/javascript" src="/js/validate-config.js"></script>
<script type="text/javascript">
    // $(function(){...}は予約状態といい、HTMLの読み込みが完了した段階で読み込まれるjavascriptのこと
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
</script>

<?php
$this->assign('title', '出退社打刻');
$weekList = array("日","月","火","水","木","金","土");
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <?php if($this->request-> getSession()->read('Auth.User.adminfrag') != 1): ?>
            <li class = "heading"><?= __('メニュー') ?></li>
            <ul class = "dotlist">
                <li>
                    <?= $this->Html->link('打刻', ['controller' => 'Users', 'action' => 'stamp']); ?>
                </li>
                <li>
                    <?= $this->Html->link('出勤簿', ['controller' => 'Edits', 'action' => 'index']); ?>
                </li>
                <li>
                    <?= $this->Html->link('物品購入申請', ['controller' => 'Kaimonos', 'action' => 'index']); ?>
                </li>
                <li>
                    <?= $this->Html->link('出勤情報一覧', ['controller' => 'Users', 'action' => 'stamp2']); ?>
                </li>
            </ul>
            <li class = "heading"><?= __('作業日報') ?></li>
            <ul class = "dotlist">
                <li>
                    <?= $this->Html->link('一覧', ['controller' => 'Reports', 'action' => 'list']); ?>
                </li>
                <li>
                    <?= $this->Html->link('新規登録・編集', ['controller' => 'Users', 'action' => 'index2']); ?>
                </li>
            </ul>           
            <li><?= $this->Html->link('ログアウト', ['controller' => 'Users', 'action' => 'logout']); ?></li>
        <?php else: ?>
            <li class = "heading"><?= __('メニュー') ?></li>
            <ul class = "dotlist">
                <li>
                    <?= $this->Html->link('打刻', ['controller' => 'Users', 'action' => 'stamp']); ?>
                </li>
                <li>
                    <?= $this->Html->link('出勤簿', ['controller' => 'Edits', 'action' => 'index']); ?>
                </li>
                <li>
                    <?= $this->Html->link("欠席連絡", ["controller" => "Absents", "action" => "index"]); ?>
                </li>
                <li>
                    <?= $this->Html->link('作業日報', ['controller' => 'Reports', 'action' => 'index']); ?>
                </li>
                <li>
                    <?= $this->Html->link('物品購入申請', ['controller' => 'Kaimonos', 'action' => 'index']); ?>
                </li>
                <li>
                    <?= $this->Html->link('スケジュール', ['controller' => 'Calendars', 'action' => 'index']); ?>
                </li>
                <li>
                    <?= $this->Html->link('出勤情報一覧', ['controller' => 'Users', 'action' => 'stamp2']); ?>
                </li>
            </ul>
            <li class = "heading"><?= __('帳票') ?></li>
                <ul class = "dotlist">
                    <li>
                        <?= $this->Html->link("出勤簿印刷", ["controller" => "Prints", "action" => "index"]); ?>
                    </li>
                    <li>
                    <?= $this->Html->link("業務日誌印刷", ["controller" => "Nisshis", "action" => "index"]); ?>
                    </li>
                    <li>
                    <?= $this->Html->link("欠勤情報出力", ["controller" => "Exports", "action" => "absent"]); ?>
                    </li>
                    <li>
                        <?= $this->Html->link("サービス記録出力", ["controller" => "Exports", "action" => "srecords"]); ?>
                    </li>
                    <li>
                        <?= $this->Html->link("CSV出力", ["controller" => "Exports", "action" => "csv"]); ?>
                    </li>
                </ul>
            <li class = "heading"><?= __('マスタ') ?></li>
                <ul class = "dotlist">
                    <li>
                        <?= $this->Html->link('ユーザー', ['controller' => 'Users', 'action' => 'index']); ?>
                    </li>
                    <li>
                    <?= $this->Html->link('デフォルト設定', ['controller' => 'Attendances', 'action' => 'default']); ?>
                </li>
                </ul>
            <li><?= $this->Html->link('ログアウト', ['controller' => 'Users', 'action' => 'logout']); ?></li>
        <?php endif; ?>
    </ul>
</nav>

<div class="users index columns content report">
    <h3>出退勤時間打刻</h3>

    <h4 class="h4-header"><?= date('Y')."年".date('m')."月".date('d')."日 (".$weekList[date("w")].")　".$name."　さん" ?></h4>

        <?= $this->Form->create(null, ['type' => 'post',"id" => "editform", 'url' => ['controller' => 'Attendances', 'action' => 'register']]) ?>
        <div class = "pdakoku">
            <div class = "dakoku">
            <?php 
                if(!empty($results["intime"])) {
                    echo $this->Form->label("出勤時間");
                    echo $this->Form->text("intime",["type" => "time","value" => $results["intime"]->i18nFormat("HH:mm")]);
                } elseif (!empty($defaults["dintime"])) {
                    echo $this->Form->label("出勤時間");
                    echo $this->Form->text("intime",["type" => "time","value" => $defaults["dintime"]->i18nFormat("HH:mm")]);
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
                } elseif (!empty($defaults["douttime"])) {
                    echo $this->Form->label("退勤時間");
                    echo $this->Form->text("outtime",["type" => "time","value" => $defaults["douttime"]->i18nFormat("HH:mm")]);
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
                } elseif (!empty($defaults["dresttime"])) {
                    echo $this->Form->label("休憩時間");
                    echo $this->Form->text("resttime",["type" => "time","value" => $defaults["dresttime"]->i18nFormat("HH:mm")]);
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
            <?php endif; ?>
        </div>

        <div class="ml10_button">
            <?php 
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

<!-- 打刻 -->
    <!-- <div class="users form columns content">
        <p id="RealtimeClockArea"></p>
        <script>
            function set2fig(num) {
            // 桁数が1桁だったら先頭に0を加えて2桁に調整する
            var ret;
            if( num < 10 ) { ret = "0" + num; }
            else { ret = num; }
            return ret;
            }
            function showClock2() {
            var nowTime = new Date();
            var nowHour = set2fig( nowTime.getHours() );
            var nowMin  = set2fig( nowTime.getMinutes() );
            var nowSec  = set2fig( nowTime.getSeconds() );
            var msg = nowHour + ":" + nowMin + ":" + nowSec;
            document.getElementById("RealtimeClockArea").innerHTML = msg;
            }
            setInterval('showClock2()',1000);
        </script>
        <?= $this->Form->create(null, ['type' => 'post', 'url' => ['controller' => 'Attendances', 'action' => 'register']]) ?>
        <?php if($results === null): ?>
        <?= $this->Form->hidden( 'attend_status' ,['value'=> 1 ]) ?>
        <?= $this->Form->button( "出社", [ "class" => "timestamp" ] ) ?>
        <?php elseif($results->intime === null): ?>
        <?= $this->Form->hidden( 'attend_status' ,['value'=> 1 ]) ?>
        <?= $this->Form->button( "出社", [ "class" => "timestamp" ] ) ?>
        <?php elseif($results->outtime === null): ?>
        <?= $this->Form->hidden( 'attend_status' ,['value'=> 2 ]) ?>
        <?= $this->Form->button( "退社", [ "class" => "timestamp" ] ) ?>
        <?php else: ?>
        <?= $this->Form->hidden( 'attend_status' ,['value'=> 3 ]) ?>
        <p>退社済</p>
        <?php endif; ?>
        <?= $this->Form->end() ?>
    </div> -->