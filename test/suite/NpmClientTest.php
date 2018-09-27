<?php

namespace Eloquent\Composer\NpmBridge;

use Eloquent\Phony\Phpunit\Phony;
use PHPUnit\Framework\TestCase;

class NpmClientTest extends TestCase
{
    protected function setUp()
    {
        $this->processExecutor = Phony::mock('Composer\Util\ProcessExecutor');
        $this->executableFinder = Phony::mock('Symfony\Component\Process\ExecutableFinder');
        $this->getcwd = Phony::stub();
        $this->chdir = Phony::stub();
        $this->client =
            new NpmClient($this->processExecutor->get(), $this->executableFinder->get(), $this->getcwd, $this->chdir);

        $this->processExecutor->execute->returns(0);
        $this->executableFinder->find->with('npm')->returns('/path/to/npm');
        $this->getcwd->returns('/path/to/cwd');
    }

    public function testInstall()
    {
        $this->assertNull($this->client->install('/path/to/project'));
        $this->assertNull($this->client->install('/path/to/project'));
        Phony::inOrder(
            $this->executableFinder->find->calledWith('npm'),
            $this->chdir->calledWith('/path/to/project'),
            $this->processExecutor->execute->calledWith("'/path/to/npm' 'install'"),
            $this->chdir->calledWith('/path/to/cwd'),
            $this->chdir->calledWith('/path/to/project'),
            $this->processExecutor->execute->calledWith("'/path/to/npm' 'install'"),
            $this->chdir->calledWith('/path/to/cwd')
        );
    }

    public function testInstallProductionMode()
    {
        $this->assertNull($this->client->install('/path/to/project', false));
        Phony::inOrder(
            $this->executableFinder->find->calledWith('npm'),
            $this->chdir->calledWith('/path/to/project'),
            $this->processExecutor->execute->calledWith("'/path/to/npm' 'install' '--production'"),
            $this->chdir->calledWith('/path/to/cwd')
        );
    }

    public function testInstallFailureNpmNotFound()
    {
        $this->executableFinder->find->with('npm')->returns(null);

        $this->expectException('Eloquent\Composer\NpmBridge\Exception\NpmNotFoundException');
        $this->client->install('/path/to/project');
    }

    public function testInstallFailureCommandFailed()
    {
        $this->processExecutor->execute->returns(1);

        $this->expectException('Eloquent\Composer\NpmBridge\Exception\NpmCommandFailedException');
        $this->client->install('/path/to/project');
    }
}
