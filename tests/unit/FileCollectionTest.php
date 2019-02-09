<?php 

use PHPUnit\Framework\TestCase;
use Faryar\FileCollection;

class FileCollectionTest extends TestCase
{
    public $collection;
    protected function setUp(): void
    {
        $this->collection=new FileCollection([]);
    }
    public function test_set_and_get_a_test_value_like_array()
    {
        $this->collection['key']='value';
        $this->assertEquals('value',$this->collection['key']);
    }
    public function test_set_and_get_value_to_collection_as_object()
    {
        $result=$this->collection->someName="this is value";
        $this->collection['key']='value';

        $this->assertEquals($this->collection['someName'],$this->collection->someName);
        $this->assertEquals('value',$this->collection->key);
        $this->assertEquals(null,$this->collection->someNotExistVal);
    }
    public function test_get_all_items()
    {
        $data=[
            [
                'name'=>'onename',
                'path'=>'path to file',
            ],
            [
                'name'=>'twoname',
                'path'=>'path to file2',
            ],
            [
                'name'=>'three name',
                'path'=>'path to file3',
            ]
        ];
        $obj=new FileCollection($data);
        $this->assertEquals($data,$obj->all());
    }
    public function test_add_new_item_to_collection()
    {
        $this->collection->add('some');
        $this->collection->add('other');
        $this->assertEquals(['some','other'],$this->collection->all());
    }
   
}