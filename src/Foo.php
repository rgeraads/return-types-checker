<?php

namespace ReturnTypesChecker;

class Foo
{
    public function bar()
    {
        $a = function () {};
        return 'bla1';
    }

}

function baz() {
    return 'bla2';
};
