<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/additional-methods.min.js"></script>
<script type="text/javascript" src="/js/validate-config.js"></script>
<script type="text/javascript">
    $(function() {
        $.validator.addMethod(
            "numeric",
            function(value,element) {
                return this.optional(element) || /^([0-9]+)$/.test(value);
            },
            "半角数字のみで入力"
        );
        $("#editform").validate({
            rules: {
                price: {
                    numeric: true,
                },
            },
        });
    });
</script>

<?php $this->assign('title', '購入申請編集'); ?>

<div class = "main1">
    <h4 class="midashih4 mt30"> 物品購入届</h4>    
    <?= $this->Form->create(__("View"),["type"=>"post","url"=>["action"=>"register2",$kaimono["id"]]]) ?>
    <div class = "shinsei">
        <h4 class = "exportdeka">　基本情報</h4>
        <div class = "pdakoku">
            <?php 
                echo $this->Form->label('分類：　');
                echo '<br>';
                
                $options = [
                            '0' => ' 購入伺　',
                            '1' => ' 購入報告　'
                            ];
                $attributes = ['name'=>'type','value'=>'0']; 
                echo $this->Form->radio('type', $options, $attributes);
            ?>
        </div>
        <div class = "pdakoku">
            <div class = "sdakoku">
                <?php if(empty($bunkatsu[2])): ?>
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>date("Y")], $years) ?>
                <?php else: ?>
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>$bunkatsu[2]], $years) ?>
                <?php endif; ?>
            </div>
            <div class = "sdakoku">
                <?php if(empty($bunkatsu[0])): ?>
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>date("m")], $months) ?>
                <?php else: ?>
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>$bunkatsu[0]], $months) ?>
                <?php endif; ?>
            </div>
            <div class = "sdakoku">
                <?php if(empty($bunkatsu[1])): ?>
                    <?= $this->Form->control('date',['type'=>'text','label'=>"日",'value'=>date("d")]) ?>
                <?php else: ?>
                    <?= $this->Form->control('date',['type'=>'text','label'=>"日",'value'=>$bunkatsu[1]]) ?>
                <?php endif; ?>
            </div>
            <div class = "w250">
                <?php if(empty($kaimono["price"])): ?>
                    <?= $this->Form->control("price",['type'=>'text','label'=>"金額 (半角数字のみで入力)"]) ?>
                <?php else: ?>
                    <?= $this->Form->control("price",['type'=>'text','label'=>"金額 (半角数字のみで入力)",'value'=>$kaimono["price"]]) ?>
                <?php endif; ?>
            </div>
            <div class = "w150">
                <?php if($this->request-> getSession()->read('Auth.User.adminfrag') != 1): ?>
                    <?= $this->Form->control('user_id',['type'=>'hidden','label'=>"hidden",'value'=>$session]) ?>
                <?php else: ?>
                    <?php if(empty($kaimono["user_id"])): ?>
                        <?= $this->Form->control('user_id',['type'=>'select','label'=>"購入者",'value'=>$session], $users) ?>
                    <?php else: ?>
                        <?= $this->Form->control('user_id',['type'=>'select','label'=>"購入者",'value'=>$kaimono["user_id"]], $users) ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <p class = "messe ml10">※ 購入伺の場合は、年月日に購入予定日を入力してください。</p>

        <div class = "pdakoku">
            <?php 
                echo $this->Form->label('支払方法：　');
                echo '<br>';
                
                $options = [
                            '0' => ' 現金　',
                            '1' => ' 請求書　',
                            '2' => ' 会社カード　'
                            ];
                if(empty($kaimono["payment"])) {
                    $attributes = ['name'=>'payment','value'=>'0']; 
                } else {
                    $attributes = ['name'=>'payment','value'=>$kaimono["payment"]]; 
                }
                echo $this->Form->radio('payment', $options, $attributes);
            ?>
        </div>
    </div>

    <?php for($i=0; $i<3; $i++): ?>
        <div class = "shinsei">
        <h4 class = "exportdeka">　品目<?=$i+1?>　詳細情報</h4>
            <div class = "odakoku ml10">
                <div class = "w500">
                    <?php if(empty($details[$i]["cinnamon"])): ?>
                        <?= $this->Form->control("cinnamon[$i]",['type'=>'text','label'=>"購入品名"]) ?>
                    <?php else: ?>
                        <?= $this->Form->control("cinnamon[$i]",['type'=>'text','label'=>"購入品名","value"=>$details[$i]["cinnamon"]]) ?>
                    <?php endif; ?>
                </div>
                <div class = "w250">
                    <?php if(empty($details[$i]["detail"])): ?>
                        <?= $this->Form->control("detail[$i]",['type'=>'text','label'=>"詳細(個数・長さなど)"]) ?>
                    <?php else: ?>
                        <?= $this->Form->control("detail[$i]",['type'=>'text','label'=>"詳細(個数・長さなど)","value"=>$details[$i]["detail"]]) ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class = "odakoku mb10">
                <div class = "w200 ml10">
                    <?php if(empty($details[$i]["shop"])): ?>
                        <?= $this->Form->control("shop[$i]",['type'=>'text','label'=>"購入先"]) ?>
                    <?php else: ?>
                        <?= $this->Form->control("shop[$i]",['type'=>'text','label'=>"購入先","value"=>$details[$i]["shop"]]) ?>
                    <?php endif; ?>
                </div>
                <div class = "w500 ml10">
                    <?php if(empty($details[$i]["url"])): ?>
                        <?= $this->Form->label("リンク"); ?>
                        <?= $this->Form->text("url[$i]",["type" => "text"]); ?>
                    <?php else: ?>
                        <?= $this->Form->label("リンク"); ?>
                        <?= $this->Form->text("url[$i]",["type" => "text","value"=>$details[$i]["url"]]); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endfor; ?>

    <?php if($this->request-> getSession()->read('Auth.User.adminfrag') == 1): ?>
        <?php if($this->request-> getSession()->read('Auth.User.kessai') == 1): ?>
            <div class = "shinsei">
                <h4 class = "exportdeka">　決裁状況</h4>
                <div class = "pdakoku">
                    <?php 
                        echo $this->Form->label('状況：　');
                        echo '<br>';
                        
                        $options = [
                            '1' => ' 決裁済み　',
                            '2' => ' 否決　'
                            ];            
                        $attributes = ['name'=>'status','value' => $kaimono["status"]]; 
                        echo $this->Form->radio('status', $options, $attributes);
                    ?>
                </div>
                <div class = "pdakoku">
                    <div class = "sdakoku">
                        <?php if($kaimono["status"] == 1 && !empty($kaimono["kessaibi"])): ?>
                            <?= $this->Form->control('kyear',['type'=>'select','label'=>"年",'value'=>$bunkatsu2[2]], $years) ?>
                        <?php else: ?>
                            <?= $this->Form->control('kyear',['type'=>'select','label'=>"年",'value'=>date('Y')], $years) ?>
                        <?php endif; ?>
                    </div>
                    <div class = "sdakoku">
                        <?php if($kaimono["status"]  == 1 && !empty($kaimono["kessaibi"])): ?>
                            <?= $this->Form->control('kmonth',['type'=>'select','label'=>"月",'value'=>$bunkatsu2[0]], $months) ?>
                        <?php else: ?>
                            <?= $this->Form->control('kmonth',['type'=>'select','label'=>"月",'value'=>date('m')], $months) ?>
                        <?php endif; ?>
                    </div>
                    <div class = "sdakoku">
                        <?php if($kaimono["status"]  == 1 && !empty($kaimono["kessaibi"])): ?>
                            <?= $this->Form->control('kdate',['type'=>'text','label'=>"日",'value'=>$bunkatsu2[1]]) ?>
                        <?php else: ?>
                            <?= $this->Form->control('kdate',['type'=>'text','label'=>"日",'value'=>date('d')]) ?>
                        <?php endif; ?>
                    </div>
                    <div class = "w150">
                        <?php if(!empty($kaimono["kessaisha"])): ?>
                            <?php echo $this->Form->label("決裁者")?>
                            <?php echo $this->Form->select('kessaisha',$users2,['label' => "対象者",'type'=> 'select','value'=> $kaimono["kessaisha"]]);?>
                        <?php else: ?> 
                            <?php echo $this->Form->label("決裁者")?>
                            <?php echo $this->Form->select('kessaisha',$users2,['label' => "対象者",'type'=> 'select','value'=> $session]);?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class = "shinsei">
        <h4 class = "exportdeka">　備考</h4>
        <div style = "margin: 20px 15px 0 10px;">
        <?php if(empty($kaimono["bikou"])): ?>
            <?= $this->Form->control("bikou",['type'=>'textarea','label'=>false]) ?>
        <?php else: ?>
            <?= $this->Form->control("bikou",['type'=>'textarea','label'=>false,'value'=>$kaimono["bikou"]]) ?>
        <?php endif; ?>
        </div>
    </div>

    <div class = "mt20 mlv27 mb30">
        <?= $this->Form->button(__("登録")) ?>
    </div>
    <?= $this -> Form -> end(); ?>
</div>