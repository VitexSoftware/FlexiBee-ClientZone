<?php

namespace Shop4FlexiBee;

/**
 * shop4flexibee - Našeptávač vyhledávače.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */
require_once 'includes/Init.php';

$query = $oPage->getRequestValue('q');

$found = [];

$searcher = new Searcher($oPage->getRequestValue('class'),
    $oPage->getRequestValue('selector'));

header('ContentType: text/json');

if (strlen($query) > 1) {
    $results = $searcher->searchAll($query);

    foreach ($results as $rectype => $records) {
        foreach ($records as $recid => $record) {
            $what = isset($record['kod']) ? $record['kod'] : current($record);

            $found[] = ['id' => $recid, 'url' => $rectype.'.php?id='.$record['id'],
                'name' => $record[0], 'type' => $rectype, 'what' => $what];
        }
    }
}
echo json_encode($found);
