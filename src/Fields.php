<?php 
namespace Faryar;

class Fields 
{
    protected $path;

    public function onPath(string $path)
    {
        $this->path=$path;
        return $this;
    }
    public function __call($func,$args)
    {
        if(is_null($this->path))
        {
            throw new \Exception("path not set!");
        }
        if(! method_exists($this,$func))
        {
            throw new \BadMethodCallException("method not found");
        }
        return $this->$func($args);
    }
    private function name() : string
    {
        return basename($this->path);
    }
    private function path()
    {
        return $this->path;
    }
    private function type()
    {
        return is_dir($this->path) ? 'dir' : 'file';
    }
    private function size()
    {
        return round(filesize($this->path));
    }
}