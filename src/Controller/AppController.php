<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\ORM\TableRegistry;

use Cake\Controller\Controller;
use Cake\Event\Event;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler', [
            'enableBeforeRedirect' => false,
        ]);
        $this->loadComponent('Flash');

        /*
         * Enable the following component for recommended CakePHP security settings.
         * see https://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        //$this->loadComponent('Security');

        //
        // 認証テーブル users -> admins切替(デフォルト:Users)

        //追加したコード
        $this->loadComponent('Auth', [
            'authenticate' => [
                'User' => [],
                'Form' => [
                    'fields' => [
                        'user' => 'user',
                        'password' => 'password'
                    ]
                ]
            ],
            'loginAction' => [
                'controller' => 'Users',
                'action' => 'login'
            ],
            'authorize' => ['Controller'],
            'unauthorizedRedirect' => $this->referer()
        ]);
 
        $this->Auth->allow(['login','logout','index','add','delete','index2','register','register2','list','view','edit',
        'edit2','update','stamp','stamp2','calendar','hiddencommand','printout','test','export','report','viewdetail',
        'excelexport','service','attendance','report','spdf','updf','umpdf','getquery0','getquery1','indexn','settings',
        'default','dregister','absent','srecords','csv','schedule','scheduleAjax','stampn','editn','detailn','hoken',
        'ichiran','newn','registern','nisshis','remote','jisseki','support','chouka','sanchouka','kokodozo','pdf','new',
        'syokuji','syukkinhyou','kissyokuhyou','sougeiexcel','sougeiregister','sougeikirokubo']);
    }
}
