<script type="text/javascript">
    $(document).ready(function() {
        // 日付妥当性チェック関数
        function validateDate(year, month, date) {
            // 数値チェック
            if (!year || !month || !date) {
                return '年、月、日をすべて選択してください';
            }
            
            // 実際の日付として存在するかチェック
            var inputDate = new Date(year, month - 1, date);
            if (inputDate.getFullYear() != year || 
                inputDate.getMonth() != month - 1 || 
                inputDate.getDate() != date) {
                return '存在しない日付です';
            }
            
            // 未来の日付チェック
            var today = new Date();
            today.setHours(0, 0, 0, 0);
            if (inputDate > today) {
                return '未来の日付は登録できません';
            }
            
            return true;
        }
        
        // 登録タイプを取得する関数
        function getRegistrationType(form) {
            var type = form.find('input[name="type"]').val();
            switch(type) {
                case '0':
                    return '在宅就労記録登録';
                case '1':
                    return '週間記録登録';
                case '2':
                    return '在宅就労記録一覧';
                default:
                    return '登録';
            }
        }
        
        // エラーがある日付フィールドにフォーカスを移す関数
        function focusOnDateField(form, errorField) {
            var fieldMap = {
                'year': 'select[name="year"]',
                'month': 'select[name="month"]',
                'date': 'select[name="date"]'
            };
            
            if (fieldMap[errorField]) {
                var targetField = form.find(fieldMap[errorField]);
                if (targetField.length > 0) {
                    targetField.focus();
                    return true;
                }
            }
            return false;
        }
        
        // 日付フィールドのエラーを特定する関数
        function identifyDateError(year, month, date) {
            if (!year) return 'year';
            if (!month) return 'month';
            if (!date) return 'date';
            
            // 実際の日付として存在するかチェック
            var inputDate = new Date(year, month - 1, date);
            if (inputDate.getFullYear() != year || 
                inputDate.getMonth() != month - 1 || 
                inputDate.getDate() != date) {
                return 'date'; // 日付が無効な場合は日フィールドにフォーカス
            }
            
            return null; // エラーなし
        }
        
        // 日報データの存在確認を行うAjax関数
        function checkReportData(year, month, date, userId, callback) {
            var jsondata = {
                "year": year,
                "month": month,
                "date": date,
                "user_id": userId
            };

            var csrf = $('input[name=_csrfToken]').val();
            
            $.ajax({
                'url'	:"<?php echo $this->Url->build(["controller" => "Remotes","action" => "checkRemoteWorkData","ajax" => true], true); ?>",
                'type': 'POST',
                'beforeSend': function(xhr){
                    xhr.setRequestHeader('X-CSRF-Token', csrf);
                },
                'async': false,
                'data': jsondata,
                'success': function(result){
                    if (callback) {
                        callback(result);
                    }
                },
                'error': function(status){
                    if (callback) {
                        callback({
                            success: false,
                            message: 'サーバーエラーが発生しました'
                        });
                    }
                }
            });
        }
        
        
        // フォーム送信時のバリデーション
        $('form').on('submit', function(e) {
            var form = $(this);
            var type = form.find('input[name="type"]').val();
            var year = form.find('select[name="year"]').val();
            var month = form.find('select[name="month"]').val();
            var date = form.find('select[name="date"]').val();
            var userId = form.find('select[name="user_id"]').val();
            
            // 在宅就労記録一覧（type=2）の場合は日付チェックをスキップ
            if (type === '2') {
                return true;
            }
            
            // 日付が選択されている場合のみチェック
            if (year && month && date && userId) {
                var validationResult = validateDate(year, month, date);
                if (validationResult !== true) {
                    var registrationType = getRegistrationType(form);
                    var errorMessage = registrationType + '：' + validationResult;
                    alert(errorMessage);
                    
                    // エラーがある日付フィールドにフォーカス
                    var errorField = identifyDateError(year, month, date);
                    if (errorField) {
                        focusOnDateField(form, errorField);
                    }
                    
                    e.preventDefault();
                    return false;
                }
                
                // 日報データの存在確認
                e.preventDefault(); // 一旦送信を停止
                
                checkReportData(year, month, date, userId, function(response) {
                    var data = response.data;
                    if (data.success) {
                        if (!data.hasReport) {
                            // 日報データがない場合
                            var registrationType = getRegistrationType(form);
                            var errorMessage = registrationType + '：指定した日付の出勤簿データが存在しません。先に出勤簿を登録してください。';
                            alert(errorMessage);
                        } else if (data.isHoliday) {
                            // 公休として登録されている場合
                            var registrationType = getRegistrationType(form);
                            var errorMessage = registrationType + '：指定された日付は公休になっています。';
                            alert(errorMessage);
                        } else if (data.isAbsent) {
                            // 欠勤として登録されている場合
                            var registrationType = getRegistrationType(form);
                            var errorMessage = registrationType + '：指定された日付は欠勤になっています。';
                            alert(errorMessage);
                        } else if (!data.isRemote && type !== '1') {
                            // 日報データはあるが在宅勤務として登録されていない場合（週間記録登録以外）
                            var registrationType = getRegistrationType(form);
                            var errorMessage = registrationType + '：指定された日付は出勤簿で在宅勤務になっていません。';
                            alert(errorMessage);
                        } else {
                            // 日報データも在宅勤務フラグもある場合は通常の送信処理を実行
                            // または週間記録登録の場合は在宅勤務でなくてもOK
                            form.off('submit').submit();
                        }
                    } else {
                        // サーバーエラーの場合
                        alert('データの確認中にエラーが発生しました：' + data.message);
                    }
                });
                
                return false;
            }
        });
    });
