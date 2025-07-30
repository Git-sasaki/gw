<script type="text/javascript">
    $(function(){
        <?php $tsuitachi = mktime(0,0,0,$month,1,$year); ?>
        <?php for($i=1; $i<=date('t',$tsuitachi); $i++):?>
            // 日にちをクリックした際の処理
            $(".schedule-<?=$i?>").click(function(){
                var id = $("#hid-<?=$i?>").val();
                var date = $("#hdate-<?=$i?>").val();
                var hidake = $("#hidake-<?=$i?>").val();
                var plana = $("#hplana-<?=$i?>").val();
                var planb = $("#hplanb-<?=$i?>").val();
                var planc = $("#hplanc-<?=$i?>").val();
                var memo = $("#hmemo-<?=$i?>").val();

                var jsondata = {
                    "id":id,
                    "date":date,
                    "hidake":hidake,
                    "plana":plana,
                    "planb":planb,
                    "planc":planc,
                    "memo":memo,
                };

                var csrf = $('input[name=_csrfToken]').val();
                $.ajax({
                    'url'	:"<?php echo $this->Url->build(["controller" => "Calendars","action" => "scheduleAjax","ajax" => true], true); ?>",
                    'type'	:'post',
                    'beforeSend': function(xhr){
                        xhr.setRequestHeader('X-CSRF-Token', csrf);
                    },
                    'async'	:false,
                    'data'	:jsondata,
                    'success':function(result){
                        var splitdate = date.split('-');
                        $("#hinichi").text(splitdate[0]+" 年 "+splitdate[1]+" 月 "+splitdate[2]+" 日 ");
                        $("#date").val(jsondata["date"]);
                        $("#plana").val(jsondata["plana"]);
                        $("#planb").val(jsondata["planb"]);
                        $("#planc").val(jsondata["planc"]);
                        $("#memo").val(jsondata["memo"]);
                    },
                    'error':function(status){
                        alert('エラー');
                    }
                });
                $("#js-modal").addClass("open");
                $("#js-overlay").addClass("open");
            });
            // モーダルウィンドウを閉じる際の処理
            $("#js-close").click(function(){
                $("#js-modal").removeClass("open");
                $("#js-overlay").removeClass("open");
            });
        <?php endfor;?>
    });
</script>

<?php $this->assign('title', 'カレンダー'); ?>
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

