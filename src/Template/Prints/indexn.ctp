<!-- 本番環境にアップロードする場合はurlに注意する -->
<!-- ローカル環境：http://[::1]:8765/prints/updf　または　http://[::1]:8765/prints/spdf -->
<!-- 本番環境：https://www.nbg-rd.com/gw/prints/updf　または　https://www.nbg-rd.com/gw/prints/spdf -->

<?php $this->assign('title', '各種出力'); ?>

<?php if($updf == 1): ?>
    <script language = javascript> 
        <!--window.open("https://www.nbg-rd.com/gw/pdfs/updf");=->
        window.open("/gw/pdfs/updf");
    </script>
<?php endif; ?>

<?php if($umpdf == 1): ?>
    <script language = javascript> 
        <!--window.open("https://www.nbg-rd.com/gw/pdfs/umpdf");--> 
        window.open("/gw/pdfs/umpdf");
    </script>
<?php endif; ?>

<?php if($spdf == 1): ?>
    <script language = javascript> 
        <!--window.open("https://www.nbg-rd.com/gw/pdfs/spdf");--> 
        window.open("/gw/pdfs/spdf"); 
    </script>
<?php endif; ?>

<?php if($paid == 1): ?>
    <script language = javascript> 
        window.open("https://www.nbg-rd.com/gw/pdfs/schedule"); 
    </script>
<?php endif; ?>

<?php if($chouka == 1): ?>
    <script language = javascript> 
        <!--window.open("https://www.nbg-rd.com/gw/pdfs/chouka");-->
        window.open("/gw/pdfs/chouka");
    </script>
<?php endif; ?>

<?php if($sancho == 1): ?>
    <script language = javascript> 
        <!--window.open("https://www.nbg-rd.com/gw/pdfs/sanchouka");-->
        window.open("/gw/pdfs/sanchouka");

    </script>
<?php endif; ?>

