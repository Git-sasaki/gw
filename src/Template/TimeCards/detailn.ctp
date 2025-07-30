<?php $this->assign('title', '出勤情報詳細'); ?>

<div class = "main1">
    <h4 class="midashih4 mt30 mb30">　<?=$year.' 年 '.$month.' 月 　'.$username.' さん' ?></h4>
        <table class="alltable">
            <tr>
                <th class = "allhead">合計勤務時間</td>
                <td class = "alldetail"><?= $alltime ?></td>
                <th class = "allhead">合計休憩時間</td>
                <td class = "alldetail"><?= $allrest ?></td>
            </tr>
            <tr>
                <th class = "allhead">全出勤日</th>
                <td class = "alldetail"><?= $allworkdays."日"?></td>
                <th class = "allhead">出勤率</th>
                <td class = "alldetail"><?= $percent ?></td>
            </tr>
            <tr>
                <th class = "allhead">公休</th>
                <td class = "alldetail"><?= $allkoukyu."回" ?></td>
                <th class = "allhead">有休</th>
                <td class = "alldetail"><?= $allpaid."回" ?></td>
            </tr>
            <tr>
                <th class = "allhead">欠勤</th>
                <td class = "alldetail"><?= $allkekkin."回" ?></td>
                <th class = "allhead">食事提供</th>
                <td class = "alldetail"><?= $allmeshi."回" ?></td>
            </tr>
            <tr>
                <th class = "allhead">医療連携</th>
                <td class = "alldetail"><?= $allmedical."回" ?></td>
                <th class = "allhead">施設外支援</th>
                <td class = "alldetail"><?= $allsupport."回" ?></td>
            </tr>
        </table>
    <p class = "bun">
        ※1 全出勤日：月の日数から8を引いたもの<br>
        ※2 (出勤率) = (出勤した日) ÷ (全出勤日) × 100
    </p>