<?php
namespace Manage\Model;

use Zend\Db\Tablegateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Adapter\Adapter;
use Zend\Session\Storage\SessionArrayStorage;
use Zend\ProgressBar\Upload\SessionProgress;
use Zend\Session\Container;
use Zend\Db\Sql\Sql;

class ConfigValueTable
{
    public $tableGateway;

    public function __construct(TableGateway $tg)
    {
        $this->tableGateway = $tg;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
}