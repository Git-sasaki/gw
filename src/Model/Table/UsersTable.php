<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Reports', [
            'className' => 'Users',
            'foreignKey' => 'user',
            'bindingKey' => 'user',
            'joinType' => 'RIGHT']);


        $this->addBehavior('Timestamp');

    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('user')
            ->maxLength('user', 100)
            ->requirePresence('user', 'create')
            ->notEmptyString('user');

        $validator
            ->scalar('name')
            ->maxLength('name', 100)
            ->allowEmptyString('name');

        $validator
            ->scalar('password')
            ->maxLength('password', 100)
            ->requirePresence('password', 'create')
            ->notEmptyString('password');
        
        $validator
            ->scalar('adminfrag')
            ->allowEmptyString('adminfrag', 0, 'create');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['user']), 'uniqueUsername', [
            'errorField' => 'user',
            'message' => 'このユーザー名は既に使用されています。'
        ]);

        return $rules;
    }

    //ログインIDを取得する
    function getCurrentUser() {
        // 現在のCakePHP 4では、セッションはRequestオブジェクトから取得する
        // このメソッドは使用されていないようなので、コメントアウトまたは削除を検討
        return null;
    }
}
