<?php

namespace TRMEngine\DataObject;

use TRMEngine\DataObject\Exceptions\TRMDataObjectContainerNoMainException;
use TRMEngine\DataObject\Interfaces\TRMDataObjectInterface;
use TRMEngine\DataObject\Interfaces\TRMDataObjectsContainerInterface;
use TRMEngine\DataObject\Interfaces\TRMIdDataObjectInterface;
use TRMEngine\DataObject\Interfaces\TRMRelationDataObjectsContainerInterface;

/**
 * ����� ��������� �������� ������, ������������ ��� ��������� ��������.
 * 
 * ������������ 
 * 1. ��� ��� ��������-�����,
 * ��������, ��� ���������� �������� �� ����� ��������������� ����������� � ���������,
 * ������� ������� �� ID-�������� �������
 * (��������� �������������, ������������� ��������, ���.����������� � �.�.)
 * 
 * 2. ��� � ��� ������������,
 * ����� ���� ������� ������ � �������-�����������,
 * �� ������� ������� ������ ������� � � ������ ����� �� ID.
 * ���� ����������� �������� ����������� ����������, ��������,
 * ������������� ����� �� ������� �� ������, 
 * �� ����� ������ ����� ���� ID_vendor � ������� �� ������������� �� ��� ID...
 */
