<?php

namespace TRMEngine\DataObject;

use TRMEngine\DataObject\Exceptions\TRMDataObjectSCollectionWrongIndexException;
use TRMEngine\DataObject\Interfaces\TRMDataObjectInterface;

/**
 * ����� ��� ������ � ����������� ���������� �������� DataObject
 * 
 * @version 2019-03-29
 */
class TRMDataObjectsCollection implements \ArrayAccess, \Iterator, \Countable
{
/**
 * @var int - ������� ������� ��������� � ������� ��� ���������� ���������� Iterator
 */
private $Position = 0;
/**
 * @var array(TRMDataObjectInterface) - ������-��������� � ��������� ������ TRMDataObject
 */
protected $DataObjectsArray = array();

/**
 * @param int $Index - ������ �������������� ������� � �������-���������
 * 
 * @return TRMDataObjectInterface - ������ ������
 * @throws TRMDataObjectSCollectionWrongIndexException
 */
public function getDataObject($Index)
{
    if( !key_exists($Index, $this->DataObjectsArray) )
    {
        throw new TRMDataObjectSCollectionWrongIndexException();
    }
    return $this->DataObjectsArray[$Index];
}

/**
 * @param int $Index - ������������� ������ ������� � ��������� ��������
 * @param TRMDataObjectInterface $DataObject - ������ ��� ��������� � ���������
 * 
 * @throws TRMDataObjectSCollectionWrongIndexException
 */
public function setDataObject($Index, TRMDataObjectInterface $DataObject)
{
    if( !key_exists($Index, $this->DataObjectsArray) )
    {
        throw new TRMDataObjectSCollectionWrongIndexException();
    }
    $this->DataObjectsArray[$Index] = $DataObject;
}

/**
 * @param TRMDataObjectInterface $DataObject - ������� ��� ������ � ���������
 */
public function addDataObject( TRMDataObjectInterface $DataObject )
{
    $this->DataObjectsArray[] = $DataObject;
    return key( $this->DataObjectsArray );
}

/**
 * ������� ������-��������� � ��������� ������,
 * ��� ��� � ������� �������� ������ ������, 
 * �� ���� ������� �������� � ������, ���� �� ���-�� ����������
 */
public function clearCollection()
{
    $this->DataObjectsArray = array();
    $this->Position = 0;
}

// ********************    **************************************************

public function count()
{
    return count($this->DataObjectsArray);
}

// ********************    **************************************************

public function current()
{
    return $this->DataObjectsArray[$this->Position];
    // return current($this->DataObjectsArray);
}

public function key()
{
    return $this->Position;
    // return key($this->DataObjectsArray);
}

public function next()
{
    $this->Position++;
    // next($this->DataObjectsArray);
}

public function rewind()
{
    $this->Position = 0;
    //rewind($this->DataObjectsArray);
}

public function valid()
{
    if(key_exists($this->Position, $this->DataObjectsArray) )
    {
        return true;
    }
    return false;
}

// ********************    **************************************************

public function offsetExists($offset)
{
    if(key_exists($offset, $this->DataObjectsArray) )
    {
        return true;
    }
    return false;
}

public function offsetGet($offset)
{
    return $this->DataObjectsArray[$offset];
}

public function offsetSet($offset, $value)
{
    $this->DataObjectsArray[$offset] = $value;
}

public function offsetUnset($offset)
{
    unset($this->DataObjectsArray[$offset]);
}

} // TRMDataObjectsCollection
