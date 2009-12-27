<?php 
/**
 *
 * Copyright (c) FaZend.com
 * All rights reserved.
 *
 * You can use this product "as is" without any warranties from authors.
 * You can change the product only through Google Code repository
 * at http://code.google.com/p/fazend
 * If you have any questions about privacy, please email privacy@fazend.com
 *
 * @copyright Copyright (c) FaZend.com
 * @version $Id$
 * @category FaZend
 */

/**
 * TODO: short description.
 * 
 * TODO: long description.
 * 
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
        $fzSnapshot = new FaZend_Pos_Model_Snapshot();
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
            ->fetchAll()
            ;
    }

    /**
     * Loads a snapshot for the given POS object, of the given version.
     * 
     * @param FaZend_Pos_Model_Object Object
     * @return FaZend_Pos_Model_Snapshot
     */
    public static function findByObject(FaZend_Pos_Model_Object $fzObject)
    {
        return self::retrieve()
            ->where('fzObject = ?', strval($fzObject))
            ->order('version DESC')
            ->setRowClass('FaZend_Pos_Model_Snapshot')
            ->limit(1)
            ->fetchRow()
            ;
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
        $this->version = self::_getNextVersion($this->getObject('fzObject', 'FaZend_Pos_Model_Object'));
        $this->properties = $properties;
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
            ->from('fzSnapshot', array(
                'ver' => new Zend_Db_Expr('MAX(version)+1')
                ))
            ->where('fzObject = ?', strval($fzObject))
            ->group('fzObject')
            ->setSilenceIfEmpty()
            ->fetchRow()
            ;

        if (empty($row)) {
            return 1;
        } else {
            return $row->ver;
        }
    }

}
