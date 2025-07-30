<?php $cakeDescription = 'Labor stacioグループウェア'; ?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrfToken" content="<?= $this->request->getAttribute('csrfToken') ?>">
    <title>
        <?= $cakeDescription ?>:
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css('base.css') ?>
    <?= $this->Html->css('style.css') ?>
    <?= $this->Html->script('jquery-2.0.3.min.js') ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>

    <!-- メニュー切り替え作業 -->
    <script defer language="javascript" type="text/javascript">
        function menuErabi(){
            const docchi = window.sessionStorage.getItem('menuSession');
            console.log("menuSession:", docchi);  // デバッグ用ログ
            if(docchi == 2) {
                document.getElementById("menu1").style.display = "none";
                document.getElementById("menu2").style.display = "flex";
            } else {
                document.getElementById("menu1").style.display = "flex";
                document.getElementById("menu2").style.display = "none";
            }
        }
        function Display(no){
            if(no == 2){
                document.getElementById("menu1").style.display = "none";
                document.getElementById("menu2").style.display = "flex";
                window.sessionStorage.setItem('menuSession',2);
            }else if(no == 1){
                document.getElementById("menu1").style.display = "flex";
                document.getElementById("menu2").style.display = "none";
                window.sessionStorage.setItem('menuSession',1);
            }
        }
    </script>
</head>

<body onload = "menuErabi()">
<!-- <body> -->
    <?= $this->Flash->render() ?>
    <?php $url = $_SERVER['REQUEST_URI']; ?>
    <?php $host = $_SERVER['HTTP_HOST']; ?>

    <?php if($url != "/users/login" && $url != "/gw/users/login" && $url != "/gw/"): ?>
        <nav class="maxwide topmenu">
    <!--利用者-->
        <?php if($this->request->getSession()->read('Auth.User.adminfrag') != 1): ?>
            <?php if($host == "[::1]:8765"): ?>
                <div class = "odakoku menulinktest" id = "menu1">
            <?php else: ?>
                <div class = "odakoku menulink" id = "menu1">
            <?php endif; ?>

                <?php if($host == "[::1]:8765"): ?>
                    <div class = "vw10 center">
                        <?= $this->Html->image("../img/hanmakanma.png", [
                            'url' => ['controller'=>'tests', 'action' => 'index']
                        ]); ?>
                        <div class = "mt5">テストページ</div>
                    </div>
                <?php endif; ?>

                <div class = "vw10 center">
                    <?= $this->Html->image("../img/stamp.png", [
                        'url' => ['controller'=>'users', 'action' => 'stampn']
                    ]); ?>
                    <div class = "mt5">打刻</div>
                </div>
                <div class = "vw10 center">
                    <?php if($host == "[::1]:8765"): ?>
                        <?= $this->Html->image("../img/shukkinbo.png", [
                            'url' => ['controller'=>'Tests', 'action' => 'edit']
                        ]); ?>
                    <?php else: ?>
                        <?= $this->Html->image("../img/shukkinbo.png", [
                            'url' => ['controller'=>'TimeCards', 'action' => 'editn']
                        ]); ?>
                    <?php endif; ?>
                    <div class = "mt5">出勤簿</div>
                </div>
                <div class = "vw10 center">
                    <?= $this->Html->image("../img/shinsei.png", [
                        'url' => ['controller'=>'Kaimonos', 'action' => 'indexn']
                    ]); ?>
                    <div class = "mt5">購入申請</div>
                </div>
                <div class = "vw10 center">
                    <?= $this->Html->image("../img/check.png", [
                        'url' => ['controller'=>'Users', 'action'=>'ichiran']
                    ]); ?>
                    <div class = "mt5">出勤情報一覧</div>
                </div>
                <div class = "vw10 center">
                    <?= $this->Html->image("../img/ichiran.png", [
                        'url' => ['controller'=>'Reports', 'action'=>'ichiran']
                    ]); ?>
                    <div class = "mt5">作業日報一覧</div>
                </div>
                <div class = "vw10 center">
                    <?= $this->Html->image("../img/edit.png", [
                        'url' => ['controller'=>'Reports', 'action'=>'editn']
                    ]); ?>
                    <div class = "mt5">作業日報登録</div>
                </div>
                <div class = "vw10 center">
                    <?= $this->Html->image("../img/logout.png", [
                        'url' => ['controller'=>'Users', 'action' => 'logout']
                    ]); ?>
                    <div class = "mt5">ログアウト</div>
                </div>
            </div>          
