<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ReportDetail Entity
 *
 * @property int $id
 * @property int $report_id
 * @property int $linenumber
 * @property \Cake\I18n\FrozenTime $starttime
 * @property \Cake\I18n\FrozenTime $endtime
 * @property string $item
 * @property string $content
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\Report $report
 */
class ReportDetail extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'report_id' => true,
        'linenumber' => true,
        'starttime' => true,
        'endtime' => true,
        'item' => true,
        'content' => true,
        'created' => true,
        'modified' => true,
        'report' => true,
    ];
}
