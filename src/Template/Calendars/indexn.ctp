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
                    "memo":memo
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
                        var response = JSON.parse(result);
                        var syokujilist = response.syokuji;
                        var sougeilist = response.sougei;

                        var splitdate = jsondata["date"].split('-');
                        $("#hinichi").text(splitdate[0]+" 年 "+splitdate[1]+" 月 "+splitdate[2]+" 日 ");
                        $("#date").val(jsondata["date"]);
                        $("#plana").val(jsondata["plana"]);
                        $("#planb").val(jsondata["planb"]);
                        $("#planc").val(jsondata["planc"]);
                        $("#memo").val(jsondata["memo"]);

                        //食事提供者表示
                        $("#syokujilist").empty();
                        for ( var i = 0;  i < syokujilist.length;  i++  ) {
                            $("#syokujilist").append("<tr><td>" + syokujilist[i]['name'] +"</td></tr>");
                        }
                        
                        //送迎者表示
                        $("#sougeilist").empty();
                        for (var i = 0; i < sougeilist.length; i++) {
                            var row = "<tr><td id='modalsougei_01d'>" + sougeilist[i]['time'] + "</td>"
                                    + "<td id='modalsougei_02d'>" + sougeilist[i]['sougei_type'] + "</td>"
                                    + "<td id='modalsougei_03d'>" + sougeilist[i]['name'] + "</td>"
                                    + "<td id='modalsougei_04d'>" + sougeilist[i]['place'] + "</td></tr>";
                            $("#sougeilist").append(row);
                        }
                        
                        // 隠しフィールドにJSONデータをセット
                        $("#SougeiData").val(JSON.stringify(sougeilist));

                        // 送迎データが 0 件なら出力ボタンを非表示
                        if (sougeilist.length === 0) {
                            $("#modalsougeibutton").hide();
                        } else {
                            $("#modalsougeibutton").show();
                        }
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
        <?= $this->Form->control('hidden',['type'=>'hidden','value'=>0]) ?>
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
    </div>
    
    <div class = "main1">
        <div style = "height:40px"></div>
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
                    <?php if(!empty($date["id"])): ?>
                        <input type = "hidden" id = "hid-<?=$date["hidake"]?>" value = <?=$date["id"]?>></input>
                        <input type = "hidden" id = "hdate-<?=$date["hidake"]?>" value = <?=$date["date"]->i18nFormat('yyyy-MM-dd')?>></input>
                        <input type = "hidden" id = "hidake-<?=$date["hidake"]?>" value = <?=$date["hidake"]?>></input>
                        <input type = "hidden" id = "hplana-<?=$date["hidake"]?>" value = "<?= htmlspecialchars($date["plana"], ENT_QUOTES) ?>"></input>
                        <input type = "hidden" id = "hplanb-<?=$date["hidake"]?>" value = "<?= htmlspecialchars($date["planb"], ENT_QUOTES) ?>"></input>
                        <input type = "hidden" id = "hplanc-<?=$date["hidake"]?>" value = "<?= htmlspecialchars($date["planc"], ENT_QUOTES) ?>"></input>
                        <input type = "hidden" id = "hmemo-<?=$date["hidake"]?>" value = "<?= htmlspecialchars($date["memo"], ENT_QUOTES) ?>"></input>
                    <?php else: ?>
                        <input type = "hidden" id = "hid-<?=$date["hidake"]?>"></input>
                        <input type = "hidden" id = "hdate-<?=$date["hidake"]?>" value = <?= date('Y-m-d',mktime(0,0,0,$month,$date["hidake"],$year))?> ></input>
                        <input type = "hidden" id = "hidake-<?=$date["hidake"]?>"></input>
                        <input type = "hidden" id = "hplana-<?=$date["hidake"]?>"></input>
                        <input type = "hidden" id = "hplanb-<?=$date["hidake"]?>"></input>
                        <input type = "hidden" id = "hplanc-<?=$date["hidake"]?>"></input>
                        <input type = "hidden" id = "hmemo-<?=$date["hidake"]?>"></input>
                    <?php endif; ?>

                    <div class = "schedule-<?=$date["hidake"]?>" id="modalopen"><?=$date["hidake"]?></div>
                    <div class = "fs10 mt5 ninja">
                        <?php if($date["flag"] == 1): ?>
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
    <?= $this->Form->create(__("View"),["type"=>"post","url"=>["action"=>"register"]]); ?>
        <?php echo $this->Form->control('date',['type'=>'hidden','id'=>'date','value'=>""]) ;?>
    <fieldset class = "mt10">
    <legend id="hinichi"></legend>
        <div id="modalscd">
            <div>
                <div class = "modalleft">
                    <div class = "w400">
                        <?= $this->Form->control('plana', ['type' => 'text','id'=>'plana','label' => '予定1', 'value'=>""]); ?>
                    </div>
                    <div class = "w400">
                        <?= $this->Form->control('planb', ['type' => 'text','id'=>'planb', 'label' => '予定2', 'value'=>""]); ?>
                    </div>
                    <div class = "w400">
                        <?= $this->Form->control('planc', ['type' => 'text','id'=>'planc', 'label' => '予定3', 'value'=>""]); ?>
                    </div>
                    <div class = "w400">
                        <?= $this->Form->control('memo', ['type' => 'textarea','id'=>'memo', 'label' => 'その他メモなど', 'value'=>""]); ?>
                    </div>
                    <div class="modalbutton">
                        <?= $this->Form->button(__("登録")) ?>
                    </div>
                </div>
            </div>
            <?= $this -> Form -> end(); ?>
            <div id="modalsyokuji">
                <table id="modalsyokuji_tbl">
                    <thead>
                        <tr>
                            <th>昼食提供者</th>
                        </tr>     
                    </thead>
                    <tbody id="syokujilist">
                    </tbody>
                </table>
            </div>
            <div id="modalsougei">
                <table id="modalsougei_tbl">
                    <thead>
                        <tr>
                            <th id="modalsougei_00" colspan="4">送迎者</th>
                        </tr>     
                        <tr>
                            <th id="modalsougei_01">時間</th>
                            <th id="modalsougei_02">送迎種別</th>
                            <th id="modalsougei_03">氏名</th>
                            <th cid="modalsougei_04">場所</th>
                        </tr>     
                    </thead>
                    <tbody id="sougeilist">
                    </tbody>
                </table>
                <div>
                    <!--
                    <?= $this->Form->create(null, [
                        'url' => ['controller' => 'Sougei', 'action' => 'sougeiexcel'],
                        'id' => 'sougeiForm'
                    ]) ?>
                    -->

                    <?php
                        $SougeiData = isset($SougeiData) ? $SougeiData : json_encode([]);
                        ?>
                        <?= $this->Form->control('SougeiData', [
                            'type' => 'hidden',
                            'id' => 'SougeiData',
                            'value' => $SougeiData
                        ]);
                    ?>
                    <!--
                    <?= $this->Form->button(__('出力'), ['id' => 'modalsougeibutton', 'class' => 'custom-btn']) ?>
                    <?= $this->Form->end() ?>
                    -->
                    </div>
            </div>
        </div>
    </fieldset>

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
    }
?>