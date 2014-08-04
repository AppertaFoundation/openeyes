<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

/**
 * This is a behaviour abstraction for nested set hierarchy.
 *
 * This is supporting the snomed hierarchy, where leafs can appear in more than one point on the tree. The functions at this point
 * operate on arrays of IDs, and some tree structure might not work out so clearly. It is initially implemented so that we
 * can more easily determine if any given disorder is of a type (i.e. anything existing under cataract or diabetes).
 *
 * Further extensions:
 * 1) return the objects themselves, in the appropriate order
 * 2) Abstract so that queries will work on different dbs (at the moment, only tested on mysql)
 *
 * The command line BuildDisorderTree yiic command will populate the tree itself.
 *
 * The model that the tree is attached to should impelement a treeTable method which returns the name of the table that is used to
 * store the tree. This table should consist of the following columns:
 *
 * @property string $id
 * @property string $lft
 * @property string $rght
 *
 */
class TreeBehavior extends CActiveRecordBehavior
{
	public $idAttribute = 'id';
	public $leftAttribute = 'lft';
	public $rightAttribute = 'rght';
	protected $_cacheStub = null;

	protected function getCacheStub()
	{
		if (is_null($this->_cacheStub)) {
			$owner = $this->getOwner();
			$this->_cacheStub = "TreeBehaviour:" . $owner->treeTable() . ":" . $owner->id;
		}
		return $this->_cacheStub;
	}

	/**
	* gets the list of left and right boundaries for any given owner objects
	*
	* @param CDbConnection $db
	* @param string $tree_table
	* @param string $obj_id
	*
	* @returns array of array(left, right)
	*/
	protected function _treeLimits($db, $tree_table, $obj_id)
	{
		$query = 'SELECT ' . $this->leftAttribute . ',' . $this->rightAttribute . ' FROM ' . $tree_table .
		' WHERE ' . $this->idAttribute . ' = ' . $db->quoteValue($obj_id);

		$res = $db->createCommand($query)->query();
		$result = array();
		foreach ($res as $r) {
			$result[] = array($r[$this->leftAttribute], $r[$this->rightAttribute]);
		}
		return $result;
	}

	/*
	* gets the list of left and right boundaries for any given owner objects
	*
	* @param CActiveRecord $owner
	*
	* @returns array of array(left, right)
	*/
	protected function treeLimits($owner)
	{
		$db = $owner->getDbConnection();

		return $this->_treeLimits($db, $owner->treeTable(), $owner->id);
	}

	/*
	 * gets the list of left and right boundaries for any given owner objects
	*
	* @param CDbConnection $db
	* @param string $tree_table
	* @param string $obj_id
	*
	* @returns array of array(left, right)
	*/
	protected function _descendentIds($db, $tree_table, $obj_id)
	{
		$limits = $this->_treeLimits($db, $tree_table, $obj_id);
		$ids = array();
		if (count($limits)) {
			$sql_strs = array();
			foreach ($limits as $l) {
				$sql_strs[] = $this->leftAttribute . ' > ' . $l[0] . ' AND ' . $this->rightAttribute . ' <  ' . $l[1];
			}

			$query = 'SELECT ' . $this->idAttribute . ' FROM ' . $tree_table . ' WHERE (' . implode(') OR (', $sql_strs ) . ') ORDER BY lft';
			$res = $db->createCommand($query)->query();

			foreach ($res as $r) {
				$ids[] = $r[$this->idAttribute];
			}
		}

		return $ids;
	}

	/**
	* Returns all the ancestor ids of the provided object id
	*
	* @param CDbConnection $db
	* @param string $table
	* @param string $obj_id
	*
	* @returns array() ids
	*/
	protected function _ancestorIds($db, $table, $obj_id)
	{
		$limits = $this->_treeLimits($db, $table, $obj_id);
		$sql_strs = array();
		foreach ($limits as $l) {
			$sql_strs[] = $this->leftAttribute . ' < ' . $l[0] . ' AND ' . $this->rightAttribute . ' >  ' . $l[1];
		}

		$query = 'SELECT ' . $this->idAttribute . ' FROM ' . $table . ' WHERE (' . implode(') OR (', $sql_strs ) . ')';

		$res = $db->createCommand($query)->query();

		$ids = array();
		foreach ($res as $r) {
			$ids[] = $r[$this->idAttribute];
		}

		return $ids;
	}

