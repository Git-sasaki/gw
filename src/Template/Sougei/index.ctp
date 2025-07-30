<?php $this->assign('title', '送迎記録簿'); ?>

<script>
function OnTouroku2() {
    const rows = document.querySelectorAll('.sougeikrokubo tbody tr:nth-child(odd)');
    let isValid = true;

    for (let index = 0; index < rows.length; index++) {
        const row = rows[index];
        const departureTimeInput = document.querySelector(`input[name="departure_time[${index}]"]`);
        const arrivalTimeInput = document.querySelector(`input[name="arrival_time[${index}]"]`);

        if (!arrivalTimeInput || arrivalTimeInput.value === '' || !departureTimeInput || departureTimeInput.value === '')  continue; // この行はスキップ

        if (departureTimeInput) {
            const departure = new Date(`2000/01/01 ${departureTimeInput.value}`);
            const arrival = new Date(`2000/01/01 ${arrivalTimeInput.value}`);

            if (arrival <= departure) {
                alert('到着時間は出発時間より後の時間を入力してください。');
                arrivalTimeInput.focus();
                isValid = false;
                break; // エラーが出たらループ終了
            }
        }
    }

    return isValid;
}
</script>

<div class="sougei-container">
    <div class="sidemenu antisp">
        <?= $this -> Form -> create(__("View"),["type" => "post","url" => ["action" => "index"]]); ?>
            <h4 class = "sideh4 ml10 pt15">年月日選択</h4>  
            <div class = "odakoku ml10">
                <div class = "sdakoku" style="width: 100px;">
                    <?= $this->Form->control('year',['type'=>'select','label'=>"年",'value'=>$year], $years) ?>
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('month',['type'=>'select','label'=>"月",'value'=>$month], $months) ?> 
                </div>
                <div class = "sdakoku">
                    <?= $this->Form->control('day',['type'=>'select','label'=>"日",'value'=>$day], $days) ?> 
                </div>
            </div>
            <div class="ml10_button mt30 ml10">
                <?= $this->Form->button(__("表示")) ?>
            </div>
        <?= $this -> Form -> end(); ?>
    </div>

    <div class="sougei-main-content">
        <div class = "main2 pt15 mlv25">
            <h4 class="midashih4 mb30"><?= $year." 年 ".$month." 月 ".$day." 日"?></h4>

            <div class = "sticky3">
                <?php if (!empty($SougeiData)) { ?>
                    <?= $this->Form->create(__("View"),
                        ["type" => "post","id" => "editform","url" => ["action" => "register", "?" => ["year" => $year, "month" => $month]]],
                        ['target' => '_blank']); ?>

                    <div>
                        <table class="sougeikrokubo">
                            <thead>
                                <tr>
                                    <th class="_sticky" rowspan="2">利用者名</th>
                                    <th class="_sticky" rowspan="2">種別</th>
                                    <th class="_sticky">出発時間</th>
                                    <th class="_sticky">出発地</th>
                                    <th class="_sticky" rowspan="2">運転者</th>
                                    <th class="_sticky" rowspan="2">同乗者</th>
                                    <th class="_sticky" rowspan="2">車両No・車両車種</th>
                                    <th class="_sticky" rowspan="2">削除</th>
                                </tr>            
                                <tr>
                                    <th class="_sticky">到着時間</th>
                                    <th class="_sticky">到着地</th>
                                </tr>   
                            </thead>
                            <tbody>
                            <?php 
                                if (!empty($SougeiData)) {
                                    $i = 0;
                                    foreach ($SougeiData as $data) :
                                        $departureplace = ($data['type'] == "迎") ? ["地下鉄 琴似駅", "JR琴似駅"] : ["事業所"];
                                        $departurecurrentValue = !empty($data['departure_place']) ? $data['departure_place'] : '';
                                        // 現在の値が基本値に含まれていない場合、配列の先頭に追加
                                        if (!preg_match('/^[\s　]*$/u', $departurecurrentValue) && !empty(trim($departurecurrentValue)) && !in_array($departurecurrentValue, $departureplace, true)) {
                                            array_unshift($departureplace, $departurecurrentValue);
                                        }
                                        
                                        $arrivalplace = ($data['type'] == "迎") ? ["事業所"] : ["地下鉄 琴似駅", "JR琴似駅"];
                                        $arrivalcurrentValue = !empty($data['arrival_place']) ? $data['arrival_place'] : '';
                                        // 現在の値が基本値に含まれていない場合、配列の先頭に追加
                                        if (!preg_match('/^[\s　]*$/u', $arrivalcurrentValue) && !empty(trim($arrivalcurrentValue)) && !in_array($arrivalcurrentValue, $arrivalplace, true)) {
                                            array_unshift($arrivalplace, $arrivalcurrentValue);
                                        }
                            ?>
                                <tr>
                                    <?= $this->Form->hidden("id[$i]", ['value' => $data['id']]) ?>
                                    <td rowspan="2"><?= h($data['name']) ?></td>
                                    <td rowspan="2"><?= h($data['type']) ?></td>
                                    <td><?= $this->Form->text("departure_time[$i]",["type" => "time","value" => $data['departure_time']]); ?></td>
                                    <td>
                                        <?= $this->Form->text("departure_place[$i]", [
                                            'value' => $departurecurrentValue,
                                            'list' => "departure_place_list_$i",
                                            'class' => 'form-control',
                                            'placeholder' => '選択または入力'
                                        ]) ?>
                                        <datalist id="departure_place_list_<?= $i ?>">
                                            <option value=" " label=" ">
                                            <?php foreach ($departureplace as $place): ?>
                                                <option value="<?= h($place) ?>">
                                            <?php endforeach; ?>
                                        </datalist>
                                    </td>
                                    <td rowspan="2"><?= $this->Form->select("driver[$i]",$staffs,['label' => false,'value'=>($data['driver'] ? $data['driver'] : ''),'empty'=>array('0'=>'')]) ?></td>
                                    <td rowspan="2"><?= $this->Form->select("passenger[$i]",$staffs,['label' => false,'value'=>$data['passenger'],'empty'=>array('0'=>'')]) ?></td>
                                    <td rowspan="2"><?= $this->Form->select("car[$i]",$cars,['label' => false,'value'=>$data['car_no'],'empty'=>array('0'=>'')]) ?></td>
                                    <?= $this->Form->hidden("del[$i]", ['value' => 0]) ?>
                                    <td rowspan="2"><?= $this -> Form -> control("del[$i]",["type" => "checkbox","label" => false,"value" => 1,"checked" => false]); ?></td>
                                </tr>
                                <tr>
                                    <td><?= $this->Form->text("arrival_time[$i]",["type" => "time","value" => $data['arrival_time']]); ?></td>
                                    <td>
                                        <?= $this->Form->text("arrival_place[$i]", [
                                            'value' => $arrivalcurrentValue,
                                            'list' => "arrival_place_list_$i",
                                            'class' => 'form-control',
                                            'placeholder' => '選択または入力'
                                        ]) ?>
                                        <datalist id="arrival_place_list_<?= $i ?>">
                                            <option value=" " label=" ">
                                            <?php foreach ($arrivalplace as $place): ?>
                                                <option value="<?= h($place) ?>">
                                            <?php endforeach; ?>
                                        </datalist>
                                    </td>
                                    <td style="display: none;"></td>
                                    <td style="display: none;"></td>
                                    <td style="display: none;"></td>
                                    <td style="display: none;"></td>
                                </tr>
                            <?php 
                                    $i++;
                                endforeach;
                                }
                            ?>
                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <p style="color:red; font-size: 20px; font-weight: bold;">データはありません。</p>
                <?php } ?>
                </div>

            <div class="sougei-register-button-container">
                <?= $this->Form->button("登録",array('onClick' => 'return OnTouroku2()')) ?>
            </div>
            <?= $this -> Form -> end(); ?>
        </div>
    </div>
</div>