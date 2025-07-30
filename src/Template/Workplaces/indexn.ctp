<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/additional-methods.min.js"></script>
<script type="text/javascript" src="/js/validate-config.js"></script>

<script type="text/javascript">
    function sgChange(obj) {
        var ctrlAry =  ["company", "mokuhyo","wrkcontentsu","styear","stmonth","stdate","edyear","edmonth","eddate"];
        
        idx = obj.name.substr( 3);

        for (i = 0; i < ctrlAry.length; i++) {
            ctrlname = ctrlAry[i]+ idx;
            document.getElementsByName(ctrlname)[0].disabled == (obj.checked) ? null : "disabled";
        }
    }

    function henkouCheck() {
        //施設数を取得
        const arryCount = document.querySelectorAll('input[name^="name["]').length;
      
        for ( i = 1; i <= arryCount; i++) {
            //施設名の空白チェック
            ctrlName = "name[" + i + "]";
            shiname = document.getElementsByName(ctrlName)[0].value;
            if (!shiname) {
                alert("施設名を入力して下さい。");
                return false;
            }

            //日付の妥当性チェック（開始日付）
            arynum= "[" + i + "]";
            styear = "styear" + arynum;
            stmonth = "stmonth" + arynum;
            stdate = "stdate" + arynum;
            if ( typeof document.getElementsByName(styear)[0] != 'undefined') {
                isExist1 = !(document.getElementsByName(styear)[0].value == 0) && 
                      !(document.getElementsByName(stmonth)[0].value == 0) &&
                      !(document.getElementsByName(stdate)[0].value == 0);  
                isExist2 = !(document.getElementsByName(styear)[0].value == 0) || 
                      !(document.getElementsByName(stmonth)[0].value == 0) ||
                      !(document.getElementsByName(stdate)[0].value == 0);  
               if ( isExist1) {
                    val = document.getElementsByName(styear)[0].value + "-" +
                          document.getElementsByName(stmonth)[0].value + "-" +
                          document.getElementsByName(stdate)[0].value;
                    date = new Date(val);
                    if (isNaN(date.getDate()) || (date.getDate() != document.getElementsByName(stdate)[0].value)) {
                        alert("入力された日付が妥当性に欠けます。");
                        return false;
                    }                    
                } else {
                    if ( isExist2) {
                        alert("日付入力が完全ではありません。");
                        return false;
                    }
                }
            }

            //日付の妥当性チェック（終了日付）
            arynum= "[" + i + "]";
            edyear = "edyear" + arynum;
            edmonth = "edmonth" + arynum;
            eddate = "eddate" + arynum;
            if ( typeof document.getElementsByName(edyear)[0] != 'undefined') {
                isExist1 = !(document.getElementsByName(edyear)[0].value == 0) && 
                      !(document.getElementsByName(edmonth)[0].value == 0) &&
                      !(document.getElementsByName(eddate)[0].value == 0);  
                isExist2 = !(document.getElementsByName(edyear)[0].value == 0) || 
                      !(document.getElementsByName(edmonth)[0].value == 0) ||
                      !(document.getElementsByName(eddate)[0].value == 0);  
               if ( isExist1) {
                    val = document.getElementsByName(edyear)[0].value + "-" +
                          document.getElementsByName(edmonth)[0].value + "-" +
                          document.getElementsByName(eddate)[0].value;
                    date = new Date(val);
                    if (isNaN(date.getDate()) || (date.getDate() != document.getElementsByName(eddate)[0].value)) {
                        alert("入力された日付が妥当性に欠けます。");
                        return false;
                    }                    
                } else {
                    if ( isExist2) {
                        alert("日付入力が完全ではありません。");
                        return false;
                    }
                }
            }
        }

        return true;
    }
   
    function shinkiname() {
        shiname = document.getElementsByName("name")[0].value;
        if (!shiname) {
            alert("施設名を入力して下さい。");
            return false;
        }

        //日付の妥当性チェック（開始日付）
        if ( typeof document.getElementsByName("styear")[0] != 'undefined') {
            isExist1 = !(document.getElementsByName("styear")[0].value == 0) && 
                    !(document.getElementsByName("stmonth")[0].value == 0) &&
                    !(document.getElementsByName("stdate")[0].value == 0);  
            isExist2 = !(document.getElementsByName("styear")[0].value == 0) || 
                    !(document.getElementsByName("stmonth")[0].value == 0) ||
                    !(document.getElementsByName("stdate")[0].value == 0);  
            if ( isExist1) {
                val = document.getElementsByName("styear")[0].value + "-" +
                        document.getElementsByName("stmonth")[0].value + "-" +
                        document.getElementsByName("stdate")[0].value;
                date = new Date(val);
                if (isNaN(date.getDate()) || (date.getDate() != document.getElementsByName("stdate")[0].value)) {
                    alert("入力された日付が妥当性に欠けます。");
                    return false;
                }                    
            } else {
                if ( isExist2) {
                    alert("日付入力が完全ではありません。");
                    return false;
                }
            }
        }

        //日付の妥当性チェック（終了日付）
        if ( typeof document.getElementsByName("edyear")[0] != 'undefined') {
            isExist1 = !(document.getElementsByName("edyear")[0].value == 0) && 
                    !(document.getElementsByName("edmonth")[0].value == 0) &&
                    !(document.getElementsByName("eddate")[0].value == 0);  
            isExist2 = !(document.getElementsByName("edyear")[0].value == 0) || 
                    !(document.getElementsByName("edmonth")[0].value == 0) ||
                    !(document.getElementsByName("eddate")[0].value == 0);  
            if ( isExist1) {
                val = document.getElementsByName("edyear")[0].value + "-" +
                        document.getElementsByName("edmonth")[0].value + "-" +
                        document.getElementsByName("eddate")[0].value;
                date = new Date(val);
                if (isNaN(date.getDate()) || (date.getDate() != document.getElementsByName("eddate")[0].value)) {
                    alert("入力された日付が妥当性に欠けます。");
                    return false;
                }                    
            } else {
                if ( isExist2) {
                    alert("日付入力が完全ではありません。");
                   return false;
                }
            }
        }

        return true;
    }


