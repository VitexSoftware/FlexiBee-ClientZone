<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ClientZone\ui;

/**
 * Description of ProductEditor
 *
 * @author vitex
 */
class ProductEditor extends \Ease\TWB\Panel
{
    public $product = null;

    /**
     * Item editor
     *
     * @param \FlexiPeeHP\Cenik $product
     */
    public function __construct($product)
    {
        $product->defaultUrlParams['detail'] = 'full';
        $this->product                       = $product;

        $this->imageUpload('thumbnail');
        $this->imageUpload('image');


        $editForm = new \Ease\TWB\Form('product', 'cenik.php', 'POST',
            new \Ease\Html\InputHiddenTag('id', $product),
            ['enctype' => 'multipart/form-data']);


        $product->defaultUrlParams['detail'] = 'custom:poznam,id,stitky';
        $allAttachments                      = \FlexiPeeHP\Priloha::getAttachmentsList($product);

        $images = \Ease\Sand::reindexArrayBy($allAttachments, 'poznam');


        $editForm->addInput(new \Ease\Html\InputFileTag('thumbnail'),
            _('Thumbnail'), _('Small 150x150px'));

        if (array_key_exists('thumbnail', $images)) {
            $editForm->addItem(new ProductImage($product, 'thumbnail'));
        }

        $editForm->addInput(new \Ease\Html\InputFileTag('image'), _('Image'),
            _('Large image'));

        if (array_key_exists('image', $images)) {
            $editForm->addItem(new ProductImage($product, 'image'));
        }


        $editForm->addItem(new \Ease\TWB\SubmitButton(_('Save'), 'success'));

        $titlerow = new \Ease\TWB\Row();
        $titlerow->addColumn(8,
            new \Ease\Html\H4Tag($product->getDataValue('nazev')));
        $titlerow->addColumn(4,
            new LinkToFlexiBeeButton($product, ['style' => 'width:20px']));

        parent::__construct($titlerow, 'warning', $editForm,
            new LabelGroup($product));

        $labels = \FlexiPeeHP\Stitek::getLabels($product);

        $this->addItem(new LabelSwitches($product));


        foreach ($allAttachments as $attachment) {
            ///  $this->addItem($attachment['data']);
        }
    }

    /**
     *
     * @param type $field
     */
    public function imageUpload($field)
    {
        if (isset($_FILES[$field]) && strlen($_FILES[$field]['tmp_name'])) {
            $attachmentFile     = $_FILES[$field]['tmp_name'];
            $attachmentFileName = sys_get_temp_dir().'/'.$_FILES[$field]['name'];

            move_uploaded_file($attachmentFile, $attachmentFileName);

            $this->removeOldAttachment($field);

            $result = \FlexiPeeHP\Priloha::addAttachmentFromFile($this->product,
                    $attachmentFileName);
            if ($result == 201) {
                $this->product->addStatusMessage(sprintf(_('%s File %s was attached'),
                        $field, basename($attachmentFileName)), 'success');
                $this->setNewAttachment($field);
                $imager = new \ClientZone\ProductImager($this->product->getMyKey());
                $imager->storeToCache([$_FILES[$field]['type'] => file_get_contents($attachmentFileName)],
                    $field);
            } else {
                $this->product->addStatusMessage('Attachment '.$this->product->getFlexiBeeURL().' Failed',
                    'error');
            }
            unlink($attachmentFileName);
        }
    }

    public function removeOldAttachment($field)
    {
        $response                                  = null;
        //Remove label with $field note
        $this->product->defaultUrlParams['detail'] = 'full';
        $allAttachments                            = \FlexiPeeHP\Priloha::getAttachmentsList($this->product);

        foreach ($allAttachments as $atId => $attachment) {
            if ($attachment['poznam'] == $field) {
                $response = $this->product->doCurlRequest($this->product->url.'/c/'.$this->product->company.'/priloha/',
                    'DELETE');
            }
        }
        return $response;
    }

    public function setNewAttachment($field)
    {
        $this->product->defaultUrlParams['detail'] = 'id';
        $allAttachments                            = \FlexiPeeHP\Priloha::getAttachmentsList($this->product);
        $attachment                                = end($allAttachments);
        $uploader                                  = new \FlexiPeeHP\Priloha();
        $response                                  = $uploader->insertToFlexiBee([
            'id' => $attachment['id'],
            'exportNaEshop' => true,
            'poznam' => $field]);
        return $response;
    }

}
