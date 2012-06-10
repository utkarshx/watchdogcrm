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
 * Copyright © 2011-2012 by X2Engine Inc. www.X2Engine.com
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

/**
 * This is the model class for table "x2_contacts".
 */
class Contacts extends X2Model {

	public $name;

	/**
	 * Returns the static model of the specified AR class.
	 * @return Contacts the static model class
	 */
	public static function model($className=__CLASS__) { return parent::model($className); }

	/**
	 * @return string the associated database table name
	 */
	public function tableName() { return 'x2_contacts'; }

	/**
	 * @return string the route to view this model
	 */
	public function getDefaultRoute() { return '/contacts'; }
	
	/**
	 * @return string the route to this model's AutoComplete data source
	 */
	public function getAutoCompleteSource() { return '/contacts/getItems'; }
	
	/**
	 * @return array relational rules.
	 */
	public function relations() {
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array();
	}

	public function afterFind() {
		$this->name = $this->firstName.' '.$this->lastName;
	}
	
	
	/**
	 * Sets the name field (full name)
	 */
	public function beforeSave() {
		$this->name = $this->firstName.' '.$this->lastName;
		return true;
	}
	
	public static function getNames() {
		$contactArray = Contacts::model()->findAll($condition='assignedTo=\''.Yii::app()->user->getName().'\' OR assignedTo=\'Anyone\'');
		$names=array(0=>'None');
		foreach($contactArray as $user){
			$first = $user->firstName;
			$last = $user->lastName;
			$name = $first . ' ' . $last;
			$names[$user->id]=$name;
		}
		return $names;
	}

	/**
	 *	Returns virtual 'name' attribute
	 *	@return string Concatenated first and last name
	 */
	// public function getName() {
		// return $this->firstName.' '.$this->lastName;
	// }
	/**
	 *	Sets virtual 'name' attribute (does not change firstName or lastName fields)
	 *	@param string $name
	 */
	// public function setName($name) {
		// $this->name = trim($name);
	// }
	
	public function behaviors() {
		return array(
			'ERememberFiltersBehavior' => array(
				'class' => 'application.components.ERememberFiltersBehavior',
				'defaults'=>array(),		/* optional line */
				'defaultStickOnClear'=>false	/* optional line */
			),
		);
	}

	/**
	 *	Returns all public contacts.
	 *	@return $names An array of strings containing the names of contacts.
	 */
	public static function getAllNames() {
		$contactArray = Contacts::model()->findAll($condition='visibility=1');
		$names=array(0=>'None');
		foreach($contactArray as $user){
			$first = $user->firstName;
			$last = $user->lastName;
			$name = $first . ' ' . $last;
			$names[$user->id]=$name;
		}
		return $names;
	}

	public static function getContactLinks($contacts) {
		if(!is_array($contacts))
			$contacts = explode(' ',$contacts);
		
		$links = array();
		foreach($contacts as &$id){
			if($id !=0 ) {
				$model = CActiveRecord::model('Contacts')->findByPk($id);
				$links[] = CHtml::link($model->name,array('/contacts/default/view','id'=>$id));
				//$links.=$link.', ';
				
			}
		}
		//$links=substr($links,0,strlen($links)-2);
		return implode(', ',$links);
	}
	
	public static function getMailingList($criteria) {
		
		$mailingList=array();
		
		$arr=Contacts::model()->findAll();
		foreach($arr as $contact){
			$i=preg_match("/$criteria/i",$contact->backgroundInfo);
			if($i>=1){
				$mailingList[]=$contact->email;
			}
		}
		return $mailingList;
	}
	
	public function searchAll() {
		$criteria=new CDbCriteria;
		$condition = 'visibility="1" OR assignedTo="Anyone" OR assignedTo="'.Yii::app()->user->getName().'"';
		$parameters = array('limit'=>ceil(ProfileChild::getResultsPerPage()));
		/* x2temp */
		$groupLinks = Yii::app()->db->createCommand()->select('groupId')->from('x2_group_to_user')->where('userId='.Yii::app()->user->getId())->queryColumn();
		if(!empty($groupLinks))
			$condition .= ' OR assignedTo IN ('.implode(',',$groupLinks).')';

		$condition .= 'OR (visibility=2 AND assignedTo IN 
			(SELECT username FROM x2_group_to_user WHERE groupId IN
				(SELECT groupId FROM x2_group_to_user WHERE userId='.Yii::app()->user->getId().')))';
		/* end x2temp */
		$parameters['condition']=$condition;
		$criteria->scopes=array('findAll'=>array($parameters));
				
