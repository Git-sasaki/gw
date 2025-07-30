<?php $this->assign('title', '出勤情報一覧'); ?>

<div class = "main1">
    <h4 class="midashih4 mt30"> 出勤情報一覧</h4>
    <div class = "odakoku" style = "justify-content:center;">
        <div>
            <h4 class = "titleh4 mt15">職員</h4>
            <table class="table01 table04">
                <thead>
                    <tr>
                        <th scope="col" style = "width: 9.5vw">名前</th>
                        <th scope="col" style = "width: 12vw">状態</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($staffs as $staff): ?>
                    <tr>
                        <td><?= $staff["name"] ?></td>
                        <?php if($staff["status"]==0): ?>
                            <td><?= $statustext[$staff["status"]] ?></td>
                        <?php elseif($staff["status"]==1 && $staff["user_id"] != $myid): ?>
                            <td style = "background: rgb(68, 240, 68);">
							<?php if (empty($staff["shisetsugai"])): ?>	
								<?= $statustext[$staff["status"]] ?></td>
							<?php else: ?>
								<?= $statustext[$staff["status"]] . "：" . $staff["shisetsugai"] ?></td>
							<?php endif; ?>
                        <?php elseif($staff["status"]==1 && $staff["user_id"] == $myid): ?>
                            <!-- 離席中へ切り替えるボタンを入力 -->
                            <td style = "background: rgb(68, 240, 68);">
                                <?= $this->Html->link(
                                    $statustext[$staff["status"]],
                                    ['action' =>'register',"?"=>["id"=>$staff["user_id"],"type"=>1]],
                                    ['class' => 'kuro'],
                                ); ?>
                            </td>
                        <?php elseif($staff["status"]==8): ?>
                            <td style = "background: rgb(68, 240, 68);"><?= $statustext[$staff["status"]] ?></td>
                        <?php elseif($staff["status"]==2): ?>
                            <td style = "background: rgb(245, 116, 116);"><?= $statustext[$staff["status"]] ?></td>
                        <?php elseif($staff["status"]==3 || $staff["status"]==6): ?>
                            <td style = "background: #b0c4de;"><?= $statustext[$staff["status"]] ?></td>
                        <?php elseif($staff["status"]==4): ?>
                            <td style = "background: #ffd700;"><?= $statustext[$staff["status"]] ?></td>
                        <?php elseif($staff["status"]==5): ?>
                            <td style = "background: #da70d6;"><?= $statustext[$staff["status"]] ?></td>
                        <?php elseif($staff["status"]==7 && $staff["user_id"] != $myid): ?>
                            <td style = "background: rgb(245, 116, 116);"><?= $statustext[$staff["status"]] ?></td>
                        <?php elseif($staff["status"]==7 && $staff["user_id"] == $myid): ?>
                            <!-- 離席中から切り替えるボタンを入力 -->
                            <td style = "background: rgb(245, 116, 116);">
                                <?= $this->Html->link(
                                    $statustext[$staff["status"]],
                                    ['action' =>'register',"?"=>["id"=>$staff["user_id"],"type"=>1]],
                                    ['class' => 'kuro'],
                                ); ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    
    <div class = "space"></div>

    <div>
        <?php for($i = 0; $i < count($workPlaces); $i++): ?>
			<!-- 削除された場所は飛ばす -->
            <?php if (!($workPlaces[$i]["del"])): ?>
	            <h4 class = "titleh4 mt15"><?= $workPlaces[$i]["name"] ?></h4>
				<table class="table01 table03">
					<thead>
						<tr>
							<th scope="col" style = "width: 9.5vw">名前</th>
							<th scope="col" style = "width: 9.5vw">状態</th>
							<th scope="col" style = "width: 10vw">日報</th>
							<?php if($this->request-> getSession()->read('Auth.User.adminfrag') == 1): ?>
								<th scope="col" style = "width: 9.5vw">担当者入力</th>
							<?php endif; ?>
						</tr>
					</thead>
					<tbody>
						
						<!-- 勤務者がいる場合に表示 -->
						<?php if(array_key_exists($i, $users)): ?>
							<?php for($j = 0; $j < count($users[$i]); $j++): ?>
							<tr>
								<!-- 名前 -->
								<td><?= $users[$i][$j]["name"] ?></td>
								
								<!-- 状態 -->
								<?php if($users[$i][$j]["status"]==0): ?>
									<td><?= $statustext[$users[$i][$j]["status"]] ?></td>
								<?php elseif($users[$i][$j]["status"]==1 || $users[$i][$j]["status"]==8 || $users[$i][$j]["status"]==9): ?>
									<td style = "background: rgb(68, 240, 68);"><?= $statustext[$users[$i][$j]["status"]] ?></td>
								<?php elseif($users[$i][$j]["status"]==2): ?>
									<td style = "background-color: rgb(245, 116, 116);"><?= $statustext[$users[$i][$j]["status"]] ?></td>
								<?php elseif($users[$i][$j]["status"]==3 || $users[$i][$j]["status"]==6): ?>
									<td style = "background-color: #b0c4de;"><?= $statustext[$users[$i][$j]["status"]] ?></td>
								<?php elseif($users[$i][$j]["status"]==4): ?>
									<td style = "background-color: #ffd700;"><?= $statustext[$users[$i][$j]["status"]] ?></td>
								<?php elseif($users[$i][$j]["status"]==5): ?>
									<td style = "background-color: #da70d6;"><?= $statustext[$users[$i][$j]["status"]] ?></td>
								<?php endif; ?>

								<!-- 日報 -->
								<?php if($this->request-> getSession()->read('Auth.User.adminfrag') == 0): ?>
									<?php if($users[$i][$j]["reportcheck"]==0): ?>
										<td></td>
									<?php else: ?>
										<td>済</td>
									<?php endif; ?>
								<?php elseif($this->request-> getSession()->read('Auth.User.adminfrag') == 1): ?>
									<?php if($users[$i][$j]["reportcheck"]==0): ?>
										<td></td>
									<?php elseif(($users[$i][$j]["reportcheck"]==1)): ?>
										<td style = "background-color: rgb(245, 116, 116);">
											<?= $this -> Form -> create(__("View"),[
												"type" => "post",
												"url" => ["controller" => "reports","action" => "getquery0"]]); ?>
											<?= $this->Form->control('year',['type'=>'hidden','value'=>date('Y')]) ?>
											<?= $this->Form->control('month',['type'=>'hidden','value'=>date('m')]) ?>
											<?= $this->Form->control('date',['type'=>'hidden','value'=>date('d')]) ?>
											<?= $this->Form->control('id',['type'=>'hidden','value'=>$users[$i][$j]["id"]]) ?>
											<?= $this->Form->button($reptext[$users[$i][$j]["reportcheck"]],["class"=>"ichibtn kuro"]) ?>
											<?= $this->Form->end(); ?>
										</td>
									<?php elseif(($users[$i][$j]["reportcheck"]==2)): ?>
										<td>
											<?= $this -> Form -> create(__("View"),[
												"type" => "post",
												"url" => ["controller" => "reports","action" => "getquery0"]]); ?>
											<?= $this->Form->control('year',['type'=>'hidden','value'=>date('Y')]) ?>
											<?= $this->Form->control('month',['type'=>'hidden','value'=>date('m')]) ?>
											<?= $this->Form->control('date',['type'=>'hidden','value'=>date('d')]) ?>
											<?= $this->Form->control('id',['type'=>'hidden','value'=>$users[$i][$j]["id"]]) ?>
											<?= $this->Form->button($reptext[$users[$i][$j]["reportcheck"]],["class"=>"ichibtn kuro"]) ?>
											<?= $this->Form->end(); ?>
										</td>
									<?php endif; ?>
								<?php endif; ?>

								<!-- 担当者入力 -->
								<?php if($this->request-> getSession()->read('Auth.User.adminfrag') == 1): ?>
									<?php if($users[$i][$j]["status"] == 0): ?>
										<td></td>
									<?php elseif($users[$i][$j]["staffcheck"]==1): ?>
										<td style = "background-color: rgb(245, 116, 116);">
											<?= $this->Html->link(
												$tantoutext[$users[$i][$j]["staffcheck"]],
												['action' =>'register',"?"=>["id"=>$users[$i][$j]["id"],"type"=>0]],
												['class' => 'bla'],
											); ?>
										</td>
									<?php elseif($users[$i][$j]["staffcheck"]==2): ?>
										<td>
											<?= $this->Html->link(
												$tantoutext[$users[$i][$j]["staffcheck"]],
												['controller' => 'TimeCards', 'action' =>'getquery1', $users[$i][$j]["id"]],
												['class' => 'bla'],
											); ?>
										</td>
									<?php else: ?>
										<td></td>
									<?php endif; ?>
								<?php endif; ?>
							</tr>    
							<?php endfor; ?>
						<?php endif ?>
					</tbody>
				</table>
			<?php endif ?>
        <?php endfor; ?>
    </div>
</div>
<br>