<div class="users index large-9 medium-8 columns content">
    <h3><?= __($year.' 年 '.$month.' 月') ?></h3>
    <table class="table01 table02">
        <thead>
            <tr>
                <th scope="col" class = "h147">日</th>
                <th scope="col" class = "h147">月</th>
                <th scope="col" class = "h147">火</th>
                <th scope="col" class = "h147">水</th>
                <th scope="col" class = "h147">木</th>
                <th scope="col" class = "h147">金</th>
                <th scope="col" class = "h147">土</th>
            </tr>
        </thead>
        <tbody>
            <?php $count=0;?>
            <!-- 一日までの空欄の処理 -->
            <?php foreach($maes as $mae): ?>
                <?php if($count==0): ?>
                    <tr>
                <?php endif; ?>
                <td><div></div></td>
                <?php $count++ ?>
            <?php endforeach; ?>
            <!-- 日にちの処理 -->
            <?php foreach($dates as $date): ?>
                <?php if($count==0): ?>
                    <tr>
                <?php endif; ?>
                <?php if(date('w',mktime(0,0,0,$month,$date["hidake"],$year))==0): ?>
                    <?php if(date('Y-m-d') == date('Y-m-d',mktime(0,0,0,$month,$date["hidake"],$year))): ?>
                        <td class = "d147 sunday" style = "background-color: #a3f1ff;">
                    <?php else: ?>
                        <td class = "d147 sunday">
                    <?php endif; ?>
                <?php elseif(date('w',mktime(0,0,0,$month,$date["hidake"],$year))==6): ?>
                    <?php if(date('Y-m-d') == date('Y-m-d',mktime(0,0,0,$month,$date["hidake"],$year))): ?>
                        <td class = "d147 saturday" style = "background-color: #a3f1ff;">
                    <?php else: ?>
                        <td class = "d147 saturday">
                    <?php endif; ?>
                <?php elseif($holidays->isHoliday(new \DateTime(date("Y-m-d",$timestamp))) == 1): ?>
                    <?php if(date('Y-m-d') == date('Y-m-d',mktime(0,0,0,$month,$date["hidake"],$year))): ?>
                        <td class = "d147 sunday" style = "background-color: #a3f1ff;">
                    <?php else: ?>
                        <td class = "d147 sunday">
                    <?php endif; ?>
                <?php else: ?>
                    <?php if(date('Y-m-d') == date('Y-m-d',mktime(0,0,0,$month,$date["hidake"],$year))): ?>
                        <td class = "d147 heijitsu" style = "background-color: #a3f1ff;">
                    <?php else: ?>
                        <td class = "d147 heijitsu">
                    <?php endif; ?>
                <?php endif; ?>
                <!-- 数値にデータを付与 -->
                    <input type = "hidden" id = "hid-<?=$date["hidake"]?>" value = <?=$date["id"]?>></input>
                    <?php if(!empty($date["id"])): ?>
                        <input type = "hidden" id = "hdate-<?=$date["hidake"]?>" value = <?=$date["date"]->i18nFormat('yyyy-MM-dd')?>></input>
                    <?php else: ?>
                        <input type = "hidden" id = "hdate-<?=$date["hidake"]?>" value = <?=$date["date"]?>></input>
                    <?php endif; ?>
                    <input type = "hidden" id = "hidake-<?=$date["hidake"]?>" value = <?=$date["hidake"]?>></input>
                    <input type = "hidden" id = "hplana-<?=$date["hidake"]?>" value = <?=$date["plana"]?>></input>
                    <input type = "hidden" id = "hplanb-<?=$date["hidake"]?>" value = <?=$date["planb"]?>></input>
                    <input type = "hidden" id = "hplanc-<?=$date["hidake"]?>" value = <?=$date["planc"]?>></input>
                    <input type = "hidden" id = "hmemo-<?=$date["hidake"]?>" value = <?=$date["memo"]?>></input>
                    <div class = "schedule-<?=$date["hidake"]?>" id="modalopen"><?=$date["hidake"]?></div>
                    <div class = "fs10 mt5 ninja">
                        <?php if(!empty($date["id"])): ?>
                            <div class = "ari ao">予定あり</div>
                            <div class = "kakure"><?= $date["plana"] ?><br><?= $date["planb"] ?><br><?= $date["planc"] ?></div>
                        <?php endif; ?>
                    </div>
                    </td>
                <?php $count++ ?>
                <?php if($count==7): ?>
                    </tr>
                    <?php $count=0?>
                <?php endif; ?>
            <?php endforeach; ?>
            <!-- 月末の後の空欄の処理 -->
            <?php foreach($atos as $ato): ?>
                <td><div></div></td>
                <?php $count++ ?>
            <?php endforeach; ?>
        </tbody>
    </table>

    <fieldset class = "mt30">
        <legend><?= __('年月を選択') ?></legend>
            <?= $this->Form->create(__("View"),["type"=>"post","url"=>["action"=>"index"]]) ?>
            <div class = "odakoku">
                <div class = "sdakoku">
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>date('Y')], $years) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>date('m')], $months) ?>
                </div>
                <div class="left_button3">
                    <?= $this->Form->button(__("送信")) ?>
                </div>
                <?= $this -> Form -> end(); ?>
            </div>  
        </div>
    </fieldset>

    <!-- モーダルウィンドウ -->
    <div class="overlay" id="js-overlay"></div>
    <div class="modal" id="js-modal">
        <!-- 右上の☓ボタン -->
        <div class="modalclose_wrap">
            <button class="modalclose" id="js-close">
            <span></span>
            <span></span>
            </button>
        </div>
        <!-- フォームの表示 -->
        <?= $this->Form->create(__("View"),["type"=>"post","url"=>["action"=>"register"]]); ?>
            <?php echo $this->Form->control('date',['type'=>'hidden','id'=>'date','value'=>""]) ;?>
        <fieldset class = "mt20">
            <legend id = "hinichi"></legend>
            <div class = "modalleft">
                <div class = "w500">
                    <?= $this->Form->control('plana', ['type' => 'text','id'=>'plana','label' => '予定1', 'value'=>""]); ?>
                </div>
                <div class = "w500">
                    <?= $this->Form->control('planb', ['type' => 'text','id'=>'planb', 'label' => '予定2', 'value'=>""]); ?>
                </div>
                <div class = "w500">
                    <?= $this->Form->control('planc', ['type' => 'text','id'=>'planc', 'label' => '予定3', 'value'=>""]); ?>
                </div>
            </div>
            <div class = "modalleft">
                <div class = "w500">
                    <?= $this->Form->control('memo', ['type' => 'textarea','id'=>'memo', 'label' => 'その他メモなど', 'value'=>""]); ?>
                </div>
                <div class="modalbutton">
                    <?= $this->Form->button(__("登録")) ?>
                </div>
            </div>
            <?= $this -> Form -> end(); ?>
        </fieldset>