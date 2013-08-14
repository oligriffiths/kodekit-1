<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Database Rowset Interface
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Library\Database
 */
interface KDatabaseRowsetInterface
{
	/**
     * Returns all data as an array.
     *
     * @param   boolean $modified If TRUE, only return the modified data. Default FALSE
     * @return array
     */
    public function getData($modified = false);

	/**
  	 * Set the rowset data based on a named array/hash
  	 *
  	 * @param   mixed 	$data       Either and associative array, a KDatabaseRow object or object
  	 * @param   boolean $modified   If TRUE, update the modified information for each column being set. Default TRUE
 	 * @return 	KDatabaseRowsetAbstract
  	 */
  	 public function setData( $data, $modified = true );

	/**
     * Add rows to the rowset
     *
     * @param  array    $data   An associative array of row data to be inserted.
     * @param  boolean  $new    If TRUE, mark the row(s) as new (i.e. not in the database yet). Default TRUE
     * @return void
     * @see __construct
     */
    public function addData(array $data, $new = true);

	/**
	 * Gets the identitiy column of the rowset
	 *
	 * @return string
	 */
	public function getIdentityColumn();

	/**
     * Returns a KDatabaseRow
     *
     * This functions accepts either a know position or associative
     * array of key/value pairs
     *
     * @param 	string 	$needle     The position or the key to search for
     * @return KDatabaseRowAbstract
     */
    public function find($needle);

	/**
     * Saves all rows in the rowset to the database
     *
     * @return KDatabaseRowsetAbstract
     */
    public function save();

	/**
     * Deletes all rows in the rowset from the database
     *
     * @return KDatabaseRowsetAbstract
     */
    public function delete();

	/**
     * Reset the rowset
     *
     * @return KDatabaseRowsetAbstract
     */
    public function reset();

	/**
     * Insert a row in the rowset
     *
     * The row will be stored by i'ts identity_column if set or otherwise by it's object handle.
     *
     * @param  KDatabaseRowInterface 	$row A KDatabaseRow object to be inserted
     * @return KDatabaseRowsetAbstract
     */
    public function insert(KObjectHandlable $row);

	/**
     * Removes a row
     *
     * The row will be removed based on its identity_column if set or otherwise by
     * it's object handle.
     *
     * @param  KDatabaseRowInterface $row 	A KDatabaseRow object to be removed
     * @return KDatabaseRowsetAbstract
     */
    public function extract(KObjectHandlable $row);

    /**
	 * Test the connected status of the rowset.
	 *
	 * @return	bool
	 */
    public function isConnected();

    /**
     * Return an associative array of the data.
     *
     * @return array
     */
    public function toArray();
}