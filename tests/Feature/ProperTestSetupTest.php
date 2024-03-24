<?php

test('check .env-test file exists', function () {
    $this->assertTrue(file_exists(base_path('.env.testing')));
});