</script>
<?php $this->assign('title', '事業所情報設定'); ?>

<div class = "main1">
<h4 class = "titleh4 mt30">事業所情報設定</h4>

<h4 class="midashih4 mt30 mb30">　基本設定</h4>
<?= $this -> Form -> create(
    __("View"),
    ["type" => "post","url" => ["action" => "register"]]); ?>
    <?= $this->Form->control('type',['type'=>'hidden','value'=>2]) ?>

    <div class = "odakoku">
        <div class = "w400 mlv48">
            <?php if(empty($getCompany["jname"])): ?>
                <?= $this->Form->control('jname', ['label' => '事業所名']); ?>
            <?php else: ?>
                <?= $this->Form->control('jname', ['label' => '事業所名','value' => $getCompany["jname"]]); ?>
            <?php endif; ?>
        </div>
        <div class = "w200">
        <?php if(empty($getCompany["jnumber"])): ?>
                <?= $this->Form->control('jnumber', ['label' => '事業所番号']); ?>
            <?php else: ?>
                <?= $this->Form->control('jnumber', ['label' => '事業所番号','value' => $getCompany["jnumber"]]); ?>
            <?php endif; ?>
        </div>
    </div>
    <div class = "odakoku">
        <div class = "w400 mlv48">
        <?php if(empty($getCompany["skubun"])): ?>
                <?= $this->Form->control('skubun', ['label' => 'サービス種別']); ?>
            <?php else: ?>
                <?= $this->Form->control('skubun', ['label' => 'サービス種別','value' => $getCompany["skubun"]]); ?>
            <?php endif; ?>
        </div>
        <div class = "w100" style="margin-left: 10px;">
        <?php if(empty($getCompany["teiin"])): ?>
                <?= $this->Form->control('teiin', ['label' => '定員数']); ?>
            <?php else: ?>
                <?= $this->Form->control('teiin', ['label' => '定員数','value' => $getCompany["teiin"]]); ?>
            <?php endif; ?>
        </div>
        <div class = "w100" style="margin-left: 10px;">
        <?php if(empty($getCompany["jinkubun"])): ?>
                <?= $this->Form->control('jinkubun', ['label' => '人員配置区分']); ?>
            <?php else: ?>
                <?= $this->Form->control('jinkubun', ['label' => '人員配置区分','value' => $getCompany["jinkubun"]]); ?>
            <?php endif; ?>
        </div>
    </div>
    <div class = "odakoku">
        <div class = "w250 mlv48">
            <?php if(empty($getCompany["kanrisha"])): ?>
                <?= $this->Form->label("管理者"); ?>
                <?= $this->Form->select('kanrisha',$staffs,['id'=>'kanrisha','label'=>false,'empty'=>false]);?>
            <?php else: ?>
                <?= $this->Form->label("管理者"); ?>
                <?= $this->Form->select('kanrisha',$staffs,['id'=>'kanrisha','label'=>false,'empty'=>false,'value' => $getCompany["kanrisha"]]);?>
            <?php endif; ?>
        </div>
        <div class = "w250 ml10">
            <?php if(empty($getCompany["sabikan"])): ?>
                <?= $this->Form->label("サービス管理責任者"); ?>
                <?= $this->Form->select('sabikan',$staffs,['id'=>'sabikan','label'=>false,'empty'=>false]);?>
            <?php else: ?>
                <?= $this->Form->label("サービス管理責任者"); ?>
                <?= $this->Form->select('sabikan',$staffs,['id'=>'sabikan','label'=>false,'empty'=>false,'value' => $getCompany["sabikan"]]);?>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt30 mlv48">
        <?= $this->Form->button(__("登録")) ?>
    </div>
