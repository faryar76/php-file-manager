<?php 
use Faryar\Condition;
use PHPUnit\Framework\TestCase;

class ConditionTest extends TestCase
{
    public $condition;

    protected function setUp() : void
    {
        $this->condition=new Condition();
    }
    
    public function test_exception_on_none_array()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("input must be array string given");
        $this->condition->add('and','none array args');
    }

    public function test_exception_on_higher_than_three_item()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("input  array count must be 2 4 given");
        $this->condition->add('and',['name','==','three','four']);
    }
    public function test_must_add_two_item_to_condition_property()
    {
        $this->condition->add('and',['name','==','john']);
        $this->condition->add('and',['name','==','test']);
        $this->condition->add('or',['type','==','john']);

        $actual=$this->condition->get();
        $this->assertCount(2,$actual);
        $this->assertCount(2,$actual['and']);
    }
    public function test_must_throw_exception_on_none_defined_condition()
    {
        $this->expectException(\InvalidArgumentException::class,3);
        $this->condition->add('and',['name','g==','john']);
    }
    public function test_must_throw_exception_on_none_defined_logic()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->condition->add('none exists',['none','john']);
    }
    public function test_must_item_two_length__args()
    {
        $this->condition->add('and',['name','john']);
        $actual=$this->condition->get();
        $this->assertEquals(['name','==','john'],$actual['and'][0]);
    }
    public function test_reset_condition_items()
    {
        $this->condition->add('and',['name','john']);
        $actual=$this->condition->reset();
        $this->assertTrue($actual);
        $actual=$this->condition->get();
        $this->assertEmpty($actual);
    }
    public function test_add_condition_with_object()
    {
        $this->condition->add('and',['name',function(){}]);
        $actual=$this->condition->get();
        $this->assertCount(1,$actual); 
    }
    public function test_must_return_exception_on_where_without_parametr()
    {
        $this->expectException(\InvalidArgumentException::class);        
        $this->condition->add('add',[]);
    }

}