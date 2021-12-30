<?php

namespace AliReaza\DotEnv\Resolver;

use LogicException;
use Symfony\Component\Process\Exception\ExceptionInterface as ProcessException;
use Symfony\Component\Process\Process;

/**
 * Class Commands
 *
 * @package AliReaza\DotEnv\Resolver
 */
class Commands
{
    /**
     * @param string $data
     * @param array  $env
     *
     * @return string
     */
    public function __invoke(string $data, array $env): string
    {
        if (str_contains($data, '$')) {
            $regex = '/
            (?<!\\\\)
            (?P<backslashes>\\\\*)               # escaped with a backslash?
            \$
            (?P<opening_parenthesis>\()          # require opening parenthesis
            (?P<cmd>(?i:[A-Z][A-Z0-9_]*+))?      # cmd
            (?P<closing_parenthesis>\))          # require closing parenthesis
        /x';

            $data = preg_replace_callback($regex, function ($matches) use ($env): string {
                // odd number of backslashes means the $ character is escaped
                if (strlen($matches['backslashes']) % 2 === 1) {
                    return substr($matches[0], 1);
                }

                if (!class_exists(Process::class)) {
                    throw new LogicException('Resolving commands requires the Symfony Process component.');
                }

                if (method_exists(Process::class, 'fromShellCommandline')) {
                    $process = Process::fromShellCommandline('echo ' . $matches[0]);
                } else {
                    $process = new Process(['echo ' . $matches[0]]);
                }

                $process->setEnv($env);

                try {
                    $process->mustRun();
                } catch (ProcessException) {
                    throw new LogicException(sprintf('Issue expanding a command (%s)', $process->getErrorOutput()));
                }

                return preg_replace('/[\r\n]+$/', '', $process->getOutput());
            }, $data);
        }

        return $data;
    }
}