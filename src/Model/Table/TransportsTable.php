<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class TransportsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('transports');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo('StaffUsers', [
            'className' => 'Users',
            'foreignKey' => 'staff_id'
        ]);

        $this->belongsTo('SubstaffUsers', [
            'className' => 'Users',
            'foreignKey' => 'substaff_id'
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->date('date')
            ->requirePresence('date', 'create')
            ->notEmptyDate('date');

        $validator
            ->integer('user_id')
            ->requirePresence('user_id', 'create')
            ->notEmptyString('user_id');

        $validator
            ->integer('kind')
            ->requirePresence('kind', 'create')
            ->notEmptyString('kind');

        $validator
            ->time('hatsutime')
            ->allowEmptyTime('hatsutime');

        $validator
            ->time('taykutime')
            ->allowEmptyTime('taykutime');

        $validator
            ->scalar('hatsuplace')
            ->maxLength('hatsuplace', 100)
            ->requirePresence('hatsuplace', 'create')
            ->notEmptyString('hatsuplace');

        $validator
            ->scalar('tyakuplace')
            ->maxLength('tyakuplace', 100)
            ->requirePresence('tyakuplace', 'create')
            ->notEmptyString('tyakuplace');

        $validator
            ->integer('staff_id')
            ->allowEmptyString('staff_id');

        $validator
            ->integer('substaff_id')
            ->allowEmptyString('substaff_id');

        $validator
            ->integer('car')
            ->allowEmptyString('car');

        return $validator;
    }
} 