<div class = "main1 mb30">
    <h4 class="titleh4 mt20">　各種出力</h4>

    <!-- 1行目 -->
    <div class = "odakoku">
        <div class = "shutsuryoku">
            <h4 class = "exportdeka">出勤簿(利用者)PDF印刷</h4>
            <?= $this->Form->create(__("View"),["type" => "post","url" => ["controller" => "pdfs", "action" => "getquery0"],'id' => 'pdf-user-form']); ?>
            <?= $this->Form->control('hidden',['type'=>'hidden','value'=>0]) ?>
            <h4 class = "exportchibi">年月日選択</h4>
                <div class = "odakoku mlv25">
                <div class = "sdakoku">
                    <?php 
                    $uyear = $this->request->getSession()->read('uyear');
                    $uyear_value = ($uyear !== null && $uyear !== '') ? $uyear : date("Y");
                    ?>
                    <?= $this->Form->control('year',['type'=>'select','id'=>'pdf_year','data-role'=>'year','label'=>"年",'value'=>$uyear_value,'onchange'=>'updateUsersList("pdf-user-form")'], $years) ?>
                </div>
                <div class = "sdakoku">
                    <?php 
                    $umonth = $this->request->getSession()->read('umonth');
                    $umonth_value = ($umonth !== null && $umonth !== '') ? $umonth : date("m");
                    ?>
                    <?= $this->Form->control('month',['type'=>'select','id'=>'pdf_month','data-role'=>'month','label'=>"月",'value'=>$umonth_value,'onchange'=>'updateUsersList("pdf-user-form")'], $months) ?>
                </div>
            </div>
             <div class = "odakoku mlv25">
                 <div class = "sdakoku" style="width: 135px;">
                     <h4 class = "exportchibi" style="margin-left: 0; padding-left: 0;">ユーザー</h4>
                     <?php if(empty($this->request->getSession()->read('uuser_id'))): ?>
                         <?= $this->Form->select('id',$users,['id'=>'pdf_user_id','data-role'=>'user','label' => false,'empty'=>array('0'=>'ALL')]);?>
                     <?php else: ?>
                         <?= $this->Form->select('id',$users,['id'=>'pdf_user_id','data-role'=>'user','label' => false,'empty'=>array('0'=>'ALL'),'value'=>$this->request->getSession()->read('uuser_id')]);?>
                     <?php endif; ?>
                 </div>
                 <div class = "sdakoku" style="width: 120px; padding-left: 20px;">
                     <h4 class = "exportchibi" style="margin-left: 0px; padding-left: 0;">就労タイプ</h4>
                     <?php 
                     $work_type = $this->request->getSession()->read('work_type') ?: '0';
                     ?>
                     <?= $this->Form->select('work_type',[
                         '0' => 'A型',
                         '1' => 'B型'
                     ], [
                         'id' => 'pdf_work_type',
                         'data-role' => 'work-type',
                         'label' => false,
                         'value' => $work_type,
                         'default' => '0',
                         'onchange' => 'updateUsersList("pdf-user-form")'
                     ]); ?>
                 </div>
             </div>
            
            <div class="mt10_button mt20 mlv25">
                <?= $this->Form->button(__("表示")) ?>
            </div>
            <?= $this -> Form -> end(); ?>
        </div>
        <div class = "shutsuryoku">
            <h4 class = "exportdeka">出勤簿(職員)PDF印刷</h4>
            <?= $this->Form->create(__("View"),["type" => "post","url" => ["controller" => "pdfs", "action" => "getquery0"]]); ?>
            <?= $this->Form->control('hidden',['type'=>'hidden','value'=>2]) ?>
            <h4 class = "exportchibi">年月日選択</h4>
                <div class = "odakoku mlv25">
                <div class = "sdakoku">
                    <?php if(empty($this->request->getSession()->read('syear'))): ?>
                        <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>date("Y")], $years) ?>
                    <?php else: ?>
                        <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>$this->request->getSession()->read('syear')], $years) ?>                   
                    <?php endif; ?>
                </div>
                <div class = "sdakoku">
                    <?php if(empty($this->request->getSession()->read('smonth'))): ?>
                        <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>date("m")], $months) ?>
                    <?php else: ?>
                        <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>$this->request->getSession()->read('smonth')], $months) ?>                   
                    <?php endif; ?>
                </div>
            </div>
            <h4 class = "exportchibi">ユーザー</h4>  
            <div class = "staffbox mlv25">
                <?php if(empty($this->request->getSession()->read('suser_id'))): ?>
                    <?= $this->Form->select('id',$staffs,['id'=>'staff_id','label' => false,'empty'=>array('0'=>'ALL')]);?>
                <?php else: ?>
                    <?= $this->Form->select('id',$staffs,['id'=>'staff_id','label' => false,'empty'=>array('0'=>'ALL'),'value'=>empty($this->request->getSession()->read('suser_id'))]);?>
                <?php endif; ?>
            </div>
            <div class="ml10_button mt20 mlv25">
                <?= $this->Form->button(__("表示")) ?>
            </div>
            <?= $this -> Form -> end(); ?>
        </div>
        <div class = "shutsuryoku">
            <h4 class = "exportdeka">出勤簿(利用者)Excel出力</h4>
            <?= $this -> Form -> create(__("View"),["type" => "post","url" => ["action" => "view"],'id' => 'excel-user-form']); ?>
            <h4 class = "exportchibi">年月日選択</h4>
                <div class = "odakoku mlv25">
                    <div class = "sdakoku">
                    <?php 
                    $excelYear = $this->request->getSession()->read('eyear');
                    $excelYearValue = ($excelYear !== null && $excelYear !== '') ? $excelYear : date("Y");
                    ?>
                    <?= $this->Form->control('year',['type'=>'select','id'=>'excel_year','data-role'=>'year','label'=>"年",'value'=>$excelYearValue,'onchange'=>'updateUsersList("excel-user-form")'], $years) ?>
                    </div>
                    <div class = "sdakoku">
                        <?php 
                        $excelMonth = $this->request->getSession()->read('emonth');
                        $excelMonthValue = ($excelMonth !== null && $excelMonth !== '') ? $excelMonth : date("m");
                        ?>
                        <?= $this->Form->control('month',['type'=>'select','id'=>'excel_month','data-role'=>'month','label'=>"月",'value'=>$excelMonthValue,'onchange'=>'updateUsersList("excel-user-form")'], $months) ?> 
                    </div>
                </div>



                <div class = "odakoku mlv25">
                    <div class = "sdakoku" style="width: 135px;">
                     <h4 class = "exportchibi" style="margin-left: 0; padding-left: 0;">ユーザー</h4>
                     <?php 
                        $excelUserId = $this->request->getSession()->read('euser_id');
                        $excelUserValue = ($excelUserId === null || $excelUserId === '') ? '0' : (string)$excelUserId;
                        ?>
                        <?= $this->Form->select('user_id',$users,['id'=>'excel_user_id','data-role'=>'user','label' => false,'empty'=>['0'=>'ALL'],'value'=>$excelUserValue]);?>
                    </div>
                    <div class = "sdakoku" style="width: 120px; padding-left: 20px;">
                        <h4 class = "exportchibi" style="margin-left: 0; padding-left: 0;">就労タイプ</h4>
                        <?php 
                        $excel_work_type = $this->request->getSession()->read('work_type') ?: '0';
                        ?>
                        <?= $this->Form->select('work_type',[
                            '0' => 'A型',
                            '1' => 'B型'
                        ],[
                            'id' => 'excel_work_type',
                            'data-role' => 'work-type',
                            'label' => false,
                            'value' => $excel_work_type,
                            'default' => '0',
                            'onchange' => 'updateUsersList("excel-user-form")'
                        ]); ?>
                    </div>
                </div>
            <div class="mt10_button mt20 mlv25">
                <?= $this->Form->button(__("表示")) ?>
            </div>
            <?= $this -> Form -> end(); ?>
        </div>
    </div>
    
    <!-- 2行目 -->
    <div class = "odakoku mt30">
        <div class = "shutsuryoku" style = "width: 725px; margin-left: 20px;">
            <h4 class = "exportdeka">出勤簿(利用者)まとめてPDF出力</h4>
            <?= $this -> Form -> create(__("View"),["type" => "post","url" => ["controller" => "pdfs", "action" => "getquery0"]]); ?>
            <?= $this->Form->control('hidden',['type'=>'hidden','value'=>1]) ?>
            <h4 class = "exportchibi">年月日選択</h4>
            <div class = "odakoku mlv25">
                <div class = "sdakoku">
                    <?php if(empty($this->request->getSession()->read('hyear'))): ?>
                        <?= $this->Form->control('hyear',['type'=>'select','label'=>"年",'value'=>date("Y")], $years) ?>
                    <?php else: ?>
                        <?= $this->Form->control('hyear',['type'=>'select','label'=>"年",'value'=>$this->request->getSession()->read('hyear')], $years) ?>                   
                    <?php endif; ?>
                </div>
                <div class = "sdakoku">
                    <?php if(empty($this->request->getSession()->read('hmonth'))): ?>
                        <?= $this->Form->control('hmonth',['type'=>'select','label'=>"月",'value'=>date("m")], $months) ?>
                    <?php else: ?>
                        <?= $this->Form->control('hmonth',['type'=>'select','label'=>"月",'value'=>$this->request->getSession()->read('hmonth')], $months) ?>                   
                    <?php endif; ?>
                </div>
                <div style = "margin: 46px 0 0 5px; width:40px;">から</div>
                <div class = "sdakoku" style = "margin-left: 10px;">
                    <?php if(empty($this->request->getSession()->read('oyear'))): ?>
                        <?= $this->Form->control('oyear',['type'=>'select','label'=>"年",'value'=>date("Y")], $years) ?>
                    <?php else: ?>
                        <?= $this->Form->control('oyear',['type'=>'select','label'=>"年",'value'=>$this->request->getSession()->read('oyear')], $years) ?>                   
                    <?php endif; ?>
                </div>
                <div class = "sdakoku">
                    <?php if(empty($this->request->getSession()->read('omonth'))): ?>
                        <?= $this->Form->control('omonth',['type'=>'select','label'=>"月",'value'=>date("m")], $months) ?>
                    <?php else: ?>
                        <?= $this->Form->control('omonth',['type'=>'select','label'=>"月",'value'=>$this->request->getSession()->read('omonth')], $months) ?>                   
                    <?php endif; ?>
                </div>
                <div style = "margin: 46px 0 0 5px; width:40px;">まで</div>
            </div>
            <h4 class = "exportchibi">ユーザー</h4>  
                <div class = "mlv25" style = "width: 150px;">
                    <?php if(empty($this->request->getSession()->read('muser_id'))): ?>
                        <?= $this->Form->select('id',$users,['id'=>'staff_id','label' => false]);?>
                    <?php else: ?>
                        <?= $this->Form->select('id',$users,['id'=>'staff_id','label' => false,'value'=>$this->request->getSession()->read('muser_id')]);?>
                    <?php endif; ?>
                </div>
            <div class="mt10_button mt30 mlv25">
                <?= $this->Form->button(__("表示")) ?>
            </div>
            <?= $this -> Form -> end(); ?>
        </div>
        <div class = "shutsuryoku">
            <h4 class = "exportdeka">CSV出力</h4>
            <?= $this -> Form -> create(__("View"),["type" => "post","url" => ["action" => "csv"]]); ?>
            <h4 class = "exportchibi">年月日選択</h4>
            <div class = "odakoku mlv25">
                <div class = "sdakoku">
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>date("Y")], $years) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>date("m")], $months) ?> 
                </div>
            </div>
            <h4 class = "exportchibi">ユーザー</h4>  
            <div class = "staffbox mlv25">
                <?= $this->Form->select('id',$users,array('id'=>'staff_id','label' => false,'empty'=>false));?>
            </div>
            <div class="mt10_button mt20 mlv25">
                <?= $this->Form->button(__("表示")) ?>
            </div>
            <?= $this -> Form -> end(); ?>
        </div>
    </div>
    <br>

    <!-- 3行目 -->
    <div class = "odakoku">
        <div class = "shutsuryoku">
            <h4 class = "exportdeka">業務日誌出力</h4>
            <?= $this -> Form -> create(__("View"),["type" => "post","url" => ["action" => "nisshis"]]); ?>
            <h4 class = "exportchibi">年月日選択</h4>
            <div class = "odakoku mlv25">
                <div class = "sdakoku">
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>date("Y")], $years) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>date("m")], $months) ?> 
                </div>
            </div>
            <h4 class = "exportchibi">ユーザー</h4>  
            <div class = "staffbox mlv25">
                <?= $this->Form->select('id',$users,array('id'=>'staff_id','label' => false,'empty'=>false));?>
            </div>
            <div class="mt10_button mt20 mlv25">
                <?= $this->Form->button(__("表示")) ?>
            </div>
            <?= $this -> Form -> end(); ?>
        </div>
        <div class = "shutsuryoku">
            <h4 class = "exportdeka">サービス提供記録出力</h4>
            <?= $this -> Form -> create(__("View"),["type" => "post","url" => ["action" => "service"]]); ?>
            <h4 class = "exportchibi">年月日選択</h4>
                <div class = "odakoku mlv25">
                <div class = "sdakoku">
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>date("Y")], $years) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>date("m")], $months) ?> 
                </div>
            </div>
            <h4 class = "exportchibi">ユーザー</h4>  
            <div class = "staffbox mlv25">
                <?= $this->Form->select('id',$users,array('id'=>'staff_id','label' => false,'empty'=>false));?>
            </div>
            <div class="mt10_button mt20 mlv25">
                <?= $this->Form->button(__("表示")) ?>
            </div>
            <?= $this -> Form -> end(); ?>
        </div>
        <div class = "shutsuryoku">
            <h4 class = "exportdeka">欠勤情報出力</h4>
            <?= $this -> Form -> create(__("View"),["type" => "post","url" => ["controller" => "Absents","action" => "excelexport"]]); ?>
            <h4 class = "exportchibi">年月日選択</h4>
                <div class = "odakoku mlv25">
                <div class = "sdakoku">
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>date("Y")], $years) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>date("m")], $months) ?> 
                </div>
            </div>
            <h4 class = "exportchibi">ユーザー</h4>  
            <div class = "staffbox mlv25">
                <?= $this->Form->select('user_id',$users,array('id'=>'staff_id','label' => false,'empty'=>array('0'=>'ALL')));?>
            </div>
            <div class="ml10_button mt20 mlv25">
                <?= $this->Form->button(__("表示")) ?>
            </div>
            <?= $this -> Form -> end(); ?>
        </div>
    </div>
    <br>

    <!-- 4行目 -->
    <div class = "odakoku">
        <div class = "shutsuryoku">
            <h4 class = "exportdeka">在宅就労一覧表出力</h4>
            <?= $this -> Form -> create(__("View"),["type" => "post","url" => ["action" => "remote"]]); ?>
            <h4 class = "exportchibi">年月日選択</h4>
            <div class = "odakoku mlv25">
                <div class = "sdakoku">
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>date("Y")], $years) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>date("m")], $months) ?> 
                </div>
            </div>
            <br>
            <br>
            <br>
            <div class="mt10_button mt20 mlv25">
                <?= $this->Form->button(__("表示")) ?>
            </div>
            <?= $this -> Form -> end(); ?>
        </div>
        <div class = "shutsuryoku">
            <h4 class = "exportdeka">施設外就労実績報告書出力</h4>
            <?= $this -> Form -> create(__("View"),["type" => "post","url" => ["action" => "support"]]); ?>
            <h4 class = "exportchibi">年月日選択</h4>
            <div class = "odakoku mlv25">
                <div class = "sdakoku">
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>date("Y")], $years) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>date("m")], $months) ?> 
                </div>
                </div>
            <h4 class = "exportchibi">施設外就労場所</h4>  
            <div class = "staffbox mlv25">
                <?= $this->form->input( 'support',array('label'=>false,'type' => 'select', 'options' => $workName,)); ?> 
            </div>
            <div class="mt10_button mt20 mlv25">
                <?= $this->Form->button(__("表示")) ?>
            </div>
            <?= $this -> Form -> end(); ?>
        </div>
        <div class = "shutsuryoku">
            <h4 class = "exportdeka">月間実績票出力</h4>
            <?= $this -> Form -> create(__("View"),["type" => "post","url" => ["action" => "jisseki"]]); ?>
            <h4 class = "exportchibi">年月日選択</h4>
            <div class = "odakoku mlv25">
                <div class = "sdakoku">
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>date("Y")], $years) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>date("m")], $months) ?> 
                </div>
            </div>
            <h4 class = "exportchibi">就労タイプ</h4>
            <div class = "staffbox mlv25">
                <?= $this->Form->select('syuroutype',[
                    '0' => 'A型',
                    '1' => 'B型'
                ], [
                    'label' => false,
                    'default' => '0'
                ]); ?>
            </div>
            <div class="mt10_button mt20 mlv25">
                <?= $this->Form->button(__("表示")) ?>
            </div>
            <?= $this -> Form -> end(); ?>
        </div>
    </div>
        
    <!-- 5行目 -->
    <div class = "odakoku mt30">
        <div class = "shutsuryoku">
            <h4 class = "exportdeka">有休取得一覧出力</h4>

            <?= $this->Form->create(__("View"),["type" => "post","url" => ["controller"=>"pdfs","action"=>"getquery0"]]); ?>
            <?= $this->Form->control('hidden',['type'=>'hidden','value'=>3]) ?>

            <h4 class = "exportchibi">年月日選択</h4>
            <div class = "odakoku mlv25">
                <div class = "sdakoku">
                    <?php if(empty($this->request->getSession()->read('pyear'))): ?>
                        <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>date("Y")], $years) ?>
                    <?php else: ?>
                        <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>$this->request->getSession()->read('pyear')], $years) ?>                   
                    <?php endif; ?>
                </div>
                <div class = "sdakoku">
                    <?php if(empty($this->request->getSession()->read('pmonth'))): ?>
                        <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>date("m")], $months) ?>
                    <?php else: ?>
                        <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>$this->request->getSession()->read('pmonth')], $months) ?>                   
                    <?php endif; ?>
                </div>
            </div>
            <div class = "staffbox mlv25">
                <div class = "mb10"><?= $this->Form->label("並び順"); ?></div>
                <?= $this->Form->select('sort',$sort,array('label'=>false));?>
            </div>

            <div class="mt10_button mt20 mlv25">
                <?= $this->Form->button(__("表示")) ?>
            </div>
            <?= $this -> Form -> end(); ?>
        </div>

        <div class = "shutsuryoku">
            <h4 class = "exportdeka">在宅就労記録出力</h4>

            <?= $this->Form->create(__("View"),["type" => "post","url" => ["action"=>"viewdetail"]]); ?>

            <h4 class = "exportchibi">年月日選択</h4>
            <div class = "odakoku mlv25">
                <div class = "sdakoku">
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>date("Y")], $years) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>date("m")], $months) ?>
                </div>
            </div>
            <h4 class = "exportchibi">ユーザー</h4>  
            <div class = "staffbox mlv25">
                <?= $this->Form->select('user_id',$remotes,array('id'=>'staff_id','label'=>false,'empty'=>false));?>
            </div>

            <div class="mt10_button mt30 mlv25">
                <?= $this->Form->button(__("表示")) ?>
            </div>
            <?= $this -> Form -> end(); ?>
        </div>

        <div class = "shutsuryoku">
            <h4 class = "exportdeka">月間定員超過率出力</h4>

            <?= $this->Form->create(__("View"),["type" => "post","url" => ["controller" => "pdfs", "action" => "getquery0"]]); ?>
            <?= $this->Form->control('hidden',['type'=>'hidden','value'=>4]) ?>
            <h4 class = "exportchibi">年月日選択</h4>
            <div class = "odakoku mlv25">
                <div class = "sdakoku">
                    <?php if(empty($this->request->getSession()->read('choyear'))): ?>
                        <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>date("Y")], $years) ?>
                    <?php else: ?>
                        <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>$this->request->getSession()->read('choyear')], $years) ?>                   
                    <?php endif; ?>
                </div>
                <div class = "sdakoku">
                    <?php if(empty($this->request->getSession()->read('chomonth'))): ?>
                        <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>date("m")], $months) ?>
                    <?php else: ?>
                        <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>$this->request->getSession()->read('chomonth')], $months) ?>                   
                    <?php endif; ?>
                </div>
            </div>
            <br>
            <br>
            <br>
            <div class="mt10_button mt30 mlv25">
                <?= $this->Form->button(__("表示")) ?>
            </div>
        <?= $this -> Form -> end(); ?>
        </div>
    </div>
    
    <!-- 6行目 -->
    <div class = "odakoku mt30">
        <div class = "shutsuryoku">
            <h4 class = "exportdeka">3ヶ月定員超過率出力</h4>
            <?= $this->Form->create(__("View"),["type" => "post","url" => ["controller" => "pdfs", "action" => "getquery0"]]); ?>
            <?= $this->Form->control('hidden',['type'=>'hidden','value'=>5]) ?>
            <h4 class = "exportchibi">年月日選択</h4>
            <div class = "odakoku mlv25">
                <div class = "sdakoku">
                    <?php if(empty($this->request->getSession()->read('choyear'))): ?>
                        <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>date("Y")], $years) ?>
                    <?php else: ?>
                        <?= $this->Form->control('year',['type'=>'select','label'=>"年",
                                                'value'=>$this->request->getSession()->read('choyear')], $years) ?>                   
                    <?php endif; ?>
                </div>
                <div class = "sdakoku2">
                    <?php if(empty($this->request->getSession()->read('chomonth'))): ?>
                        <?= $this->Form->label("月") ?>
                        <?= $this->Form->select("sanchom",$sanchom,['empty'=>false,
                                                "value"=>$defsan[date('n')]]); ?>
                    <?php else: ?>
                        <?= $this->Form->label("月") ?>
                        <?= $this->Form->select("sanchom",$sanchom,['empty'=>false,
                                                "value"=>$this->request->getSession()->read('chomonth')]); ?>              
                    <?php endif; ?>
                </div>
            </div>
            <br>
            <br>
            <br>
            <div class="mt10_button mt20 mlv25">
                <?= $this->Form->button(__("表示")) ?>
            </div>
            <?= $this -> Form -> end(); ?>

        </div>

        <div class = "shutsuryoku">
            <h4 class = "exportdeka">雇用保険基準判定</h4>
            <?= $this->Form->create(__("View"),["type" => "post","url" => ["action" => "hoken"]]); ?>
            <h4 class = "exportchibi ">期間選択</h4>
            <div class = "staffbox mlv25">
                <?= $this->Form->select('kikan', $kikan,['type'=>'select','label'=>false,'empty'=>false]) ?>
            </div>
            <h4 class = "exportchibi">ユーザー</h4>  
            <div class = "staffbox mlv25 mb30">
                <?= $this->Form->select('user_id',$users,['id'=>'staff_id','label' => false,'empty'=>['0'=>'ALL']]);?>
            </div>
            <div class="mt10_button mlv25" style = "margin: 60px 0 0 2.5vw">
                <?= $this->Form->button(__("表示")) ?>
            </div>
            <?= $this -> Form -> end(); ?>
        </div>

        <div class = "shutsuryoku">
            <h4 class = "exportdeka">食事管理表出力</h4>

            <?= $this->Form->create(__("View"),["type" => "post","url" => ["action"=>"syokuji"]]) ?>
            <!-- PDF
            <?= $this->Form->create(__("View"),["type" => "post","url" => ["controller" => "pdfs", "action" => "getquery0"]]) ?>
            <?= $this->Form->control('hidden',['type'=>'hidden','value'=> 6]) ?>
            -->

            <h4 class = "exportchibi">年月日選択</h4>
            <div class = "odakoku mlv25">
                <div class = "sdakoku">
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>date("Y")], $years) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>date("m")], $months) ?>
                </div>
            </div>
            <br>
            <br>
            <br>
           <div class="mt10_button mt20 mlv25">
                <?= $this->Form->button(__("表示")) ?>
            </div>
            <?= $this -> Form -> end(); ?>
        </div>
    </div>  

    <!-- 7行目 -->
    <div class = "odakoku mt30">
        <div class = "shutsuryoku">
            <h4 class = "exportdeka">出勤表</h4>
            <?= $this->Form->create(__("View"),["type" => "post","url" => ["action"=>"syukkinhyou"]]) ?>
            <h4 class = "exportchibi">年月日選択</h4>
            <div class = "odakoku mlv25">
                <div class = "sdakoku">
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>date("Y")], $years) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>date("m")], $months) ?>
                </div>
            </div>
            <br>
            <br>
            <br>
            <div class="mt10_button mt20 mlv25">
                <?= $this->Form->button(__("表示")) ?>
            </div>
            <?= $this -> Form -> end(); ?>
        </div>
        <br>
        <br>
        <br>
        <div class = "shutsuryoku">
            <h4 class = "exportdeka">喫食表</h4>
            <?= $this->Form->create(__("View"),["type" => "post","url" => ["action"=>"kissyokuhyou"]]) ?>
            <h4 class = "exportchibi">年月日選択</h4>
            <div class = "odakoku mlv25">
                <div class = "sdakoku">
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>date("Y")], $years) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>date("m")], $months) ?>
                </div>
            </div>
            <br>
            <br>
            <br>
            <div class="mt10_button mt20 mlv25">
                <?= $this->Form->button(__("表示")) ?>
            </div>
            <?= $this -> Form -> end(); ?>
        </div>
        <div class = "shutsuryoku">
            <h4 class = "exportdeka">送迎記録簿</h4>
            <?= $this->Form->create(__("View"),["type" => "post","url" => ["action"=>"sougeikirokubo"]]) ?>
            <h4 class = "exportchibi">年月日選択</h4>
            <div class = "odakoku mlv25">
                <div class = "sdakoku">
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>date("Y")], $years) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>date("m")], $months) ?>
                </div>
            </div>
            <br>
            <br>
            <br>
            <div class="mt10_button mt20 mlv25">
                <?= $this->Form->button(__("表示")) ?>
            </div>
            <?= $this -> Form -> end(); ?>
        </div>
        </div>  
