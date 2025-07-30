<?php $this->assign('title', 'ログイン'); ?>

<div class="main4">
<h4 class = "titleh4 pt35 mb35 centermoji">Labor stacioグループウェア</h4>
    <?= $this->Form->create() ?>
    <div class = "loginform centermonot">
        <div class = "w250 pt30 mt30 centermonot">
            <?= $this->Form->control('user',['label'=>'ユーザー名']) ?>
        </div>
        <div class = "w250 centermono">
            <?= $this->Form->control('password',['label'=>'パスワード']) ?>
        </div>
        <div class = "loginbutton">
            <?= $this->Form->button('ログイン',['class'=>'loginbutton']) ?>
        </div>
    </div>
    <?= $this->Form->end() ?>
</div>