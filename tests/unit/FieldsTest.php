<?php 

use PHPUnit\Framework\TestCase;
use Faryar\Fields;
use org\bovigo\vfs\vfsStream;

class FieldsTest extends TestCase
{
    public $field;
    public $vfd;

    protected function setUp() :void
    {
        $this->field=new Fields();
        $this->vfd=vfsStream::setup('baseFolder',null,['one.txt'=>'someval']);

    }
    public function test_must_return_exception_on_non_exist_method()
    {
        $this->expectException(\BadMethodCallException::class);
        $path=$this->vfd->url().DIRECTORY_SEPARATOR.'one.txt';
        $actual=$this->field->onPath($path)->noneExists();
    }
    public function test_must_reuturn_exception_if_path_not_set()
    {
        $this->expectException(\Exception::class);
        $actual=$this->field->noneExists();
    }
    public function test_get_file_name()
    {
        $path=$this->vfd->url().DIRECTORY_SEPARATOR.'one.txt';
        $actual=$this->field->onPath($path)->name();
        $this->assertEquals('one.txt',$actual);
    }
    public function test_get_file_path()
    {
        $path=$this->vfd->url().DIRECTORY_SEPARATOR.'one.txt';
        $actual=$this->field->onPath($path)->path();
        $this->assertEquals($path,$actual);
    }
    public function test_get_file_type()
    {
        $path=$this->vfd->url().DIRECTORY_SEPARATOR.'one.txt';
        $actual=$this->field->onPath($path)->type();
        $this->assertEquals('file',$actual);
    }
    public function test_get_file_size()
    {
        $path=$this->vfd->url().DIRECTORY_SEPARATOR.'one.txt';
        $actual=$this->field->onPath($path)->size();
        $this->assertEquals(7,$actual);
    }
   
}