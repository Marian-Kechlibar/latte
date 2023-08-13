<?php

/**
 * This file is part of the Latte (https://latte.nette.org)
 * Copyright (c) 2008 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace Latte\Essential\Nodes;

use Latte;
use Latte\CompileException;
use Latte\Compiler\Nodes\StatementNode;
use Latte\Compiler\PrintContext;
use Latte\Compiler\Tag;


/**
 * n:tag="..."
 */
final class NTagNode extends StatementNode
{
	public static function create(Tag $tag): void
	{
		if (preg_match('(style$|script$)iA', $tag->htmlElement->name)) {
			throw new CompileException('Attribute n:tag is not allowed in <script> or <style>', $tag->position);
		}

		$tag->expectArguments();
		$newName = $tag->parser->parseExpression();
		$origName = $tag->htmlElement->name;
		$tag->htmlElement->variableName = Latte\Compiler\ExpressionBuilder::class(self::class)
			->staticMethod('check', [$origName, $newName])->build();
	}


	public function print(PrintContext $context): string
	{
		throw new \LogicException('Cannot directly print');
	}


	public static function check(string $orig, mixed $new): mixed
	{
		if ($new === null) {
			return $orig;
		} elseif (is_string($new)
			&& isset(Latte\Helpers::$emptyElements[strtolower($orig)]) !== isset(Latte\Helpers::$emptyElements[strtolower($new)])
		) {
			throw new Latte\RuntimeException("Forbidden tag <$orig> change to <$new>");
		}

		return $new;
	}


	public function &getIterator(): \Generator
	{
		false && yield;
	}
}