<!-- 職員 -->
    <!-- メニュー1 -->
        <?php else: ?>  
            <?php if($host == "[::1]:8765"): ?>
                <div class = "odakoku menulinktest" id = "menu1">
            <?php else: ?>
                <div class = "odakoku menulink" id = "menu1">
            <?php endif; ?>

            <?php if($host == "[::1]:8765"): ?>
                <div class = "vw10 center">
                    <?= $this->Html->image("../img/hanmakanma.png", [
                        'url' => ['controller'=>'tests', 'action' => 'index']
                    ]); ?>
                    <div class = "mt5">テストページ</div>
                </div>
            <?php endif; ?>

                <div class = "vw10 center">
                    <?= $this->Html->image("../img/stamp.png", [
                        'url' => ['controller'=>'users', 'action' => 'stampn']
                    ]); ?>
                    <div class = "mt5">打刻</div>
                </div>
                <div class = "vw10 center">
                    <?php if($host == "[::1]:8765"): ?>
                        <?= $this->Html->image("../img/shukkinbo.png", [
                            'url' => ['controller'=>'Tests', 'action' => 'edit']
                        ]); ?>
                    <?php else: ?>
                        <?= $this->Html->image("../img/shukkinbo.png", [
                            'url' => ['controller'=>'TimeCards', 'action' => 'editn']
                        ]); ?>
                    <?php endif; ?>
                    <div class = "mt5">出勤簿</div>
                </div>
                <div class = "vw10 center">
                    <?= $this->Html->image("../img/absents.png", [
                        'url' => ['controller' => 'Absents', 'action' => 'indexn']
                    ]); ?>
                    <div class = "mt5">欠勤連絡</div>
                </div>
                <div class = "vw10 center">
                    <?= $this->Html->image("../img/calendar.png", [
                        'url' => ['controller' => 'Calendars', 'action' => 'indexn']
                    ]); ?>
                    <div class = "mt5">スケジュール</div>
                </div>
                <div class = "vw10 center">
                    <?= $this->Html->image("../img/sougei.png", [
                        'url' => ['controller'=>'Sougei', 'action' => 'index']
                    ]); ?>
                    <div class = "mt5">送迎記録簿</div>
                </div>
                <div class = "vw10 center">
                    <?= $this->Html->image("../img/torichan.png", [
                        'url' => ['controller' => 'Reports', 'action' => 'report']
                    ]); ?>
                    <div class = "mt5">日報チェック</div>
                </div>
                <div class = "vw10 center">
                    <?= $this->Html->image("../img/check.png", [
                        'url' => ['controller' => 'Users', 'action' => 'ichiran']
                    ]); ?>
                    <div class = "mt5">出勤情報一覧</div>
                </div>
                <div class = "vw10 center">
                    <?= $this->Html->image("../img/export.png", [
                        'url' => ['controller'=>'Prints', 'action'=>'indexn']
                    ]); ?>
                    <div class = "mt5">各種出力</div>
                </div>
                <div class = "vw10 center">
                    <?= $this->Html->image("../img/logout.png", [
                        'url' => ['controller' => 'Users', 'action' => 'logout']
                    ]); ?>
                    <div class = "mt5">ログアウト</div>
                </div>
                <div class = "vw10 center">
                    <!--<a href="javascript:;" onclick="Display(2)"><img src = "../img/kiri2.png"></a>-->
                    <a href="javascript:;" onclick="Display(2)">
                        <?= $this->Html->image("kiri2.png") ?>
                    </a>
                    <div class = "mt5">切り替え</div>
                </div>
            </div>
    <!-- メニュー2 -->
            <?php if($host == "[::1]:8765"): ?>
                <div class = "odakoku menulinktest" id = "menu2">
            <?php else: ?>
                <div class = "odakoku menulink" id = "menu2">
            <?php endif; ?>

            <?php if($host == "[::1]:8765"): ?>
                <div class = "vw10 center">
                    <?= $this->Html->image("../img/hanmakanma.png", [
                        'url' => ['controller'=>'tests', 'action' => 'index']
                    ]); ?>
                    <div class = "mt5">テストページ</div>
                </div>
            <?php endif; ?>
                <div class = "vw10 center">
                    <?= $this->Html->image("../img/thisman.png", [
                        'url' => ['controller' => 'Users', 'action' => 'indexn']
                    ]); ?>
                    <div class = "mt5">ユーザー</div>
                </div>
                <div class = "vw10 center">
                    <?= $this->Html->image("../img/desk.png", [
                        'url' => ['controller'=>'Workplaces', 'action' => 'indexn']
                    ]); ?>
                    <div class = "mt5">事業所情報</div>
                </div>
                <div class = "vw10 center">
                    <?= $this->Html->image("../img/shinsei.png", [
                        'url' => ['controller'=>'Kaimonos', 'action' => 'kokodozo','?'=>['type'=>0]]
                    ]); ?>
                    <div class = "mt5">購入申請</div>
                </div>
                <div class = "vw10 center">
                    <?= $this->Html->image("../img/calendar.png", [
                        'url' => ['controller' => 'Calendars', 'action' => 'indexn']
                    ]); ?>
                    <div class = "mt5">スケジュール</div>
                </div>
                <div class = "vw10 center">
                    <?= $this->Html->image("../img/home.png", [
                        'url' => ['controller' => 'Remotes', 'action' => 'indexn']
                    ]); ?>
                    <div class = "mt5">在宅勤務</div>
                </div>