</div>
<br>

<script>
/**
 * 指定フォームの年月と就労タイプに基づいて利用者リストを更新
 * @param {string} formId 更新対象フォームのID
 */
function updateUsersList(formId) {
    if (!formId) {
        console.error('フォームIDが指定されていません');
        return;
    }

    const form = document.getElementById(formId);
    if (!form) {
        console.error(`ID:${formId} のフォームが見つかりません`);
        return;
    }
    
    const yearSelect = form.querySelector('select[data-role="year"]');
    const monthSelect = form.querySelector('select[data-role="month"]');
    const workTypeSelect = form.querySelector('select[data-role="work-type"]');
    const userSelect = form.querySelector('select[data-role="user"]');
    const submitButton = form.querySelector('button[type="submit"]');
    
    if (!yearSelect || !monthSelect || !workTypeSelect || !userSelect || !submitButton) {
        console.error('必要な要素が見つかりません');
        return;
    }
    
    const year = yearSelect.value;
    const month = monthSelect.value;
    const workType = workTypeSelect.value;
    
    userSelect.disabled = true;
    submitButton.disabled = true;
    
    // 現在選択されている利用者IDを保存
    const currentUserId = userSelect.value;
    
    // CSRFトークンを取得（Calendars/indexn.ctpと同じ方法）
    const csrfToken = form.querySelector('input[name="_csrfToken"]')?.value || document.querySelector('input[name="_csrfToken"]')?.value || '';
    
    // AJAXリクエストを送信
    fetch('<?= $this->Url->build(["controller" => "Prints", "action" => "getUsersByDateAndWorkType"], true) ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-Token': csrfToken
        },
        body: new URLSearchParams({
            year: year,
            month: month,
            work_type: workType
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.error) {
            console.error('エラーが発生しました:', data.error);
            return;
        }
        
        // 利用者リストを更新
        userSelect.innerHTML = '';
        
        if (Object.keys(data).length === 0) {
            // 利用者がいない場合
            userSelect.disabled = true;
            submitButton.disabled = true;
            // 利用者がいない場合のメッセージを表示
            const noUserOption = document.createElement('option');
            noUserOption.value = '';
            noUserOption.textContent = '利用者がいません';
            noUserOption.disabled = true;
            userSelect.appendChild(noUserOption);
        } else {
            // 利用者がいる場合
            userSelect.disabled = false;
            submitButton.disabled = false;
            
            const hasAllInData = Object.prototype.hasOwnProperty.call(data, '0');
            if (!hasAllInData) {
                const allOption = document.createElement('option');
                allOption.value = '0';
                allOption.textContent = 'ALL';
                userSelect.appendChild(allOption);
            }
            
            Object.keys(data).forEach(userId => {
                const option = document.createElement('option');
                option.value = userId;
                option.textContent = data[userId];
                userSelect.appendChild(option);
            });
            
            // 以前選択されていた利用者が新しいリストに存在する場合は選択状態を復元
            if (currentUserId && (data.hasOwnProperty(currentUserId) || currentUserId === '0')) {
                userSelect.value = currentUserId;
            } else {
                // 存在しない場合はALLを選択
                userSelect.value = '0';
            }
        }
    })
    .catch(error => {
        console.error('AJAXリクエストでエラーが発生しました:', error);
        // エラー時はフォーム操作を可能に戻す
        userSelect.disabled = false;
        submitButton.disabled = false;
    });
}

// ページ読み込み時に初期化
document.addEventListener('DOMContentLoaded', function() {
    // 少し遅延させてから初期化（フォーム要素が完全に読み込まれるのを待つ）
    setTimeout(function() {
        ['pdf-user-form', 'excel-user-form'].forEach(function(formId) {
            if (document.getElementById(formId)) {
                updateUsersList(formId);
            }
        });
    }, 100);
});
</script>