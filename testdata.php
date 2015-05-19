<?php

return [
    'eq and numbers' => [
        'rql'   => 'eq(id,1)',
        'valid' => true,
    ],
    'eq and strings' => [
        'rql'   => 'eq(id,a)',
        'valid' => true,
    ],

    'in and numbers' => [
        'rql'   => 'in(id,[1,2,3])',
        'valid' => false,
    ],
    'in and strings' => [
        'rql'   => 'in(id,[a,b,c])',
        'valid' => true,
    ],

    'eq and strings with special chars' => [
        'rql'   => 'eq(id,a-1),eq(id,b+2),eq(id,c:3)',
        'valid' => false,
    ],
    'in and strings with special chars' => [
        'rql'   => 'in(id,[a-1,b+2,c:3])',
        'valid' => false,
    ],

    'missing limit' => [
        'rql'   => 'eq(id,1),sort(+a,-b),limit(1,2)',
        'valid' => false,
    ],
    'missing sort' => [
        'rql'   => 'eq(id,1),limit(1,2),sort(+a,-b)',
        'valid' => false,
    ],
    'missing query' => [
        'rql'   => 'sort(+a,-b),eq(id,1)',
        'valid' => false,
    ],

    'correct limit' => [
        'rql'   => 'limit(1)',
        'valid' => true,
    ],
    'correct limit and offset' => [
        'rql'   => 'limit(1,2)',
        'valid' => true,
    ],

    'more limit arguments' => [
        'rql'   => 'limit(1,2,3)',
        'valid' => false,
    ],
    'string as fields in limit' => [
        'rql'   => 'limit(a,b)',
        'valid' => false,
    ],
];
