<?php
use Cake\ORM\TableRegistry;
use Cake\Log\Log;
?>

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

<?php $this->assign('title', '新規購入申請登録'); ?>

<div class = "main1">
    <h4 class="midashih4 mt30"> 物品購入届</h4>    
    <?= $this->Form->create(__("View"),["type"=>"post","id" => "editform","url"=>["action"=>"register"]]) ?>
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
                <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>date("Y")], $years) ?>
            </div>
            <div class = "sdakoku">
                <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>date("m")], $months) ?>
            </div>
            <div class = "sdakoku">
                <?= $this->Form->control('date',['type'=>'text','label'=>"日",'value'=>date("d")]) ?>
            </div>
            <?php if($saikoflag==1): ?>
                <div class = "w250">
                    <?= $this->Form->control("price",['type'=>'text','label'=>"金額 (半角数字のみで入力)",'value'=>$saiko["price"]]) ?>
                </div>
            <?php else: ?>
                <div class = "w250">
                    <?= $this->Form->control("price",['type'=>'text','label'=>"金額 (半角数字のみで入力)"]) ?>
                </div>
            <?php endif; ?>
            <div class = "w150">
                <?php if($this->request-> getSession()->read('Auth.User.adminfrag') != 1): ?>
                    <?= $this->Form->control('user_id',['type'=>'hidden','label'=>"hidden",'value'=>$session]) ?>
                <?php else: ?>
                    <?= $this->Form->control('user_id',['type'=>'select','label'=>"購入者",'value'=>$session], $users) ?>
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
                if($saikoflag==1) {
                    $attributes = ['name'=>'payment','value'=>$saiko["payment"]]; 
                } else {
                    $attributes = ['name'=>'payment','value'=>'0']; 
                }
                echo $this->Form->radio('payment', $options, $attributes);
            ?>
        </div>
    </div>

    <?php for($i=0; $i<3; $i++): ?>
        <div class = "shinsei">
            <h4 class = "exportdeka">　品目<?=$i+1?>　詳細情報</h4>
            <?php if(empty($saikodetails[$i])): ?>
                <div class = "odakoku ml10">
                    <?php if(!empty($saiko["cinnamon"]) && $i == 0): ?>
                        <div class = "w500">
                            <?= $this->Form->control("cinnamon[$i]",['type'=>'text','label'=>"購入品名",'value'=>$saiko["cinnamon"]]) ?>
                        </div>
                    <?php else: ?>
                        <div class = "w500">
                            <?= $this->Form->control("cinnamon[$i]",['type'=>'text','label'=>"購入品名"]) ?>
                        </div>
                    <?php endif; ?>
                    <div class = "w200">
                        <?= $this->Form->control("detail[$i]",['type'=>'text','label'=>"詳細(個数・長さなど)"]) ?>
                    </div>
                </div>
                <div class = "odakoku mb10">
                    <div class = "w200 ml10">
                        <?= $this->Form->control("shop[$i]",['type'=>'text','label'=>"購入先"]) ?>
                    </div>
                    <div class = "w500 ml10">
                        <?= $this->Form->label("リンク"); ?>
                        <?= $this->Form->text("url[$i]",["type" => "text"]); ?>
                    </div>
                </div>
            <?php else: ?>
                <div class = "odakoku ml10">
                    <div class = "w500">
                        <?= $this->Form->control("cinnamon[$i]",['type'=>'text','label'=>"購入品名",'value'=>$saikodetails[$i]["cinnamon"]]) ?>
                    </div>
                    <div class = "w200">
                        <?= $this->Form->control("detail[$i]",['type'=>'text','label'=>"詳細(個数・長さなど)",'value'=>$saikodetails[$i]["detail"]]) ?>
                    </div>
                </div>
                <div class = "odakoku mb10">
                    <div class = "w200 ml10">
                        <?= $this->Form->control("shop[$i]",['type'=>'text','label'=>"購入先",'value'=>$saikodetails[$i]["shop"]]) ?>
                    </div>
                    <div class = "w500 ml10">
                        <?= $this->Form->label("リンク"); ?>
                        <?= $this->Form->text("url[$i]",["type" => "text",'value'=>$saikodetails[$i]["url"]]); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php endfor; ?>

    <div class = "shinsei">
        <h4 class = "exportdeka">　備考</h4>
        <div style = "margin: 20px 15px 0 10px;">
            <?php if($saikoflag==1): ?>
                <?= $this->Form->control("bikou",['type'=>'textarea','label'=>false,'value'=>$saiko["bikou"]]) ?>
            <?php else: ?>
                <?= $this->Form->control("bikou",['type'=>'textarea','label'=>false]) ?>
            <?php endif; ?>
        </div>
    </div>

    <div class = "mt20 mlv27 mb30">
        <?= $this->Form->button(__("登録")) ?>
    </div>
    <?= $this -> Form -> end(); ?>
    </div>
</div>