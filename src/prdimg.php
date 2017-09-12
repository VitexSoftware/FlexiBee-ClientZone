<?php
/**
 * shop4flexibee - Obrázek produktu.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2017 VitexSoftware v.s.cz
 */
namespace Shop4FlexiBee;

require_once 'includes/Init.php';

$id = $oPage->getRequestValue('id');
if (is_null($id)) {
    die(_('Missing product ID'));
}

$imager = new ProductImager($id);
ProductImager::output($imager->getProductImage($oPage->getRequestValue('type') ? $oPage->getRequestValue('type')
                : 'image'));
