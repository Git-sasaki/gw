<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class WorkplacesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('workplaces');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->boolean('sub')
            ->notEmptyString('sub');

        $validator
            ->boolean('del')
            ->notEmptyString('del');

        return $validator;
    }
} 