<?php
namespace App\View;

use Cake\View\View;

/**
 * Application View
 *
 * Your application's default view class
 *
 * @link https://book.cakephp.org/4/en/views.html#the-app-view
 */
class AppView extends View
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading helpers.
     *
     * e.g. `$this->loadHelper('Html');`
     *
     * @return void
     */
    public function initialize(): void
    {
    }

    /**
     * A型/B型の表示用ヘルパー関数
     *
     * @param int|null $userType 0=A型、1=B型、null=職員
     * @return string 表示用文字列
     */
    public function getUserTypeLabel($userType)
    {
        if ($userType === null) {
            return '職員';
        } elseif ($userType == 0) {
            return 'A型';
        } elseif ($userType == 1) {
            return 'B型';
        } else {
            return '不明';
        }
    }
} 