</script>

<div class = "main3">
    <h4 class = "midashih4 mt30">　在宅勤務管理</h4>

    <div class = "odakoku mt30">
        <div class = "shutsuryoku">
            <h4 class = "exportdeka">　在宅就労記録登録</h4>
            <?= $this->Form->create(__("View"),["type"=>"post","url"=>["action"=>"getquery0"]]); ?>
            <?= $this->Form->control('type',['type'=>'hidden','value'=>0]) ?>
            <div class = "odakoku mlv25">
                <div class = "sdakoku">
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>$year], $years) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>$month], $months) ?> 
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('date',['type'=>'select','label'=>"日",'value'=>$date,['empty'=>null]], $dates) ?> 
                </div>
            </div>
                <div class = "staffbox mlv25">
                    <?= $this->Form->label("ユーザー")?>
                    <?= $this->Form->select('user_id',$remotes,array('id'=>'user_id','type'=> 'select'));?>
                </div>
            <div class="ml10_button mt30 mlv25">
                <?= $this->Form->button(__("表示")) ?>
            </div>
            <?= $this->Form->end(); ?>
            <br>
        </div>
        <div class = "shutsuryoku" style = "margin-left: 4vw;">
            <h4 class = "exportdeka">　週間記録登録</h4>
            <?= $this->Form->create(__("View"),["type"=>"post","url"=>["action"=>"getquery0"]]); ?>
            <?= $this->Form->control('type',['type'=>'hidden','value'=>1]) ?>
            <div class = "odakoku mlv25">
                <div class = "sdakoku">
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>$year], $years) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>$month], $months) ?> 
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('date',['type'=>'select','label'=>"日",'value'=>$date,['empty'=>null]], $dates) ?> 
                </div>
            </div>
            <div class = "staffbox mlv25">
                <?= $this->Form->label("ユーザー")?>
                <?= $this->Form->select('user_id',$remotes,array('id'=>'user_id','type'=> 'select'));?>
            </div>
            <div class="ml10_button mt30 mlv25">
                <?= $this->Form->button(__("表示")) ?>
            </div>
            <?= $this->Form->end(); ?>
            <br>
        </div>
        <div class = "shutsuryoku" style = "margin-left: 4vw;">
            <h4 class = "exportdeka">　在宅就労記録一覧</h4>
            <?= $this->Form->create(__("View"),["type"=>"post","url"=>["action"=>"getquery0"]]); ?>
            <?= $this->Form->control('type',['type'=>'hidden','value'=>2]) ?>
            <div class = "odakoku mlv25">
                <div class = "sdakoku">
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>$year], $years) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>$month], $months) ?> 
                </div>
            </div>
            <div class = "staffbox mlv25">
                <?= $this->Form->label("ユーザー")?>
                <?= $this->Form->select('user_id',$remotes,array('id'=>'user_id','type'=> 'select'));?>
            </div>
            <div class="ml10_button mt30 mlv25">
                <?= $this->Form->button(__("表示")) ?>
            </div>
            <?= $this->Form->end(); ?>
            <br>
        </div>
    </div>
</div>