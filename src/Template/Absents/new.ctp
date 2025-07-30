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
                time: {
                    time: true,
                    required: true,
                },
                date: {
                    required: true,
                },
                naiyou: {
                    required: true,
                }
            },
            messages: {
                time: {
                    required: "必須項目です",
                },
                date: {
                    required: "必須項目です",
                },
                naiyou: {
                    required: "必須項目です",
                },
            },
        });
    });
</script>

<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
$this->assign('title', '欠席情報登録');
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
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
    </ul>
</nav>

<!-- 
<?php 
    pr($admresults);
    pr($users);
?> -->

<div class="users index large-9 medium-8 columns content">
    <h3><?= __('欠席情報登録') ?></h3>
    <?= $this->Form->create(null, ['type' => 'post',"id" => "editform", 'url' => ['action' => 'register']]) ?>
    <fieldset>
        <legend><?= __('基本情報') ?></legend>
        <div class = "odakoku">
            <div class = "sdakoku">
                <?= $this->Form->control('year', ['type' => 'select', 'label' => "年", 'value'=> $year], $years) ?>
            </div>
            <div class = "sdakoku">
                <?= $this->Form->control('month', ['type' => 'select', 'label' => "月", 'value'=> $month], $months) ?>
            </div>
            <div class = "sdakoku">
                <?= $this->Form->control('date', ['type' => 'text', 'label' => '日', 'value'=> $date]) ?>
            </div>
            <div class = "futu3">
                <?php if(empty($notnew["time"])) {
                    echo $this->Form->label("時間");
                    echo $this->Form->text('time', ['id' => 'reporttime','type' => 'time','label' => '時間']);
                } else {
                    echo $this->Form->label("時間");
                    echo $this->Form->text('time', ['id' => 'reporttime','type' => 'time','label' => '時間', 'value' => $notnew["time"]->i18nFormat("H:mm")]);
                } 
                ?>
            </div>
            <div class = "futu3">
                <?php if(empty($notnew["shudan"])) {
                    echo $this->Form->control('shudan', ['type' => 'text', 'label' => '手段','value' => '電話']);
                } else {
                    echo$this->Form->control('shudan', ['type' => 'text', 'label' => '手段', 'value' => $notnew["shudan"]]);
                } 
                ?>
            </div>
        </div>
        <div class = "odakoku">
            <div class = "staffbox2">
                <?php
                    echo $this->Form->label("受けた人");
                    echo $this->Form->select('user_staffid',$admresults,array('id'=>'user_staffid','label' => "受けた人",'type'=> 'select','value' => $staff_id));
                ?>
            </div>
            <div class = "staffbox2">
                <?php echo $this->Form->label("欠席者")?>
                    <?php echo $this->Form->select('user_id',$users,array('id'=>'user_id','label' => "欠席者",'type'=> 'select','value' => $user_id));?>
                </div>
            <div class = "futu2">
                <?php if(empty($notnew["relation"])) {
                    echo $this->Form->control('relation', ['type' => 'text', 'label' => '関係','value' => '本人']);
                } else {
                    echo$this->Form->control('relation', ['type' => 'text', 'label' => '関係', 'value' => $notnew["relation"]]);
                } 
                ?>
            </div>
        </div>
        <br>
        <legend><?= __('報告内容') ?></legend>
            <div class = "textboxx">
                <?php if(empty($notnew["naiyou"])) {
                    echo $this->Form->control('naiyou', ['type' => 'textarea', 'label' => '内容']);
                } else {
                    echo $this->Form->control('naiyou', ['type' => 'textarea', 'label' => '内容', 'value' => $notnew["naiyou"]]);
                } 
                ?>
            </div>
            <div class = "odakoku">
                <?php 
                    echo $this->Form->label('次回利用の促し：');
                    echo '<br>';
                    
                    //radioボタンの作成
                    $options = [
                                '1' => ' 行った　　',
                                '0' => ' 行えなかった　　'
                                ];
                    if(empty($notnew["next"])) {
                        $attributes = array('value' => '1');
                    } else {
                        $attributes = array('value' => $notnew["next"]);
                    }           
                    echo $this->Form->radio('next', $options, $attributes);
                ?>
            </div>
            <br>
            <div class = "hiroshi">
                <?php if(empty($notnew["answer1"])) {
                    echo $this->Form->control('answer1', ['type' => 'text', 'label' => '相手の回答：']);
                } else {
                    echo $this->Form->control('answer1', ['type' => 'text', 'label' => '相手の回答：', 'value' => $notnew["answer1"]]);
                } 
                ?>
            </div>
            <div class = "hiroshi">
                <?php if(empty($notnew["answer2"])) {
                    echo $this->Form->control('answer2', ['type' => 'text', 'label' => '相手の回答：']);
                } else {
                    echo $this->Form->control('answer2', ['type' => 'text', 'label' => '相手の回答：', 'value' => $notnew["answer2"]]);
                } 
                ?>
            </div>
            <div class = "hiroshi">
                <?php if(empty($notnew["answer3"])) {
                    echo $this->Form->control('answer3', ['type' => 'text', 'label' => '相手の回答：']);
                } else {
                    echo $this->Form->control('answer3', ['type' => 'text', 'label' => '相手の回答：', 'value' => $notnew["answer3"]]);
                } 
                ?>
            </div>
            <div class = "hiroshi">
                <?php if(empty($notnew["answer4"])) {
                    echo $this->Form->control('answer4', ['type' => 'text', 'label' => '相手の回答：']);
                } else {
                    echo $this->Form->control('answer4', ['type' => 'text', 'label' => '相手の回答：', 'value' => $notnew["answer4"]]);
                } 
                ?>
            </div>
        <legend><?= __('備考') ?></legend>
            <br>
            <div class = "textboxx">
                <?php if(empty($notnew["bikou"])) {
                    echo $this->Form->control('bikou', ['type' => 'textarea', 'label' => false]);
                } else {
                    echo $this->Form->control('bikou', ['type' => 'textarea', 'label' => false, 'value' => $notnew["bikou"]]);
                } 
                ?>
            </div>
    </fieldset>
        
    <div class="absent_button">
            <?= $this->Form->button(__("送信")) ?>
    </div>
    <?php $this -> Form -> end(); ?>