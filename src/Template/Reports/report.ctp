<?php $this->assign('title', '日報忘れ一覧'); ?>

<div class = "main1">
    <div class = "odakoku mt30" style = "justify-content:center;">
        <div>
            <h4 class = "titleh4 mt15">日報記入忘れ</h4>
            <?php if(!empty($wasuremons)): ?>
                <table class="table01 table04">
                    <thead>
                        <tr>
                            <th scope="col" style = "width: 11vw">ユーザー名</th>
                            <th scope="col" style = "width: 11vw">日時</th>
                            <th scope="col" style = "width: 9.5vw">状態</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($wasuremons as $wasuremon): ?>
                        <tr>
                            <td><?= $wasuremon["user_name"] ?></td>
                            <td><?= date('Y年m月d日',$wasuremon["date"]) ?></td>
                            <td><?= $statustext[$wasuremon["status"]] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class = "naidesu mt15">
                    <div class = "arimasen">ありません</div>
                </div>
            <?php endif; ?>
        </div>

        <div class = "space"></div>

        <div>
            <h4 class = "titleh4 mt15">日誌記入忘れ</h4>
            <?php if(!empty($mikans)): ?>
                <table class="table01 table04">
                    <thead>
                        <tr>
                            <th scope="col" style = "width: 11vw">ユーザー名</th>
                            <th scope="col" style = "width: 11vw">日時</th>
                            <th scope="col" style = "width: 9.5vw">状態</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($mikans as $mikan): ?>
                        <tr>
                            <td><?= $mikan["user_name"] ?></td>
                            <td><?= date('Y年m月d日',$mikan["date"]) ?></td>
                            <?php if($mikan["status"] == 2): ?>
                                <td style = "background-color: rgb(245, 116, 116);"><?= $statustext[$mikan["status"]] ?></td>
                            <?php elseif($mikan["status"] == 1): ?>
                                <td style = "background: rgb(68, 240, 68);">
                                    <?= $this -> Form -> create(__("View"),[
                                            "type" => "post",
                                            "url" => ["controller" => "reports","action" => "getquery0"]]); ?>
                                    <?= $this->Form->control('year',['type'=>'hidden','value'=>date('Y',$mikan["date"])]) ?>
                                    <?= $this->Form->control('month',['type'=>'hidden','value'=>date('m',$mikan["date"])]) ?>
                                    <?= $this->Form->control('date',['type'=>'hidden','value'=>date('d',$mikan["date"])]) ?>
                                    <?= $this->Form->control('id',['type'=>'hidden','value'=>$mikan["user_id"]]) ?>
                                    <?= $this->Form->button($statustext[$mikan["status"]],["class"=>"ichibtn kuro"]) ?>
                                    <?= $this->Form->end(); ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class = "naidesu mt15">
                    <div class = "arimasen">ありません</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<br>