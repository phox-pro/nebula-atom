<?php

namespace Phox\Nebula\Atom\Implementation;

use Phox\Nebula\Atom\Implementation\Exceptions\ConsoleException;

class Console 
{
    protected array $arguments;

    protected ?string $caller;

    protected string $module;

    protected string $command;

    protected array $options = [];

	public function __construct(array $arguments)
	{
        $this->arguments = $arguments;
    }

    public function run()
    {
        $this->prepare(); 
    }
    
    protected function prepare()
    {
        $arguments = $this->arguments;
        $this->caller = $arguments[0];
        unset($arguments[0]);
        array_key_exists(1, $arguments) ?: error(ConsoleException::class, 'Atom command is required');
        $command = array_shift($arguments);
        preg_match('/([\w\d]+)::([\w\d]+)/i', $command, $matches) ?: error(ConsoleException::class, "Command must match '{module}::{command}' pattern, '{$command}' was given");
        $this->module = $matches[1];
        $this->command = $matches[2];
        $options = array_filter($arguments, function ($item) {
			return strpos($item, '-') === 0 || strpos($item, '--') === 0;
        });
		foreach ($options as $index => $value) {
			if (array_key_exists($index + 1, $options)) {
				$this->options[$value] = true;
				continue;
			} elseif (array_key_exists($index + 1, $arguments)) {
				$this->options[$value] = $arguments[$index + 1];
			} else {
				$this->options[$value] = true;
			}
		}
    }
}