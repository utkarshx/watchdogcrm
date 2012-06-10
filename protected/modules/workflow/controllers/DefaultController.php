<?php
/*********************************************************************************
 * The X2CRM by X2Engine Inc. is free software. It is released under the terms of 
 * the following BSD License.
 * http://www.opensource.org/licenses/BSD-3-Clause
 * 
 * X2Engine Inc.
 * P.O. Box 66752
 * Scotts Valley, California 95066 USA
 * 
 * Company website: http://www.x2engine.com 
 * Community and support website: http://www.x2community.com 
 * 
 * Copyright � 2011-2012 by X2Engine Inc. www.X2Engine.com
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification, 
 * are permitted provided that the following conditions are met:
 * 
 * - Redistributions of source code must retain the above copyright notice, this 
 *   list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice, this 
 *   list of conditions and the following disclaimer in the documentation and/or 
 *   other materials provided with the distribution.
 * - Neither the name of X2Engine or X2CRM nor the names of its contributors may be 
 *   used to endorse or promote products derived from this software without 
 *   specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND 
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED 
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. 
 * IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, 
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, 
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, 
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF 
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE 
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED 
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 ********************************************************************************/

class DefaultController extends x2base {


	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules() {
		return array(
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('index','view','getWorkflow','getStageDetails','updateStageDetails','startStage','completeStage','revertStage','getStageMembers'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','create','update','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	// Load model
	public function loadModel($id) {
		$model=Workflow::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	// Lists all workflows
	public function actionIndex() {
		$dataProvider = new CActiveDataProvider('Workflow');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	// Admin view of all workflows
	public function actionAdmin() {
		$dataProvider = new CActiveDataProvider('Workflow');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
		// $model=new Workflow('search');
		// $model->unsetAttributes();  // clear any default values
		// if(isset($_GET['Workflow']))
			// $model->attributes=$_GET['Workflow'];

		// $this->render('admin',array(
			// 'model'=>$model,
		// ));
	}

	// Displays workflow table/funnel diagram
	public function actionView($id) {
	
		if(isset($_GET['stage']) && is_numeric($_GET['stage']))
			$viewStage = $_GET['stage'];
		else
			$viewStage = null;

		$this->render('view',array(
			'model'=>$this->loadModel($id),'viewStage'=>$viewStage
		));
	}

	// Creates a new Workflow model
	// Creates 1 or more associated WorkflowStage models
	// If creation is successful, the browser will be redirected to the 'view' page.
	public function actionCreate() {
	
	
	
		$workflowModel=new Workflow;

		$stages = array();
		
		if(isset($_POST['Workflow'], $_POST['WorkflowStages'])) {
		
		
			$workflowModel->attributes = $_POST['Workflow'];
			if($workflowModel->save()) {
				$validStages = true;
				for($i=0; $i<count($_POST['WorkflowStages']); $i++) {
					
					$stages[$i] = new WorkflowStage;
					$stages[$i]->workflowId = $workflowModel->id;
					$stages[$i]->attributes = $_POST['WorkflowStages'][$i+1];
					$stages[$i]->stageNumber = $i+1;
					$stages[$i]->roles = $_POST['WorkflowStages'][$i+1]['roles'];
					if(empty($stages[$i]->roles) || in_array('',$stages[$i]->roles))
						$stages[$i]->roles = array();

					if(!$stages[$i]->validate())
						$validStages = false;
				}

				if($validStages) {

					foreach($stages as &$stage) {
						$stage->save();
						foreach($stage->roles as $roleId)
							Yii::app()->db->createCommand()->insert('x2_role_to_workflow',array(
								'stageId'=>$stage->id,
								'roleId'=>$roleId,
								'workflowId'=>$workflowModel->id,
							));
					}
					if($workflowModel->save())
						$this->redirect(array('view','id'=>$workflowModel->id));
				}
			}
		}

		$this->render('create',array(
			'model'=>$workflowModel,
		));
	}

	// Updates a particular model
	// Deletes and recreates all associated WorkflowStage models
	// If update is successful, the browser will be redirected to the 'view' page.
	public function actionUpdate($id) {
		$workflowModel = $this->loadModel($id);

		$stages = array();
		// echo '<html><body>'.var_dump($_POST['WorkflowStages']).'</body></html>';

		// die(var_dump($_POST['WorkflowStages']));
		if(isset($_POST['Workflow'], $_POST['WorkflowStages'])) {

			$validStages = true;
			for($i=0; $i<count($_POST['WorkflowStages']); $i++) {
				
				$stages[$i] = new WorkflowStage;
				$stages[$i]->workflowId = $id;
				$stages[$i]->attributes = $_POST['WorkflowStages'][$i+1];
				$stages[$i]->stageNumber = $i+1;
				$stages[$i]->roles = $_POST['WorkflowStages'][$i+1]['roles'];
				if(empty($stages[$i]->roles) || in_array('',$stages[$i]->roles))
					$stages[$i]->roles = array();

				if(!$stages[$i]->validate())
					$validStages = false;
			}

			$workflowModel->attributes = $_POST['Workflow'];
			
			if($validStages && $workflowModel->validate()) {
			
				Yii::app()->db->createCommand()->delete('x2_workflow_stages','workflowId='.$workflowModel->id);	// delete old stages
				Yii::app()->db->createCommand()->delete('x2_role_to_workflow','workflowId='.$id);	// delete role stuff too
				foreach($stages as &$stage) {
					$stage->save();
					foreach($stage->roles as $roleId)
						Yii::app()->db->createCommand()->insert('x2_role_to_workflow',array(
							'stageId'=>$stage->id,
							'roleId'=>$roleId,
							'workflowId'=>$id,
						));
				}
				$workflowModel->save();

			}
		}
		$this->render('update',array(
			'model'=>$workflowModel,
		));
	}

	// Deletes Workflow model
	// Associated WorkflowStage models will automatically be deleted by the DB
	public function actionDelete($id) {
		if(Yii::app()->request->isPostRequest) {
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}


	public function actionGetWorkflow($workflowId,$modelId,$type) {
	
		if(is_numeric($workflowId) && is_numeric($modelId) && ctype_alpha($type)) {
			$workflowStatus = Workflow::getWorkflowStatus($workflowId,$modelId,$type);
			// echo var_dump($workflowStatus);
			echo Workflow::renderWorkflow($workflowStatus);
		}
	}
	
	public function actionGetStageDetails($workflowId,$stage,$modelId,$type) {
		if(is_numeric($workflowId) && is_numeric($stage) && is_numeric($modelId) && ctype_alpha($type)) {
			
			$model = Workflow::getWorkflowDetails($workflowId,$stage,$modelId,$type);
			
			$this->renderPartialAjax('_workflowDetail',array('model'=>$model),false);
			
		}
	}
	
	public function actionUpdateStageDetails($id) {
	
		$model = CActiveRecord::model('Actions')->findByPk($id);
		$model->setScenario('workflow');
		
		if(isset($model, $_POST['Actions'])) {
		
			$model->createDate = $this->parseDate($_POST['Actions']['createDate']);
			$model->completeDate = $this->parseDate($_POST['Actions']['completeDate']);
			$model->completedBy = $_POST['Actions']['completedBy'];
			$model->actionDescription = $_POST['Actions']['actionDescription'];
			
			// don't save if createDate isn't valid
			if($model->createDate === false)
				return;
			if($model->completeDate === false) {
				$model->complete = 'No';
				$model->completedBy = null;
			} else {
				if($model->completeDate < $model->createDate)
					$model->completeDate = $model->createDate;	// we can't have the completeDate before the createDate now can we
				$model->complete = 'Yes';
			}
			$model->save();
		}
	}

	public function actionStartStage($workflowId,$stageNumber,$modelId,$type) {
		if(is_numeric($workflowId) && is_numeric($stageNumber) && is_numeric($modelId) && ctype_alpha($type)) {

			$workflowStatus = Workflow::getWorkflowStatus($workflowId,$modelId,$type);
			// die(var_dump($workflowStatus));
			if((!isset($workflowStatus[$stageNumber]['createDate']) || $workflowStatus[$stageNumber]['createDate'] == 0) 
				&& (!isset($workflowStatus[$stageNumber]['completeDate']) || $workflowStatus[$stageNumber]['completeDate'] == 0)) {
				
				$action = new Actions('workflow');
				$action->associationId = $modelId;
				$action->associationType = $type;
				$action->assignedTo = Yii::app()->user->getName();
				$action->updatedBy = Yii::app()->user->getName();
				$action->complete = 'No';
				$action->type = 'workflow';
				$action->visibility = 1;
				$action->createDate = time();
				$action->lastUpdated = time();
				$action->workflowId = (int)$workflowId;
				$action->stageNumber = (int)$stageNumber;
				$action->save();
				// die(var_dump($action->getErrors()));
				// die(var_dump($action->rules()));
				
				$this->updateWorkflowChangelog($action,'start');
			}
		}
		$workflowStatus = Workflow::getWorkflowStatus($workflowId,$modelId,$type);
		echo Workflow::renderWorkflow($workflowStatus);
	}
	
	public function actionCompleteStage($workflowId,$stageNumber,$modelId,$type,$comment = '') {
		if(!(is_numeric($workflowId) && is_numeric($stageNumber) && is_numeric($modelId) && ctype_alpha($type)))
			return;

		$comment = trim($comment);
	
		$workflowStatus = Workflow::getWorkflowStatus($workflowId,$modelId,$type);
		$stageCount = count($workflowStatus)-1;
		
		$stage = &$workflowStatus[$stageNumber];
		
		if(isset($stage['createDate']) && empty($stage['completeDate'])) {
		
			$previousCheck = true;
			if($workflowStatus[$stageNumber]['requirePrevious'] == 1) {	// check if all stages before this one are complete
				for($i=1; $i<$stageNumber; $i++) {
					if(empty($workflowStatus[$i]['complete'])) {
						$previousCheck = false;
						break;
					}
				}
			} else if($workflowStatus[$stageNumber]['requirePrevious'] < 0) {		// or just check if the specified stage is complete
				if(empty($workflowStatus[ -1*$workflowStatus[$stageNumber]['requirePrevious'] ]['complete']))
					$previousCheck = false;
			}
			// is this stage is OK to complete? if a comment is required, then is $comment empty?
			if($previousCheck && (!$stage['requireComment'] || ($stage['requireComment'] && !empty($comment)))) {
			
			
				// find selected stage (and duplicates)
				$actionModels = CActiveRecord::model('Actions')->findAllByAttributes(
					array('associationId'=>$modelId,'associationType'=>$type,'type'=>'workflow','workflowId'=>$workflowId,'stageNumber'=>$stageNumber),
					new CDbCriteria(array('order'=>'createDate DESC'))
				);
				
				if(count($actionModels) > 1)				// if there is more than 1 action for this stage,
				for($i=1;$i<count($actionModels);$i++)		// delete all but the most recent one
					$actionModels[$i]->delete();

				$actionModels[0]->setScenario('workflow');
				$actionModels[0]->completeDate = time();	// set completeDate and save model
				$actionModels[0]->complete = 'Yes';
				$actionModels[0]->completedBy = Yii::app()->user->getName();
				// $actionModels[0]->actionDescription = $workflowId.':'.$stageNumber.$comment;
				$actionModels[0]->actionDescription = $comment;
				$actionModels[0]->save();
				
				$this->updateWorkflowChangelog($actionModels[0],'complete');
				
				for($i=0; $i<=$stageCount; $i++) {
					if($i != $stageNumber && empty($workflowStatus[$i]['completeDate']) && !empty($workflowStatus[$i]['createDate']))
						break;
				
				
					if(empty($workflowStatus[$i]['createDate'])) {
						$nextAction = new Actions('workflow');					// start the next one (unless there is already one)
						$nextAction->associationId = $modelId;
						$nextAction->associationType = $type;
						$nextAction->assignedTo = Yii::app()->user->getName();
						$nextAction->type = 'workflow';
						$nextAction->complete = 'No';
						$nextAction->visibility = 1;
						$nextAction->createDate = time();
						$nextAction->workflowId = $workflowId;
						$nextAction->stageNumber = $i;
						// $nextAction->actionDescription = $comment;
						$nextAction->save();
						
						$this->updateWorkflowChangelog($nextAction,'start');
						
						// $changes=$this->calculateChanges($oldAttributes, $model->attributes, $model);
						// $this->updateChangelog($model,$changes);
						break;
					}
				}
				// if($stageNumber < $stageCount && empty($workflowStatus[$stageNumber+1]['createDate'])) {	// if this isn't the final stage,
					
				// }
				$workflowStatus = Workflow::getWorkflowStatus($workflowId,$modelId,$type);	// refresh the workflow status
			}
		}
		echo Workflow::renderWorkflow($workflowStatus);
	}
	
	public function actionRevertStage($workflowId,$stageNumber,$modelId,$type) {
		if(is_numeric($workflowId) && is_numeric($stageNumber) && is_numeric($modelId) && ctype_alpha($type)) {

			$workflowStatus = Workflow::getWorkflowStatus($workflowId,$modelId,$type);
			$stageCount = count($workflowStatus)-1;
			
			if(isset($workflowStatus[$stageNumber]['createDate'])) {

				// find selected stage (and duplicates)
				$actions = CActiveRecord::model('Actions')->findAllByAttributes(
					array('associationId'=>$modelId,'associationType'=>$type,'type'=>'workflow','workflowId'=>$workflowId,'stageNumber'=>$stageNumber),
					new CDbCriteria(array('order'=>'createDate DESC'))
				);

				if(count($actions) > 1)				// if there is more than 1 action for this stage,
				for($i=1;$i<count($actions);$i++)		// delete all but the most recent one
					$actions[$i]->delete();

				if($workflowStatus[$stageNumber]['complete']) {		// the stage is complete, so just set it to 'started'
					$actions[0]->setScenario('workflow');
					$actions[0]->complete = 'No';
					$actions[0]->completeDate = null;
					$actions[0]->completedBy = '';
					$actions[0]->actionDescription = '';	// original completion note no longer applies
					$actions[0]->save();
					
					$this->updateWorkflowChangelog($actions[0],'revert');
					
					// delete all incomplete stages after this one
					// CActiveRecord::model('Actions')->deleteAll(new CDbCriteria(
						// array('condition'=>"associationId=$modelId AND associationType='$type' AND type='workflow' AND workflowId=$workflowId AND stageNumber > $stageNumber AND (completeDate IS NULL OR completeDate=0)")
					// ));
					
					
				} else {	// the stage is already incomplete, so delete it and all subsequent stages
					$subsequentActions = CActiveRecord::model('Actions')->findAll(new CDbCriteria(
						array('condition'=>"associationId=$modelId AND associationType='$type' AND type='workflow' AND workflowId=$workflowId AND stageNumber >= $stageNumber")
					));
					foreach($subsequentActions as &$action) {
						$this->updateWorkflowChangelog($action,'revert');
						$action->delete();
					}
				}
				$workflowStatus = Workflow::getWorkflowStatus($workflowId,$modelId,$type);
			}
			echo Workflow::renderWorkflow($workflowStatus);
		}
	}

	public function actionGetStageMembers($workflowId,$stage) {

		// $contactIds = Yii::app()->db->createCommand()->select('contactId')->from('x2_list_items')->where('x2_list_items.listId='.$id)->queryColumn();
		// die(var_dump($contactIds));
		// $search = CActiveRecord::model('ContactChild')->findAllByPk($contactIds);
		// return $search;
		
		if(!is_numeric($workflowId) || !is_numeric($stage))
			return new CActiveDataProvider();
		
		$actionDescription = $workflowId.':'.$stage;
		
		$contactsSql = Yii::app()->db->createCommand()
			->select('x2_contacts.*')
			->from('x2_contacts')
			->join('x2_actions','x2_contacts.id = x2_actions.associationId')
			->where("x2_actions.workflowId=$workflowId AND x2_actions.stageNumber=$stage AND x2_actions.associationType='contacts' AND complete!='Yes' AND (completeDate IS NULL OR completeDate=0) AND (x2_contacts.visibility=1 OR x2_contacts.assignedTo='".Yii::app()->user->getName()."')")
			->getText();
		
		$contactsCount = Yii::app()->db->createCommand()->select('COUNT(*)')->from('x2_actions')->where("x2_actions.workflowId=$workflowId AND x2_actions.stageNumber=$stage AND x2_actions.associationType='contacts' AND complete!='Yes' AND (completeDate IS NULL OR completeDate=0)")->queryScalar();

		$contactsDataProvider = new CSqlDataProvider($contactsSql,array(
			// 'criteria'=>$criteria,
			// 'data'=>$results,
			// 'modelClass'=>'ContactChild',
			'totalItemCount'=>$contactsCount,
			'sort'=>array(
				'attributes'=>array('firstName','lastName','phone','phone2','createDate','lastUpdated','leadSource'),
				'defaultOrder'=>'lastUpdated DESC',
			),
			'pagination'=>array(
				'pageSize'=>ProfileChild::getResultsPerPage(),
			),
		));
		
		$salesSql = Yii::app()->db->createCommand()
			->select('x2_sales.*')
			->from('x2_sales')
			->join('x2_actions','x2_sales.id = x2_actions.associationId')
			->where("x2_actions.workflowId=$workflowId AND x2_actions.stageNumber=$stage AND x2_actions.associationType='sales' AND complete!='Yes' AND (completeDate IS NULL OR completeDate=0)")
			->getText();
		
		$salesCount = Yii::app()->db->createCommand()->select('COUNT(*)')->from('x2_actions')->where("x2_actions.workflowId=$workflowId AND x2_actions.stageNumber=$stage AND x2_actions.associationType='sales' AND complete!='Yes' AND (completeDate IS NULL OR completeDate=0)")->queryScalar();

		$salesDataProvider = new CSqlDataProvider($salesSql,array(
			// 'criteria'=>$criteria,
			// 'data'=>$results,
			// 'modelClass'=>'ContactChild',
			'totalItemCount'=>$salesCount,
			'sort'=>array(
				'attributes'=>array('firstName','lastName','phone','phone2','createDate','lastUpdated','leadSource'),
				'defaultOrder'=>'lastUpdated DESC',
			),
			'pagination'=>array(
				'pageSize'=>ProfileChild::getResultsPerPage(),
			),
		));
		
		if(!empty($contactsDataProvider)) {
		
		$this->widget('zii.widgets.grid.CGridView', array(
			'id'=>'contacts-grid',
			'baseScriptUrl'=>Yii::app()->theme->getBaseUrl().'/css/gridview',
			'template'=> '<h2>'.Yii::t('contacts','Contacts').'</h2><div class="title-bar">'
				// .CHtml::link(Yii::t('app','Advanced Search'),'#',array('class'=>'search-button')) . ' | '
				// .CHtml::link(Yii::t('app','Clear Filters'),array('list','id'=>$listId,'clearFilters'=>1))
				.'{summary}</div>{items}{pager}',
			'dataProvider'=>$contactsDataProvider, //CActiveRecord::model('ContactChild')->searchList($listId),
			'enableSorting'=>false,
			// 'filter'=>$model,
			'columns'=>array(
				//'id',
				array(
					'name'=>'lastName',
					'header'=>Yii::t('contacts','Name'),
					'value'=>'CHtml::link($data["firstName"]." ".$data["lastName"],array("/contacts/default/view","id"=>$data["id"]))',
					'type'=>'raw',
					'htmlOptions'=>array('width'=>'30%')
				),
				array(
					'name'=>'phone',
					'header'=>Yii::t('contacts','Work Phone'),
				),
				array(
					'name'=>'createDate',
					'header'=>Yii::t('contacts','Create Date'),
					'value'=>'date("Y-m-d",$data["createDate"])',
					'type'=>'raw',
					'htmlOptions'=>array('width'=>'15%')
				),
						array(
					'name'=>'lastUpdated',
					'header'=>Yii::t('contacts','Last Updated'),
					'value'=>'date("Y-m-d",$data["lastUpdated"])',
					'type'=>'raw',
					'htmlOptions'=>array('width'=>'15%')
				),
				array(
					'name'=>'leadSource',
					'header'=>Yii::t('contacts','Lead Source'),
				),
				
			),
		));
		
		}
		
		if(!empty($salesDataProvider)) {
		
		$this->widget('zii.widgets.grid.CGridView', array(
			'id'=>'contacts-grid',
			'baseScriptUrl'=>Yii::app()->theme->getBaseUrl().'/css/gridview',
			'template'=> '<h2>'.Yii::t('sales','Sales').'</h2><div class="title-bar">'
				// .CHtml::link(Yii::t('app','Advanced Search'),'#',array('class'=>'search-button')) . ' | '
				// .CHtml::link(Yii::t('app','Clear Filters'),array('list','id'=>$listId,'clearFilters'=>1))
				.'{summary}</div>{items}{pager}',
			'dataProvider'=>$salesDataProvider, //CActiveRecord::model('ContactChild')->searchList($listId),
			// 'filter'=>$model,
			'columns'=>array(
				//'id',
				array(
					'header'=>Sales::model()->getAttributeLabel('name'),
					'name'=>'name',
					'value'=>'CHtml::link($data["name"],array("/sales/default/view","id"=>$data["id"]))',
					'type'=>'raw',
					'htmlOptions'=>array('width'=>'40%'),
				),
				//'description',
				array(
					'header'=>Sales::model()->getAttributeLabel('quoteAmount'),
					'name'=>'quoteAmount',
					'value'=>'Yii::app()->locale->numberFormatter->formatCurrency($data["quoteAmount"],Yii::app()->params->currency)',
					'type'=>'raw',
				),
				array(
					'header'=>Sales::model()->getAttributeLabel('salesStage'),
					'name'=>'salesStage',
					'value'=>'Yii::t("sales",$data["salesStage"])',
					'type'=>'raw',
				),
				
				array(
					'header'=>Sales::model()->getAttributeLabel('expectedCloseDate'),
					'name'=>'expectedCloseDate',
					'value'=>'empty($data->expectedCloseDate)?"":date("Y-m-d",$data["expectedCloseDate"])',
					'type'=>'raw',
					'htmlOptions'=>array('width'=>'13%'),
				),
				// 'probability',
				array(
					'header'=>Sales::model()->getAttributeLabel('assignedTo'),
					'name'=>'assignedTo',
					'value'=>'empty($data["assignedTo"])?Yii::t("app","Anyone"):$data["assignedTo"]',
					'type'=>'raw',
				),
				
			),
		));
		
		}
	}
	
	private function updateWorkflowChangelog(&$action,$changeType) {
	
		// die(var_dump($action));
		$changelog = new Changelog;
		$changelog->type = ucfirst($action->associationType);
		$changelog->itemId = $action->associationId;

		$changelog->changedBy = Yii::app()->user->getName();
		$changelog->timestamp = time();
		
		$workflowName = Yii::app()->db->createCommand()->select('name')->from('x2_workflows')->where('id=:id',array(':id'=>$action->workflowId))->queryScalar();
		$stageName = Yii::app()->db->createCommand()->select('name')->from('x2_workflow_stages')->where('workflowId=:id AND stageNumber=:sn',array(':sn'=>$action->stageNumber,':id'=>$action->workflowId))->queryScalar();
		
		$stageText = Yii::t('workflow','<b>Stage {n}: {stageName}</b> in <b>{workflowName}</b>',array('{n}'=>$action->stageNumber,'{stageName}'=>$stageName,'{workflowName}'=>$workflowName));
		
		if($changeType == 'start')
			$changelog->changed = Yii::t('workflow','<u>STARTED</u> {stage}',array('{stage}'=>$stageText));
		elseif($changeType == 'complete')
			$changelog->changed = Yii::t('workflow','<u>COMPLETED</u> {stage}',array('{stage}'=>$stageText));
		elseif($changeType == 'revert')
			$changelog->changed = Yii::t('workflow','<u>REVERTED</u> {stage}',array('{stage}'=>$stageText));
		else
			return;

		$changelog->save();
	}
	
	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='workflow-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
