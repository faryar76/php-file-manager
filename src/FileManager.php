<?php
namespace Faryar;

use  Faryar\FileCollection;

class FileManager
{
    protected $path;

    public function scan(String $path)
    {
        return array_diff(scandir($path),['.','..']);
    }
    
    public function onQueryScan()
    {
        return $this->full_scan($this->path);
    }

    public function full_scan(String $path)
    {
        $items=$this->scan($path);
        foreach($items as $item)
        {
            $curent_file=$path.DIRECTORY_SEPARATOR.$item;

            if(is_file($curent_file))
            {
                $return[]=$curent_file;
                continue;
            }
            $return[]=$curent_file;
            $subItems=$this->full_scan($curent_file);

            foreach($subItems as $subItem)
            {
                $return[]=$subItem;
            }
        }
        return $return ?? [];
    }
    public function query()
    {
        return new Query($this);
    }
    public function setPath(string $path)
    {
        $this->path=$path;
        return $this;
    }
    public function on(string $path)
    {
        $this->path=$path;
        return $this;
    }
    public function __call($func,$args)
    {
        if(method_exists($this,$func))
        {
            return $this->$func($args);
        }
        if(method_exists(Query::class,$func))
        {
            return ($this->query())->$func($args);
        }
        
        throw new \BadMethodCallException("method not found");
        
    }

}