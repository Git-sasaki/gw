<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ReportDetails Model
 *
 * @property \App\Model\Table\ReportsTable&\Cake\ORM\Association\BelongsTo $Reports
 *
 * @method \App\Model\Entity\ReportDetail get($primaryKey, $options = [])
 * @method \App\Model\Entity\ReportDetail newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ReportDetail[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ReportDetail|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ReportDetail saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ReportDetail patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ReportDetail[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ReportDetail findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ReportDetailsTable extends Table
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

        $this->setTable('report_details');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Reports', [
            'foreignKey' => 'report_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    // public function validationDefault(Validator $validator)
    // {
    //     $validator
    //         ->integer('id')
    //         ->allowEmptyString('id', null, 'create');

    //     $validator
    //         ->integer('linenumber')
    //         ->requirePresence('linenumber', 'create')
    //         ->notEmptyString('linenumber');

    //     $validator
    //         ->time('starttime')
    //         ->requirePresence('starttime', 'create')
    //         ->notEmptyTime('starttime');

    //     $validator
    //         ->time('endtime')
    //         ->requirePresence('endtime', 'create')
    //         ->notEmptyTime('endtime');

    //     $validator
    //         ->scalar('item')
    //         ->notEmpty('item','項目を入力してください');

    //     $validator
    //         ->scalar('reportcontent')
    //         ->notEmpty('reportcontent','内容を入力してください');

    //     return $validator;
    // }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['report_id'], 'Reports'));

        return $rules;
    }
}
