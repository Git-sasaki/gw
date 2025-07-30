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