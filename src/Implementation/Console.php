<?php

namespace Phox\Nebula\Atom\Implementation;

use Phox\Nebula\Atom\Implementation\Basics\Collection;
use Phox\Nebula\Atom\Implementation\Exceptions\ConsoleException;
use Phox\Nebula\Atom\Notion\Interfaces\ICommand;

class Console 
{
    protected array $arguments;

    protected ?string $caller;

    protected string $module;

    protected string $command;

    protected Collection $options;

    protected Collection $commands;

	public function __construct(array $arguments)
	{
        $this->commands = new Collection(Collection::class);
        $this->options = new Collection('string');
        $this->arguments = $arguments;
    }

    /**
	 * Execute command
	 *
	 * @return void
	 */
    public function run()
    {
        $this->prepare();
        if (!$this->commands->hasIndex($this->module)) {
            error(ConsoleException::class, "Module '{$this->module}' does not contain a commands");
        }
        $moduleCommands = $this->commands->get($this->module);
        if (!$moduleCommands->hasIndex($this->command)) {
            error(ConsoleException::class, "Module '{$this->module}' does not contain '{$this->command}' command");
        }
        $command = $moduleCommands->get($this->command);
        $command->run();
    }

    /**
	 * Get value of console option
	 *
	 * @param string $name Option name
	 * @return mixed
	 */

    public function option(string $name)
    {
        switch (true) {
            case $this->options->hasIndex($name): return $this->options->get($name);
            case $this->options->hasIndex('-' . $name): return $this->options->get('-' . $name);
            case $this->options->hasIndex('--' . $name): return $this->options->get('--' . $name);
            default: return null;
        }
    }

    /**
	 * Check if option exists
	 *
	 * @param string $name Option name
	 * @return boolean
	 */
    public function hasOption(string $name) : bool
    {
        return !is_null($this->option($name));
    }

    /**
     * Get all concole options
     *
     * @return Collection
     */
    public function getOptions() : Collection
    {
        return $this->options;
    }

    /**
     * Register command in collection
     *
     * @param string $commandClass
     * @return void
     */
    public function registerCommand(string $commandClass)
    {
        $module = $commandClass::$module;
        $command = $commandClass::$command;
        $this->commands->hasIndex($module) ?: $this->commands->set($module, new Collection(ICommand::class));
        $moduleCommands = $this->commands->get($module);
        $moduleCommands->hasIndex($command)
            ? error(ConsoleException::class, "Command '{$command}' already exists in module '{$module}'")
            : $moduleCommands->set($command, make($commandClass));
    }
    
    protected function prepare()
    {
        !empty($this->arguments) ?: error(ConsoleException::class, 'Bad arguments');
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
                $this->options->set($value, (string)true);
				continue;
			} elseif (array_key_exists($index + 1, $arguments)) {
                $this->options->set($value, $arguments[$index + 1]);
			} else {
                $this->options->set($value, (string)true);
			}
		}
    }
}