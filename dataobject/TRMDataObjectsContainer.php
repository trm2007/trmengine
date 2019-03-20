<?php

namespace TRMEngine\DataObject;

use TRMEngine\DataObject\Exceptions\TRMDataObjectContainerNoMainException;
use TRMEngine\DataObject\Interfaces\TRMDataObjectInterface;
use TRMEngine\DataObject\Interfaces\TRMDataObjectsContainerInterface;

/**
 * ����� ��������� �������� ������, ������������ ��� ��������� ��������,
 * ��������, ��� ���������� �������� �� ����� ��������������� ����������� � ���������
 * (��������������, ��������, ���.������������� � �.�.)
 */
abstract class TRMDataObjectsContainer implements TRMDataObjectsContainerInterface // extends TRMIdDataObject
{
/**
 * @var TRMDataObjectInterface - �������� ������
 */
protected $MainDataObject;
/**
 * @var array(TRMDataObjectInterface) - ������ �������� ������, ����������� �������� ������, 
 * �������� ��������� �������������, ���.�����������, ���������, ������ � �.�.
 */
protected $ObjectsArray = array();
/**
 * @var integer - ������� ������� ���������, ��� ���������� ���������� ��������� - Iterator
 */
private $Position = 0;


/**
 * ���������� �����,
 * ��������� �������� ������� �� ���������� ��������� � ��� ��� � ��������� ������
 * TRMDataObjectsContainer->ObjectName
 * 
 * @param string $name - ��� ��������-�������
 * @return TRMDataObjectInterface
 */
public function __get($name)
{
    return $this->getDataObject($name);
}
/**
 * ���������� �����,
 * ��������� ��������� ������� � ���������, ��������� � ��� ��� � ��������� ������
 * TRMDataObjectsContainer->ObjectName = $value;
 * 
 * @param string $name
 * @param TRMDataObjectInterface $value
 */
/*
public function __set($name, $value)
{
    $this->setDataObject($name, $value);
}
*/

/**
 * @return TRMDataObjectInterface - ���������� ������� (����������� ��� 0-� ������� � �������) ������ ������
 */
public function getMainDataObject()
{
    return $this->MainDataObject;
}

/**
 * ������������� ������� ������ ������,
 * 
 * @param TRMDataObjectInterface $do - ������� ������ ������
 */
public function setMainDataObject(TRMDataObjectInterface $do)
{
    $this->MainDataObject = $do;
}

/**
 * �������� ������ ������ � ������ ��� ������� $Index, ����������� ������ ������, ������ �� �����������!!!
 * 
 * @param string $Index - �����-������, ��� ������� ����� �������� ������ � ����������
 * @param TRMDataObjectInterface $do - ����������� ������
 */
public function setDataObject($Index, TRMDataObjectInterface $do) // ��� TRMParentedDataObject, �� ����� ������ ��� ��� �������� ������
{
    if( method_exists($do, "setParentDataObject") )
    {
        $do->setParentDataObject($this);
    }
    $this->ObjectsArray[$Index] = $do;
}

/**
 * ���������� ������ �� ���������� ��� ������� $Index
 * 
 * @param integer $Index - ����� ������� � ����������
 * 
 * @return TRMDataObjectInterface - ������ �� ����������
 */
public function getDataObject($Index)
{
    if( isset($this->ObjectsArray[$Index]) ) { return $this->ObjectsArray[$Index]; }
    return null;
}

/**
 * @return array - ���������� ������ �������� ������, ����������� �������� ������
 */
public function getObjectsArray()
{
    return $this->ObjectsArray;
}

/**
 * ������� ������ � ���. ��������� ������
 */
public function clearObjectsArray()
{
    // ��� ��� � ������� �������� ������ �� �������� �������, �� ��� �� ��������� ��� ����������� �������,
    // ������� ������� ������������ ��� ������� ������� ������ �������� � null, ����� ��� �� �������� �� ��������� �� �������� ��� �������
    foreach( $this->ObjectsArray as $object )
    {
        if( method_exists($object, "setParentDataObject") )
        {
            $object->setParentDataObject(null);
        }
    }
    $this->ObjectsArray = array();
}

/**
 * @return array - ������ ������ �� ���� ��������� ���� :
 * array(
 * "Main" => ������ �������� �������,
 * "Children" => array(
 *      "NameOfChild1" => ������ ������� ��������� �������,
 *      "NameOfChild2" => ������ ������� ��������� �������,
 *      "NameOfChild3" => ������ �������� ��������� �������,
 * ...
 *      )
 * )
 */
public function getOwnData()
{
    $arr = array( 
        "Main" => $this->MainDataObject->getOwnData(), 
        "Children" => array() );
    
    foreach ($this->ObjectsArray as $Name => $Child)
    {
        if( $Child->count() )
        {
            $arr["Children"][$Name] = $Child->getOwnData();
        }
    }

    return $arr;
}

/**
 * 
 * @param array $data  - ������ �� ���� ��������� ���� :
 * array(
 * "Main" => ������ �������� �������,
 * "Children" => array(
 *      "NameOfChild1" => ������ ������� ��������� �������,
 *      "NameOfChild2" => ������ ������� ��������� �������,
 *      "NameOfChild3" => ������ �������� ��������� �������,
 * ...
 *      )
 * ), ��� ���� � ������� $this->ObjectsArray - ��� ������ ���� �������������������� �������, 
 * �������������� �����, ��� �� ������� ������, � ������ $this->MainDataObject ���� ������ ���� ������
 * 
 * @throws TRMDataObjectContainerNoMainException - � ������� ������ ���� �� ����������� ������ � ������� ����� ���������� - Main, ����� ������������� ����������
 * // ���� �����-�� �� ������ �� ����� � ������� $data, �� ������������� ����������
 */
public function setOwnData(array $data)
{
    // �������� ����� ������� ������ ���� ����������� ������
    if( !isset($data["Main"]) )
    {
        throw new TRMDataObjectContainerNoMainException( __METHOD__ );
    }
    // ��� ��������� ����� ���� �������
    /*
    if( !isset($data["Children"]) )
    {
        throw new Exception( __METHOD__ . " �������� ������ ������! ���������� ���� Children!");
    }
     */
    $this->MainDataObject->setOwnData($data["Main"]);

    foreach( $this->ObjectsArray as $Name => $Child )
    {
        if( !isset($data["Children"][$Name]) )
        {
            // ���� ����� ������ �� ���������, �� ����������
            continue;
            // throw new Exception( __METHOD__ . " �������� ������ ������! ���������� ����� ������� - {$Name} � ������� Children!");
        }
        $Child->setOwnData( $data["Children"][$Name] );
    }
}


/**
 * ���������� ������ ������ ��� ���������-�������� �������!!!
 */
public function getDataArray()
{
    return $this->MainDataObject->getDataArray();
}

/**
 * ������������� ������ ������ � �������� �������
 * @param array $data
 */
public function setDataArray(array $data)
{
    $this->MainDataObject->setDataArray($data);
}
/**
 * ���������� ������ ������ ��� ���������-�������� �������!!!
 * @parm integer $rownum - ����� ������ � ������� (�������) ������� � 0
 * @param string $fieldname - ��� ���� (�������), �� �������� ���������� ������ ��������
 *
 * @retrun mixed|null - ���� ��� ������ � ����� ������� ������ ��� ��� ���� � ����� ������ �������� null, ���� ����, �� ������ ��������
 */
public function getData($rownum, $fieldname)
{
    return $this->MainDataObject->getData($rownum, $fieldname);
}
/**
 * ������������� ������ ������ � �������� �������
 * @param integer $rownum - ����� ������ � ������� (�������) ������� � 0
 * @param string $fieldname - ��� ���� (�������), � ������� ���������� ������ ��������
 * @param mixed $value - ���� ������������ ��������
 */
public function setData($rownum, $fieldname, $value)
{
    $this->MainDataObject->setData($rownum, $fieldname, $value);
}

/**
 * ������������ ������ � ������ ��������� �������!!!
 * ��� �������� ����� ���������� � ������� ������� ��������� ��������
 * @param array $data
 */
public function mergeDataArray(array $data)
{
    $this->MainDataObject->mergeDataArray($data);
}

/**
 * ��������� ������� ������ ������ � �������� �������!!!
 * @param integer  $rownum
 * @param array $fieldnames
 */
public function presentDataIn($rownum, array &$fieldnames)
{
    $this->MainDataObject->presentDataIn($rownum, $fieldnames);
}

public function getFieldValue()
{}

/**
 * ���������� ���������� Countable,
 * ���������� ���������� �������� � ��������� �������� �������� ������
 */
public function count()
{
    return count($this->ObjectsArray);
}


/**
 * ���������� ���������� Iterator,
 * ���������� ������� ������ �� �������-��������� � ��������� ���������
 */
public function current()
{
    return current($this->ObjectsArray);
}

/**
 * 
 * @return mixed - ���������� ��������-��� �������� ������� (�����) ��� ��������� � ��������� ��������� ������,
 * ����� ���� ��������� ��� ���������
 */
public function key()
{
    return key($this->ObjectsArray);
}

/**
 * ������������ ���������� ���������-������� �� ��������� ������� ������� � ��������� ���������
 */
public function next()
{
    next($this->ObjectsArray);
    ++$this->Position;
}

/**
 * ������������� ���������� ������� ������� � ������ - ���������� ���������� Iterator
 */
public function rewind()
{
    reset($this->ObjectsArray);
    $this->Position = 0;
}

/**
 * ���� ������� ��������� ��� ����� ������� �������, ������ � ���� �������� ��� ������ ���,
 * $this->Position ������ ������ ���� < count($this->ObjectsArray)
 * 
 * @return boolean
 */
public function valid()
{
    return ($this->Position < count($this->ObjectsArray));
}


} // TRMDataObjectsContainer