<?= $this -> Form -> end(); ?>    
<br>

<h4 class="midashih4 mt30 mb30">　施設一覧</h4>
<?= $this -> Form -> create(
    __("View"),
    ["type" => "post","url" => ["action" => "register"]]); ?>
    <?= $this->Form->control('type',['type'=>'hidden','value'=>0]) ?>

    <table class="table01 table02">
        <thead>
            <tr>
                <th scope="col" style = "width:10px">ID</th>
                <th scope="col" style = "width:50px">施設名</th>
                <th scope="col" style = "width:100px">住所</th>
                <th scope="col" style = "width:100px">施設外関係</th>
                <th scope="col" style = "width:20px">削除</th>
            </tr>
        </thead>
        <tbody>
            <?php $z = 1;?>
            <?php foreach ($getPlaces as $getPlace): ?>
                <tr>
                    <?= $this->Form->control("id[$z]",["type"=>'hidden','value'=>$getPlace["id"]]) ?>
                    <td><?= $getPlace["id"] ?></td>
                    <?php if(empty($getPlace["name"])): ?>
                        <td><?= $this->Form->control("name[$z]",["type" => "text","label" => false]); ?></td>
                    <?php else: ?>
                        <td><?= $this->Form->control("name[$z]",["type" => "text","label" => false,"value" => $getPlace["name"]]); ?></td>
                    <?php endif; ?>
                    <?php if(empty($getPlace["address"])): ?>
                        <td><?= $this->Form->control("address[$z]",["type" => "text","label" => false]); ?></td>
                    <?php else: ?>
                        <td><?= $this->Form->control("address[$z]",["type" => "text","label" => false,"value" => $getPlace["address"]]); ?></td>
                    <?php endif; ?>
                    <!-- 施設外関連 -->
                    <?php if(empty($getPlace["sub"]) || $getPlace["sub"] == 0): ?>
                    <?php else: ?>
                        <td>
                            <?= $this->Form->control("sub[$z]",array("type" => "checkbox","label" => false,"value" => 1,"checked"=>true,"onchange" =>"sgChange(this)"));?>
                            <div style="padding-top:10px;">就労先企業名</div><?= $this->Form->control("company[$z]",["type" => "text","label" => false, "value" => $getPlace["company"]]); ?>
                            <div style="padding-top:10px;">目標・目的等</div><?= $this->Form->control("mokuhyo[$z]",["type" => "text","label" => false, "value" => $getPlace["mokuhyo"]]); ?>
                            <div style="padding-top:10px;">受注作業内容</div><?= $this->Form->control("wrkcontentsu[$z]",["type" => "text","label" => false, "value" => $getPlace["wrkcontentsu"]]); ?><br>
                            <div>契約期間</div>
                            <div style="display: flex; align-items: center; justify-content: center;">
                                <div class="mt30">開始</div>
                                <div class="ml5">
                                    <?= $this->Form->label("年") ?>
                                    <div>
                                        <?php $styear = (empty($getPlace["stdate"])) ? null : date('Y', strtotime($getPlace["stdate"])); ?>
                                        <?= $this->Form->select("styear[$z]",$years,['class'=>'shisetugai_year','empty'=>array('0'=>null),'value'=>$styear]) ?>
                                    </div>
                                </div>
                                <div class="ml5">
                                    <?= $this->Form->label("月") ?>  
                                    <div>  
                                        <?php $stmonth = (empty($getPlace["stdate"])) ? null : date('m', strtotime($getPlace["stdate"])); ?>
                                        <?= $this->Form->select("stmonth[$z]",$months,['class'=>'shisetugai_month','empty'=>array('0'=>null),'value'=>$stmonth]); ?>
                                    </div>
                                </div>
                                <div class="ml5">       
                                    <?= $this->Form->label("日") ?>    
                                    <?php $stdate = (empty($getPlace["stdate"])) ? null : date('d', strtotime($getPlace["stdate"])); ?>
                                    <?= $this->Form->control("stdate[$z]",["type"=>"text","label"=>false, "style"=>"width:40px", 'value'=>$stdate]) ?>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; justify-content: center;">
                            <div class="mt30">終了</div>
                                <div class="ml5">
                                    <?= $this->Form->label("年") ?>
                                    <div>
                                        <?php $edyear = (empty($getPlace["eddate"])) ? null : date('Y', strtotime($getPlace["eddate"])); ?>
                                        <?= $this->Form->select("edyear[$z]",$years,['class'=>'shisetugai_year','empty'=>array('0'=>null),'value'=>$edyear]) ?>
                                    </div>
                                </div>
                                <div class="ml5">
                                    <?= $this->Form->label("月") ?>  
                                    <div>  
                                    <?php $edmonth = (empty($getPlace["eddate"])) ? null : date('m', strtotime($getPlace["eddate"])); ?>
                                    <?= $this->Form->select("edmonth[$z]",$months,['class'=>'shisetugai_month','empty'=>array('0'=>null),'value'=>$edmonth]); ?>
                                    </div>
                                </div>
                                <div class="ml5">       
                                    <?= $this->Form->label("日") ?>    
                                    <?php $eddate = (empty($getPlace["eddate"])) ? null : date('d', strtotime($getPlace["eddate"])); ?>
                                    <?= $this->Form->control("eddate[$z]",["type"=>"text","label"=>false, "style"=>"width:40px", 'value'=>$eddate]) ?>
                                </div>
                            </div>
                        </td>
                    <?php endif; ?>

                    <!-- 削除フラグ -->
                    <?php if(empty($getPlace["del"]) || $getPlace["del"] == 0): ?>
                        <?php if ( $z != 1): ?>
                            <td><?= $this->Form->control("del[$z]",array("type" => "checkbox","label" => false,"checked"=>false));?></td>
                        <?php endif; ?>
                    <?php else: ?>
                        <td><?= $this->Form->control("del[$z]",array("type" => "checkbox","label" => false,"value" => 1,"checked"=>true));?></td>
                    <?php endif; ?>
