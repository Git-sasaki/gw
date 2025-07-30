<script type="text/javascript">
    //登録ボタン押下時の昼食・送迎のチェック（スタッフ）
    function OnSend() {
        //喫食データセレクトボックス有無確認
        const hasSelect = document.querySelector('select[name="kissyoku"]') !== null;

        //無指定か確認
        if (hasSelect) {
            const select = document.querySelector('select[name="kissyoku"]');
            if ( select.value == 0) {
                alert("喫食データを指定して下さい。");
                return false;
            }
        }

        return true;
    }
</script>

<?php $this->assign('title', '作業日報詳細'); ?>

<div class = "odakoku">
<?php if($this->request-> getSession()->read('Auth.User.adminfrag') != 1): ?>
    <div class = "sidemenu mvh120 antisp">
<?php else: ?>
    <div class = "sidemenu mvh200">
<?php endif; ?>
    <?= $this -> Form -> create(__("View"),["type" => "post","url" => ["action" => "getquery0"]]); ?>
        <h4 class = "sideh4 ml10 pt15">年月日選択</h4> 
            <div class = "odakoku ml10"> 
                <div class = "sdakoku">
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>$year], $years) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>$month], $months) ?> 
                </div>
            </div>
                <div class = "sdakoku ml10">
                    <?= $this->Form->control('date',['type'=>'select','label'=>"日",'value'=>$date,['empty'=>null]], $dates) ?> 
                </div>
            <?php if($this->request-> getSession()->read('Auth.User.adminfrag') == 1): ?>
        <h4 class = "sideh4 ml10 pt15">ユーザー</h4>  
                <div class = "staffbox mt30 ml10">
                    <?= $this->Form->select('id',$staffs,array('id'=>'staff_id','label' => false,'value'=>$user_id,'empty'=>false));?>
                </div>
            <?php endif; ?>
        <div class="ml10_button mt30 ml10">
            <?= $this->Form->button(__("表示")) ?>
        </div>
        <?= $this -> Form -> end(); ?>
    </div>

    <div class = "main2 pt15">
    <div class = "mlv25">
        <?php if($this->request-> getSession()->read('Auth.User.adminfrag') == 1): ?>
            <h4 class="midashih4 mt10">
                <?= $getname . "さん　".$year."年".$month."月".$date."日(".$weekList[date("w",$postdate2)].")"?>
            </h4>
        <?php else: ?>
            <h4 class="midashih4 mt10">
                <?= $year."年".$month."月".$date."日(".$weekList[date("w",$postdate2)].")　作業日報"?>
            </h4>
        <?php endif; ?>
    </div>

    <div class = "spmenu">
        <?= $this -> Form -> create(__("View"),["type" => "post","url" => ["action" => "getquery0"]]); ?>
        <h4 class = "sideh4 ml10 pt15">　年月日選択</h4>  
            <div class = "odakoku mlv25" style = "align-items: center;">
                <div class = "sdakoku">
                    <?= $this->Form->control('year',['type'=>'select','label'=>false,'value'=>$year], $years) ?>
                </div>
                <div>年</div>
                <div class = "sdakoku ml10">
                    <?= $this->Form->control('month',['type'=>'select','label'=>false,'value'=>$month], $months) ?> 
                </div>
                <div>月</div>
                <div class = "sdakoku ml10">
                    <?= $this->Form->control('date',['type'=>'select','label'=>false,'value'=>$date,['empty'=>null]], $dates) ?> 
                </div>
                <div>日</div>
            </div>
        <div class="spmenu_button mt10 ml10" style = "margin-bottom: 15px;">
            <?= $this->Form->button(__("表示")) ?>
        </div>
        <?= $this->Form->end(); ?>
    </div>
    
    <div class = "mlsp">
    <?= $this->Form->create(null, ['type' => 'post',
                                   'url' => ['controller' => 'Reports', 'action' => 'registern',
                                   "?" => ["id" => $user_id, "year" => $year, "month" => $month, "date" => $date]]]) ?>
    <div class = "odakoku ml10">
        <div class = "dakoku">
            <?php 
                echo $this->Form->label("出勤時間");
                if(!empty($rep["intime"])) {
                    echo $this->Form->text("intime",["type" => "time","value" => $rep["intime"]->i18nFormat("HH:mm")]);
                } elseif (!empty($attendances["intime"])) {
                    echo $this->Form->text("intime",["type" => "time","value" => $attendances["intime"]->i18nFormat("HH:mm")]);
                } elseif (!empty($defaults["dintime"])) {
                    echo $this->Form->text("intime",["type" => "time","value" => $defaults["dintime"]->i18nFormat("HH:mm")]);
                } else {
                    echo $this->Form->text("intime",["type" => "time"]);
                } ?>
            </div>
            <div class = "dakoku">
            <?php 
                echo $this->Form->label("退勤時間");
                if(!empty($rep["outtime"])) {
                    echo $this->Form->text("outtime",["type" => "time","value" => $rep["outtime"]->i18nFormat("HH:mm")]);
                } elseif (!empty($attendances["outtime"])) {
                    echo $this->Form->text("outtime",["type" => "time","value" => $attendances["outtime"]->i18nFormat("HH:mm")]);
                } elseif (!empty($defaults["douttime"])) {
                    echo $this->Form->text("outtime",["type" => "time","value" => $defaults["douttime"]->i18nFormat("HH:mm")]);
                } else {
                    echo $this->Form->text("outtime",["type" => "time"]);
                } ?>
            </div>
            <div class = "dakoku">
            <?php 
                echo $this->Form->label("休憩時間");
                if(!empty($rep["resttime"])) {
                    echo $this->Form->text("resttime",["type" => "time","value" => $rep["resttime"]->i18nFormat("HH:mm")]);
                } elseif (!empty($attendances["resttime"])) {
                    echo $this->Form->text("resttime",["type" => "time","value" => $attendances["resttime"]->i18nFormat("HH:mm")]);
                } elseif (!empty($defaults["dresttime"])) {
                    echo $this->Form->text("resttime",["type" => "time","value" => $defaults["dresttime"]->i18nFormat("HH:mm")]);
                } else {
                    echo $this->Form->text("resttime",["type" => "time"]);
                } ?>
            </div>

            <?php
                if ( $attendances["meshi"] == 1) {
                    echo '<div class = "dakoku">';
                        echo $this->Form->label("喫食データ");
                        $kisssyokulist = ["", "完食", "1/2", "1/3", "1/4"];
                        $rep['kissyoku'] = (empty($rep['kissyoku'])) ? 0 : $rep['kissyoku'];
                        echo $this->form->input( "kissyoku",array('style' => 'margin-top:0px;width:70px;','label'=>false,'type' => 'select', 'options' => $kisssyokulist, 'value' => $rep['kissyoku'])); 
                    echo '</div>';
                }
            ?>
        </div>

        <div class = "ml10">
            <?= $this->Form->label("業務内容"); ?>
            <?php if(empty($rep["content"])) {
                echo $this->Form->control('content', ['type' => 'textarea', 'label' => false]);  
            } else {
                echo $this->Form->control('content', ['type' => 'textarea', 'label' => false, 
                                                        'value' => $rep["content"]]);
            }?>
        </div>

        <div class = "itenai">
            <div class = "odakoku" style = "align-items: center;">
                <div>項目：</div>
                <div class = "yayahiro">
                    <?php if(empty($red[0]["item"])) {
                        echo $this->Form->control('item0', ['type' => 'text', 'label' => false]);  
                    } else {
                        echo $this->Form->control('item0', ['type' => 'text', 'label' => false,
                                                            'value' => $red[0]["item"]]);
                    }?>
                </div>
            </div>
            <div class = "odakoku" style = "align-items: center;">
                <div class = "ml10sp">内容：</div>
                <div class = "waritohiro">
                    <?php if(empty($red[0]["reportcontent"])) {
                        echo $this->Form->control('reportcontent0', ['type' => 'text', 'label' => false]);  
                    } else {
                        echo $this->Form->control('reportcontent0', ['type' => 'text', 'label' => false,
                                                                     'value' => $red[0]["reportcontent"]]);
                    }?>
                </div>
            </div>              
        </div>
        <div class = "itenai">
            <div class = "odakoku" style = "align-items: center;">
                <div>項目：</div>
                <div class = "yayahiro">
                    <?php if(empty($red[1]["item"])) {
                        echo $this->Form->control('item1', ['type' => 'text', 'label' => false]);  
                    } else {
                        echo $this->Form->control('item1', ['type' => 'text', 'label' => false,
                                                            'value' => $red[1]["item"]]);
                    }?>
                </div>
            </div>
            <div class = "odakoku" style = "align-items: center;">
                <div class = "ml10sp">内容：</div>
                <div class = "waritohiro">
                    <?php if(empty($red[1]["reportcontent"])) {
                        echo $this->Form->control('reportcontent1', ['type' => 'text', 'label' => false]);  
                    } else {
                        echo $this->Form->control('reportcontent1', ['type' => 'text', 'label' => false,
                                                                    'value' => $red[1]["reportcontent"]]);
                    }?>
                </div>               
            </div>
        </div>
        <div class = "itenai">
            <div class = "odakoku" style = "align-items: center;">
                <div>項目：</div>
                <div class = "yayahiro">
                    <?php if(empty($red[2]["item"])) {
                        echo $this->Form->control('item2', ['type' => 'text', 'label' => false]);  
                    } else {
                        echo $this->Form->control('item2', ['type' => 'text', 'label' => false,
                                                            'value' => $red[2]["item"]]);
                    }?>
                </div>
            </div>
            <div class = "odakoku" style = "align-items: center;">
                <div class = "ml10sp">内容：</div>
                <div class = "waritohiro">
                    <?php if(empty($red[2]["reportcontent"])) {
                        echo $this->Form->control('reportcontent2', ['type' => 'text', 'label' => false]);  
                    } else {
                        echo $this->Form->control('reportcontent2', ['type' => 'text', 'label' => false, 
                                                                     'value' => $red[2]["reportcontent"]]);
                    }?>
                </div>               
            </div>
        </div>

        <div class = "ml10">
            <?php if(empty($rep["notice"])) {
                echo $this->Form->control('notice', ['type' => 'textarea', 'label' => '反省・特記事項']);  
            } else {
                echo $this->Form->control('notice', ['type' => 'textarea', 'label' => '反省・特記事項', 
                                                    'value' => $rep["notice"]]);
            }?>
        </div>

        <div class = "ml10">
            <?php if(empty($rep["plan"])) {
                echo $this->Form->control('plan', ['type' => 'textarea', 'label' => '次回の予定']);  
            } else {
                echo $this->Form->control('plan', ['type' => 'textarea', 'label' => '次回の予定', 
                                                'value' => $rep["plan"]]);
            }?>
        </div>
        
        <?php if($this->request-> getSession()->read('Auth.User.adminfrag') == 1): ?>
            <h4 class="legend"><?= __('業務日誌') ?></h4>
            <?php if(!empty($staff_id)) {
                echo $this->Form->label("記録者");
                echo $this->Form->select('recorder',$admresults,array('label' => false,'value' => $staff_id)); 
            } elseif(!empty($rep["recorder"])) {
                echo $this->Form->label("記録者");
                echo $this->Form->select('recorder',$admresults,array('label' => false,'value' => $rep["recorder"])); 
            } else {
                echo $this->Form->label("記録者");
                echo $this->Form->select('recorder',$admresults,array('label' => false,'value' => $auser_id));
            } ?>
            <?php if(empty($rep["state"])) {
                echo $this->Form->control('state', ['type' => 'textarea', 'label' => '業務内容・様子']);  
            } else {
                echo $this->Form->control('state', ['type' => 'textarea', 'label' => '業務内容・様子', 'value' => $rep['state']]);
            }?>
            <?php if(empty($rep["information"])) {
                echo $this->Form->control('information', ['type' => 'textarea', 'label' => '体調・連絡事項など']);  
            } else {
                echo $this->Form->control('information', ['type' => 'textarea', 'label' => '体調・連絡事項など', 'value' => $rep['information']]);
            }?>
            
            <?php if(empty($rep["bikou"])) {
                echo $this->Form->control('bikou', ['type' => 'textarea', 'label' => '備考']);  
            } else {
                echo $this->Form->control('bikou', ['type' => 'textarea', 'label' => '備考', 'value' => $rep['bikou']]);
            }?>
        <?php endif; ?>
        <div class = "ml10">
            <?= $this->Form->button("送信",array('onClick' => 'return OnSend()')) ?>
            <!--<?= $this->Form->button(__("送信")) ?>->
        </div>
        <?php $this->Form->end(); ?>
    </div>
    </div>
</div>