	/**
	 * @return string the associated database table name
	 */
	public function treeTable()
	{
		throw new Exception('object using TreeBehaviour must implement the treeTable method');
	}

	/**
	 * works out the starting point for a new tree
	 *
	 * @returns int start
	 */
	public function treeStart()
	{
		$owner = $this->getOwner();
		$db = $owner->getDbConnection();
		$query = 'SELECT MAX(' . $this->rightAttribute . ') AS maxright FROM ' . $owner->treeTable();
		$val = $db->createCommand($query)->queryRow();

		if ($val['maxright']) {
			return $val['maxright'] + 1;
		} else {
			return 1;
		}
	}

	/**
	 * returns all descendant ids of the object, across any trees the the object exists in
	 *
	 * @returns array() of object ids
	 */
	public function descendentIds()
	{
		$owner = $this->getOwner();
		$cache_id = $this->getCacheStub() . ':descendentIds';
		$descendent_ids = Yii::app()->cache->get($cache_id);
		if ($descendent_ids === false) {
			$descendent_ids = $this->_descendentIds($owner->getDbConnection(), $owner->treeTable(), $owner->id);
			Yii::app()->cache->set($cache_id, $descendent_ids);
		}

		return $descendent_ids;
	}

	/**
	 * immediate child ids of the object (note that these children might not be all in one tree, given that a node can exist in more than one tree)
	 *
	 * @returns array() of object ids
	 *
	 */
	public function childIds()
	{
		$owner = $this->getOwner();
		$db = $owner->getDbConnection();
		$limits = $this->treeLimits($owner);

		// This was pretty much culled from the nested sets guide at:
		// http://mirror.neu.edu.cn/mysql/tech-resources/articles/hierarchical-data.html
		$query = 'SELECT leaf.' . $this->idAttribute . ', (COUNT(parent.' . $this->idAttribute . ') - (sub_tree.depth+1)) as depth ' .
				'FROM ' . $owner->treeTable() . ' AS leaf, ' .
				$owner->treeTable() . ' AS parent, ' .
				$owner->treeTable() . ' AS sub_parent, ' .
				'( SELECT leaf.' . $this->idAttribute . ', (COUNT(parent.' . $this->idAttribute . ') - 1) AS depth ' .
				'FROM ' . $owner->treeTable() . ' AS leaf, ' .
				$owner->treeTable() . ' AS parent ' .
				'WHERE leaf.' . $this->leftAttribute . ' BETWEEN parent.' . $this->leftAttribute . ' AND parent.' .$this->rightAttribute .
				' AND leaf.' . $this->idAttribute . ' = ' . $db->quoteValue($owner->id) .
				' GROUP BY leaf.' . $this->idAttribute . ' ORDER BY leaf.' . $this->leftAttribute .
				') AS sub_tree ' .
			'WHERE leaf.' . $this->leftAttribute . ' BETWEEN parent.' . $this->leftAttribute . ' AND parent.' . $this->rightAttribute .
			' AND leaf.' . $this->leftAttribute . ' BETWEEN sub_parent.' . $this->leftAttribute . ' AND sub_parent.' . $this->rightAttribute .
			' AND sub_parent.' . $this->idAttribute . ' = sub_tree.' . $this->idAttribute . ' ' .
			'GROUP BY leaf.' . $this->idAttribute . ' HAVING depth = ' .
			'(SELECT count(*) FROM ' . $owner->treeTable() . ' AS tree WHERE tree.' . $this->idAttribute . ' = ' . $db->quoteValue($owner->id) . ') ' .
			'ORDER BY leaf.' . $this->leftAttribute;

		$res = $db->createCommand($query)->query();
		$result = array();
		foreach ($res as $r) {
			$result[] = $r['id'];
		}
		return $result;
	}

	public function children()
	{
		$owner = $this->getOwner();
		$cids  = $owner->childIds();

		$idorder = implode(',',$cids);

		$criteria = new CDbCriteria();
		$criteria->order = "FIELD(id, $idorder)";
		$kls = get_class($owner);
		return $kls::model()->findAllByPk($cids, $criteria);
	}