<!--
                    <td>
                        <?= $this->Form->postLink(__('[削除]'),
                        ['action'=>'delete',"?"=>["type"=>0], $getPlace["id"]],
                        ['confirm'=> __('本当に削除しますか？ #{0}?', $getPlace["id"])]) ?>
                    </td>
-->
                </tr>
                <?php $z++; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="mt30" style = "margin-left: 83.5%;">
        <?= $this->Form->button("変更",array('onClick' => 'return henkouCheck()')) ?>
        <!--<?= $this->Form->button(__("変更")) ?>-->
    </div>
<?= $this -> Form -> end(); ?>
<br>
<?= $this -> Form -> create(
    __("View"),
    ["type" => "post","url" => ["action" => "register"]]); ?>
    <?= $this->Form->control('type',['type'=>'hidden','value'=>1]) ?>
    <div class = "kensakusan mt30">
        <h4 class="midashih4 mt30 mb30">　新規施設登録</h4>
        <div class = "odakoku" style = "align-items:stretch; padding: 0 auto;">
            <div class = "ml10" style = "width:150px;">
                <?= $this->Form->control("name",["type" => "text","label" => "施設名"]); ?>
            </div>
            <div class = "ml10" style = "width:250px;">
                <?= $this->Form->control("address",["type" => "text","label" => "住所"]); ?>
            </div>
            <div class = "ml10" style = "width:45px;" text-align:center; padding: 0 auto;">
                <?= $this->Form->label("施設外"); ?>
                <div style="text-align:center;">
                    <?= $this->Form->control("sub",["type" => "checkbox","label" => false,"value" => 1]); ?>
                </div>
            </div>
            <div class = "ml10" style = "width:150px;">
                <?= $this->Form->control("company",["type" => "text","label" => "就労先企業名"]); ?>
            </div>
        </div>
        <div style="display:flex;">
            <div class = "ml10" style = "width:350px;">
                <?= $this->Form->control("mokuhyo",["type" => "text","label" => "目標・目的等"]); ?>
            </div>
            <div class = "ml10" style = "width:350px;">
                <?= $this->Form->control("wrkcontentsu",["type" => "text","label" => "受注作業内容"]); ?>
            </div>
        </div>
        <div style="display:flex;">
            <div class="mt20"><?= $this->Form->label("契約期間") ?></div>
            <div class="ml20">
                <?= $this->Form->label("年") ?>
                <div>
                    <?= $this->Form->select("styear",$years,['class'=>'shisetugai_year','empty'=>array('0'=>null),'value'=>'']) ?>
                </div>
            </div>
            <div class="ml5">
                <?= $this->Form->label("月") ?>  
                <div>  
                    <?= $this->Form->select("stmonth",$months,['class'=>'shisetugai_month','empty'=>array('0'=>null),'value'=>'']); ?>
                </div>
            </div>
            <div class="ml5">       
                <?= $this->Form->label("日") ?>    
                <?= $this->Form->control("stdate",["type"=>"text","label"=>false, "style"=>"width:40px"]) ?>
            </div>
            <div class="ml10" style="margin-top: 40px;">～</div>
            <div class="ml5">
                <?= $this->Form->label("年") ?>
                <div>
                    <?= $this->Form->select("edyear",$years,['class'=>'shisetugai_year','empty'=>array('0'=>null),'value'=>'']) ?>
                </div>
            </div>
            <div class="ml5">
                <?= $this->Form->label("月") ?>  
                <div>  
                    <?= $this->Form->select("edmonth",$months,['class'=>'shisetugai_month','empty'=>array('0'=>null),'value'=>'']); ?>
                </div>
            </div>
            <div class="ml5">       
                <?= $this->Form->label("日") ?>    
                <?= $this->Form->control("eddate",["type"=>"text","label"=>false, "style"=>"width:40px"]) ?>
            </div>
        </div>
        <div style = "margin:30px 0 20px 89%">
            <?= $this->Form->button("登録",array('onClick' => 'return shinkiname()')) ?>
        </div>
    </div>
