<?php

declare(strict_types=1);

namespace AliReaza\Tests\DotEnv\Resolver\Unit;

use AliReaza\DotEnv\DotEnv;
use AliReaza\DotEnv\Resolver\Commands;
use PHPUnit\Framework\TestCase;

class CommandsTest extends TestCase
{
    public function test_When_use_Commands_Resolver_Expect_env_property_must_array_of_file_variables_and_all_commands_inside_values_have_value()
    {
        mkdir($tmpdir = sys_get_temp_dir() . '/dotenv');

        $file = tempnam($tmpdir, 'alireaza-');

        file_put_contents($file, 'FOO=$(date \'+%Y-%m-%d\')');

        $env = new DotEnv($file, [
            new Commands()
        ]);

        unlink($file);

        rmdir($tmpdir);

        $this->assertTrue(is_array($env->toArray()) && $env->has('FOO') && $env->get('FOO') === date('Y-m-d'));
    }
}
