<?php 

use PHPUnit\Framework\TestCase;

use org\bovigo\vfs\vfsStream;
use Faryar\FileManager;
use Faryar\Query;

class FileManagerTest extends TestCase
{
    public $folder;
    public $file_manager;

    protected function setUp() : void
    {
        $this->file_manager=new FileManager;
        $this->folder=vfsStream::setup('basefolder',null,[
            'file.txt'=>'some val',
            'folder1'=>
                [
                    'file2.txt'=>'value',
                    'folder2'=>
                    [
                        'file3.txt'=>'none'
                    ]
                ]
            ]);
    }

    public function test_scan_directory()
    {
        $path=$this->folder->url();
        $actual=$this->file_manager->scan($path);
        $this->assertCount(2,$actual);
    }

    public function test_scan_directory_with_sub_items()
    {
        $path=$this->folder->url();
        $actual=$this->file_manager->full_scan($path);
        $this->assertCount(5,$actual);
    }
    public function test_must_return_new_instant_of_query()
    {
        $actual=$this->file_manager->query();
        $this->assertInstanceOf(Query::class,$actual);
    }
    public function test_scan_must_return_full_path()
    {
        $path=$this->folder->url();
        $actual=$this->file_manager->full_scan($path);
        $this->assertEquals('vfs://basefolder/file.txt',$actual[0]);
    }
    public function test_must_return_exception_on_none_exist_method_on_this_and_Query_class()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->file_manager->noneExistMethod();
    }
    public function test_must_return_query_class_method_if_method_on_this_not_exists()
    {
        $actual=$this->file_manager->where('name','like','faryar');
        $this->assertInstanceOf(Query::class,$actual);

    }
}