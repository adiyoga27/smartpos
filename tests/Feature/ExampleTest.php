<?php

test('unauthenticated users are redirected to login', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});

test('login page returns successful response', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});