	/**
	 * get immediate parent ids for the object (note there may be more than one because any disorder can exist in more than one tree)
	 *
	 * @returns array() of object ids
	 */
	public function parentIds()
	{
		$owner = $this->getOwner();
		$db = $owner->getDbConnection();

		$query = 'SELECT ' . $this->idAttribute . ' as owner, (' .
					'SELECT id FROM ' . $owner->treeTable() . ' AS t2 ' .
					'WHERE t2.' . $this->leftAttribute . ' < t1.' . $this->leftAttribute .
					' AND t2.' . $this->rightAttribute . ' > t1.' . $this->rightAttribute .
					' ORDER BY t2.' .$this->rightAttribute . ' - t1.' . $this->rightAttribute . ' ASC LIMIT 1)' .
					' AS parent' .
				' FROM ' . $owner->treeTable() . ' AS t1' .
				' WHERE t1.' . $this->idAttribute . ' = ' . $db->quoteValue($owner->id);

		$res = $db->createCommand($query)->query();

		$ids = array();
		foreach ($res as $r) {
			if ($r['parent'] != null) {
				$ids[] = $r['parent'];
			}
		}

		return $ids;
	}

	/**
	 * returns all parent object ids (note there may be more than one because any object can exist in more than one tree)
	 *
	 * @returns array() of object ids
	 */
	public function ancestorIds()
	{
		$owner = $this->getOwner();
		$cache_id = $this->getCacheStub() . ':ancestorIds';
		$ancestor_ids = Yii::app()->cache->get($cache_id);
		if ($ancestor_ids === false) {
			$ancestor_ids = $this->_ancestorIds($owner->getDbConnection(), $owner->treeTable(), $owner->id);
			Yii::app()->cache->set($cache_id, $ancestor_ids);
		}
		return $ancestor_ids;

	}

	/**
	 * returns the object ids that are at the top of trees
	 *
	 * @returns array() of object ids
	 */
	public function rootIds()
	{
		$owner = $this->getOwner();
		$db = $owner->getDbConnection();

		$query = 'SELECT leaf.id FROM (SELECT leaf.'. $this->idAttribute . ' AS id, (COUNT(parent. ' . $this->idAttribute . ') -1) AS DEPTH ' .
				'FROM ' . $owner->treeTable() . ' AS leaf, ' . $owner->treeTable() . ' AS parent ' .
				'WHERE leaf.' . $this->leftAttribute . ' BETWEEN parent.' . $this->leftAttribute . ' AND parent.' . $this->rightAttribute .
				' GROUP BY leaf.id) as leaf WHERE depth = 0';

		print $query;

		$res = $db->createCommand($query)->query();

		$ids = array();
		foreach ($res as $r) {
			$ids[] = $r['id'];
		}
		return $ids;
	}

	/**
	 * returns true if the owner is an ancestor of any of the ids passed in
	 *
	 * @param array() list of object ids
	 *
	 * @returns bool
	 */
	public function ancestorOfIds($ids)
	{
		$owner = $this->getOwner();
		$descendents = $owner->descendentIds();

		return count(array_intersect($ids, $descendents)) > 0;
	}

	/*
	 * utlity function that will check whether any of the list $ids appear in the trees of any of the $ancestor_ids (including the
	 * ancestor ids themselves)
	 *
	 * @param array() $ids - ids to look for
	 * @param array() $ancestor_ids - tree parent ids to check in
	 *
	 * @returns bool
	 *
	 */
	public function ancestorIdsMatch($ids, $ancestor_ids)
	{
		$obj = $this->getOwner();
		$db = $obj->getDbConnection();
		$tree_table = $obj->treeTable();

		$all_ancestor_ids = array();
		foreach ($ancestor_ids as $aid) {
			if (!in_array($aid, $all_ancestor_ids) ) {
				$all_ancestor_ids = array_merge($this->_descendentIds($db, $tree_table, $aid), $all_ancestor_ids);
			}
		}
		// include the ids we're checking for as all the descendents
		$all_ancestors = array_merge($ancestor_ids,$all_ancestor_ids);

		return count(array_intersect($ids, $all_ancestors)) > 0;

	}
}