		if(isset($_GET['tagField']) && !empty($_GET['tagField'])) {	// process the tags filter
			
			$tags = explode(',',preg_replace('/\s?,\s?/',',',trim($_GET['tagField'])));	//remove any spaces around commas, then explode to array
			for($i=0; $i<count($tags); $i++) {
				if(empty($tags[$i])) {
					unset($tags[$i]);
					$i--;
					continue;
				} else {
					if($tags[$i][0] != '#')
						$tags[$i] = '#'.$tags[$i];
					$tags[$i] = 'x2_tags.tag = "'.$tags[$i].'"';
				}
			}
			// die($str);
			$tagConditions = implode(' OR ',$tags);
			
			$criteria->distinct = true;
			$criteria->join = 'RIGHT JOIN x2_tags ON (x2_tags.itemId=t.id AND x2_tags.type="Contacts" AND ('.$tagConditions.'))';
		}
		return $this->searchBase($criteria);
	}

	public function searchMyContacts() {
		$criteria=new CDbCriteria;
		$condition = 'assignedTo="'.Yii::app()->user->getName().'"';
		$parameters=array('limit'=>ceil(ProfileChild::getResultsPerPage()));

		$parameters['condition']=$condition;
		$criteria->scopes=array('findAll'=>array($parameters));
		
		return $this->searchBase($criteria);
	
	
	}

	public function searchNewContacts() {
		$criteria=new CDbCriteria;
		$condition = 'assignedTo="'.Yii::app()->user->getName().'" AND createDate > '.mktime(0,0,0);
		$parameters=array('limit'=>ceil(ProfileChild::getResultsPerPage()));

		$parameters['condition']=$condition;
		$criteria->scopes=array('findAll'=>array($parameters));
		
		return $this->searchBase($criteria);
	}
	
	
	public function search() {
		$criteria=new CDbCriteria;
		$condition = 'assignedTo="'.Yii::app()->user->getName().'"';
			$parameters=array('limit'=>ceil(ProfileChild::getResultsPerPage()));
			/* x2temp */
			$groupLinks = Yii::app()->db->createCommand()->select('groupId')->from('x2_group_to_user')->where('userId='.Yii::app()->user->getId())->queryColumn();
			if(!empty($groupLinks))
				$condition .= ' OR assignedTo IN ('.implode(',',$groupLinks).')';
			/* end x2temp */
		$parameters['condition']=$condition;
		$criteria->scopes=array('findAll'=>array($parameters));
		
		return $this->searchBase($criteria);
	}
	
	public function searchAdmin() {
		$criteria=new CDbCriteria;
		return $this->searchBase($criteria);
	}

	public function searchAccount($id) {
		$criteria=new CDbCriteria;
		$condition = "company='$id'";
		$parameters=array('limit'=>ceil(ProfileChild::getResultsPerPage()));
		$parameters['condition']=$condition;
		$criteria->scopes=array('findAll'=>array($parameters));
		return $this->searchBase($criteria);
	}

	/**
	 * Returns a DataProvider for all the contacts in the specified list,
	 * using this Contact model's attributes as a search filter
	 */
	public function searchList($id, $pageSize=null) {
		$list = X2List::model()->findByPk($id);

		if(isset($list)) {
			$search = $list->dbCriteria();
				
			$search->compare('name',$this->name,true);
			$search->compare('firstName',$this->firstName,true);
			$search->compare('lastName',$this->lastName,true);
			$search->compare('title',$this->title,true);
			$search->compare('company',$this->company,true);
			$search->compare('phone',$this->phone,true);
			$search->compare('phone2',$this->phone2,true);
			$search->compare('email',$this->email,true);
			$search->compare('website',$this->website,true);
			$search->compare('address',$this->address,true);
			$search->compare('city',$this->city,true);
			$search->compare('state',$this->state,true);
			$search->compare('zipcode',$this->zipcode,true);
			$search->compare('country',$this->country,true);
			$search->compare('visibility',$this->visibility);
			$search->compare('assignedTo',$this->assignedTo,true);
			$search->compare('backgroundInfo',$this->backgroundInfo,true);
			$search->compare('twitter',$this->twitter,true);
			$search->compare('linkedin',$this->linkedin,true);
			$search->compare('skype',$this->skype,true);
			$search->compare('googleplus',$this->googleplus,true);
			// $search->compare('lastUpdated',$this->lastUpdated,true);
			$search->compare('updatedBy',$this->updatedBy,true);
			$search->compare('priority',$this->priority,true);
			$search->compare('leadSource',$this->leadSource,true);
			$search->compare('rating',$this->rating);

			return new CActiveDataProvider('Contacts',array(
				'criteria'=>$search,
				'sort'=>array(
					'defaultOrder'=>'lastupdated DESC'	// true = ASC
				),
				'pagination'=>array(
					'pageSize'=>isset($pageSize)? $pageSize : ProfileChild::getResultsPerPage(),
				),
			));
			
		} else {
			//if list is not working, return all contacts
			return new CActiveDataProvider('Contacts',array(
				'sort'=>array(
					'defaultOrder'=>'createDate DESC',
				),
				'pagination'=>array(
					'pageSize'=>ProfileChild::getResultsPerPage(),
				),
			));
		}
	}
	
	
	public function searchBase($criteria) {
		
                $fields=Fields::model()->findAllByAttributes(array('modelName'=>'Contacts'));
                foreach($fields as $field){
                    $fieldName=$field->fieldName;
                    switch($field->type){
                        case 'boolean':
                            $criteria->compare($field->fieldName,$this->compareBoolean($this->$fieldName), true);
                            break;
                        case 'link':
                            $criteria->compare($field->fieldName,$this->compareLookup($field->linkType, $this->$fieldName), true);
                            $criteria->compare($field->fieldName,$this->$fieldName, true, 'OR');
                            break;
                        case 'assignment':
                            $criteria->compare($field->fieldName,$this->compareAssignment($this->$fieldName), true);
                            break;
                        default:
                            $criteria->compare($field->fieldName,$this->$fieldName,true);
                    }
                    
                }
                 
                $criteria->compare('CONCAT(firstName," ",lastName)', $this->name,true, 'OR');


		return new SmartDataProvider(get_class($this), array(
			'sort'=>array(
				'defaultOrder'=>'createDate DESC',
			),
			'pagination'=>array(
				'pageSize'=>ProfileChild::getResultsPerPage(),
			),
			'criteria'=>$criteria,
		));
	}
        
       
        
        

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
}
