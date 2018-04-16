<?php //File: faces/DbAdapterInterface.php
/* vim: set expandtab tabstop=2 shiftwidth=2 softtabstop=2: */
/**
 * Summary: managing required methods for each provided layer
 */
/**
 * DbAdapterInterface - interface for managing required methods
 *
 * PHP version 5.6 / 7.0
 *
 * @package    DBLayer
 * @author     Tereza Simcic <tereza.simcic@gmail.com>
 * @copyright  2015 Tereza Simcic <updaed: 2018>
 * @license    Tereza Simcic
 *
 *
 * @link       http://tesispro.eu
 * @name       DbAdapterInterface.php
 *
 *
 */

namespace Tesis\DBLayer\Faces;

interface DbAdapterInterface
{
  public function openConnection();
  public function closeConnection($conn);
  public function insert(array $data = null);
  public function update(array $data = null);
  public function delete($id = '');
  public function get();
  public function first();
  public function all();
  public function execute();
  public function executeSql($sql = '');
}
