<?php
/**
 * Author: Radek
 * Date: 26/03/15 12:03
 */

namespace DBBuilder;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

	/**
	 * Generates the configuration tree builder.
	 *
	 * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
	 */
	public function getConfigTreeBuilder()
	{
		$treeBuilder = new TreeBuilder();
		$rootNode    = $treeBuilder->root('dumper');
		$rootNode->children()
			->arrayNode('config')
				->children()
					->arrayNode('source')
						->children()
							->scalarNode('user')
							->end()
							->scalarNode('host')
							->end()
							->scalarNode('password')
							->end()
						->end()
					->end()
					->arrayNode('destination')
						->children()
							->scalarNode('user')
							->end()
							->scalarNode('host')
							->end()
							->scalarNode('password')
							->end()
						->end()
					->end()
				->end()
			->end()
			->arrayNode('definitions')
				->prototype('array')
					->children()
						->arrayNode('databases')
							->prototype('array')
								->children()
									->arrayNode('excludeData')
										->defaultValue([])
										->prototype('scalar')
										->end()
									->end()
									->arrayNode('includeData')
										->defaultValue([])
										->prototype('scalar')
										->end()
									->end()
								->end()
							->end()
						->end()
					->end()
				->end()
			->end()
		->end();


		return $treeBuilder;
	}
}