<?= $this -> Form -> end(); ?>
<br>

<!-- 送迎車種データ新規登録・更新 -->
<?= $this -> Form -> create(
    __("View"),
    ["type" => "post","url" => ["action" => "register2"]]); ?>
    <?= $this->Form->control('type',['type'=>'hidden','value'=>0]) ?>
    <div class = "kensakusan mt30" id="sougeicar-section">
        <h4 class="midashih4 mt30 mb30">　送迎車種一覧
            <span style="font-size: 16px; margin-left:10px;">
                <a href="#" id="openModal">[新規登録]</a>
            </span>
        </h4>

        <div class="cartable-scroll-container">
            <table class="cartable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>車種No</th>
                        <th>車種名</th>
                        <th>削除</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($getsougeicar as $index => $sougeicar): ?>
                        <tr class="hvrow">
                            <td style="text-align: center;">
                                <?= h($sougeicar['id']) ?>
                                <?= $this->Form->hidden("getsougeicar.$index.id", ['value' => $sougeicar['id']]) ?>
                            </td>
                            <td>
                                <?= $this->Form->control("getsougeicar.$index.no", [
                                    'label' => false,
                                    'value' => $sougeicar['no'],
                                    'class' => 'form-control',
                                    'style' => 'width: 100%;'
                                ]) ?>
                            </td>
                            <td>
                                <?= $this->Form->control("getsougeicar.$index.name", [
                                    'label' => false,
                                    'value' => $sougeicar['name'],
                                    'class' => 'form-control',
                                    'style' => 'width: 100%;'
                                ]) ?>
                            </td>
                            <td style="text-align: center;">
                                <?= $this->Form->checkbox("getsougeicar.$index.del", [
                                    'hiddenField' => false,
                                    'checked' => ($sougeicar['del'] == 1)
                                ]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div style = "margin:30px 0 20px 89%">
            <button type="submit" class="btn btn-success" onclick="return validateSougeicarForm()" <?= empty($getsougeicar) ? 'disabled' : '' ?>>登録</button>
        </div>
    </div>
<?= $this -> Form -> end(); ?>

<!-- モーダルのHTML -->
<div id="addCarModal" class="modal-add-car">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h4 class="midashih4 mt30 mb30">　送迎車種データ 新規登録</h4>
        <?= $this->Form->create(null, ['id' => 'addCarForm', 'url' => ['action' => 'sougei_register']]) ?>
            <label for="carNo" style="color:#000;">車種No:</label>
            <input type="text" id="carNo" name="carNo" required>
            <br>
            <label for="carName" style="color:#000;">車種名 :</label>
            <input type="text" id="carName" name="carName" required>
            <br>
            <button type="submit" class="btn btn-success">登録</button>
        <?= $this->Form->end() ?>
    </div>
</div>

<!-- jQueryによるモーダル開閉処理 -->
<script>
    $(document).ready(function () {
        $('#openModal').click(function (event) {
            event.preventDefault(); // デフォルトのリンク動作を防ぐ
            $('#addCarModal').fadeIn();
        });
        $('.close').click(function () {
            $('#addCarModal').fadeOut();
        });

        // フォームのバリデーション
        $('#addCarForm').submit(function(event) {
            event.preventDefault();
            
            // 入力値を取得して前後の空白を削除
            var carNo = $('#carNo').val().trim();  // 前後の空白を削除
            var carName = $('#carName').val().trim();  // 前後の空白を削除
            
            // 値を更新
            $('#carNo').val(carNo);
            $('#carName').val(carName);
            
            // 車種Noのチェック
            if (!carNo) {
                alert('車種Noを入力してください。');
                $('#carNo').focus();
                return false;
            }
            
            // 車種名のチェック
            if (!carName) {
                alert('車種名を入力してください。');
                $('#carName').focus();
                return false;
            }
            
            // バリデーション成功時はフォームを送信
            this.submit();
        });
    });

    function validateSougeicarForm() {
        // 送迎車種データの行を取得
        const rows = document.querySelectorAll('.cartable tr.hvrow');
        
        for (let row of rows) {
            // 車種Noと車種名の入力フィールドを取得
            const noInput = row.querySelector('input[name$="[no]"]');
            const nameInput = row.querySelector('input[name$="[name]"]');
            
            // 削除チェックボックスを取得
            const delCheckbox = row.querySelector('input[type="checkbox"]');
            
            // 削除されていない行のみチェック
            if (!delCheckbox.checked) {
                // 前後の空白を削除して値を更新
                let noValue = noInput.value.trim();
                let nameValue = nameInput.value.trim();
                
                // 値を更新
                noInput.value = noValue;
                nameInput.value = nameValue;
                
                // 車種Noのチェック
                if (!noValue) {
                    alert('車種Noを入力してください。');
                    noInput.focus();
                    return false;
                }
                
                // 車種名のチェック
                if (!nameValue) {
                    alert('車種名を入力してください。');
                    nameInput.focus();
                    return false;
                }
            }
        }
        
        return true;
    }
</script>

<!-- スタイル追加 -->
<style>
.modal-add-car {
    display: none; /* 初期状態では非表示 */
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}
.modal-content {
    background-color: white;
    margin: 10% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 30%;
    border-radius: 10px;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
    position: relative;
}
.close {
    color: #aaa;
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}
</style>
