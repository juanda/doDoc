<?php

namespace Jazzyweb\MarkDownServerBundle\Services;

use dflydev\markdown\MarkdownParser;

class Markdown2Html{
    
    public function __construct(array $configuration = null){
        
        $this->mdParser = new MarkdownParser($configuration);
    }
    
    public function buildHtml($mdData)
    {
        return $this->mdParser->transformMarkdown($mdData);
    }
}
