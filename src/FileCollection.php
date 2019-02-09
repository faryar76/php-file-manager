<?php
 namespace Faryar;

 use ArrayAccess;
 use CountAble;

 class FileCollection implements ArrayAccess,CountAble
 {  
    private $items=[];

    public function __construct($data=[])
    {
        $this->items=$data;
    }
    public function __set($key,$value)
    {
        if($key==null)
        {
            $this->items[]=$value;
        }else{
            $this->items[$key]=$value;
        }
    }
    public function all(){
        return $this->items;
    }
    public function __get($key)
    {
        return $this->items[$key] ?? null;
    }
    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }
    public function count()
    {
        return count($this->items);
    }
    public function offsetGet($offset)
    {
        return $this->items[$offset] ?? null;
    }

    public function offsetSet($offset, $value)
    {
        
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }       
    }

    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }
    public function add($item,$key=null)
    {
        if(is_null($key))
        {
            return (bool) $this->items[]=$item;
        }   
        return (bool) $this->items[$key]=$item;
    }
 }