<!--
                <div class = "vw10 center">
                    <?= $this->Html->image("../img/sougeiset.png", [
                        'url' => ['controller' => 'Remotes', 'action' => 'indexn']
                    ]); ?>
                    <div class = "mt5">送迎データ記録</div>
                </div>
-->
                <div class = "vw10 center">
                    <?= $this->Html->image("../img/ichiran.png", [
                        'url' => ['controller' => 'Reports','action' => 'kokodozo','?'=>['type'=>0]]
                    ]); ?>
                    <div class = "mt5">日報一覧</div>
                </div>
                <div class = "vw10 center">
                    <?= $this->Html->image("../img/settings.png", [
                        'url' => ['controller' => 'Attendances', 'action' => 'settings']
                    ]); ?>
                    <div class = "mt5">基本設定</div>
                </div>
                <div class = "vw10 center">
                    <?= $this->Html->image("../img/logout.png", [
                        'url' => ['controller' => 'Users', 'action' => 'logout']
                    ]); ?>
                    <div class = "mt5">ログアウト</div>
                </div>
                <div class = "vw10 center">
                    <!--<a href="javascript:;" onclick="Display(1)"><img src = "../img/kiri2.png"></a>-->
                    <a href="javascript:;" onclick="Display(1)">
                        <?= $this->Html->image("kiri2.png") ?>
                    </a>
                    <div class = "mt5">切り替え</div>
                </div>
            </div>
            <?php endif; ?>
        </nav>
    <?php endif; ?>

<!-- スマホ版 -->
    <?php if($url != "/users/login" && $url != "/gw/users/login" && $url != "/gw/"): ?>
    <nav class="spnavi">
        <div class = "odakoku menulink">
            <div class = "pc25 center">
                <?= $this->Html->image("../img/stamp.png", [
                    'url' => ['controller'=>'users', 'action' => 'stampn']
                ]); ?>
                <div class = "mt5">打刻</div>
            </div>
            <div class = "pc25 center">
                <?= $this->Html->image("../img/shukkinbo.png", [
                    'url' => ['controller'=>'TimeCards', 'action' => 'editn']
                ]); ?>
                <div class = "mt5">出勤簿</div>
            </div>
            <div class = "pc25 center">
                <?= $this->Html->image("../img/edit.png", [
                    'url' => ['controller'=>'Reports', 'action'=>'editn']
                ]); ?>
                <div class = "mt5">日報登録</div>
            </div>
            <div class = "pc25 center">
                <?= $this->Html->image("../img/logout.png", [
                    'url' => ['controller'=>'Users', 'action' => 'logout']
                ]); ?>
                <div class = "mt5">ログアウト</div>
            </div>
        </div>
    </nav>
    <?php endif; ?>

    <?= $this->fetch('content') ?>
    </div>

    <footer>
    </footer>
</html>
