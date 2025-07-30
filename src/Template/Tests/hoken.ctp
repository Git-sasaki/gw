<?php $this->assign('title', 'エラーサンプル'); ?>

<div class = "main1">
<h4 class="titleh4 mt20">　4大エラーサンプル</h4>
<br>
<?= $this->Html->link('i18nFormatエラー', ['action'=>'stamp']); ?><br>
    <p>
        CakePHPの日時のフォーマットであるi18nFormatは、NULLに対してエラーを吐きます。<br>
        データの受け取り時にNULLになり得る可能性を排除しきれていないと発生するので、よく見かけると思います。
    </p>
<?= $this->Html->link('ページ指定ミス', ['action'=>'nainai']); ?><br>
    <p>
        これは基本ウッカリで発生するので、すぐわかるはずです。<br>
    </p>
<?= $this->Html->link('許可されていないページ', ['action'=>'akan']); ?><br>
    <p>
        AppControllerでAllowの中に入っていないページを指定すると出てきます。<br>
        新しい機能を追加しようとページを新しく作るころにはつい忘れがちなので、時々見ることになるかもしれません。
    </p>
<?= $this->Html->link('Yasumi読み込みエラー', ['action'=>'stamp2']); ?><br>
    <p>
        休日を一覧で出してくれるライブラリのyasumiにyearが適切に指定されていない場合の画面です。<br>
        何らかの理由でyasumi自体が読み込まれない場合はエラー画面に" Class 'Yasumi\Yasumi' not found "と出てきます。
    </p>
