<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CalendarsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('calendars');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
        ]);
    }
}
