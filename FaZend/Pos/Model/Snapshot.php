<?php
/**
 * FaZend Framework
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt. It is also available 
 * through the world-wide-web at this URL: http://www.fazend.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@fazend.com so we can send you a copy immediately.
 *
 * @copyright Copyright (c) FaZend.com
 * @version $Id$
 * @category FaZend
 */

/**
 * 'fzSnapshot' PHP representative
 * 
 * @package Pos
 */
class FaZend_Pos_Model_Snapshot extends FaZend_Db_Table_ActiveRow_fzSnapshot
{

    /**
     * Creates a new snapshot.
     * 
     * @param FaZend_Pos_Model_Object Object
     * @param integer|string User ID
     * @param string Serialized array of properties
     * @return FaZend_Pos_Model_Snapshot
     */
    public static function create(FaZend_Pos_Model_Object $fzObject, $userId, $properties)
    {        
        $fzSnapshot = new self();
        $fzSnapshot->fzObject = $fzObject;
        $fzSnapshot->user = $userId;
        $fzSnapshot->version = self::_getNextVersion($fzObject);
        $fzSnapshot->properties = $properties;
        $fzSnapshot->alive = true;
        $fzSnapshot->baselined = false;
        $fzSnapshot->save();

        return $fzSnapshot;
    }

    /**
     * Retrieve all versions of the object
     * 
     * @param FaZend_Pos_Model_Object Object
     * @return FaZend_Pos_Model_Snapshot[]
     */
    public static function retrieveVersions(FaZend_Pos_Model_Object $fzObject)
    {
        return self::retrieve()
            ->where('fzObject = ?', strval($fzObject))
            ->order('version DESC')
            ->setRowClass('FaZend_Pos_Model_Snapshot')
            ->fetchAll();
    }

    /**
     * Loads a snapshot for the given POS object, of the given version.
     * 
     * @param FaZend_Pos_Model_Object Object
     * @return FaZend_Pos_Model_Snapshot
     * @throws FaZend_Pos_Model_Snapshot_NotFoundException
     */
    public static function findByObject(FaZend_Pos_Model_Object $fzObject)
    {
        return self::retrieve()
            ->where('fzObject = ?', strval($fzObject))
            ->order('version DESC')
            ->setRowClass('FaZend_Pos_Model_Snapshot')
            ->limit(1)
            ->fetchRow();
    }

    /**
     * Update it
     * 
     * @param integer|string User ID
     * @param string Serialized array of properties
     * @return void
     */
    public function update($userId, $properties)
    {        
        $this->user = $userId;
        $this->version = self::_getNextVersion($this->fzObject);
        $this->properties = $properties;
        $this->updated = Zend_Date::now()->getIso();
        $this->alive = true;
        $this->baselined = false;
        $this->save();
    }

    /**
     * Get next version for this object
     * 
     * @param FaZend_Pos_Model_Object Object
     * @return integer
     */
    protected static function _getNextVersion(FaZend_Pos_Model_Object $fzObject)
    {
        $row = self::retrieve(false)
            ->from(
                'fzSnapshot', 
                array(
                    'ver' => new Zend_Db_Expr('MAX(version)+1')
                )
            )
            ->where('fzObject = ?', strval($fzObject))
            ->group('fzObject')
            ->setSilenceIfEmpty()
            ->fetchRow();

        if (empty($row)) {
            return 1;
        } else {
            return $row->ver;
        }
    }

}
