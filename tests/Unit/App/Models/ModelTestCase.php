<?php

namespace Tests\Unit\App\Models;

use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;

abstract class ModelTestCase extends TestCase
{
    abstract protected function model(): Model;
    abstract protected function traits(): array;
    abstract protected function fillable(): array;
    abstract protected function casts(): array;

    public function test_check_traits()
    {
        $traits = array_keys(class_uses($this->model()));

        $this->assertEquals($this->traits(), $traits);
    }

    public function test_check_fillable()
    {
        $fillables = $this->model()->getFillable();

        $this->assertEquals($this->fillable(), $fillables);
    }

    public function test_incrementing_is_false()
    {
        $incrementing = $this->model()->incrementing;

        $this->assertFalse($incrementing);
    }

    public function test_casts_check()
    {
        $casts = $this->model()->getCasts();


        $this->assertEquals($this->casts(), $casts);
    }
}
