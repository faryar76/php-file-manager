<?php 

use PHPUnit\Framework\TestCase;
use Faryar\Query;
use Faryar\FileManager;
use org\bovigo\vfs\vfsStream;


class QueryTest extends TestCase
{
    protected function setUp() : void 
    {
        $fileManager=new FileManager;
        $vfd=vfsStream::setup('baseFolder',null,['one'=>'d','two'=>'d']);
        $fileManager->setPath($vfd->url());
        $this->query=new Query($fileManager);
    }
    public function test_add_new_condition()
    {
        $this->query->where('name','like','john');
        $actual=$this->query->condition->get(); 
        $this->assertCount(1,$actual);
    }
    public function test_must_return_all_items_if_any_condition_not_set()
    {   
        $actual=$this->query->result();
        $this->assertCount(2,$actual);
    }
    public function test_must_return_result_with_conditions()
    {
        $actual=$this->query->where('name','like','one')->result();
        $this->assertCount(1,$actual);
        $actual=$this->query->where('name','like','test')->result();
        $this->assertCount(0,$actual);
    }
    public function test_where_in_multi_request()
    {
        $actual=$this->query->where('name','==','onfe')->where('name','==','one')->result();
        $this->assertCount(0,$actual);
        $actual=$this->query->where('name','like','one')->result();
        $this->assertCount(1,$actual);
    }
    public function test_where_with_object_as_parameter()
    {
        $actual=$this->query->where('name',function($item){
            return strstr($item,'one');
        })->result();
        $this->assertCount(1,$actual);
    }
    public function test_add_items_to_appends_array_with_duplicate_input()
    {
        $this->query->addAppend('name');
        $actual=$this->query->getAppend();
        $this->assertCount(1,$actual);
        $this->query->addAppend(['name','size','name','path']);
        $actual=$this->query->getAppend();
        $this->assertCount(2,$actual);

    }
    public function test_append_fields_to_input_array()
    {
        $actual=$this->query->where('name','like','one')->result();
        $this->assertEquals([['path'=>'vfs://baseFolder/one']],$actual);
        
        $this->query->addAppend('name');
        $actual=$this->query->where('name','like','one')->result();
        $this->assertEquals([['name'=>'one','path'=>'vfs://baseFolder/one']],$actual);

        $this->query->addAppend('name','none');
        $actual=$this->query->where('name','like','one')->result();
        $this->assertEquals([['name'=>'one','path'=>'vfs://baseFolder/one']],$actual);
    }
    public function test_Must_return_exception_on_invalid_regex()
    {
        $this->expectException(\InvalidArgumentException::class);
        $actual=$this->query->where('name','regex','one')->result();
    }
}