abstract class TRMDataObjectsContainer implements 
        TRMDataObjectsContainerInterface, 
        TRMRelationDataObjectsContainerInterface
{
/**
 * @var TRMIdDataObjectInterface - �������� ������ � ���������� ��������������� ID,
 * �� ����������� ID ������� � ���������� ����������� � ������� ��������
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
 * @var array(TRMIdDataObjectInterface) - ������ �������� ������, ����������� �������� ������, 
 * �������� ��������� �������������, ���.�����������, ���������, ������ � �.�.
 */
protected $DependenciesObjectsArray = array();
/**
 * @var array - ������ ������������, 
 * ������ ������� ������� - ��� ������������� ������� � �����������,
 * ���������� ��� ���-������� � ������� ������� � ��� ���� ����� ���-�������
 * ��� ����� � ID-�����������
 * (..., "ObjectIndex" => array( "RelationSubObjectName" => type, "RelationFieldName" =>fieldname ), ... )
 */
protected $DependenciesArray = array();


/**
 * �������� ������ ������ � ������ $Index � ������-��������� ������������, 
 * ����������� ������ ������, ������ �� �����������!!!
 * 
 * @param string $Index - ���/�����-������, ��� ������� ����� �������� ������ � ����������
 * @param TRMIdDataObjectInterface $do - ����������� ������
 * @param string $ObjectName - ��� ���-������� � ������� �������, �� �������� ����������� �����������
 * @param string $FieldName - ��� ���� ��������� ���-������� � ������� �������, �� �������� ����������� �����������
 */
public function setDependence($Index, TRMIdDataObjectInterface $do, $ObjectName, $FieldName )
{
    $this->DependenciesArray[$Index] = array( strval($ObjectName), strval($FieldName) ); 
    
    $this->setDataObject($Index, $do);
}

/**
 * ���������� ������ � ������� ����� ����������� � �������� $Index
 * 
 * @param string $Index - ���/�����-������ ������� � ����������
 * 
 * @return array - ��� ���-������� � ���� � ���-������� �������� �������, 
 * �� �������� ����������� ����� � ID ����������� ��� �������� $Index
 */
public function getDependence($Index)
{
    return isset($this->DependenciesArray[$Index]) ? $this->DependenciesArray[$Index] : null;
}

/**
 * ���������� ������ ����������� � �������� $Index �� ���������� ��������
 * 
 * @param string $Index - ���/�����-������ ������� � ����������
 * 
 * @return array - ��� ���-������� � ���� � ���-������� �������� �������, 
 * �� �������� ����������� ����� � ID ����������� ��� �������� $Index
 */
public function getDependenceObject($Index)
{
    return $this->getDataObject($Index);
}

/**
 * 
 * @param string $Index - ������ ������� � ����������
 * @return bool - ���� ������ � ���������� ��� ���� �������� ������������ ��� ��������� �� ��������,
 * ��������, ������ ������������� ��� ������, �� �������� true, ���� ����������� �� ����������, �� - false
 */
public function isDependence($Index)
{
    return key_exists($Index, $this->DependenciesArray);
}

/**
 * @return array - ������ �������� � ������������� ����:
 * array("ObjectName" => array( "RelationSubObjectName" => type, "RelationFieldName" =>fieldname ), ... )
 */
public function getDependenciesArray()
{
    return $this->DependenciesArray;
}














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
 * @return TRMIdDataObjectInterface - ���������� ������� (����������� ��� 0-� ������� � �������) ������ ������
 */
public function getMainDataObject()
{
    return $this->MainDataObject;
}

/**
 * ������������� ������� ������ ������,
 * 
 * @param TRMIdDataObjectInterface $do - ������� ������ ������
 */
public function setMainDataObject(TRMIdDataObjectInterface $do)
{
    $this->MainDataObject = $do;
}

/**
 * �������� ������ ������ � ������ ��� ������� $Index, ����������� ������ ������, ������ �� �����������!!!
 * 
 * @param string $Index - �����-������, ��� ������� ����� �������� ������ � ����������
 * @param TRMDataObjectInterface $do - ����������� ������
 */
public function setChildObject($Index, Interfaces\TRMParentedDataObjectInterface $do) // ��� TRMParentedDataObject, �� ����� ������ ��� ��� �������� ������
{
    $do->setParentDataObject($this);

    $this->setDataObject($Index, $do);
}

/**
 * �������� ������ ������ � ������ ��� ������� $Index, ����������� ������ ������, ������ �� �����������!!!
 * 
 * @param string $Index - �����-������, ��� ������� ����� �������� ������ � ����������
 * @param TRMDataObjectInterface $do - ����������� ������
 */
private function setDataObject($Index, TRMDataObjectInterface $do) // ��� TRMParentedDataObject, �� ����� ������ ��� ��� �������� ������
{
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
 * ������� ������ � ���. ��������� ������,
 * ��� �� � ���� �������� �������� ������ �� ���� ������������ ���������
 */
public function clearObjectsArray()
{
    // ��� ��� � ������� �������� ������ �� �������� �������, �� ��� �� ��������� ��� ����������� �������,
    // ������� ������� ������������� ��� ������� ������� ������ �������� � null, 
    // ����� ��� �� ��������� �� ��������� �� �������� ��� �������
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
 * @param string $objectname - ��� ������� � ������ � ������� $rownum, ��� �������� ���������� ������
 * @param string $fieldname - ��� ���� (�������), �� �������� ���������� ������ ��������
 *
 * @retrun mixed|null - ���� ��� ������ � ����� ������� ������ ��� ��� ���� � ����� ������ �������� null, ���� ����, �� ������ ��������
 */
public function getData($rownum, $objectname, $fieldname)
{
    return $this->MainDataObject->getData($rownum, $objectname, $fieldname);
}
/**
 * ������������� ������ ������ � �������� �������
 * @param integer $rownum - ����� ������ � ������� (�������) ������� � 0
 * @param string $objectname - ��� ������� � ������ � ������� $rownum, ��� �������� ��������������� ������
 * @param string $fieldname - ��� ���� (�������), � ������� ���������� ������ ��������
 * @param mixed $value - ���� ������������ ��������
 */
public function setData($rownum, $objectname, $fieldname, $value)
{
    $this->MainDataObject->setData($rownum, $objectname, $fieldname, $value);
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
 * @param string $objectname - ��� ������� � ������ � ������� $rownum, ��� �������� ����������� ����� ������
 * @param array $fieldname
 */
public function presentDataIn($rownum, $objectname, array &$fieldname)
{
    $this->MainDataObject->presentDataIn($rownum, $objectname, $fieldname);
}
/****************************************************************************
 * ���������� ���������� TRMIdDataObjectInterface
 ****************************************************************************/
public function getId()
{
    return $this->MainDataObject->getId();
}
public function setId($id)
{
    $this->MainDataObject->setId($id);
}
public function resetId()
{
    $this->MainDataObject->resetId();
}

public function getIdFieldName()
{
    return $this->MainDataObject->getIdFieldName();
}
public function setIdFieldName(array $IdFieldName)
{
    $this->MainDataObject->setIdFieldName($IdFieldName);
}

/**
 * ���������� �������� ���������� � ���� $fieldname ������� $objectname
 * 
 * @param string $objectname - ��� �������, ��� �������� ���������� ������
 * @param string $fieldname - ��� ����
 * @return mixed|null - ���� ���� �������� � ���� $fieldname, �� �������� ��� ��������, ���� null,
 */
public function getFieldValue($objectname, $fieldname)
{
    $this->MainDataObject->getData(0, $objectname, $fieldname);
}
/**
 * ������������� �������� � ���� $fieldname ������� $objectname
 * 
 * @param string $objectname - ��� �������, ��� �������� ���������� ������
 * @param string $fieldname - ��� ����
 * @param mixed -  ��������, ������� ������� ���� ����������� � ���� $fieldname ������� $objectname
 */
public function setFieldValue($objectname, $fieldname, $value)
{
    $this->MainDataObject->setData(0, $objectname, $fieldname, $value);
}

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