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
                case (strtolower($operator) == 'regex' && $this->validate_regex($value)  && preg_match($value,$actual)):
                return true;
            default:
                return false;
        }
    }
    private function validate_regex($regex)
    {
            
        if(@preg_match($regex,null)===false)
        {
            throw new \InvalidArgumentException("Error on regex syntax : ". preg_last_error() ?? "");
        }
        return true;
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
    public function rename($newName)
    {
        $items=$this->result();
        foreach($items as $index=>$item)
        { 
            $name=$newName;
            if(is_object($newName) && is_callable($newName))
            {
                $name=$newName($item,$index);
            }
            $file_number=0;
            $base_path=dirname($item['path']).DIRECTORY_SEPARATOR.$name;
            while(file_exists($base_path.($file_number==0 ? "" : $file_number)))
            {
                $file_number+=1;
            }
            rename($item['path'],$base_path.($file_number==0 ? "" : $file_number));
        }
        return true;
    }
    public function delete($input_item)
    {
        $result=$input_item;
        $items=$this->result();
        foreach($items as $index=>$item)
        { 
            if(is_object($input_item) && is_callable($input_item))
            {
                $result=$input_item($item,$index);
            }
            if($result)
            {
                if(is_file($item['path']))
                {
                    return unlink($item['path']);
                }
                return rmdir($item['path']);
            }
        }
        return false;
    }
}