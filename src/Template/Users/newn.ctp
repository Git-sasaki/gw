<script type="text/javascript">
    function OnChangeadminfrag() {
        obj = document.getElementsByName("adminfrag");
        obj2 = document.getElementsByName("wrkCase");
        obj3 = document.getElementById("medichk");
        obj4 = document.getElementById("mailaddress");
        obj5 = document.getElementById("kessai");
        obj6 = document.getElementsByName("oufuku_place_div");
        obj7 = document.getElementById("mail_kessai_div");
        obj8 = document.getElementById("wrkCase_div");
        obj9 = document.getElementById("sapporo_div");
        obj10 = document.getElementById("service_div");
 
        if (obj[0].value == 0) {
            obj2[0].disabled = null;
            obj2[0].selected = true;
            obj2[0].value = 0;
            obj3.disabled = false;
            obj4.disabled = true;
            obj5.disabled = true;
            obj6[0].style.display = '';
            obj7.style.display = 'none';
            obj8.style.display = '';
            obj9.style.display = '';
            obj10.style.display = '';
        } else {
            obj2[0].value = "";
            obj2[0].disabled = "disabled";
            obj3.disabled = true;
            obj4.disabled = null;
            obj5.disabled = null;
            obj6[0].style.display = 'none';
            obj7.style.display = '';
            obj8.style.display = 'none';
            obj9.style.display = 'none';
            obj10.style.display = 'none';
        }
    }

    // 重複メッセージ表示用の共通関数
    function showDuplicateMessage() {
        var usernameField = document.getElementsByName("user")[0];
        var duplicateMessage = document.getElementById("duplicate-message");
        if (!duplicateMessage) {
            duplicateMessage = document.createElement("div");
            duplicateMessage.id = "duplicate-message";
            duplicateMessage.style.color = "red";
            duplicateMessage.style.fontSize = "12px";
            duplicateMessage.style.marginTop = "5px";
            usernameField.parentNode.appendChild(duplicateMessage);
        }
        duplicateMessage.textContent = "このユーザー名は既に使用されています。";
        duplicateMessage.style.display = "block";
    }

    // ユーザー名重複チェック機能
    function checkUsernameDuplicate() {
        var username = document.getElementsByName("user")[0].value;
        var duplicateMessage = document.getElementById("duplicate-message");
        var submitButton = document.querySelector('button[type="submit"]');
        
        if (username.trim() === "") {
            if (duplicateMessage) {
                duplicateMessage.style.display = "none";
            }
            // ユーザー名が空の場合は送信ボタンを有効にする
            if (submitButton) {
                submitButton.disabled = false;
            }
            return;
        }

        if (typeof $ === 'undefined') {
            console.error('jQuery is not available');
            return;
        }

        // CSRFトークンを取得（Calendarsの実装を参考）
        var csrf = $('input[name=_csrfToken]').val();

        // AJAXでユーザー名の重複チェック
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "<?= $this->Url->build(['controller' => 'Users', 'action' => 'checkUsernameDuplicate'], true) ?>", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhr.setRequestHeader("X-CSRF-Token", csrf);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.duplicate) {
                        showDuplicateMessage();
                        document.getElementsByName("user")[0].focus();
                        if (submitButton) {
                            submitButton.disabled = true;
                            submitButton.style.opacity = "0.5";
                            submitButton.style.cursor = "not-allowed";
                        }
                    } else {
                        // 重複メッセージを非表示
                        if (duplicateMessage) {
                            duplicateMessage.style.display = "none";
                        }
                        
                        // 送信ボタンを有効にする
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.style.opacity = "1";
                            submitButton.style.cursor = "pointer";
                        }
                    }
                } else {
                    console.error('AJAX request failed with status:', xhr.status);
                }
            }
        };
        
        // ユーザー名のみを送信
        xhr.send("username=" + encodeURIComponent(username));
    }

    // ユーザー名入力フィールドにイベントリスナーを追加
    document.addEventListener('DOMContentLoaded', function() {
        var usernameField = document.getElementsByName("user")[0];
        if (usernameField) {
            usernameField.addEventListener('blur', function() {
                var duplicateMessage = document.getElementById("duplicate-message");
                // すでにメッセージが表示されていれば何もしない
                if (duplicateMessage && duplicateMessage.style.display === "block") {
                    return;
                }
                checkUsernameDuplicate();
            });
            usernameField.addEventListener('input', function() {
                var duplicateMessage = document.getElementById("duplicate-message");
                if (duplicateMessage) {
                    duplicateMessage.style.display = "none";
                }
                
                // 入力中は送信ボタンを有効にする（チェック中は送信可能にする）
                var submitButton = document.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.style.opacity = "1";
                    submitButton.style.cursor = "pointer";
                }
            });
        }
    });

    function SendCheck() {
        /*メールアドレスのパターン 正規表現*/
        var pattern = /^[A-Za-z0-9]{1}[A-Za-z0-9_.-]*@{1}[A-Za-z0-9_.-]+\.[A-Za-z0-9]+$/;

        // 区分が職員（adminfrag==1）の場合はメールアドレス必須
        var adminfrag = document.getElementsByName("adminfrag")[0].value;
        var mailaddress = document.getElementsByName("mailaddress")[0].value;
        if (adminfrag == "1" && mailaddress.trim() === "") {
            alert("メールアドレスは必須です（職員の場合）");
            document.getElementsByName("mailaddress")[0].focus();
            return false;
        }

        // ユーザー名重複チェック
        var username = document.getElementsByName("user")[0].value;
        if (username.trim() === "") {
            alert("ユーザー名が入力されていません。");
            return false;
        }

        // 送信前に同期AJAXで重複チェック
        if (typeof $ === 'undefined') {
            console.error('jQuery is not available');
            return false;
        }

        // CSRFトークンを取得
        var csrf = $('input[name=_csrfToken]').val();
        
        // 同期AJAXでユーザー名の重複チェック
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "<?= $this->Url->build(['controller' => 'Users', 'action' => 'checkUsernameDuplicate'], true) ?>", false); // 同期実行
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhr.setRequestHeader("X-CSRF-Token", csrf);
        xhr.send("username=" + encodeURIComponent(username));
        
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.duplicate) {
                showDuplicateMessage();
                document.getElementsByName("user")[0].focus();
                var submitButton = document.querySelector('button[type="submit"]');
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.style.opacity = "0.5";
                    submitButton.style.cursor = "not-allowed";
                }
                return false;
            }
        } else {
            console.error('AJAX request failed with status:', xhr.status);
            return false;
        }

        // 重複がない場合は他のバリデーションを実行
        return validateOtherFields();
    }

    // 他のバリデーション処理を別関数に分離
    function validateOtherFields() {
        // より厳密なメールアドレス正規表現
        var pattern = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;

        //決済資格者はメールアドレス必須
        if ( document.getElementById("kessai").checked) {
            if( document.getElementsByName("mailaddress")[0].value == "") {
                alert("メールアドレスが入力されていません。");
                return false;
            }
            if (!pattern.test(document.getElementsByName("mailaddress")[0].value)) {
                alert("メールアドレスの形式が異常です。");
                return false;
            }         
        } else {
            if( document.getElementsByName("mailaddress")[0].value != "") {
                if (!pattern.test(document.getElementsByName("mailaddress")[0].value)) {
                    alert("メールアドレスの形式が異常です。");
                    return false;
                }         
            }
        }

        //日付の妥当性チェック（入社日付）
        if ( typeof document.getElementsByName("jyear")[0] != 'undefined') {
            isExist1 = !(document.getElementsByName("jyear")[0].value == 0) && 
                       !(document.getElementsByName("jmonth")[0].value == 0) &&
                       !(document.getElementsByName("jdate")[0].value == 0);  
            isExist2 = !(document.getElementsByName("jyear")[0].value == 0) || 
                       !(document.getElementsByName("jmonth")[0].value == 0) ||
                       !(document.getElementsByName("jdate")[0].value == 0);  
            
           if ( isExist1) {
                val = document.getElementsByName("jyear")[0].value + "-" +
                        document.getElementsByName("jmonth")[0].value + "-" +
                        document.getElementsByName("jdate")[0].value;
                date = new Date(val);
                if (isNaN(date.getDate()) || (date.getDate() != document.getElementsByName("jdate")[0].value)) {
                    alert("入力された日付が妥当性に欠けます。（入社日）");
                    return false;
                }                    
            } else {
                if ( isExist2) {
                    alert("日付入力が完全ではありません。（入社日）");
                    return false;
                }
            }
        }

        //利用者の場合サービス受給者証のチェック
        DateCheck1 = false;
        DateCheck2 = false;
        obj = document.getElementsByName("adminfrag");
        if ( document.getElementsByName("adminfrag")[0].value == 0) {
            //受給者番号
            isNo = !document.getElementsByName("sjnumber")[0].value;

            //日付の妥当性チェック（開始日付）
            isExist1 = !(document.getElementsByName("sjhyear")[0].value == 0) && 
                    !(document.getElementsByName("sjhmonth")[0].value == 0) &&
                    !(document.getElementsByName("sjhdate")[0].value == 0);  
            isExist2 = !(document.getElementsByName("sjhyear")[0].value == 0) || 
                    !(document.getElementsByName("sjhmonth")[0].value == 0) ||
                    !(document.getElementsByName("sjhdate")[0].value == 0);  

            if ( isExist1) {
                    val = document.getElementsByName("sjhyear")[0].value + "-" +
                            document.getElementsByName("sjhmonth")[0].value + "-" +
                            document.getElementsByName("sjhdate")[0].value;
                    date1 = new Date(val);
                    if (isNaN(date1.getDate()) || (date1.getDate() != document.getElementsByName("sjhdate")[0].value)) {
                        alert("入力された日付が妥当性に欠けます。（サービス受給者証 開始日付）");
                        return false;
                    } else {
                        DateCheck1 = true;
                    }                    
            } else {
                if ( isExist2) {
                    alert("日付入力が完全ではありません。（サービス受給者証 開始日付）");
                    return false;
                }
            }

            //日付の妥当性チェック（終了日付）
            isExist1 = !(document.getElementsByName("sjoyear")[0].value == 0) && 
                    !(document.getElementsByName("sjomonth")[0].value == 0) &&
                    !(document.getElementsByName("sjodate")[0].value == 0);  
            isExist2 = !(document.getElementsByName("sjoyear")[0].value == 0) || 
                    !(document.getElementsByName("sjomonth")[0].value == 0) ||
                    !(document.getElementsByName("sjodate")[0].value == 0);  
            
            if ( isExist1) {
                    val = document.getElementsByName("sjoyear")[0].value + "-" +
                            document.getElementsByName("sjomonth")[0].value + "-" +
                            document.getElementsByName("sjodate")[0].value;
                    date2 = new Date(val);
                    if (isNaN(date2.getDate()) || (date2.getDate() != document.getElementsByName("sjodate")[0].value)) {
                        alert("入力された日付が妥当性に欠けます。（サービス受給者証 終了日付）");
                        return false;
                    } else {
                        DateCheck2 = true;
                    }                             
            } else {
                if ( isExist2) {
                    alert("日付入力が完全ではありません。（サービス受給者証 終了日付）");
                    return false;
                }
            }

            //日付チェック
            if ( DateCheck1 && DateCheck2) {
                if ( date1 >= date2) {
                    alert("終了日付が開始日付より前になっています。（サービス受給者証 開始終了日付）");
                    return false;
                } else {
                    //受給者番号チェック
                    if ((DateCheck1 && DateCheck2) && isNo) {
                        alert("サービス受給者証番号が入力されていません");
                        return false;
                    } else {
                        return true;
                    }
                }    
            } 

            //未入力
            if (isNo && (!DateCheck1 && !DateCheck2)) {
                return true;
            } else {
                alert("サービス受給者証 開始終了日付が入力されていません。");
                return false;
            }
        }

        return true;
    }

    // 区分変更時にrequired属性を付与/除去

    document.addEventListener('DOMContentLoaded', function() {
        var adminfragSelect = document.getElementsByName("adminfrag")[0];
        var mailInput = document.getElementsByName("mailaddress")[0];
        if (adminfragSelect && mailInput) {
            adminfragSelect.addEventListener('change', function() {
                if (this.value == "1") {
                    mailInput.required = true;
                } else {
                    mailInput.required = false;
                }
            });
        }
    });
