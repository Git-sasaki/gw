<script type="text/javascript">
    $(function(){
        <?php $tsuitachi = mktime(0,0,0,$month,1,$year); ?>
        <?php for($i=1; $i<=date('t',$tsuitachi); $i++):?>
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
                    'url'	:"<?php echo $this->Url->build(["controller" => "Calendars",
                             "action" => "scheduleAjax","ajax" => true], true); ?>",
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
            $("#js-close").click(function(){
                $("#js-modal").removeClass("open");
                $("#js-overlay").removeClass("open");
            });
        <?php endfor;?>
    });
</script>

<?php $this->assign('title', 'スケジュール'); ?>

<div class = "odakoku">
    <div class = "sidemenu mvh80">
    <?= $this -> Form -> create(__("View"),["type" => "post","url" => ["action" => "getquery0"]]); ?>
        <?= $this->Form->control('hidden',['type'=>'hidden','value'=>2]) ?>
        <h4 class = "sideh4 ml10 pt15">以下の年月を表示</h4>  
            <div class = "odakoku ml10">
                <div class = "sdakoku">
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>$year], $years) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>$month], $months) ?> 
                </div>
            </div>
        <div class="ml10_button mt30 ml10">
            <?= $this->Form->button(__("表示")) ?>
        </div>
    <?= $this -> Form -> end(); ?>
    <?= $this -> Form -> create(__("View"),["type" => "post","url" => ["action" => "getquery0"]]); ?>
        <?= $this->Form->control('hidden',['type'=>'hidden','value'=>3]) ?>
        <h4 class = "sideh4 ml10 pt15">スケジュール出力</h4>  
            <div class = "odakoku ml10">
                <div class = "sdakoku">
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>$year], $years) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>$month], $months) ?> 
                </div>
            </div>
        <div class="ml10_button mt30 ml10">
            <?= $this->Form->button(__("表示")) ?>
        </div>
    <?= $this -> Form -> end(); ?>
    </div>
    
    <div class = "main1">
        <!-- <div style = "height:40px"></div> -->
        <h4 class = "titleh4 mt20"><?= $userName?> さん　<?=$year?>年<?=$month?>月スケジュール</h4>
    <table class="table01 table02 mt30 mb30">
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
            <?php foreach($maes as $mae): ?>
                <?php if($count==0): ?>
                    <tr>
                <?php endif; ?>
                <td><div></div></td>
                <?php $count++ ?>
            <?php endforeach; ?>
            <?php foreach($dates as $date): ?>
                <?php if($count==0): ?>
                    <tr>
                <?php endif; ?>
                <?php if($host != "[::1]:8765"): ?>
                    <?php if($holidays->isHoliday(new \DateTime(date('Y-m-d',mktime(0,0,0,$month,$date["hidake"],$year)))) == 1): ?>
                        <?php if(date('Y-m-d') == date('Y-m-d',mktime(0,0,0,$month,$date["hidake"],$year))): ?>
                            <td class = "d147 sunday" style = "background-color: #a3f1ff;">
                        <?php else: ?>
                            <td class = "d147 sunday">
                        <?php endif; ?>
                    <?php elseif(date('w',mktime(0,0,0,$month,$date["hidake"],$year)) == 0): ?>
                        <?php if(date('Y-m-d') == date('Y-m-d',mktime(0,0,0,$month,$date["hidake"],$year))): ?>
                            <td class = "d147 sunday" style = "background-color: #a3f1ff;">
                        <?php else: ?>
                            <td class = "d147 sunday">
                        <?php endif; ?>
                    <?php elseif(date('w',mktime(0,0,0,$month,$date["hidake"],$year)) == 6): ?>
                        <?php if(date('Y-m-d') == date('Y-m-d',mktime(0,0,0,$month,$date["hidake"],$year))): ?>
                            <td class = "d147 saturday" style = "background-color: #a3f1ff;">
                        <?php else: ?>
                            <td class = "d147 saturday">
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if(date('Y-m-d') == date('Y-m-d',mktime(0,0,0,$month,$date["hidake"],$year))): ?>
                            <td class = "d147 heijitsu" style = "background-color: #a3f1ff;">
                        <?php else: ?>
                            <td class = "d147 heijitsu">
                        <?php endif; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if(date('w',mktime(0,0,0,$month,$date["hidake"],$year)) == 0): ?>
                        <?php if(date('Y-m-d') == date('Y-m-d',mktime(0,0,0,$month,$date["hidake"],$year))): ?>
                            <td class = "d147 sunday" style = "background-color: #a3f1ff;">
                        <?php else: ?>
                            <td class = "d147 sunday">
                        <?php endif; ?>
                    <?php elseif(date('w',mktime(0,0,0,$month,$date["hidake"],$year)) == 6): ?>
                        <?php if(date('Y-m-d') == date('Y-m-d',mktime(0,0,0,$month,$date["hidake"],$year))): ?>
                            <td class = "d147 saturday" style = "background-color: #a3f1ff;">
                        <?php else: ?>
                            <td class = "d147 saturday">
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if(date('Y-m-d') == date('Y-m-d',mktime(0,0,0,$month,$date["hidake"],$year))): ?>
                            <td class = "d147 heijitsu" style = "background-color: #a3f1ff;">
                        <?php else: ?>
                            <td class = "d147 heijitsu">
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
                    <?php if(!empty($date["id"])): ?>
                        <input type = "hidden" id = "hid-<?=$date["hidake"]?>" value = <?=$date["id"]?>></input>
                        <input type = "hidden" id = "hdate-<?=$date["hidake"]?>" value = <?=$date["date"]->i18nFormat('yyyy-MM-dd')?>></input>
                        <input type = "hidden" id = "hidake-<?=$date["hidake"]?>" value = <?=$date["hidake"]?>></input>
                        <input type = "hidden" id = "hplana-<?=$date["hidake"]?>" value = "<?= htmlspecialchars($date["plana"], ENT_QUOTES) ?>"></input>
                        <input type = "hidden" id = "hplanb-<?=$date["hidake"]?>" value = "<?= htmlspecialchars($date["planb"], ENT_QUOTES) ?>"></input>
                        <input type = "hidden" id = "hplanc-<?=$date["hidake"]?>" value = "<?= htmlspecialchars($date["planc"], ENT_QUOTES) ?>"></input>
                    <?php else: ?>
                        <input type = "hidden" id = "hid-<?=$date["hidake"]?>"></input>
                        <input type = "hidden" id = "hdate-<?=$date["hidake"]?>" value = <?= date('Y-m-d',mktime(0,0,0,$month,$date["hidake"],$year))?> ></input>
                        <input type = "hidden" id = "hidake-<?=$date["hidake"]?>"></input>
                        <input type = "hidden" id = "hplana-<?=$date["hidake"]?>"></input>
                        <input type = "hidden" id = "hplanb-<?=$date["hidake"]?>"></input>
                        <input type = "hidden" id = "hplanc-<?=$date["hidake"]?>"></input>
                    <?php endif; ?>

                    <div class = "schedule-<?=$date["hidake"]?>" id="modalopen"><?=$date["hidake"]?></div>
                    <div class = "fs10 mt5 ninja">
                        <?php if($date["flag"] == 1): ?>
                            <div class = "ari ao">予定あり</div>
                        <?php endif; ?>
                    </div>
                    </td>
                <?php $count++ ?>
                <?php if($count==7): ?>
                    </tr>
                    <?php $count=0?>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php foreach($atos as $ato): ?>
                <td><div></div></td>
                <?php $count++ ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<!-- モーダルウィンドウ -->
<div class="overlay" id="js-overlay"></div>
<div class="modal maeofmae" id="js-modal">
    <div class="modalclose_wrap">
        <button class="modalclose" id="js-close">
        <span></span>
        <span></span>
        </button>
    </div>
    <?= $this->Form->create(__("View"),["type"=>"post","url"=>["action"=>"register2"]]); ?>
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
            <div class="modalbutton">
                <?= $this->Form->button(__("登録")) ?>
            </div>
        </div>
        <?= $this -> Form -> end(); ?>
    </fieldset>