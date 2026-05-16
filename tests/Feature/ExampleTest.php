<?php

test('returns a successful response', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
});

test('application is named Iluma Todo', function () {
    expect(config('app.name'))->toBe('Iluma Todo');
});