</script>

<?php $this->assign('title', '新規ユーザー登録'); ?>

<div class = "main1">
<?= $this->Form->create($user) ?>
    <h4 class="midashih4 mt30">ユーザー追加</h4>
    <div class = "odakoku">
        <div class = "w200">
            <?= $this->Form->control('lastname', ['label' => '姓', 'required']); ?>
        </div>
        <div class = "w200">
            <?= $this->Form->control('firstname', ['label' => '名', 'required']); ?>
        </div>
    </div>
    <div class = "odakoku">
        <div class = "w200">
            <?= $this->Form->control('user', ['label' => 'ユーザー名']); ?>
        </div>
        <div class = "w200">
            <?= $this->Form->control('password', ['label' => 'パスワード','value'=>'redmoon']); ?>
        </div>
        <div class = "w80" style="margin-left:10px";> 
            <?php $kubun = array("利用者", '職員'); ?>
            <?= $this->form->input( 'adminfrag',array('label'=>'区分','type' => 'select', 'options' => $kubun, 'onchange'=>'OnChangeadminfrag()')); ?>
        </div>
        <div class="odakoku" id="wrk_sapporo_div" <?= ($user["adminfrag"] == 1) ? 'style="display: none;"' : '' ?>>
            <div class="w80" id="wrkCase_div" style="margin-left:10px;<?= ($user["adminfrag"] == 1) ? 'display:none;' : '' ?>">
                <?php $wrkCase = array("Ａ型", 'Ｂ型'); ?>
                <?= $this->form->input('wrkCase', array('label' => '就労タイプ', 'type' => 'select', 'options' => $wrkCase)); ?>
            </div>
            <div class="dakoku" id="sapporo_div" style="<?= ($user["adminfrag"] == 1) ? 'display:none;' : '' ?>">
                <label for="medichk">札幌市在住</label>
                <?= $this->Form->checkbox("sapporo", ["id" => "medichk", "value" => 1, "checked" => true, "label" => false, "style" => "margin-top: 5px;"]) ?>
            </div>
        </div>
        <div name="oufuku_place_div" class = "w130 ml10" <?= ($user["adminfrag"] == 1) ? 'style="display: none;"' : '' ?>> 
            <label>送迎場所</label>
            <datalist id = "placelist">
                    <option value = "地下鉄 琴似駅">
                    <option value = "JR琴似駅">
            </datalist>
            <?php if((empty($user["oufuku_place"]))): ?>
                <?= $this->Form->control("oufuku_place",[
                    "type" => "text",
                    "label" => false,
                    "list" => "placelist",
                ]); ?>
            <?php else: ?>
                <?= $this->Form->control("oufuku_place",[
                    "type" => "text",
                    "label" => false,
                    "list" => "placelist",
                    "value"=>$user["oufuku_place"],
                ]); ?>
            <?php endif; ?>
        </div>
    </div>
    <div class = "odakoku" id="mail_kessai_div" <?= ($user["adminfrag"] == 0) ? 'style="display: none;"' : '' ?>>
        <div class = "w200">
            <?= $this->Form->control('mailaddress', ['label' => 'メールアドレス','disabled' => true]); ?>
        </div>
        <div class = "dakoku" style="text-align:center; padding: 0 auto; margin-left:20px">
            <label style= "margin-top: 10px";>決済資格</label>
            <?= $this->Form->control("kessai",["type" => "checkbox","id"=>"kessai","label"=>false,"value" => 1,"checked"=>false,'disabled' => true]); ?>
        </div>
    </div>
    
    <h4 class="midashih4 mt30 mb20">入社日を登録</h4>
    <div class = "odakoku ml10">
        <div class = "sdakoku" style = "width: 150px;">
            <?= $this->Form->label("年") ?>
            <div class = "mt5">
                <?= $this->Form->select("jyear",$years,['empty'=>array('0'=>null),'value'=>date('Y')]); ?>
            </div>
        </div>
        <div class = "sdakoku">
            <?= $this->Form->label("月") ?>    
            <div class = "mt5">            
                <?= $this->Form->select("jmonth",$months,['empty'=>array('0'=>null),'value'=>date('m')]); ?>
            </div>
        </div>
        <div class = "sdakoku">       
            <?= $this->Form->label("日") ?>    
            <div class = "mt5">            
                <?= $this->Form->control("jdate",["maxlength"=>"2","type"=>"text","label"=>false,'value'=>date('d'), 'required']); ?>
            </div>
        </div>
    </div>

    <div id="service_div" <?= ($user["adminfrag"] == 1) ? 'style="display: none;"' : '' ?>>
        <h4 class="midashih4 mt30 mb20">サービス受給者証）</h4>
            <div class = "odakoku ml10">
                <div class = "mt5">            
                    <?= $this->Form->control("sjnumber",["pattern"=>"^[0-9]+$","maxlength"=>"10","minlength"=>"10","type"=>"text","label"=>"受給者証番号（数字10桁）"]); ?>
                </div>
            </div>
            <div class = "odakoku ml10">
                <div class = "sdakoku" style = "width: 150px;">
                    <?= $this->Form->label("年") ?>
                    <div class = "mt5">
                    <?= $this->Form->select("sjhyear",$years,['empty'=>array('0'=>null)]); ?>
                    </div>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->label("月") ?>    
                    <div class = "mt5">            
                    <?= $this->Form->select("sjhmonth",$months,['empty'=>array('0'=>null)]); ?>
                    </div>
                </div>
                <div class = "sdakoku">       
                    <?= $this->Form->label("日") ?>    
                    <div class = "mt5">            
                    <?= $this->Form->control("sjhdate",["pattern"=>"^[0-9]+$","maxlength"=>"2","type"=>"text","label"=>false]); ?>
                    </div>
                </div>
            <p style = "padding: 42px 10px 0">　～　</p>
                <div class = "sdakoku ml10" style = "width: 150px;">
                    <?= $this->Form->label("年") ?>
                    <div class = "mt5">
                    <?= $this->Form->select("sjoyear",$years,['empty'=>array('0'=>null)]); ?>
                    </div>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->label("月") ?>  
                    <div class = "mt5">  
                    <?= $this->Form->select("sjomonth",$months,['empty'=>array('0'=>null)]); ?>
                    </div>
                </div>
                <div class = "sdakoku">       
                    <?= $this->Form->label("日") ?>    
                    <div class = "mt5">            
                    <?= $this->Form->control("sjodate",["pattern"=>"^[0-9]+$","maxlength"=>"2","type"=>"text","label"=>false]); ?>
                    </div>
                </div>
            </div>
    </div>
    <div class = "mt30 ml10">
        <!--<?= $this->Form->button(__('送信')) ?>-->
        <?= $this->Form->button("送信",array('onClick' => 'return SendCheck()')) ?>
    </div>
    <?= $this->Form->end() ?>
</div>
<br>