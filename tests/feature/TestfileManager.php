<?php 

use Faryar\FileManager;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class TestfileManager extends TestCase
{
    public $fileManager;
    public $vfd;
    protected function setUp() : void 
    {
        $this->fileManager=new FileManager;
        $folder=[
            'file1.txt'=>'',
            'file2.txt'=>'',
            'file3.txt'=>'',
            'folder1'=>
                [
                    'file4.txt'=>'',
                    'file5.txt'=>'',
                    'folder2'=>
                        [
                            'file6.txt'=>''
                        ],
                ],
            ];
        $this->vfd=vfsStream::setup('baseFolder',null,$folder);
    }
     
    /**
     * @dataProvider provide_data_for_items_on_where_with_none_oparator
     */
    public function test_must_return_items_on_none_oparator($data,$expected)
    {
        $actual=$this->fileManager->on($this->vfd->url())->query()->where('name',$data)->result();
        $this->assertCount($expected, $actual);
    }
    public function provide_data_for_items_on_where_with_none_oparator()
    {
        return 
            [
                ['file3.txt',1],
                ['fakeFile.txt',0],
                ['folder1',1]
            ];
    }

    /**
     * @dataProvider provide_data_for_items_on_where_with_regex
     */
    public function test_must_return_items_on_where_with_regex($data,$expected)
    {
        $actual=$this->fileManager->on($this->vfd->url())->query()->where('name','regeX',$data)->result();
        $this->assertCount($expected, $actual);
    }
    public function provide_data_for_items_on_where_with_regex()
    {
        return 
            [
                ['/file/',6],
                ['/folder/',2],
                ['/folder$/',0],
            ];
    }

    /**
     * @dataProvider provide_data_for_items_on_where_with_type
     */
    public function test_must_return_items_on_where_with_type($data,$expected)
    {
        $actual=$this->fileManager->on($this->vfd->url())->query()->where('type',$data)->result();
        $this->assertCount($expected, $actual);
    }
    public function provide_data_for_items_on_where_with_type()
    {
        return 
            [
                ['file',6],
                ['dir',2],
            ];
    }

}