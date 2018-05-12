<?php

/**
 * Description of SysXML.
 *
 * @author vitex
 */
namespace ClientZone;

class XML extends \Ease\Html\PairTag
{
    public function populate($data)
    {
        foreach ($data as $tagName => $tagValue) {
            if (is_array($tagValue)) {
                $this->addItem(new self($tagName, null, $tagValue));
            } else {
            }
        }
    }
}
