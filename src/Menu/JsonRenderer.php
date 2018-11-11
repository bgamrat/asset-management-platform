<?php

Namespace App\Menu;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\MatcherInterface;
use Knp\Menu\Renderer\RendererInterface;
use Symfony\Component\Translation\TranslatorInterface;

class JsonRenderer implements RendererInterface
{

    /**
     * @var \Twig_Environment
     */
    private $environment;
    private $matcher;
    private $defaultOptions;
    private $translator;

    /**
     * @param \Twig_Environment $environment
     * @param string            $template
     * @param MatcherInterface  $matcher
     * @param array             $defaultOptions
     */
    public function __construct( \Twig_Environment $environment, String $template = '', MatcherInterface $matcher, array $defaultOptions = array(), TranslatorInterface $translator )
    {
        $this->environment = $environment;
        $this->matcher = $matcher;
        $this->defaultOptions = array_merge( array(
            'depth' => null,
            'matchingDepth' => null,
            'currentAsLink' => true,
            'currentClass' => 'current',
            'ancestorClass' => 'current_ancestor',
            'firstClass' => 'first',
            'lastClass' => 'last',
            'template' => $template,
            'compressed' => false,
            'allow_safe_labels' => false,
            'clear_matcher' => true,
            'leaf_class' => null,
            'branch_class' => null
                ), $defaultOptions );
        $this->translator = $translator;
    }

    public function render( ItemInterface $item, array $options = array() )
    {
        $options = array_merge( $this->defaultOptions, $options );
        if( empty( $options['depth'] ) )
        {
            $options['depth'] = PHP_INT_MAX;
        }

        $itemIterator = new \Knp\Menu\Iterator\RecursiveItemIterator( $item );

        $iterator = new \RecursiveIteratorIterator( $itemIterator, \RecursiveIteratorIterator::SELF_FIRST );

        $tree = [];
        $parent = null;
        $levelParent[0] = null;

        $lastLevel = null;
        foreach( $iterator as $item )
        {
            $translatedLabel = $this->translator->trans( $item->getLabel() );
            $id = strtolower( $item->getName() );
            $level = $item->getLevel();
            if( $level <= $options['depth'] )
            {
                $node = [];
                $node['id'] = $id;
                $node['name'] = $translatedLabel;
                $node['href'] = '/#'.$item->getUri();
                $node['has_children'] = $item->hasChildren();
                $node['level'] = $level;
                if( $lastLevel !== null )
                {
                    if( $level > $lastLevel )
                    {
                        $parent = $levelParent[$level] = $lastNode['id'];
                    }
                    else
                    {
                        if( $level < $lastLevel )
                        {
                            $parent = $levelParent[$level];
                        }
                    }
                    $lastParent = $parent;
                }
                $node['parent'] = $parent;
                $tree[$id] = $node;
                $lastLevel = $level;
                $lastNode = $node;
            }
        }
        return $tree;
    }

}
