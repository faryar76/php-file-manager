<?php
namespace Faryar;

use Faryar\Condition;
use Faryar\FileManager;
use Faryar\Fields;

class Query 
{
    public $condition;
    protected $fileManager;
    protected $appends=[];
    protected $default_appends=['path'];
    public function __construct(FileManager $fileManager)
    {
        $this->condition=new Condition;
        $this->fileManager=$fileManager;
        $this->fields=new Fields;
    }
    public function where(...$args)
    {
        $args=isset($args[0]) && is_array($args[0]) ? $args[0] : $args;
        
        $this->condition->add('and',$args);
        return $this;
    }
    public function get_all()
    {
        return $this->fileManager->onQueryScan();
    }
    public function result()
    {
        return $this->append_fields($this->run());
    }
    private function append_fields($items)
    {
        $append_fields=array_merge($this->default_appends,$this->appends);
        foreach($items as $index=>$item)
        {
            foreach($append_fields as $field)
            {
                $return[$index][$field]=$this->fields->onPath($item)->$field();
            }
        }
        $this->reset_appends();
        return $return ?? [];
    }
    private function run()
    {
        $conditions=$this->condition->get();
        $this->condition->reset();
        $items=$this->get_all();
        
        if(empty($conditions))
        {
            return $items;
        }
        foreach($conditions['and'] as $condition)
        {
            list($field,$operator,$value)=$condition;
            
            $items=array_values(array_filter($items,function($actual) use ($field,$operator,$value){

                $actual=$this->fields->onPath($actual)->$field();

                $index=isset($index) ? $index+1 : 0;
                if(is_callable($value) && is_object($value))
                {
                    return $value($actual,$index);
                }
                
                return $this->match($actual,$field,$operator,$value);

            }));
            $return=$items;
        }
        return $return ?? [];
    }
    private function match($actual,$field,$operator,$value)
    {
        switch (true) {
                case ($operator == "=="     && $actual==$value):
                return true;
                case ($operator == "==="    && $actual===$value):
                return true;
                case ($operator == "!=="    && $actual!==$value):
                return true;
                case (strtolower($operator) == 'like'   && strstr($actual,$value)):
                return true;
                case ($operator == '>='     && $actual >= $value):
                return true;
                case ($operator == '<='     && $actual <= $value):
                return true;
                case ($operator == '!='     && $actual != $value):
                return true;
                case (strtolower($operator) == 'regex'  && preg_match($value,$actual)):
                return true;
            default:
                return false;
        }
    }
    private function reset_appends()
    {
        $this->appends=[];
        return $this;
    }
    public function with(...$input)
    {
        return $this->addAppend(...$input);
    }
    public function addAppend(...$input)
    {
        if(is_array($input[0]))
        {
            $input=$input[0];
        }
        $input=array_values(array_filter($input,function($item){
            return in_array($item,$this->condition->get_fields());
        }));
        $this->appends=array_diff(array_unique(array_merge($this->appends,(array)$input)),$this->default_appends);
        return $this;
    }
    public function getAppend()
    {
        return $this->appends;
    }
}