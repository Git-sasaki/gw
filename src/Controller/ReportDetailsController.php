<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * ReportDetails Controller
 *
 * @property \App\Model\Table\ReportDetailsTable $ReportDetails
 *
 * @method \App\Model\Entity\ReportDetail[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ReportDetailsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Reports'],
        ];
        $reportDetails = $this->paginate($this->ReportDetails);

        $this->set(compact('reportDetails'));
    }

    /**
     * View method
     *
     * @param string|null $id Report Detail id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $reportDetail = $this->ReportDetails->get($id, [
            'contain' => ['Reports'],
        ]);

        $this->set('reportDetail', $reportDetail);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $reportDetail = $this->ReportDetails->newEntity();
        if ($this->request->is('post')) {
            $reportDetail = $this->ReportDetails->patchEntity($reportDetail, $this->request->getData());
            if ($this->ReportDetails->save($reportDetail)) {
                $this->Flash->success(__('The report detail has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The report detail could not be saved. Please, try again.'));
        }
        $reports = $this->ReportDetails->Reports->find('list', ['limit' => 200]);
        $this->set(compact('reportDetail', 'reports'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Report Detail id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $reportDetail = $this->ReportDetails->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $reportDetail = $this->ReportDetails->patchEntity($reportDetail, $this->request->getData());
            if ($this->ReportDetails->save($reportDetail)) {
                $this->Flash->success(__('The report detail has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The report detail could not be saved. Please, try again.'));
        }
        $reports = $this->ReportDetails->Reports->find('list', ['limit' => 200]);
        $this->set(compact('reportDetail', 'reports'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Report Detail id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $reportDetail = $this->ReportDetails->get($id);
        if ($this->ReportDetails->delete($reportDetail)) {
            $this->Flash->success(__('The report detail has been deleted.'));
        } else {
            $this->Flash->error(__('The report detail could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
