<?php

/**
 * Description of SysWinstrom.
 *
 * @author vitex
 */
namespace Shop4FlexiBee;

class XMLWinstrom extends XML
{
    public function __construct($content = null)
    {
        parent::__construct('winstrom', ['version' => '1.0'], $content);
    }
}
