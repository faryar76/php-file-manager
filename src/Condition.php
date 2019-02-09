<?php 

namespace Faryar;

class Condition
{
    protected $conditions=[];
    protected $oparators=
            [
                '==',
                '===',
                '!==',
                '!=',
                '>=',
                '<=',
                'regex',
                'like',
            ];

    protected $fields=
            [
                'name',
                'type',
                'path',
                'size',
            ];  


    public function add($logic,$args)
    {
        $this->validateBeforeAdd($args);
        if(count($args) == 2)
        {
            $args=[$args[0],'==',$args[1]];
        }

        $this->conditions[$logic][]=$args;
    }

    public function get_fields()
    {
        return $this->fields;
    }
    public function get()
    {
        return $this->conditions;
    }
    public function reset()
    {
        $this->conditions=[];
        return  true;
    }
    private function validateBeforeAdd($args)
    {
        switch (true) {
            case (!is_array($args)):
            throw new \InvalidArgumentException(sprintf("input must be array %s given",gettype($args)));

            case (!in_array($args[0],$this->fields)):
            throw new \InvalidArgumentException(sprintf("fields [%s] not found!",$args[0]));

            case (count($args) == 3 && !in_array(strtolower($args[1]),$this->oparators)):
            throw new \InvalidArgumentException(sprintf("oparator [%s] not found!",$args[1]));

            case (count($args) > 3 || count($args) < 2):
            throw new \InvalidArgumentException(sprintf("input  array count must be 2 %s given",count($args)));

            default:
            return true;    
        }
    }
}