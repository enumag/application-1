<?php

/**
 * Test: TemplateFactory custom template
 */

use Nette\Application\UI;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Bridges\ApplicationLatte\TemplateFactory;
use Tester\Assert;


require __DIR__ . '/../bootstrap.php';


class TemplateMock extends Template
{
	private $file = 'ko';


	public function render($file = null, array $params = [])
	{
		return strrev($this->file);
	}


	public function setFile($file)
	{
		$this->file = $file;
	}


	public function getFile()
	{
		return $this->file;
	}
}


test(function () {
	$latteFactory = Mockery::mock(ILatteFactory::class);
	$latteFactory->shouldReceive('create')->andReturn(new Latte\Engine);
	$factory = new TemplateFactory($latteFactory);
	Assert::type(Template::class, $factory->createTemplate());
});

Assert::exception(function () {
	$factory = new TemplateFactory(Mockery::mock(ILatteFactory::class), null, null, null, stdClass::class);
}, \Nette\InvalidArgumentException::class, 'Class stdClass does not extend Nette\Bridges\ApplicationLatte\Template or it does not exist.');


test(function () {
	$latteFactory = Mockery::mock(ILatteFactory::class);
	$latteFactory->shouldReceive('create')->andReturn(new Latte\Engine);
	$factory = new TemplateFactory($latteFactory, null, null, null, TemplateMock::class);
	$template = $factory->createTemplate();
	Assert::type(TemplateMock::class, $template);
	Assert::type(UI\ITemplate::class, $template);
	Assert::same([], $template->flashes);
	Assert::same('ok', $template->render());
	$template->setFile('bla');
	Assert::same('alb', $template->render());
});
